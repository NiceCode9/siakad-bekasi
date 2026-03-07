<?php

namespace App\Http\Controllers;

use App\Models\Raport;
use App\Models\RaportDetail;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\Nilai;
use App\Models\NilaiSikap;
use App\Models\NilaiEkstrakurikuler;
use App\Models\NilaiPkl;
use App\Models\PresensiSiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $semester = Semester::where('is_active', true)->first();

        // If user is a student
        if ($user->hasRole('siswa')) {
            $siswa = $user->siswa;
            $raports = Raport::where('siswa_id', $siswa->id)
                ->with(['semester', 'kelas'])
                ->orderByDesc('created_at')
                ->get();

            return view('raport.student', compact('siswa', 'raports', 'semester'));
        }

        $isWaliKelas = $user->hasRole('guru') && $user->guru && $user->guru->kelasWali;

        if (!$isWaliKelas && !$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            abort(403, 'Anda bukan wali kelas atau admin.');
        }

        // Get filter inputs
        $filterTahun = $request->get('tahun_akademik_id');
        $filterSemester = $request->get('semester_id');
        $filterSiswa = $request->get('siswa_id');

        // Determine active semester if not filtered
        if (!$filterSemester) {
            $currentSemester = Semester::where('is_active', true)->first();
            $filterSemester = $currentSemester?->id;
            $filterTahun = $currentSemester?->tahun_akademik_id;
        } else {
            $currentSemester = Semester::find($filterSemester);
            $filterTahun = $currentSemester?->tahun_akademik_id;
        }

        $kelas = null;
        $siswasQuery = Siswa::query();

        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            // Admin filters
            if ($filterSemester) {
                $siswasQuery->whereHas('kelas', function($q) use ($filterSemester) {
                    $q->where('semester_id', $filterSemester);
                });
            }

            if ($filterSiswa) {
                $siswasQuery->where('id', $filterSiswa);
            }

            $siswas = $siswasQuery->with(['raports' => function($q) use ($filterSemester) {
                if ($filterSemester) {
                    $q->where('semester_id', $filterSemester);
                }
            }])->get();

            $tahunAkademiks = TahunAkademik::orderBy('nama', 'desc')->get();
            $allSiswa = Siswa::orderBy('nama_lengkap')->get();

            $komponens = \App\Models\KomponenNilai::where('kurikulum_id', $semester->tahunAkademik->kurikulum_id ?? 0)->get();
            // For Admin, we allow selecting any semester
            $semester = $currentSemester;
            return view('raport.index', compact('siswas', 'kelas', 'semester', 'tahunAkademiks', 'allSiswa', 'filterTahun', 'filterSemester', 'filterSiswa', 'komponens'));
        }

        // Wali Kelas logic
        $kelas = $user->guru->kelasWali()->where('semester_id', $semester->id)->first();
        if ($kelas) {
            $siswas = Siswa::whereHas('kelas', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })->with(['raports' => function($q) use ($semester) {
                $q->where('semester_id', $semester->id);
            }])->get();
        } else {
            $siswas = [];
        }

        $komponens = \App\Models\KomponenNilai::where('kurikulum_id', $semester->tahunAkademik->kurikulum_id ?? 0)->get();

        return view('raport.index', compact('siswas', 'kelas', 'semester', 'komponens'));
    }

    public function getSemestersByTahun($tahun_id)
    {
        $semesters = Semester::where('tahun_akademik_id', $tahun_id)
            ->orderBy('nama', 'asc')
            ->get(['id', 'nama']);

        return response()->json($semesters);
    }

    public function generate($siswa_id, $semester_id, $komponen_nilai_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $semester = Semester::findOrFail($semester_id);
        $kelas = $siswa->kelas()->wherePivot('status', 'aktif')->first();

        if (!$kelas) {
             $kelas = $siswa->kelas()->latest()->first();
        }

        DB::beginTransaction();
        try {
            $raport = Raport::updateOrCreate(
                ['siswa_id' => $siswa_id, 'semester_id' => $semester_id, 'komponen_nilai_id' => $komponen_nilai_id],
                [
                    'kelas_id' => $kelas->id,
                    'tanggal_generate' => now(),
                    'status' => 'draft'
                ]
            );

            // 1. Aggregate Attendance
            $attendance = PresensiSiswa::where('siswa_id', $siswa_id)
                ->whereHas('kelas', function($q) use ($semester_id) {
                    $q->where('semester_id', $semester_id);
                })
                ->selectRaw("SUM(CASE WHEN status='S' THEN 1 ELSE 0 END) as sakit")
                ->selectRaw("SUM(CASE WHEN status='I' THEN 1 ELSE 0 END) as izin")
                ->selectRaw("SUM(CASE WHEN status='A' THEN 1 ELSE 0 END) as alpha")
                ->first();

            $raport->update([
                'jumlah_sakit' => $attendance->sakit ?? 0,
                'jumlah_izin' => $attendance->izin ?? 0,
                'jumlah_alpha' => $attendance->alpha ?? 0,
            ]);

            // 2. Aggregate Academic Scores
            $mapelKelas = MataPelajaranKelas::where('kelas_id', $kelas->id)->get();

            foreach ($mapelKelas as $mk) {
                $scores = Nilai::where('siswa_id', $siswa_id)
                    ->where('mata_pelajaran_kelas_id', $mk->id)
                    ->where('semester_id', $semester_id)
                    ->where('komponen_nilai_id', $komponen_nilai_id)
                    ->get();

                if ($scores->count() > 0) {
                    $nilaiAkhir = $scores->avg('nilai');
                    $nilaiPengetahuan = $nilaiAkhir; // Diadaptasi, jika butuh breakdown bisa ditambah kolom
                    $nilaiKeterampilan = $nilaiAkhir;

                    // Calculate subject-specific attendance from presensi_mapel
                    $journalIds = \App\Models\JurnalMengajar::whereHas('jadwalPelajaran', function($q) use ($mk) {
                            $q->where('mata_pelajaran_kelas_id', $mk->id);
                        })
                        ->pluck('id');

                    $totalPertemuan = $journalIds->count();
                    $jumlahHadir = \App\Models\PresensiMapel::whereIn('jurnal_mengajar_id', $journalIds)
                        ->where('siswa_id', $siswa_id)
                        ->where('status', 'H')
                        ->count();

                    $persentaseKehadiran = $totalPertemuan > 0 ? ($jumlahHadir / $totalPertemuan) * 100 : 0;

                    $predikatHuruf = $this->calculatePredikat($nilaiAkhir, $mk->kkm);
                    
                    $predikatField = 'capaian_kompetensi_' . strtolower($predikatHuruf);
                    $deskripsiAkhir = $mk->$predikatField;
                    
                    if (empty(trim($deskripsiAkhir))) {
                        $predikatWords = [
                            'A' => 'SANGAT BAIK',
                            'B' => 'BAIK',
                            'C' => 'CUKUP',
                            'D' => 'KURANG'
                        ];
                        $predikatDeskripsi = $predikatWords[$predikatHuruf] ?? 'CUKUP';
                        $deskripsiAkhir = "Menunjukkan capaian kompetensi dalam mata pelajaran " . $mk->mataPelajaran->nama . " dengan predikat " . $predikatDeskripsi . ".";
                    }

                    RaportDetail::updateOrCreate(
                        ['raport_id' => $raport->id, 'mata_pelajaran_id' => $mk->mata_pelajaran_id],
                        [
                            'nilai_pengetahuan' => $nilaiPengetahuan,
                            'nilai_keterampilan' => $nilaiKeterampilan,
                            'nilai_akhir' => $nilaiAkhir,
                            'predikat' => $predikatHuruf,
                            'deskripsi' => $deskripsiAkhir,
                            'jumlah_pertemuan' => $totalPertemuan,
                            'jumlah_hadir' => $jumlahHadir,
                            'persentase_kehadiran' => round($persentaseKehadiran, 2)
                        ]
                    );
                }
            }

            // dd($scores);

            DB::commit();
            return redirect()->route('raport.show', $raport->id)->with('success', 'Raport berhasil di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Gagal generate raport: ' . $e->getMessage());
        }
    }

    private function calculatePredikat($nilai, $kkm = null)
    {
        $kkm = $kkm ?? 70; // Fallback to 70 if not set
        $interval = (100 - $kkm) / 3;

        if ($nilai >= (100 - $interval)) return 'A';
        if ($nilai >= ($kkm + $interval)) return 'B';
        if ($nilai >= $kkm) return 'C';
        return 'D';
    }

    public function show($id)
    {
        $raport = Raport::with(['siswa', 'semester', 'kelas', 'raportDetail.mataPelajaran'])->findOrFail($id);

        $user = Auth::user();
        if ($user->hasRole('siswa') && $raport->siswa_id !== $user->siswa->id) {
            abort(403, 'Anda tidak memiliki hak akses melihat raport ini.');
        }

        $nilaiSikap = NilaiSikap::where('siswa_id', $raport->siswa_id)->where('semester_id', $raport->semester_id)->first();
        $nilaiEkskul = NilaiEkstrakurikuler::with('ekstrakurikuler')->where('siswa_id', $raport->siswa_id)->where('semester_id', $raport->semester_id)->get();
        $nilaiPkl = NilaiPkl::whereHas('pkl', function($q) use ($raport) {
            $q->where('siswa_id', $raport->siswa_id);
        })->first();

        return view('raport.show', compact('raport', 'nilaiSikap', 'nilaiEkskul', 'nilaiPkl'));
    }

    public function update(Request $request, $id)
    {
        $raport = Raport::findOrFail($id);
        $raport->update($request->only(['catatan_wali_kelas', 'jumlah_sakit', 'jumlah_izin', 'jumlah_alpha']));

        return back()->with('success', 'Data tambahan raport berhasil disimpan.');
    }

    public function approve($id)
    {
        if (!Auth::user()->can('approve-raport')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menyetujui raport.');
        }

        $raport = Raport::findOrFail($id);
        $raport->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Raport berhasil disetujui.');
    }

    public function publish($id)
    {
        $raport = Raport::findOrFail($id);

        if ($raport->status !== 'approved') {
            return back()->with('error', 'Raport harus disetujui oleh Kepala Sekolah terlebih dahulu.');
        }

        $raport->update(['status' => 'published']);

        return back()->with('success', 'Raport berhasil dipublikasikan.');
    }

    public function print($id)
    {
        $raport = Raport::with(['siswa', 'semester.tahunAkademik', 'kelas', 'raportDetail.mataPelajaran'])->findOrFail($id);

        $user = Auth::user();
        if ($user->hasRole('siswa') && $raport->siswa_id !== $user->siswa->id) {
            abort(403, 'Anda tidak memiliki hak akses mencetak raport ini.');
        }

        $nilaiSikap = NilaiSikap::where('siswa_id', $raport->siswa_id)->where('semester_id', $raport->semester_id)->first();
        $nilaiEkskul = NilaiEkstrakurikuler::with('ekstrakurikuler')->where('siswa_id', $raport->siswa_id)->where('semester_id', $raport->semester_id)->get();
        $nilaiPkl = NilaiPkl::with('pkl.perusahaanPkl')->whereHas('pkl', function($q) use ($raport) {
            $q->where('siswa_id', $raport->siswa_id);
        })->first();

        $pdf = Pdf::loadView('raport.pdf', compact('raport', 'nilaiSikap', 'nilaiEkskul', 'nilaiPkl'));
        return $pdf->stream('Raport_'.$raport->siswa->nama_lengkap.'_'.$raport->semester->nama.'.pdf');
    }
}
