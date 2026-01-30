<?php

namespace App\Http\Controllers;

use App\Models\NilaiEkstrakurikuler;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NilaiEkstrakurikulerController extends Controller
{
    /**
     * Dashboard Nilai Ekskul.
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
            $isWali = $user->guru && $user->guru->kelasWali()->exists();
            $isPembina = \App\Models\Ekstrakurikuler::where('pembina_id', $user->guru->id ?? 0)->exists();

            if (!$isWali && !$isPembina) {
                return redirect()->route('dashboard')->with('error', 'Akses ditolak. Menu ini hanya untuk Wali Kelas atau Pembina Ekstrakurikuler.');
            }
            
            // If they are only Wali Kelas, filter classes. If they are Pembina, show all active classes because they might have students in any class.
            if ($isWali && !$isPembina) {
                $kelasQuery->where('wali_kelas_id', $user->guru->id);
            }
        }

        $kelas = $kelasQuery->orderBy('nama')->get();

        return view('pembelajaran.nilai-ekstrakurikuler.index', compact('kelas'));
    }

    /**
     * Show form.
     */
    public function create(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $user = Auth::user();
        $kelas = Kelas::findOrFail($request->kelas_id);
        $semester = Semester::active()->first();

        // Authorization check
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $isWali = ($kelas->wali_kelas_id == ($user->guru->id ?? 0));
            $isPembina = \App\Models\Ekstrakurikuler::where('pembina_id', $user->guru->id ?? 0)->exists();

            if (!$isWali && !$isPembina) {
                return redirect()->route('nilai-ekstrakurikuler.index')->with('error', 'Anda tidak memiliki akses untuk kelas ini.');
        }

        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get()
            ->sortBy(fn($sk) => $sk->siswa->nama_lengkap);
        // Get active Ekstrakurikuler choices
        $ekskulList = \App\Models\Ekstrakurikuler::where('is_active', true)->orderBy('nama')->get();

        $existing = NilaiEkstrakurikuler::where('semester_id', $semester->id)
            ->whereIn('siswa_id', $siswa->pluck('siswa_id'))
            ->get()
            ->groupBy('siswa_id');

        return view('pembelajaran.nilai-ekstrakurikuler.create', compact('kelas', 'siswa', 'existing', 'semester', 'ekskulList'));
    }

    /**
     * Store data.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'nilai' => 'required|array',
            'nilai.*.ekstrakurikuler_id' => 'nullable|exists:ekstrakurikuler,id',
            'nilai.*.predikat' => 'nullable|in:A,B,C,D,E',
            'nilai.*.keterangan' => 'nullable|string',
        ]);

        // Authorization check
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $kelas = Kelas::findOrFail($validated['kelas_id']);
            $isWali = ($kelas->wali_kelas_id == ($user->guru->id ?? 0));
            $isPembina = \App\Models\Ekstrakurikuler::where('pembina_id', $user->guru->id ?? 0)->exists();

            if (!$isWali && !$isPembina) {
                return redirect()->route('nilai-ekstrakurikuler.index')->with('error', 'Gagal menyimpan: Akses ditolak.');
            }
        }

        DB::beginTransaction();
        try {
            foreach ($validated['nilai'] as $siswaId => $data) {
                if (!empty($data['ekstrakurikuler_id'])) {
                    NilaiEkstrakurikuler::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'semester_id' => $validated['semester_id'],
                        ],
                        [
                            'ekstrakurikuler_id' => $data['ekstrakurikuler_id'],
                            'predikat' => $data['predikat'],
                            'keterangan' => $data['keterangan'] ?? null,
                            'nilai' => 0,
                        ]
                    );
                }
            }
            DB::commit();

            return redirect()->route('nilai-ekstrakurikuler.index')->with('success', 'Nilai Ekstrakurikuler berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
