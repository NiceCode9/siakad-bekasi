<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaranGuru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ELearningController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subjects = [];

        if ($user->hasRole('guru')) {
            $subjects = MataPelajaranGuru::with(['mataPelajaranKelas.kelas', 'mataPelajaranKelas.mataPelajaran'])
                ->where('guru_id', $user->guru->id)
                ->get();
        } elseif ($user->hasRole('siswa')) {
            $siswa = $user->siswa;
            $kelas = $siswa->kelasAktif->first();
            if ($kelas) {
                $subjects = MataPelajaranGuru::with(['mataPelajaranKelas.mataPelajaran', 'guru'])
                    ->whereHas('mataPelajaranKelas', function($q) use ($kelas) {
                        $q->where('kelas_id', $kelas->id);
                    })->get();
            }
        } elseif ($user->hasRole(['admin', 'super-admin'])) {
            $subjects = MataPelajaranGuru::with(['mataPelajaranKelas.kelas', 'mataPelajaranKelas.mataPelajaran', 'guru'])->get();
        }

        return view('elearning.index', compact('subjects'));
    }

    public function course($id)
    {
        $subject = MataPelajaranGuru::with([
            'mataPelajaranKelas.kelas', 
            'mataPelajaranKelas.mataPelajaran', 
            'guru',
            'materiAjar' => fn($q) => $q->orderBy('urutan'),
            'tugas' => fn($q) => $q->latest(),
            'forumDiskusi' => fn($q) => $q->latest()
        ])->findOrFail($id);

        return view('elearning.course', compact('subject'));
    }
}
