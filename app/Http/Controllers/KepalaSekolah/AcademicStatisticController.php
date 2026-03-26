<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\JurnalMengajar;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class AcademicStatisticController extends Controller
{
    public function index()
    {
        $semesterAktif = Semester::active()->with('tahunAkademik')->first();
        if (!$semesterAktif) {
            return back()->with('error', 'Tidak ada semester aktif.');
        }

        // 1. Rata-rata Nilai per Kelas
        $averageGrades = Nilai::where('nilai.semester_id', $semesterAktif->id)
            ->join('mata_pelajaran_kelas', 'nilai.mata_pelajaran_kelas_id', '=', 'mata_pelajaran_kelas.id')
            ->join('kelas', 'mata_pelajaran_kelas.kelas_id', '=', 'kelas.id')
            ->select('kelas.nama as label', DB::raw('AVG(nilai.nilai) as value'))
            ->groupBy('kelas.id', 'kelas.nama')
            ->orderBy('value', 'desc')
            ->get();

        // 2. Progres Jurnal per Guru (Monitoring Keaktifan)
        $journalProgress = JurnalMengajar::whereHas('jadwalPelajaran.mataPelajaranKelas.kelas', function($q) use ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            })
            ->join('jadwal_pelajaran', 'jurnal_mengajar.jadwal_pelajaran_id', '=', 'jadwal_pelajaran.id')
            ->join('mata_pelajaran_kelas', 'jadwal_pelajaran.mata_pelajaran_kelas_id', '=', 'mata_pelajaran_kelas.id')
            ->join('guru', 'mata_pelajaran_kelas.guru_id', '=', 'guru.id')
            ->select('guru.nama_lengkap as label', DB::raw('COUNT(jurnal_mengajar.id) as value'))
            ->groupBy('guru.id', 'guru.nama_lengkap')
            ->orderBy('value', 'desc')
            ->limit(10)
            ->get();

        // 3. Statistik Presensi Global
        $attendanceStatsRaw = DB::table('presensi_mapel')
            ->join('jurnal_mengajar', 'presensi_mapel.jurnal_mengajar_id', '=', 'jurnal_mengajar.id')
            ->join('jadwal_pelajaran', 'jurnal_mengajar.jadwal_pelajaran_id', '=', 'jadwal_pelajaran.id')
            ->join('mata_pelajaran_kelas', 'jadwal_pelajaran.mata_pelajaran_kelas_id', '=', 'mata_pelajaran_kelas.id')
            ->join('kelas', 'mata_pelajaran_kelas.kelas_id', '=', 'kelas.id')
            ->where('kelas.semester_id', $semesterAktif->id)
            ->select('presensi_mapel.status as label', DB::raw('COUNT(*) as value'))
            ->groupBy('presensi_mapel.status')
            ->get();

        $attendanceMap = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpa'];
        $attendanceColors = ['Hadir' => '#28a745', 'Izin' => '#ffc107', 'Sakit' => '#17a2b8', 'Alpa' => '#dc3545'];

        $attendanceStats = $attendanceStatsRaw->map(function($item) use ($attendanceMap, $attendanceColors) {
            $label = $attendanceMap[$item->label] ?? $item->label;
            return [
                'label' => $label,
                'value' => $item->value,
                'color' => $attendanceColors[$label] ?? '#6c757d'
            ];
        });

        return view('kepalasekolah.statistics.index', compact(
            'semesterAktif',
            'averageGrades',
            'journalProgress',
            'attendanceStats'
        ));
    }
}
