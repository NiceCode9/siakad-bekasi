<?php

namespace App\Http\Controllers;

use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function create(Request $request)
    {
        $user = Auth::user();
        $schedules = [];
        $students = [];
        $selectedSchedule = null;

        $semesterAktif = \App\Models\Semester::active()->first();
        if ($user->hasRole('guru')) {
            $schedules = JadwalPelajaran::whereHas('mataPelajaranKelas.kelas', function ($q) use ($user, $semesterAktif) {
                $q->where('guru_id', $user->guru->id);
                if ($semesterAktif) $q->where('semester_id', $semesterAktif->id);
            })->with('mataPelajaranKelas.mataPelajaran', 'mataPelajaranKelas.kelas')->get();

            if ($request->filled('jadwal_pelajaran_id')) {
                $selectedSchedule = JadwalPelajaran::with('mataPelajaranKelas.kelas')->findOrFail($request->jadwal_pelajaran_id);
                
                $students = \App\Models\SiswaKelas::with('siswa')
                    ->where('kelas_id', $selectedSchedule->mataPelajaranKelas->kelas_id)
                    ->where('status', 'aktif')
                    ->get()
                    ->sortBy(fn($sk) => $sk->siswa->nama_lengkap);
            }
        }

        return view('jurnal-mengajar.create', compact('schedules', 'students', 'selectedSchedule'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'materi' => 'required|string',
            'presensi' => 'required|array',
            'presensi.*' => 'required|in:H,I,S,A',
        ]);

        $data = $request->only([
            'jadwal_pelajaran_id', 'tanggal', 'jam_mulai', 'jam_selesai', 
            'materi', 'metode_pembelajaran', 'hambatan', 'solusi', 'catatan'
        ]);
        
        // Auto count attendance
        $presensiData = $request->input('presensi');
        $data['jumlah_hadir'] = collect($presensiData)->where('status', 'H')->count(); // Wait, it's just value
        $data['jumlah_hadir'] = count(array_filter($presensiData, fn($s) => $s == 'H'));
        $data['jumlah_tidak_hadir'] = count(array_filter($presensiData, fn($s) => $s != 'H'));
        $data['is_approved'] = false;

        DB::beginTransaction();
        try {
            $journal = JurnalMengajar::create($data);

            foreach ($presensiData as $siswaId => $status) {
                \App\Models\PresensiMapel::create([
                    'jurnal_mengajar_id' => $journal->id,
                    'siswa_id' => $siswaId,
                    'status' => $status,
                    'keterangan' => $request->input("keterangan.$siswaId")
                ]);
            }

            DB::commit();
            return redirect()->route('jurnal-mengajar.index')->with('success', 'Jurnal mengajar dan presensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $journal = JurnalMengajar::with([
            'jadwalPelajaran.mataPelajaranKelas.mataPelajaran', 
            'jadwalPelajaran.mataPelajaranKelas.guru', 
            'approvedBy',
            'presensiMapel.siswa'
        ])->findOrFail($id);
        
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
