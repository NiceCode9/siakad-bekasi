<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\KomponenNilai;
use App\Models\Semester;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    /**
     * Dashboard Penilaian.
     */
    public function index(Request $request)
    {
        $semesterAktif = Semester::active()->first();
        
        // Load classes for filter
        $kelas = $semesterAktif 
            ? Kelas::where('semester_id', $semesterAktif->id)->orderBy('nama')->get()
            : collect();

        // If class selected, load subjects
        $subjects = collect();
        if ($request->filled('kelas_id')) {
            $subjects = MataPelajaranKelas::with('mataPelajaran')
                ->where('kelas_id', $request->kelas_id)
                ->get();
        }

        // If subject selected, load components
        $components = KomponenNilai::where('kurikulum_id', $semesterAktif->tahunAkademik->kurikulum_id ?? 0)->get();

        return view('pembelajaran.nilai.index', compact('kelas', 'subjects', 'components'));
    }

    /**
     * Show form for bulk input grades.
     */
    public function create(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
            'komponen_nilai_id' => 'required|exists:komponen_nilai,id',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $mpk = MataPelajaranKelas::with('mataPelajaran')->findOrFail($request->mata_pelajaran_kelas_id);
        $komponen = KomponenNilai::findOrFail($request->komponen_nilai_id);
        $semester = Semester::active()->first();

        // Get students
        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get()
            ->sortBy(fn($sk) => $sk->siswa->nama_lengkap);

        // Get existing grades
        $existing = Nilai::where('mata_pelajaran_kelas_id', $mpk->id)
            ->where('komponen_nilai_id', $komponen->id)
            ->get()
            ->keyBy('siswa_id');

        return view('pembelajaran.nilai.create', compact('kelas', 'mpk', 'komponen', 'siswa', 'existing', 'semester'));
    }

    /**
     * Store bulk grades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
            'komponen_nilai_id' => 'required|exists:komponen_nilai,id',
            'semester_id' => 'required|exists:semester,id',
            'nilai' => 'required|array',
            'nilai.*.angka' => 'nullable|numeric|min:0|max:100',
            'nilai.*.keterangan' => 'nullable|string|max:255',
        ]);

        $userId = Auth::id();
        $mpkId = $validated['mata_pelajaran_kelas_id'];
        $komponenId = $validated['komponen_nilai_id'];
        $semesterId = $validated['semester_id'];
        
        // Determine jenis_nilai based on Component mapping or default to component name slug
        // For simplicity, we assume component name maps to jenis_nilai if needed, or we just store component_id primarily.
        // The `jenis_nilai` column in table might be redundant if we have `komponen_nilai_id`, but let's fill it for legacy support if needed.
        $komponen = KomponenNilai::find($komponenId);
        $jenisNilai = \Illuminate\Support\Str::slug($komponen->nama, '_');

        DB::beginTransaction();
        try {
            foreach ($validated['nilai'] as $siswaId => $data) {
                // If value is null/empty, we might want to skip or delete? 
                // Let's assume we updateOrCreate. If empty, maybe set to 0 or null?
                // Standard behavior: if empty, do nothing or delete? PROPOSAL: Update if provided.
                
                if (isset($data['angka']) && $data['angka'] !== null) {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'mata_pelajaran_kelas_id' => $mpkId,
                            'komponen_nilai_id' => $komponenId,
                            'semester_id' => $semesterId,
                        ],
                        [
                            'jenis_nilai' => $jenisNilai,
                            'nilai' => $data['angka'],
                            'keterangan' => $data['keterangan'] ?? null,
                            'penginput_id' => $userId,
                            'tanggal_input' => now(),
                        ]
                    );
                }
            }
            DB::commit();

            return redirect()->route('nilai.index', [
                'kelas_id' => $validated['kelas_id'], 
                'mata_pelajaran_kelas_id' => $mpkId
            ])->with('success', 'Nilai berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Ledger (Rekap) View.
     */
    public function rekap(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
        ]);

        $user = Auth::user();
        $kelasId = $request->kelas_id;
        $mpkId = $request->mata_pelajaran_kelas_id;

        $semesterAktif = Semester::active()->first();
        $kelas = Kelas::findOrFail($kelasId);
        $mpk = MataPelajaranKelas::with('mataPelajaran', 'guru')->findOrFail($mpkId);

        // Authorization check: Admin, Wali Kelas of this class, or the Subject Teacher
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $isWali = ($kelas->wali_kelas_id == ($user->guru->id ?? 0));
            $isTeacher = ($mpk->guru_id == ($user->guru->id ?? 0));
            
            if (!$isWali && !$isTeacher) {
                return redirect()->route('nilai.index')->with('error', 'Anda tidak memiliki akses rekap nilai untuk kelas/mapel ini.');
            }
        }

        $components = KomponenNilai::where('kurikulum_id', $semesterAktif->kurikulum_id ?? 0)->get();
        
        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get()
            ->sortBy(fn($sk) => $sk->siswa->nama_lengkap);

        $existingGrades = Nilai::where('mata_pelajaran_kelas_id', $mpkId)
            ->where('semester_id', $semesterAktif->id)
            ->get()
            ->groupBy('siswa_id');

        // Get raport details for overrides
        $raportDetails = \App\Models\RaportDetail::where('mata_pelajaran_id', $mpk->mata_pelajaran_id)
            ->whereHas('raport', function($q) use ($kelasId, $semesterAktif) {
                $q->where('kelas_id', $kelasId)->where('semester_id', $semesterAktif->id);
            })
            ->get()
            ->keyBy(function($item) {
                return $item->raport->siswa_id;
            });

        $isWali = $user->hasRole(['admin', 'super-admin']) || ($kelas->wali_kelas_id == ($user->guru->id ?? 0));

        return view('pembelajaran.nilai.rekap', compact('kelas', 'mpk', 'components', 'siswa', 'existingGrades', 'raportDetails', 'semesterAktif', 'isWali'));
    }

    /**
     * Override Nilai Akhir.
     */
    public function overrideNilaiAkhir(Request $request)
    {
        $request->validate([
            'raport_detail_id' => 'required|exists:raport_detail,id',
            'nilai_akhir_manual' => 'required|numeric|min:0|max:100',
            'override_reason' => 'required|string',
        ]);

        $user = Auth::user();
        $detail = \App\Models\RaportDetail::with('raport.kelas', 'mataPelajaran')->findOrFail($request->raport_detail_id);

        // Security check: Only Admin OR Wali Kelas of this class can override
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $isWali = ($detail->raport->kelas->wali_kelas_id == ($user->guru->id ?? 0));

            if (!$isWali) {
                return response()->json(['success' => false, 'message' => 'Hanya Wali Kelas yang dapat melakukan override nilai akhir.'], 403);
            }
        }

        $detail->update([
            'nilai_akhir_manual' => $request->nilai_akhir_manual,
            'is_manual_override' => true,
            'override_reason' => $request->override_reason,
            'nilai_akhir' => $request->nilai_akhir_manual, // Also update final for display
        ]);

        return response()->json(['success' => true, 'message' => 'Nilai akhir berhasil di-override.']);
    }
}
