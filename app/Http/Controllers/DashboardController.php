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
        } elseif ($user->hasRole('orang-tua')) {
            $data = $this->getOrangTuaDashboard($user);
        }

        return view('dashboard', $data);
    }

    private function getAdminDashboard()
    {
        return [
            'totalSiswa' => Siswa::count(),
            'totalGuru' => Guru::count(),
            'totalKelas' => Kelas::count(),
            'recentNotifications' => Notifikasi::latest()->limit(5)->get(),
        ];
    }

    private function getGuruDashboard($user)
    {
        $guru = $user->guru;
        return [
            'todaySchedules' => JadwalPelajaran::whereHas('mataPelajaranGuru', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })->where('hari', $this->getTodayIndonesian())->get(),
            'pendingGrades' => PengumpulanTugas::where('status', 'dikirim')
                ->whereHas('tugas.mataPelajaranGuru', function($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })->count(),
            'recentTasks' => Tugas::whereHas('mataPelajaranGuru', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })->latest()->limit(5)->get(),
        ];
    }

    private function getSiswaDashboard($user)
    {
        $siswa = $user->siswa;
        // Get active class for student
        $activeKelas = $siswa->kelas()->wherePivot('status', 'aktif')->first();
        
        return [
            'todaySchedules' => $activeKelas ? JadwalPelajaran::whereHas('mataPelajaranGuru', function($q) use ($activeKelas) {
                $q->where('mata_pelajaran_kelas_id', function($sub) use ($activeKelas) {
                    $sub->select('id')->from('mata_pelajaran_kelas')->where('kelas_id', $activeKelas->id);
                });
            })->where('hari', $this->getTodayIndonesian())->get() : [],
            'upcomingDeadlines' => Tugas::whereHas('mataPelajaranGuru.mataPelajaranKelas', function($q) use ($activeKelas) {
                if ($activeKelas) $q->where('kelas_id', $activeKelas->id);
            })->where('tanggal_deadline', '>=', now())
              ->whereDoesntHave('pengumpulanTugas', function($q) use ($siswa) {
                  $q->where('siswa_id', $siswa->id);
              })->get(),
        ];
    }

    private function getOrangTuaDashboard($user)
    {
        $parent = $user->orangTua;
        $children = $parent->siswa()->with(['kelas' => function($q) {
            $q->wherePivot('status', 'aktif');
        }])->get();

        $childData = [];
        foreach ($children as $child) {
            $childData[] = [
                'siswa' => $child,
                'recentGrades' => \App\Models\Nilai::where('siswa_id', $child->id)
                    ->with('mataPelajaranKelas.mataPelajaran')
                    ->latest()
                    ->limit(3)
                    ->get(),
                'attendanceSummary' => [
                    'hadir' => \App\Models\PresensiSiswa::where('siswa_id', $child->id)->where('status', 'hadir')->count(),
                    'absen' => \App\Models\PresensiSiswa::where('siswa_id', $child->id)->whereIn('status', ['sakit', 'izin', 'alfa'])->count(),
                ]
            ];
        }

        return [
            'children' => $childData
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
