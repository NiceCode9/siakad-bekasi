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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isWaliKelas = $user->hasRole('guru') && $user->guru && $user->guru->kelasWali;

        if (!$isWaliKelas && !$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            abort(403, 'Anda bukan wali kelas.');
        }

        $semester = Semester::where('is_active', true)->first();
        $kelas = null;

        if ($user->hasRole('guru')) {
            $kelas = $user->guru->kelasWali()->first();
        }

        $siswas = [];
        if ($kelas) {
            $siswas = Siswa::whereHas('kelas', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })->with(['raports' => function($q) use ($semester) {
                $q->where('semester_id', $semester->id);
            }])->get();
        }

        return view('raport.index', compact('siswas', 'kelas', 'semester'));
    }

    public function generate($siswa_id, $semester_id)
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
                ['siswa_id' => $siswa_id, 'semester_id' => $semester_id],
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
                    ->get();

                if ($scores->count() > 0) {
                    $nilaiPengetahuan = $scores->whereIn('jenis_nilai', ['tugas', 'ulangan_harian', 'uts', 'uas'])->avg('nilai');
                    $nilaiKeterampilan = $scores->whereIn('jenis_nilai', ['praktik', 'proyek'])->avg('nilai');
                    
                    // Fallback if no skills assessment
                    if (is_null($nilaiKeterampilan)) $nilaiKeterampilan = $nilaiPengetahuan;

                    $nilaiAkhir = ($nilaiPengetahuan + $nilaiKeterampilan) / 2;

                    // Calculate subject-specific attendance from presensi_mapel
                    $journalIds = \App\Models\JurnalMengajar::whereHas('jadwalPelajaran', function($q) use ($mk) {
                            $q->where('mata_pelajaran_kelas_id', $mk->id);
                        })
                        ->where('semester_id', $semester_id)
                        ->pluck('id');

                    $totalPertemuan = $journalIds->count();
                    $jumlahHadir = \App\Models\PresensiMapel::whereIn('jurnal_mengajar_id', $journalIds)
                        ->where('siswa_id', $siswa_id)
                        ->where('status', 'H')
                        ->count();

                    $persentaseKehadiran = $totalPertemuan > 0 ? ($jumlahHadir / $totalPertemuan) * 100 : 0;

                    RaportDetail::updateOrCreate(
                        ['raport_id' => $raport->id, 'mata_pelajaran_id' => $mk->mata_pelajaran_id],
                        [
                            'nilai_pengetahuan' => $nilaiPengetahuan,
                            'nilai_keterampilan' => $nilaiKeterampilan,
                            'nilai_akhir' => $nilaiAkhir,
                            'predikat' => $this->calculatePredikat($nilaiAkhir),
                            'deskripsi' => "Menunjukkan pemahaman yang baik dalam mata pelajaran " . $mk->mataPelajaran->nama,
                            'jumlah_pertemuan' => $totalPertemuan,
                            'jumlah_hadir' => $jumlahHadir,
                            'persentase_kehadiran' => round($persentaseKehadiran, 2)
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->route('raport.show', $raport->id)->with('success', 'Raport berhasil di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate raport: ' . $e->getMessage());
        }
    }

    private function calculatePredikat($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }

    public function show($id)
    {
        $raport = Raport::with(['siswa', 'semester', 'kelas', 'raportDetail.mataPelajaran'])->findOrFail($id);
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
        $raport = Raport::with(['siswa.biodata', 'semester.tahunAkademik', 'kelas', 'raportDetail.mataPelajaran'])->findOrFail($id);
        $nilaiSikap = NilaiSikap::where('siswa_id', $raport->siswa_id)->where('semester_id', $raport->semester_id)->first();
        $nilaiEkskul = NilaiEkstrakurikuler::with('ekstrakurikuler')->where('siswa_id', $raport->siswa_id)->where('semester_id', $raport->semester_id)->get();
        $nilaiPkl = NilaiPkl::with('pkl.perusahaanPkl')->whereHas('pkl', function($q) use ($raport) {
            $q->where('siswa_id', $raport->siswa_id);
        })->first();

        $pdf = Pdf::loadView('raport.pdf', compact('raport', 'nilaiSikap', 'nilaiEkskul', 'nilaiPkl'));
        return $pdf->download('Raport_'.$raport->siswa->nama.'_'.$raport->semester->nama.'.pdf');
    }
}
