<?php

namespace App\Http\Controllers;

use App\Models\ForumDiskusi;
use App\Models\ForumKomentar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumDiskusiController extends Controller
{
    use \App\Traits\SendsNotifications;

    public function show($id)
    {
        $forum = ForumDiskusi::with(['pembuat', 'forumKomentar.user', 'mataPelajaranGuru.mataPelajaranKelas.mataPelajaran'])->findOrFail($id);
        $forum->increment('view_count');

        return view('elearning.forum_detail', compact('forum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_guru_id' => 'required|exists:mata_pelajaran_guru,id',
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
        ]);

        ForumDiskusi::create([
            'mata_pelajaran_guru_id' => $request->mata_pelajaran_guru_id,
            'pembuat_id' => Auth::id(),
            'judul' => $request->judul,
            'konten' => $request->konten,
        ]);

        return back()->with('success', 'Topik diskusi berhasil dibuat.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'konten' => 'required|string',
        ]);

        $comment = ForumKomentar::create([
            'forum_diskusi_id' => $id,
            'user_id' => Auth::id(),
            'konten' => $request->konten,
        ]);

        $forum = $comment->forumDiskusi;
        if ($forum->pembuat_id != Auth::id()) {
            $this->notifyUser(
                $forum->pembuat_id,
                'Komentar Baru di Diskusi: ' . $forum->judul,
                Auth::user()->name . ' membalas diskusi Anda.',
                'info',
                route('forum.show', $forum->id)
            );
        }

        return back()->with('success', 'Komentar berhasil dikirim.');
    }
}
