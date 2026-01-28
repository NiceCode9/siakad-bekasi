<?php

namespace App\Http\Controllers;

use App\Models\MateriAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MateriAjarController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:file,url,video',
            'file' => 'nullable|file|max:10240', // 10MB
            'url' => 'nullable|url',
        ]);

        $data = $request->only(['mata_pelajaran_kelas_id', 'judul', 'deskripsi', 'tipe', 'url']);

        $data['is_published'] = $request->has('is_published');
        $data['tanggal_publish'] = now();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materi', 'public');
            $data['file_path'] = $path;
        }

        MateriAjar::create($data);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function download($id)
    {
        $materi = MateriAjar::findOrFail($id);
        if (!$materi->file_path) return back();

        $materi->increment('view_count');
        return Storage::disk('public')->download($materi->file_path, $materi->judul . '.' . pathinfo($materi->file_path, PATHINFO_EXTENSION));
    }

    public function destroy($id)
    {
        $materi = MateriAjar::findOrFail($id);
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }
        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }
}
