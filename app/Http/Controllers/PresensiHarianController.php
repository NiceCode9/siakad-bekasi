<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\PresensiSiswa;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiHarianController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manage-absensi-harian');

        $activeTahun = TahunAkademik::active()->first();
        if (!$activeTahun) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        $kelas = Kelas::whereHas('semester', function ($q) use ($activeTahun) {
            $q->where('tahun_akademik_id', $activeTahun->id);
        })->get();

        $selectedKelasId = $request->kelas_id;
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $students = collect([]);
        $attendanceData = collect([]);

        if ($selectedKelasId) {
            $students = Siswa::whereHas('siswaKelas', function ($q) use ($selectedKelasId) {
                $q->where('kelas_id', $selectedKelasId)->where('status', 'aktif');
            })->orderBy('nama_lengkap')->get();

            $attendanceData = PresensiSiswa::where('kelas_id', $selectedKelasId)
                ->where('tanggal', $tanggal)
                ->get()
                ->keyBy('siswa_id');
        }

        return view('presensi-harian.index', compact('kelas', 'selectedKelasId', 'tanggal', 'students', 'attendanceData'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-daily-attendance');

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:H,I,S,A',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $siswaId => $data) {
                // Determine if we need to save (skip if 'H' and no note to save space? Optional. 
                // For now, save everything to be explicit.)
                
                PresensiSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'tanggal' => $request->tanggal,
                    ],
                    [
                        'kelas_id' => $request->kelas_id,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'user_id' => Auth::id(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('presensi-harian.index', [
                'kelas_id' => $request->kelas_id, 
                'tanggal' => $request->tanggal
            ])->with('success', 'Data presensi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
        }
    }
}
