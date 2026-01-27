<?php

namespace App\Http\Controllers;

use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalMengajarController extends Controller
{
    use \App\Traits\SendsNotifications;

    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('guru')) {
            $journals = JurnalMengajar::whereHas('jadwalPelajaran.mataPelajaranGuru', function ($q) use ($user) {
                $q->where('guru_id', $user->guru->id);
            })->with('jadwalPelajaran.mataPelajaranGuru.mataPelajaranKelas.mataPelajaran')->latest()->get();
        } else {
            $journals = JurnalMengajar::with(['jadwalPelajaran.mataPelajaranGuru.mataPelajaranKelas.mataPelajaran', 'jadwalPelajaran.mataPelajaranGuru.guru'])->latest()->get();
        }

        return view('jurnal-mengajar.index', compact('journals'));
    }

    public function create()
    {
        $user = Auth::user();
        $schedules = [];

        if ($user->hasRole('guru')) {
            $schedules = JadwalPelajaran::whereHas('mataPelajaranGuru', function ($q) use ($user) {
                $q->where('guru_id', $user->guru->id);
            })->with('mataPelajaranGuru.mataPelajaranKelas.mataPelajaran', 'mataPelajaranGuru.mataPelajaranKelas.kelas')->get();
        }

        return view('jurnal-mengajar.create', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'materi' => 'required|string',
            'jumlah_hadir' => 'nullable|integer',
            'jumlah_tidak_hadir' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_approved'] = false;

        JurnalMengajar::create($data);

        return redirect()->route('jurnal-mengajar.index')->with('success', 'Jurnal mengajar berhasil disimpan.');
    }

    public function show($id)
    {
        $journal = JurnalMengajar::with(['jadwalPelajaran.mataPelajaranGuru.mataPelajaranKelas.mataPelajaran', 'jadwalPelajaran.mataPelajaranGuru.guru', 'approvedBy'])->findOrFail($id);
        return view('jurnal-mengajar.show', compact('journal'));
    }

    public function approve($id)
    {
        $journal = JurnalMengajar::findOrFail($id);
        $journal->update([
            'is_approved' => true,
            'approved_by' => Auth::id()
        ]);

        // Notify Teacher
        $teacherUser = $journal->jadwalPelajaran->mataPelajaranGuru->guru->user;
        if ($teacherUser) {
            $this->notifyUser(
                $teacherUser->id,
                'Jurnal Disetujui',
                'Jurnal mengajar Anda untuk ' . $journal->jadwalPelajaran->mataPelajaranGuru->mataPelajaranKelas->mataPelajaran->nama . ' telah disetujui.',
                'success',
                route('jurnal-mengajar.show', $journal->id)
            );
        }

        return back()->with('success', 'Jurnal mengajar berhasil disetujui.');
    }
}
