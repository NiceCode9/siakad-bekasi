<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\SiswaKelas;
use App\Models\MataPelajaranKelas;
use App\Models\Raport;
use App\Models\RaportDetail;
use App\Models\NilaiSikap;
use App\Models\NilaiEkstrakurikuler;
use App\Models\NilaiPkl;
use App\Models\PresensiMapel;
use App\Models\JurnalMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardNilaiController extends Controller
{
    /**
     * Display a listing of students in Wali Kelas's class.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $semesterAktif = Semester::active()->first();
        
        if (!$semesterAktif) {
            return redirect()->route('dashboard')->with('error', 'Semester aktif tidak ditemukan.');
        }

        // Must be Admin or Wali Kelas
        if (!$user->hasRole(['admin', 'super-admin'])) {
            if (!$user->guru || !$user->guru->kelasWali()->exists()) {
                return redirect()->route('dashboard')->with('error', 'Menu ini hanya dapat diakses oleh Wali Kelas.');
            }
            $kelas = $user->guru->kelasWali()->where('semester_id', $semesterAktif->id)->first();
        } else {
            $kelasId = $request->kelas_id ?? Kelas::where('semester_id', $semesterAktif->id)->first()?->id;
            $kelas = $kelasId ? Kelas::find($kelasId) : null;
        }

        if (!$kelas) {
            return view('pembelajaran.dashboard-nilai.index', ['siswas' => collect(), 'kelas' => null, 'allKelas' => Kelas::where('semester_id', $semesterAktif->id)->get()]);
        }

        $allKelas = $user->hasRole(['admin', 'super-admin']) ? Kelas::where('semester_id', $semesterAktif->id)->get() : collect([$kelas]);

        $siswas = SiswaKelas::with(['siswa', 'raports' => function($q) use ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            }])
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get();

        // Count academic subjects for completion calculation
        $subjectCount = MataPelajaranKelas::where('kelas_id', $kelas->id)->count();

        foreach ($siswas as $sk) {
            $raport = $sk->raports->first();
            
            // Completion stats
            $sk->stats = [
                'akademik_count' => $raport ? $raport->raportDetails()->count() : 0,
                'akademik_percent' => $subjectCount > 0 ? (($raport ? $raport->raportDetails()->count() : 0) / $subjectCount) * 100 : 0,
                'sikap_spiritual' => NilaiSikap::where('siswa_id', $sk->siswa_id)->where('semester_id', $semesterAktif->id)->where('aspek', 'spiritual')->exists(),
                'sikap_sosial' => NilaiSikap::where('siswa_id', $sk->siswa_id)->where('semester_id', $semesterAktif->id)->where('aspek', 'sosial')->exists(),
                'ekskul' => NilaiEkstrakurikuler::where('siswa_id', $sk->siswa_id)->where('semester_id', $semesterAktif->id)->exists(),
                'raport_status' => $raport->status ?? 'belum_generate',
                'raport_id' => $raport->id ?? null,
            ];
        }

        return view('pembelajaran.dashboard-nilai.index', compact('siswas', 'kelas', 'allKelas', 'subjectCount', 'semesterAktif'));
    }

    /**
     * Display detailed grades for a student.
     */
    public function show($siswa_id)
    {
        $user = Auth::user();
        $semesterAktif = Semester::active()->first();
        $siswa = Siswa::findOrFail($siswa_id);
        
        // Find student class
        $sk = SiswaKelas::where('siswa_id', $siswa_id)
            ->where('status', 'aktif')
            ->whereHas('kelas', function($q) use ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            })->first();

        if (!$sk) {
            return redirect()->route('dashboard-nilai.index')->with('error', 'Siswa tidak ditemukan di kelas aktif manapun.');
        }

        $kelas = $sk->kelas;

        // Authorization check
        if (!$user->hasRole(['admin', 'super-admin'])) {
            if ($kelas->wali_kelas_id != ($user->guru->id ?? 0)) {
                return redirect()->route('dashboard-nilai.index')->with('error', 'Anda tidak memiliki akses untuk melihat detail siswa ini.');
            }
        }

        // Get Academic Grades (from RaportDetail)
        $raport = Raport::where('siswa_id', $siswa_id)->where('semester_id', $semesterAktif->id)->first();
        $grades = $raport ? RaportDetail::with('mataPelajaran')->where('raport_id', $raport->id)->get() : collect();

        // Get Attitude Grades
        $sikap = NilaiSikap::where('siswa_id', $siswa_id)->where('semester_id', $semesterAktif->id)->get();

        // Get Ekskul
        $ekskul = NilaiEkstrakurikuler::with('ekstrakurikuler')->where('siswa_id', $siswa_id)->where('semester_id', $semesterAktif->id)->get();

        // Get attendance aggregate
        $attendance = \App\Models\PresensiSiswa::where('siswa_id', $siswa_id)
            ->whereHas('kelas', function($q) use ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            })
            ->selectRaw("SUM(CASE WHEN status='S' THEN 1 ELSE 0 END) as sakit")
            ->selectRaw("SUM(CASE WHEN status='I' THEN 1 ELSE 0 END) as izin")
            ->selectRaw("SUM(CASE WHEN status='A' THEN 1 ELSE 0 END) as alpha")
            ->first();

        return view('pembelajaran.dashboard-nilai.show', compact('siswa', 'kelas', 'grades', 'sikap', 'ekskul', 'attendance', 'semesterAktif', 'raport'));
    }
}
