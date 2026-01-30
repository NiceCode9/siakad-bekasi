<?php

namespace App\Http\Controllers;

use App\Models\NilaiSikap;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NilaiSikapController extends Controller
{
    /**
     * Dashboard Nilai Sikap.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $semesterAktif = Semester::active()->first();
        
        // Filter kelas: Wali Kelas hanya bisa menginput untuk kelas binaannya
        $kelasQuery = Kelas::query();
        if ($semesterAktif) {
            $kelasQuery->where('semester_id', $semesterAktif->id);
        }

        if (!$user->hasRole(['admin', 'super-admin'])) {
            // Check if user is Guru and has Wali Kelas assignment
            if (!$user->guru || !$user->guru->kelasWali()->exists()) {
                return redirect()->route('dashboard')->with('error', 'Akses ditolak. Menu ini hanya untuk Wali Kelas.');
            }
            $kelasQuery->where('wali_kelas_id', $user->guru->id);
        }

        $kelas = $kelasQuery->orderBy('nama')->get();

        return view('pembelajaran.nilai-sikap.index', compact('kelas'));
    }

    /**
     * Show form.
     */
    public function create(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'aspek' => 'required|in:spiritual,sosial',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $semester = Semester::active()->first();
        $aspek = $request->aspek;

        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get()
            ->sortBy(fn($sk) => $sk->siswa->nama_lengkap);

        $existing = NilaiSikap::where('kelas_id', $kelas->id)
            ->where('aspek', $aspek)
            ->get()
            ->keyBy('siswa_id');

        return view('pembelajaran.nilai-sikap.create', compact('kelas', 'aspek', 'siswa', 'existing', 'semester'));
    }

    /**
     * Store data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'aspek' => 'required|in:spiritual,sosial',
            'nilai' => 'required|array',
            'nilai.*.predikat' => 'nullable|in:SB,B,C,K',
            'nilai.*.deskripsi' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $aspek = $validated['aspek'];

        DB::beginTransaction();
        try {
            foreach ($validated['nilai'] as $siswaId => $data) {
                if (!empty($data['predikat'])) {
                    NilaiSikap::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'kelas_id' => $validated['kelas_id'],
                            'semester_id' => $validated['semester_id'],
                            'aspek' => $aspek,
                        ],
                        [
                            'predikat' => $data['predikat'],
                            'deskripsi' => $data['deskripsi'] ?? null,
                            'penginput_id' => $userId,
                        ]
                    );
                }
            }
            DB::commit();

            return redirect()->route('nilai-sikap.index')->with('success', 'Nilai Sikap berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
