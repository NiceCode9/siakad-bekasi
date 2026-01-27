<?php

namespace App\Http\Controllers;

use App\Models\NilaiEkstrakurikuler;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiEkstrakurikulerController extends Controller
{
    /**
     * Dashboard Nilai Ekskul.
     */
    public function index(Request $request)
    {
        $semesterAktif = Semester::active()->first();
        
        $kelas = $semesterAktif 
            ? Kelas::where('semester_id', $semesterAktif->id)->orderBy('nama')->get()
            : collect();

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

        $kelas = Kelas::findOrFail($request->kelas_id);
        $semester = Semester::active()->first();

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
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'nilai' => 'required|array',
            'nilai.*.ekstrakurikuler_id' => 'nullable|exists:ekstrakurikuler,id',
            'nilai.*.predikat' => 'nullable|in:A,B,C,D,E',
            'nilai.*.keterangan' => 'nullable|string',
        ]);

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
