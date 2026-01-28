<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaranKelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ELearningController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $semesterAktif = \App\Models\Semester::active()->first();

        if ($user->hasRole('guru')) {
            $subjects = MataPelajaranKelas::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $user->guru->id)
                ->whereHas('kelas', function($q) use ($semesterAktif) {
                    if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
                })
                ->get();
        } elseif ($user->hasRole('siswa')) {
            $siswa = $user->siswa;
            $kelas = $siswa->kelasAktif->first();
            if ($kelas) {
                $subjects = MataPelajaranKelas::with(['mataPelajaran', 'guru'])
                    ->where('kelas_id', $kelas->id)
                    ->get();
            }
        } elseif ($user->hasRole(['admin', 'super-admin'])) {
            $subjects = MataPelajaranKelas::with(['kelas', 'mataPelajaran', 'guru'])
                ->whereHas('kelas', function($q) use ($semesterAktif) {
                    if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
                })
                ->get();
        }

        return view('elearning.index', compact('subjects'));
    }

    public function course($id)
    {
        $subject = MataPelajaranKelas::with([
            'kelas', 
            'mataPelajaran', 
            'guru',
            'materiAjar' => fn($q) => $q->orderBy('urutan'),
            'tugas' => fn($q) => $q->latest(),
            'forumDiskusi' => fn($q) => $q->latest()
        ])->findOrFail($id);

        return view('elearning.course', compact('subject'));
    }
}
