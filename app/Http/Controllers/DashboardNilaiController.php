<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\NilaiEkstrakurikuler;
use App\Models\NilaiSikap;
use App\Models\Raport;
use App\Models\RaportDetail;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
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

        if (! $semesterAktif) {
            return redirect()->route('dashboard')->with('error', 'Semester aktif tidak ditemukan.');
        }

        // Must be Admin or Wali Kelas
        if (! $user->hasRole(['admin', 'super-admin'])) {
            if (! $user->guru || ! $user->guru->kelasWali()->exists()) {
                return redirect()->route('dashboard')->with('error', 'Menu ini hanya dapat diakses oleh Wali Kelas.');
            }
            $kelas = $user->guru->kelasWali()->where('semester_id', $semesterAktif->id)->first();
        } else {
            $kelasId = $request->kelas_id ?? Kelas::where('semester_id', $semesterAktif->id)->first()?->id;
            $kelas = $kelasId ? Kelas::find($kelasId) : null;
        }

        if (! $kelas) {
            return view('pembelajaran.dashboard-nilai.index', ['siswas' => collect(), 'kelas' => null, 'allKelas' => Kelas::where('semester_id', $semesterAktif->id)->get()]);
        }

        $allKelas = $user->hasRole(['admin', 'super-admin']) ? Kelas::where('semester_id', $semesterAktif->id)->get() : collect([$kelas]);

        $siswas = SiswaKelas::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->with(['siswa' => function($q) use ($semesterAktif) {
                $q->with(['raports' => function($rq) use ($semesterAktif) {
                    $rq->where('semester_id', $semesterAktif->id)->with('raportDetail');
                }]);
            }])
            ->get();

        // Get all components for curriculum of this semester
        $komponens = \App\Models\KomponenNilai::where('kurikulum_id', $semesterAktif->tahunAkademik->kurikulum_id ?? 0)->get();
        $komponenIds = $komponens->pluck('id');
        $komponenCount = $komponens->count();

        // Count academic subjects for completion calculation
        $subjectCount = MataPelajaranKelas::where('kelas_id', $kelas->id)->count();

        foreach ($siswas as $sk) {
            $raports = $sk->siswa->raports;
            
            // Calculate unique subjects with grades across all components
            $uniqueMapelIds = collect();
            $anyDraft = false;
            $anyApproved = false;
            $allPublished = $komponenCount > 0;
            $anyGenerated = $raports->count() > 0;

            foreach ($raports as $r) {
                foreach ($r->raportDetail as $rd) {
                    $uniqueMapelIds->push($rd->mata_pelajaran_id);
                }
                
                if ($r->status == 'draft') $anyDraft = true;
                if ($r->status == 'approved') $anyApproved = true;
                if ($r->status != 'published') $allPublished = false;
            }

            if ($raports->count() < $komponenCount) $allPublished = false;

            $statusText = 'belum_generate';
            if ($allPublished && $komponenCount > 0) {
                $statusText = 'published';
            } elseif ($anyApproved) {
                $statusText = 'approved';
            } elseif ($anyGenerated || $anyDraft) {
                $statusText = 'draft';
            }

            // Completion stats
            $sk->stats = [
                'akademik_count' => $uniqueMapelIds->unique()->count(),
                'akademik_percent' => $subjectCount > 0 ? ($uniqueMapelIds->unique()->count() / $subjectCount) * 100 : 0,
                'sikap_spiritual' => NilaiSikap::where('siswa_id', $sk->siswa_id)->where('semester_id', $semesterAktif->id)->where('aspek', 'spiritual')->exists(),
                'sikap_sosial' => NilaiSikap::where('siswa_id', $sk->siswa_id)->where('semester_id', $semesterAktif->id)->where('aspek', 'sosial')->exists(),
                'ekskul' => NilaiEkstrakurikuler::where('siswa_id', $sk->siswa_id)->where('semester_id', $semesterAktif->id)->exists(),
                'raport_status' => $statusText,
                'raport_id' => $raports->first()?->id ?? null, // Default link to first component
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
            ->whereHas('kelas', function ($q) use ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            })->first();

        if (! $sk) {
            return redirect()->route('dashboard-nilai.index')->with('error', 'Siswa tidak ditemukan di kelas aktif manapun.');
        }

        $kelas = $sk->kelas;

        // Authorization check
        if (! $user->hasRole(['admin', 'super-admin'])) {
            if ($kelas->wali_kelas_id != ($user->guru->id ?? 0)) {
                return redirect()->route('dashboard-nilai.index')->with('error', 'Anda tidak memiliki akses untuk melihat detail siswa ini.');
            }
        }

        // Get Academic Grades (from all Raport components for this semester)
        $raports = Raport::where('siswa_id', $siswa_id)->where('semester_id', $semesterAktif->id)->with('raportDetail.mataPelajaran')->get();
        
        $grades = collect();
        foreach ($raports as $r) {
            foreach ($r->raportDetail as $rd) {
                // Add component name to each grade for clarity in view if needed
                $rd->komponen_nama = $r->komponenNilai->nama ?? 'N/A';
                $grades->push($rd);
            }
        }

        $gradesGrouped = $grades->groupBy('mata_pelajaran_id');

        // Get Attitude Grades
        $sikap = NilaiSikap::where('siswa_id', $siswa_id)->where('semester_id', $semesterAktif->id)->get();

        // Get Ekskul
        $ekskul = NilaiEkstrakurikuler::with('ekstrakurikuler')->where('siswa_id', $siswa_id)->where('semester_id', $semesterAktif->id)->get();

        // Get attendance aggregate
        $attendance = \App\Models\PresensiSiswa::where('siswa_id', $siswa_id)
            ->whereHas('kelas', function ($q) use ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            })
            ->selectRaw("SUM(CASE WHEN status='S' THEN 1 ELSE 0 END) as sakit")
            ->selectRaw("SUM(CASE WHEN status='I' THEN 1 ELSE 0 END) as izin")
            ->selectRaw("SUM(CASE WHEN status='A' THEN 1 ELSE 0 END) as alpha")
            ->first();

        return view('pembelajaran.dashboard-nilai.show', compact('siswa', 'kelas', 'gradesGrouped', 'sikap', 'ekskul', 'attendance', 'semesterAktif', 'raports'));
    }
}
