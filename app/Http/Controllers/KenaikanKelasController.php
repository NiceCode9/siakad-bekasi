<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\TahunAkademik;
use App\Models\KenaikanKelas;
use App\Models\KenaikanKelasDetail;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Raport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index()
    {
        $classes = Kelas::with('jurusan')->get();
        $tahunAkademiks = TahunAkademik::orderBy('nama', 'desc')->get();
        $history = KenaikanKelas::with(['tahunAkademik', 'processedBy'])->latest()->get();

        return view('kenaikan-kelas.index', compact('classes', 'tahunAkademiks', 'history'));
    }

    public function simulasi(Request $request)
    {
        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $kelasAsal = Kelas::findOrFail($request->kelas_asal_id);
        $tahunAkademik = TahunAkademik::findOrFail($request->tahun_akademik_id);

        // Get students in this class for the current active semester/year
        $students = Siswa::whereHas('siswaKelas', function ($q) use ($request) {
            $q->where('kelas_id', $request->kelas_asal_id)->where('status', 'aktif');
        })->with(['raport' => function ($q) {
            $q->latest(); // Get latest raport for average/attendance check
        }])->get();

        // Target classes (usually next level, same jurusan)
        $targetClasses = Kelas::where('jurusan_id', $kelasAsal->jurusan_id)
            ->where('id', '!=', $kelasAsal->id)
            ->get();

        return view('kenaikan-kelas.simulasi', compact('kelasAsal', 'tahunAkademik', 'students', 'targetClasses'));
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
                    'total_absensi' => ($raport->jumlah_sakit ?? 0) + ($raport->jumlah_izin ?? 0) + ($raport->jumlah_alpha ?? 0),
                ]);

                // Update current class status to 'pindah' (or alumni)
                SiswaKelas::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $request->kelas_asal_id)
                    ->where('status', 'aktif')
                    ->update([
                        'status' => $sData['status'] == 'lulus' ? 'keluar' : 'pindah',
                        'tanggal_keluar' => now()
                    ]);

                // If promoted or repeating, add to new/same class
                if (in_array($sData['status'], ['naik', 'tidak_naik', 'mengulang']) && isset($sData['kelas_tujuan_id'])) {
                    SiswaKelas::create([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $sData['kelas_tujuan_id'],
                        'tanggal_masuk' => now(),
                        'status' => 'aktif'
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
            return back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $kenaikan = KenaikanKelas::with(['tahunAkademik', 'processedBy', 'kenaikanKelasDetail.siswa', 'kenaikanKelasDetail.kelasAsal', 'kenaikanKelasDetail.kelasTujuan'])->findOrFail($id);
        return view('kenaikan-kelas.show', compact('kenaikan'));
    }
}
