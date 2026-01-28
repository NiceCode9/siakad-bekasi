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
        $semesterAktif = \App\Models\Semester::active()->first();
        if ($user->hasRole('guru')) {
            $journals = JurnalMengajar::whereHas('jadwalPelajaran.mataPelajaranKelas.kelas', function ($q) use ($user, $semesterAktif) {
                $q->where('guru_id', $user->guru->id);
                if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
            })->with('jadwalPelajaran.mataPelajaranKelas.mataPelajaran')->latest()->get();
        } else {
            $journals = JurnalMengajar::whereHas('jadwalPelajaran.mataPelajaranKelas.kelas', function ($q) use ($semesterAktif) {
                if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
            })->with(['jadwalPelajaran.mataPelajaranKelas.mataPelajaran', 'jadwalPelajaran.mataPelajaranKelas.guru'])->latest()->get();
        }

        return view('jurnal-mengajar.index', compact('journals'));
    }

    public function create()
    {
        $user = Auth::user();
        $schedules = [];

        $semesterAktif = \App\Models\Semester::active()->first();
        if ($user->hasRole('guru')) {
            $schedules = JadwalPelajaran::whereHas('mataPelajaranKelas.kelas', function ($q) use ($user, $semesterAktif) {
                $q->where('guru_id', $user->guru->id);
                if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
            })->with('mataPelajaranKelas.mataPelajaran', 'mataPelajaranKelas.kelas')->get();
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
        $journal = JurnalMengajar::with(['jadwalPelajaran.mataPelajaranKelas.mataPelajaran', 'jadwalPelajaran.mataPelajaranKelas.guru', 'approvedBy'])->findOrFail($id);
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
        $teacherUser = $journal->jadwalPelajaran->mataPelajaranKelas->guru->user;
        if ($teacherUser) {
            $this->notifyUser(
                $teacherUser->id,
                'Jurnal Disetujui',
                'Jurnal mengajar Anda untuk ' . $journal->jadwalPelajaran->mataPelajaranKelas->mataPelajaran->nama . ' telah disetujui.',
                'success',
                route('jurnal-mengajar.show', $journal->id)
            );
        }

        return back()->with('success', 'Jurnal mengajar berhasil disetujui.');
    }
}
