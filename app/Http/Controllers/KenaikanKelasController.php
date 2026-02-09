<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KenaikanKelas;
use App\Models\KenaikanKelasDetail;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index()
    {
        // For the new workflow, we only need all academic years initially
        $tahunAkademiks = TahunAkademik::orderBy('nama', 'desc')->get();
        $history = KenaikanKelas::with(['tahunAkademik', 'processedBy'])->latest()->get();

        return view('kenaikan-kelas.index', compact('tahunAkademiks', 'history'));
    }

    public function getClassesByYear(Request $request)
    {
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $classes = Kelas::with('jurusan')
            ->whereHas('semester', function ($q) use ($request) {
                $q->where('tahun_akademik_id', $request->tahun_akademik_id);
            })
            ->get();

        // Check which classes have already been processed in the history
        $processedClassIds = KenaikanKelasDetail::whereHas('kenaikanKelas', function ($q) {
            // Technically a class is processed if its students from THAT specific source class/year were moved
            $q->where('status', 'selesai');
        })
            ->pluck('kelas_asal_id')
            ->unique()
            ->toArray();

        $data = $classes->map(function ($class) use ($processedClassIds) {
            return [
                'id' => $class->id,
                'nama' => $class->nama,
                'jurusan' => $class->jurusan->singkatan,
                'is_processed' => in_array($class->id, $processedClassIds),
            ];
        });

        return response()->json($data);
    }

    public function simulasi(Request $request)
    {
        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $kelasAsal = Kelas::with('semester.tahunAkademik')->findOrFail($request->kelas_asal_id);
        $tahunAkademikTarget = TahunAkademik::findOrFail($request->tahun_akademik_id);

        // Validation: Source class cannot be in the target year
        if ($kelasAsal->semester->tahun_akademik_id == $tahunAkademikTarget->id) {
            return back()->with('error', 'Gagal: Kelas asal tidak boleh berada di tahun akademik yang sama dengan target kenaikan kelas.');
        }

        // Get students in this class for the original semester/year
        $students = Siswa::whereHas('siswaKelas', function ($q) use ($request) {
            $q->where('kelas_id', $request->kelas_asal_id)->where('status', 'aktif');
        })->with(['raports' => function ($q) use ($kelasAsal) {
            $q->where('semester_id', $kelasAsal->semester_id)->latest();
        }])->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa aktif di kelas asal yang dipilih.');
        }

        // Logic to filter target classes: only same level (for repeaters) and next level
        $romanToLevel = ['X' => 10, 'XI' => 11, 'XII' => 12];
        $levelToRoman = [10 => 'X', 11 => 'XI', 12 => 'XII'];

        $currentTingkat = $kelasAsal->tingkat;
        $currentLevelInt = $romanToLevel[$currentTingkat] ?? 0;
        $nextLevelInt = $currentLevelInt + 1;

        $allowedTingkat = [$currentTingkat];
        if (isset($levelToRoman[$nextLevelInt])) {
            $allowedTingkat[] = $levelToRoman[$nextLevelInt];
        }

        // Filter target classes: must be in the target academic year, same jurusan, and allowed levels
        $targetClasses = Kelas::with('semester')->whereHas('semester', function ($q) use ($tahunAkademikTarget) {
            $q->where('tahun_akademik_id', $tahunAkademikTarget->id);
        })
            ->where('jurusan_id', $kelasAsal->jurusan_id)
            ->whereIn('tingkat', $allowedTingkat)
            ->get();

        if ($targetClasses->isEmpty()) {
            return back()->with('error', 'Gagal: Tidak ditemukan kelas tujuan di Tahun Akademik Target ('.$tahunAkademikTarget->nama.') untuk jurusan yang sama.');
        }

        return view('kenaikan-kelas.simulasi', compact('kelasAsal', 'tahunAkademikTarget', 'students', 'targetClasses'));
    }

    public function eksekusi(Request $request)
    {
        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'students' => 'required|array',
            'students.*.id' => 'required|exists:siswa,id',
            'students.*.status' => 'required|in:naik,tidak_naik,lulus,mengulang',
            'students.*.kelas_tujuan_id' => 'nullable|exists:kelas,id',
        ]);

        $tahunAkademikTarget = TahunAkademik::findOrFail($request->tahun_akademik_id);

        DB::beginTransaction();
        try {
            $kenaikan = KenaikanKelas::create([
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'tanggal_proses' => now(),
                'status' => 'selesai',
                'total_siswa' => count($request->students),
                'total_naik' => collect($request->students)->where('status', 'naik')->count() + collect($request->students)->where('status', 'lulus')->count(),
                'total_tidak_naik' => collect($request->students)->whereIn('status', ['tidak_naik', 'mengulang'])->count(),
                'processed_by' => Auth::id(),
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->students as $sData) {
                $siswa = Siswa::findOrFail($sData['id']);

                // Get latest academic data for detail
                $raport = Raport::where('siswa_id', $siswa->id)->latest()->first();

                KenaikanKelasDetail::create([
                    'kenaikan_kelas_id' => $kenaikan->id,
                    'siswa_id' => $siswa->id,
                    'kelas_asal_id' => $request->kelas_asal_id,
                    'kelas_tujuan_id' => $sData['kelas_tujuan_id'] ?? null,
                    'status_kenaikan' => $sData['status'],
                    'rata_rata_nilai' => $raport->average_score ?? 0,
                    'total_absensi' => $raport ? (($raport->jumlah_sakit ?? 0) + ($raport->jumlah_izin ?? 0) + ($raport->jumlah_alpha ?? 0)) : 0,
                ]);

                // Update current class status to 'pindah' (or alumni)
                SiswaKelas::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $request->kelas_asal_id)
                    ->where('status', 'aktif')
                    ->update([
                        'status' => $sData['status'] == 'lulus' ? 'keluar' : 'pindah',
                        'tanggal_keluar' => now(),
                    ]);

                // If promoted or repeating, add to new/same class
                if (in_array($sData['status'], ['naik', 'tidak_naik', 'mengulang']) && isset($sData['kelas_tujuan_id'])) {
                    SiswaKelas::create([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $sData['kelas_tujuan_id'],
                        'tanggal_masuk' => now(),
                        'status' => 'aktif',
                    ]);
                }

                // If graduated
                if ($sData['status'] == 'lulus') {
                    $siswa->update(['status' => 'lulus', 'tanggal_keluar' => now()]);
                }
            }

            DB::commit();

            return redirect()->route('kenaikan-kelas.index')->with('success', 'Proses kenaikan kelas berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses kenaikan kelas: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $kenaikan = KenaikanKelas::with(['tahunAkademik', 'processedBy', 'kenaikanKelasDetail.siswa', 'kenaikanKelasDetail.kelasAsal', 'kenaikanKelasDetail.kelasTujuan'])->findOrFail($id);

        return view('kenaikan-kelas.show', compact('kenaikan'));
    }
}
