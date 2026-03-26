<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole(['admin', 'super-admin'])) {
            $data = $this->getAdminDashboard();
        } elseif ($user->hasRole('guru')) {
            $data = $this->getGuruDashboard($user);
        } elseif ($user->hasRole('siswa')) {
            $data = $this->getSiswaDashboard($user);
        }

        // Fetch latest official announcement for the user
        $data['latestAnnouncement'] = Notifikasi::where('user_id', $user->id)
            ->where('tipe', 'announcement')
            ->unread()
            ->latest()
            ->first();

        return view('dashboard', $data);
    }

    private function getAdminDashboard()
    {
        $semesterAktif = \App\Models\Semester::with('tahunAkademik')->active()->first();
        
        $totalSiswa = Siswa::active()->count();
        $totalKelas = 0;
        $totalMapel = 0;
        $totalMateri = 0;
        $totalTugas = 0;
        
        if ($semesterAktif) {
            $totalSiswa = Siswa::where('status', 'aktif')
                ->whereHas('kelasAktif', function($q) use ($semesterAktif) {
                    $q->where('semester_id', $semesterAktif->id);
                })->count();
                
            $totalKelas = Kelas::where('semester_id', $semesterAktif->id)->count();
            
            $totalMapel = \App\Models\MataPelajaran::where('is_active', true)
                ->whereHas('mataPelajaranKelas', function($q) use ($semesterAktif) {
                    $q->whereHas('kelas', function($q2) use ($semesterAktif) {
                        $q2->where('semester_id', $semesterAktif->id);
                    });
                })->count();
                
            $totalMateri = \App\Models\MateriAjar::whereHas('mataPelajaranKelas', function($q) use ($semesterAktif) {
                $q->whereHas('kelas', function($q2) use ($semesterAktif) {
                    $q2->where('semester_id', $semesterAktif->id);
                });
            })->count();
            
            $totalTugas = \App\Models\Tugas::whereHas('mataPelajaranKelas', function($q) use ($semesterAktif) {
                $q->whereHas('kelas', function($q2) use ($semesterAktif) {
                    $q2->where('semester_id', $semesterAktif->id);
                });
            })->count();
        }

        return [
            'totalSiswa' => $totalSiswa,
            'totalGuru' => Guru::active()->count(),
            'totalKelas' => $totalKelas,
            'totalJurusan' => \App\Models\Jurusan::where('is_active', true)->count(),
            'totalMapel' => $totalMapel,
            'totalMateri' => $totalMateri,
            'totalTugas' => $totalTugas,
            'semesterAktif' => $semesterAktif,
            'recentLogs' => \App\Models\LogAktivitas::with('user')->orderBy('id', 'desc')->limit(6)->get(),
            'recentNotifications' => Notifikasi::latest()->limit(5)->get(),
        ];
    }

    private function getGuruDashboard($user)
    {
        $guru = $user->guru;
        $semesterAktif = \App\Models\Semester::active()->first();
        
        return [
            'todaySchedules' => JadwalPelajaran::whereHas('mataPelajaranKelas.kelas', function($q) use ($guru, $semesterAktif) {
                $q->where('guru_id', $guru->id);
                if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
            })->where('hari', $this->getTodayIndonesian())->get(),
            'pendingGrades' => PengumpulanTugas::where('status', 'dikirim')
                ->whereHas('tugas.mataPelajaranKelas.kelas', function($q) use ($guru, $semesterAktif) {
                    $q->where('guru_id', $guru->id);
                    if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
                })->count(),
            'recentTasks' => Tugas::whereHas('mataPelajaranKelas.kelas', function($q) use ($guru, $semesterAktif) {
                $q->where('guru_id', $guru->id);
                if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
            })->latest()->limit(5)->get(),
        ];
    }

    private function getSiswaDashboard($user)
    {
        $siswa = $user->siswa;
        // Get active class for student
        $activeKelas = $siswa->kelas()->wherePivot('status', 'aktif')->first();
        
        return [
            'todaySchedules' => $activeKelas ? JadwalPelajaran::whereHas('mataPelajaranKelas', function($q) use ($activeKelas) {
                $q->where('kelas_id', $activeKelas->id);
            })->where('hari', $this->getTodayIndonesian())->get() : [],
            'upcomingDeadlines' => Tugas::whereHas('mataPelajaranKelas', function($q) use ($activeKelas) {
                if ($activeKelas) $q->where('kelas_id', $activeKelas->id);
            })->where('tanggal_deadline', '>=', now())
              ->whereDoesntHave('pengumpulanTugas', function($q) use ($siswa) {
                  $q->where('siswa_id', $siswa->id);
              })->get(),
        ];
    }



    private function getTodayIndonesian()
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        return $days[date('l')];
    }
}
