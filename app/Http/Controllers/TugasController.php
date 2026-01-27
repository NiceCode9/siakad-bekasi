<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    use \App\Traits\SendsNotifications;

    public function show($id)
    {
        $tugas = Tugas::with(['mataPelajaranGuru.mataPelajaranKelas.mataPelajaran', 'pengumpulanTugas.siswa'])->findOrFail($id);
        $submission = null;

        if (Auth::user()->hasRole('siswa')) {
            $submission = PengumpulanTugas::where('tugas_id', $id)
                ->where('siswa_id', Auth::user()->siswa->id)
                ->first();
        }

        return view('elearning.tugas_detail', compact('tugas', 'submission'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_guru_id' => 'required|exists:mata_pelajaran_guru,id',
            'judul' => 'required|string|max:255',
            'tanggal_deadline' => 'required|date',
            'file_lampiran' => 'nullable|file|max:5120',
        ]);

        $data = $request->all();
        $data['tanggal_buat'] = now();
        $data['is_published'] = true;

        if ($request->hasFile('file_lampiran')) {
            $data['file_lampiran'] = $request->file('file_lampiran')->store('tugas/lampiran', 'public');
        }

        $tugas = Tugas::create($data);

        // Notify Students
        $students = $tugas->mataPelajaranGuru->mataPelajaranKelas->kelas->siswa;
        foreach($students as $siswa) {
            $this->notifyUser(
                $siswa->user_id, 
                'Tugas Baru: ' . $tugas->judul,
                'Guru Anda telah mempublikasikan tugas baru untuk mata pelajaran ' . $tugas->mataPelajaranGuru->mataPelajaranKelas->mataPelajaran->nama,
                'info',
                route('tugas.show', $tugas->id)
            );
        }

        return back()->with('success', 'Tugas berhasil dipublikasikan.');
    }

    public function submit(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'jawaban' => 'nullable|string',
        ]);

        $tugas = Tugas::findOrFail($id);
        if ($tugas->isTerlambat()) {
            // Optional: prevent submission or mark as late
        }

        $path = $request->file('file')->store('tugas/pengumpulan', 'public');

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $id, 'siswa_id' => Auth::user()->siswa->id],
            [
                'file_path' => $path,
                'jawaban' => $request->jawaban,
                'tanggal_submit' => now(),
                'status' => 'dikirim'
            ]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function grade(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission = PengumpulanTugas::findOrFail($id);
        $submission->update([
            'nilai' => $request->nilai,
            'feedback' => $request->feedback,
            'status' => 'dinilai',
            'tanggal_dinilai' => now(),
        ]);

        // Notify Student
        $this->notifyUser(
            $submission->siswa->user_id,
            'Tugas Dinilai: ' . $submission->tugas->judul,
            'Tugas Anda telah dinilaioleh guru dengan skor: ' . $request->nilai,
            'success',
            route('tugas.show', $submission->tugas_id)
        );

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}
