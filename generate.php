<?php

// ============================================
// CONTROLLERS - MASTER DATA
// ============================================

// app/Http/Controllers/KurikulumController.php
namespace App\Http\Controllers;

use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KurikulumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kurikulum = Kurikulum::orderBy('tahun_mulai', 'desc')->get();

        return view('master-data.kurikulum.index', compact('kurikulum'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-data.kurikulum.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:kurikulum,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'is_active' => 'boolean',
        ]);

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            Kurikulum::where('is_active', true)->update(['is_active' => false]);
        }

        Kurikulum::create($validated);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum)
    {
        $kurikulum->load(['tahunAkademik', 'mataPelajaran', 'komponenNilai']);

        return view('master-data.kurikulum.show', compact('kurikulum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum)
    {
        return view('master-data.kurikulum.edit', compact('kurikulum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:kurikulum,kode,' . $kurikulum->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'is_active' => 'boolean',
        ]);

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            Kurikulum::where('is_active', true)
                ->where('id', '!=', $kurikulum->id)
                ->update(['is_active' => false]);
        }

        $kurikulum->update($validated);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum)
    {
        // Check jika masih digunakan
        if ($kurikulum->tahunAkademik()->exists()) {
            return redirect()->route('kurikulum.index')
                ->with('error', 'Kurikulum tidak dapat dihapus karena masih digunakan di Tahun Akademik');
        }

        $kurikulum->delete();

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil dihapus');
    }

    /**
     * Set kurikulum as active.
     */
    public function setActive(Kurikulum $kurikulum)
    {
        DB::beginTransaction();
        try {
            // Nonaktifkan semua
            Kurikulum::where('is_active', true)->update(['is_active' => false]);

            // Aktifkan yang dipilih
            $kurikulum->update(['is_active' => true]);

            DB::commit();

            return redirect()->route('kurikulum.index')
                ->with('success', 'Kurikulum berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('kurikulum.index')
                ->with('error', 'Gagal mengaktifkan kurikulum: ' . $e->getMessage());
        }
    }
}

// ============================================
// app/Http/Controllers/TahunAkademikController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAkademikController extends Controller
{
    public function index()
    {
        $tahunAkademik = TahunAkademik::with('kurikulum')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('master-data.tahun-akademik.index', compact('tahunAkademik'));
    }

    public function create()
    {
        $kurikulum = Kurikulum::active()->get();

        return view('master-data.tahun-akademik.create', compact('kurikulum'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:tahun_akademik,kode',
            'nama' => 'required|string|max:50',
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Jika set active, nonaktifkan yang lain
            if ($request->is_active) {
                TahunAkademik::where('is_active', true)->update(['is_active' => false]);
            }

            $tahunAkademik = TahunAkademik::create($validated);

            // Auto-create 2 semester (Ganjil & Genap)
            $this->createDefaultSemester($tahunAkademik);

            DB::commit();

            return redirect()->route('tahun-akademik.index')
                ->with('success', 'Tahun Akademik berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tahun akademik: ' . $e->getMessage());
        }
    }

    public function show(TahunAkademik $tahunAkademik)
    {
        $tahunAkademik->load(['kurikulum', 'semester.kelas']);

        return view('master-data.tahun-akademik.show', compact('tahunAkademik'));
    }

    public function edit(TahunAkademik $tahunAkademik)
    {
        $kurikulum = Kurikulum::active()->get();

        return view('master-data.tahun-akademik.edit', compact('tahunAkademik', 'kurikulum'));
    }

    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:tahun_akademik,kode,' . $tahunAkademik->id,
            'nama' => 'required|string|max:50',
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            TahunAkademik::where('is_active', true)
                ->where('id', '!=', $tahunAkademik->id)
                ->update(['is_active' => false]);
        }

        $tahunAkademik->update($validated);

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun Akademik berhasil diperbarui');
    }

    public function destroy(TahunAkademik $tahunAkademik)
    {
        // Check jika masih ada kelas
        if ($tahunAkademik->semester()->whereHas('kelas')->exists()) {
            return redirect()->route('tahun-akademik.index')
                ->with('error', 'Tahun Akademik tidak dapat dihapus karena masih ada kelas');
        }

        $tahunAkademik->delete();

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun Akademik berhasil dihapus');
    }

    public function setActive(TahunAkademik $tahunAkademik)
    {
        DB::beginTransaction();
        try {
            // Nonaktifkan semua tahun akademik
            TahunAkademik::where('is_active', true)->update(['is_active' => false]);

            // Nonaktifkan semua semester
            DB::table('semester')->update(['is_active' => false]);

            // Aktifkan tahun akademik yang dipilih
            $tahunAkademik->update(['is_active' => true]);

            DB::commit();

            return redirect()->route('tahun-akademik.index')
                ->with('success', 'Tahun Akademik berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tahun-akademik.index')
                ->with('error', 'Gagal mengaktifkan tahun akademik: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Create default semester (Ganjil & Genap)
     */
    private function createDefaultSemester(TahunAkademik $tahunAkademik)
    {
        $tanggalMulai = \Carbon\Carbon::parse($tahunAkademik->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($tahunAkademik->tanggal_selesai);

        // Hitung tengah tahun
        $tengah = $tanggalMulai->copy()->addMonths(6);

        // Semester Ganjil (Semester 1)
        $tahunAkademik->semester()->create([
            'nama' => 'Ganjil',
            'kode' => $tahunAkademik->kode . '-1',
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tengah->copy()->subDay(),
            'is_active' => false,
        ]);

        // Semester Genap (Semester 2)
        $tahunAkademik->semester()->create([
            'nama' => 'Genap',
            'kode' => $tahunAkademik->kode . '-2',
            'tanggal_mulai' => $tengah,
            'tanggal_selesai' => $tanggalSelesai,
            'is_active' => false,
        ]);
    }
}

// ============================================
// app/Http/Controllers/SemesterController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index()
    {
        $semester = Semester::with('tahunAkademik')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('master-data.semester.index', compact('semester'));
    }

    public function create()
    {
        $tahunAkademik = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();

        return view('master-data.semester.create', compact('tahunAkademik'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama' => 'required|in:Ganjil,Genap',
            'kode' => 'required|string|max:20|unique:semester,kode',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        // Validasi: tidak boleh ada semester ganda di tahun akademik yang sama
        $exists = Semester::where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('nama', $request->nama)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Semester ' . $request->nama . ' sudah ada di Tahun Akademik ini');
        }

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        Semester::create($validated);

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil ditambahkan');
    }

    public function show(Semester $semester)
    {
        $semester->load(['tahunAkademik', 'kelas.jurusan', 'kelas.waliKelas']);

        // Statistik
        $stats = [
            'total_kelas' => $semester->kelas()->count(),
            'total_siswa' => DB::table('siswa_kelas')
                ->whereIn('kelas_id', $semester->kelas()->pluck('id'))
                ->where('status', 'aktif')
                ->count(),
        ];

        return view('master-data.semester.show', compact('semester', 'stats'));
    }

    public function edit(Semester $semester)
    {
        $tahunAkademik = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();

        return view('master-data.semester.edit', compact('semester', 'tahunAkademik'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama' => 'required|in:Ganjil,Genap',
            'kode' => 'required|string|max:20|unique:semester,kode,' . $semester->id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        // Validasi: tidak boleh ada semester ganda
        $exists = Semester::where('tahun_akademik_id', $request->tahun_akademik_id)
            ->where('nama', $request->nama)
            ->where('id', '!=', $semester->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Semester ' . $request->nama . ' sudah ada di Tahun Akademik ini');
        }

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            Semester::where('is_active', true)
                ->where('id', '!=', $semester->id)
                ->update(['is_active' => false]);
        }

        $semester->update($validated);

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil diperbarui');
    }

    public function destroy(Semester $semester)
    {
        // Check jika masih ada kelas
        if ($semester->kelas()->exists()) {
            return redirect()->route('semester.index')
                ->with('error', 'Semester tidak dapat dihapus karena masih ada kelas');
        }

        $semester->delete();

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil dihapus');
    }

    public function setActive(Semester $semester)
    {
        DB::beginTransaction();
        try {
            // Nonaktifkan semua semester
            Semester::where('is_active', true)->update(['is_active' => false]);

            // Aktifkan semester yang dipilih
            $semester->update(['is_active' => true]);

            // Aktifkan tahun akademiknya juga
            $semester->tahunAkademik->update(['is_active' => true]);

            DB::commit();

            return redirect()->route('semester.index')
                ->with('success', 'Semester berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('semester.index')
                ->with('error', 'Gagal mengaktifkan semester: ' . $e->getMessage());
        }
    }
}


// ============================================
// CONTROLLERS - JURUSAN & KELAS
// ============================================

// app/Http/Controllers/JurusanController.php
namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::withCount('kelas')->get();

        return view('master-data.jurusan.index', compact('jurusan'));
    }

    public function create()
    {
        return view('master-data.jurusan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jurusan,kode',
            'nama' => 'required|string|max:100',
            'singkatan' => 'required|string|max:10',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Jurusan::create($validated);

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan');
    }

    public function show(Jurusan $jurusan)
    {
        $jurusan->load(['kelas.semester.tahunAkademik', 'kelas.waliKelas']);

        // Statistik per tingkat
        $stats = [
            'X' => $jurusan->kelas()->where('tingkat', 'X')->count(),
            'XI' => $jurusan->kelas()->where('tingkat', 'XI')->count(),
            'XII' => $jurusan->kelas()->where('tingkat', 'XII')->count(),
        ];

        return view('master-data.jurusan.show', compact('jurusan', 'stats'));
    }

    public function edit(Jurusan $jurusan)
    {
        return view('master-data.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jurusan,kode,' . $jurusan->id,
            'nama' => 'required|string|max:100',
            'singkatan' => 'required|string|max:10',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $jurusan->update($validated);

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui');
    }

    public function destroy(Jurusan $jurusan)
    {
        // Check jika masih ada kelas
        if ($jurusan->kelas()->exists()) {
            return redirect()->route('jurusan.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena masih ada kelas');
        }

        $jurusan->delete();

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Jurusan $jurusan)
    {
        $jurusan->update(['is_active' => !$jurusan->is_active]);

        $status = $jurusan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('jurusan.index')
            ->with('success', "Jurusan berhasil {$status}");
    }
}

// ============================================
// app/Http/Controllers/KelasController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Jurusan;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(['semester.tahunAkademik', 'jurusan', 'waliKelas']);

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        } else {
            // Default: semester aktif
            $semesterAktif = Semester::active()->first();
            if ($semesterAktif) {
                $query->where('semester_id', $semesterAktif->id);
            }
        }

        // Filter by tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Filter by jurusan
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $kelas = $query->orderBy('tingkat')->orderBy('nama')->get();

        // Data untuk filter
        $semester = Semester::orderBy('tanggal_mulai', 'desc')->get();
        $jurusan = Jurusan::active()->get();

        return view('master-data.kelas.index', compact('kelas', 'semester', 'jurusan'));
    }

    public function create()
    {
        $semester = Semester::orderBy('tanggal_mulai', 'desc')->get();
        $jurusan = Jurusan::active()->get();
        $guru = Guru::active()->orderBy('nama_lengkap')->get();

        return view('master-data.kelas.create', compact('semester', 'jurusan', 'guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:20|unique:kelas,kode',
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'kuota' => 'required|integer|min:1|max:50',
            'ruang_kelas' => 'nullable|string|max:50',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load([
            'semester.tahunAkademik',
            'jurusan',
            'waliKelas',
            'siswaKelas.siswa',
            'mataPelajaranKelas.mataPelajaran'
        ]);

        // Statistik
        $stats = [
            'total_siswa' => $kelas->siswaKelas()->where('status', 'aktif')->count(),
            'total_laki' => $kelas->siswaKelas()
                ->where('status', 'aktif')
                ->whereHas('siswa', fn($q) => $q->where('jenis_kelamin', 'L'))
                ->count(),
            'total_perempuan' => $kelas->siswaKelas()
                ->where('status', 'aktif')
                ->whereHas('siswa', fn($q) => $q->where('jenis_kelamin', 'P'))
                ->count(),
            'total_mapel' => $kelas->mataPelajaranKelas()->count(),
            'sisa_kuota' => $kelas->kuota - $kelas->siswaKelas()->where('status', 'aktif')->count(),
        ];

        return view('master-data.kelas.show', compact('kelas', 'stats'));
    }

    public function edit(Kelas $kelas)
    {
        $semester = Semester::orderBy('tanggal_mulai', 'desc')->get();
        $jurusan = Jurusan::active()->get();
        $guru = Guru::active()->orderBy('nama_lengkap')->get();

        return view('master-data.kelas.edit', compact('kelas', 'semester', 'jurusan', 'guru'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:20|unique:kelas,kode,' . $kelas->id,
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'kuota' => 'required|integer|min:1|max:50',
            'ruang_kelas' => 'nullable|string|max:50',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        // Check jika masih ada siswa
        if ($kelas->siswaKelas()->where('status', 'aktif')->exists()) {
            return redirect()->route('kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih ada siswa aktif');
        }

        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil dihapus');
    }

    /**
     * Assign wali kelas
     */
    public function assignWaliKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'required|exists:guru,id',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.show', $kelas)
            ->with('success', 'Wali kelas berhasil ditugaskan');
    }

    /**
     * Remove wali kelas
     */
    public function removeWaliKelas(Kelas $kelas)
    {
        $kelas->update(['wali_kelas_id' => null]);

        return redirect()->route('kelas.show', $kelas)
            ->with('success', 'Wali kelas berhasil dihapus');
    }

    /**
     * Get siswa in kelas (AJAX)
     */
    public function getSiswa(Kelas $kelas)
    {
        $siswa = $kelas->siswaKelas()
            ->with('siswa')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($sk) {
                return [
                    'id' => $sk->siswa->id,
                    'nisn' => $sk->siswa->nisn,
                    'nis' => $sk->siswa->nis,
                    'nama' => $sk->siswa->nama_lengkap,
                    'jenis_kelamin' => $sk->siswa->jenis_kelamin,
                    'tanggal_masuk' => $sk->tanggal_masuk->format('d/m/Y'),
                ];
            });

        return response()->json($siswa);
    }

    /**
     * Bulk create kelas untuk semester baru
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required|in:X,XI,XII',
            'jumlah_kelas' => 'required|integer|min:1|max:10',
            'kuota' => 'required|integer|min:1|max:50',
        ]);

        $semester = Semester::find($validated['semester_id']);
        $jurusan = Jurusan::find($validated['jurusan_id']);

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $validated['jumlah_kelas']; $i++) {
                $nama = "{$validated['tingkat']} {$jurusan->singkatan} {$i}";
                $kode = strtoupper("{$validated['tingkat']}-{$jurusan->kode}-{$i}-{$semester->kode}");

                Kelas::create([
                    'semester_id' => $validated['semester_id'],
                    'jurusan_id' => $validated['jurusan_id'],
                    'tingkat' => $validated['tingkat'],
                    'nama' => $nama,
                    'kode' => $kode,
                    'kuota' => $validated['kuota'],
                ]);
            }

            DB::commit();

            return redirect()->route('kelas.index')
                ->with('success', "{$validated['jumlah_kelas']} kelas berhasil ditambahkan");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Copy kelas dari semester sebelumnya
     */
    public function copyFromPrevious(Request $request)
    {
        $validated = $request->validate([
            'semester_tujuan_id' => 'required|exists:semester,id',
            'semester_asal_id' => 'required|exists:semester,id',
            'tingkat_asal' => 'required|in:X,XI,XII',
            'tingkat_tujuan' => 'required|in:X,XI,XII',
        ]);

        $semesterTujuan = Semester::find($validated['semester_tujuan_id']);
        $kelasAsal = Kelas::where('semester_id', $validated['semester_asal_id'])
            ->where('tingkat', $validated['tingkat_asal'])
            ->get();

        if ($kelasAsal->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada kelas di semester asal');
        }

        DB::beginTransaction();
        try {
            foreach ($kelasAsal as $kelas) {
                // Generate nama & kode baru
                $nama = str_replace($validated['tingkat_asal'], $validated['tingkat_tujuan'], $kelas->nama);
                $kode = str_replace($validated['tingkat_asal'], $validated['tingkat_tujuan'], $kelas->kode);
                $kode = str_replace($kelas->semester->kode, $semesterTujuan->kode, $kode);

                Kelas::create([
                    'semester_id' => $validated['semester_tujuan_id'],
                    'jurusan_id' => $kelas->jurusan_id,
                    'tingkat' => $validated['tingkat_tujuan'],
                    'nama' => $nama,
                    'kode' => $kode,
                    'wali_kelas_id' => $kelas->wali_kelas_id,
                    'kuota' => $kelas->kuota,
                    'ruang_kelas' => $kelas->ruang_kelas,
                ]);
            }

            DB::commit();

            return redirect()->route('kelas.index')
                ->with('success', count($kelasAsal) . ' kelas berhasil dicopy');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal copy kelas: ' . $e->getMessage());
        }
    }
}

// ============================================
// CONTROLLERS - MATA PELAJARAN & JADWAL
// ============================================

// app/Http/Controllers/MataPelajaranController.php
namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Kurikulum;
use App\Models\KelompokMapel;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::with(['kurikulum', 'kelompokMapel']);

        // Filter by kurikulum
        if ($request->filled('kurikulum_id')) {
            $query->where('kurikulum_id', $request->kurikulum_id);
        }

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by kelompok
        if ($request->filled('kelompok_mapel_id')) {
            $query->where('kelompok_mapel_id', $request->kelompok_mapel_id);
        }

        $mataPelajaran = $query->orderBy('kelompok_mapel_id')
            ->orderBy('nama')
            ->get();

        // Data untuk filter
        $kurikulum = Kurikulum::all();
        $kelompokMapel = KelompokMapel::orderBy('urutan')->get();

        return view('master-data.mata-pelajaran.index', compact(
            'mataPelajaran',
            'kurikulum',
            'kelompokMapel'
        ));
    }

    public function create()
    {
        $kurikulum = Kurikulum::all();
        $kelompokMapel = KelompokMapel::orderBy('urutan')->get();

        return view('master-data.mata-pelajaran.create', compact('kurikulum', 'kelompokMapel'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'kelompok_mapel_id' => 'nullable|exists:kelompok_mapel,id',
            'kode' => 'required|string|max:20|unique:mata_pelajaran,kode',
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:umum,kejuruan,muatan_lokal',
            'kategori' => 'required|in:wajib,peminatan,lintas_minat',
            'kkm' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load([
            'kurikulum',
            'kelompokMapel',
            'mataPelajaranKelas.kelas.semester',
        ]);

        // Statistik
        $stats = [
            'total_kelas' => $mataPelajaran->mataPelajaranKelas()->count(),
            'total_bank_soal' => $mataPelajaran->bankSoal()->count(),
        ];

        return view('master-data.mata-pelajaran.show', compact('mataPelajaran', 'stats'));
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $kurikulum = Kurikulum::all();
        $kelompokMapel = KelompokMapel::orderBy('urutan')->get();

        return view('master-data.mata-pelajaran.edit', compact(
            'mataPelajaran',
            'kurikulum',
            'kelompokMapel'
        ));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'kelompok_mapel_id' => 'nullable|exists:kelompok_mapel,id',
            'kode' => 'required|string|max:20|unique:mata_pelajaran,kode,' . $mataPelajaran->id,
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:umum,kejuruan,muatan_lokal',
            'kategori' => 'required|in:wajib,peminatan,lintas_minat',
            'kkm' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $mataPelajaran->update($validated);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata Pelajaran berhasil diperbarui');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        // Check jika masih digunakan
        if ($mataPelajaran->mataPelajaranKelas()->exists()) {
            return redirect()->route('mata-pelajaran.index')
                ->with('error', 'Mata Pelajaran tidak dapat dihapus karena masih digunakan');
        }

        $mataPelajaran->delete();

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata Pelajaran berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->update(['is_active' => !$mataPelajaran->is_active]);

        $status = $mataPelajaran->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('mata-pelajaran.index')
            ->with('success', "Mata Pelajaran berhasil {$status}");
    }

    /**
     * Assign ke kelas
     */
    public function assignToKelas(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|array',
            'kelas_id.*' => 'exists:kelas,id',
            'jam_per_minggu' => 'required|integer|min:1|max:20',
        ]);

        foreach ($validated['kelas_id'] as $kelasId) {
            $mataPelajaran->mataPelajaranKelas()->updateOrCreate(
                ['kelas_id' => $kelasId],
                ['jam_per_minggu' => $validated['jam_per_minggu']]
            );
        }

        return redirect()->route('mata-pelajaran.show', $mataPelajaran)
            ->with('success', 'Mata Pelajaran berhasil ditugaskan ke kelas');
    }
}

// ============================================
// app/Http/Controllers/JadwalPelajaranController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\MataPelajaranGuru;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPelajaran::with([
            'mataPelajaranGuru.mataPelajaranKelas.mataPelajaran',
            'mataPelajaranGuru.mataPelajaranKelas.kelas',
            'mataPelajaranGuru.guru'
        ]);

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('mataPelajaranGuru.mataPelajaranKelas', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwal = $query->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Data untuk filter
        $semesterAktif = Semester::active()->first();
        $kelas = $semesterAktif
            ? Kelas::where('semester_id', $semesterAktif->id)->orderBy('nama')->get()
            : collect();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pembelajaran.jadwal-pelajaran.index', compact('jadwal', 'kelas', 'hari'));
    }

    public function create()
    {
        $semesterAktif = Semester::active()->first();

        if (!$semesterAktif) {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Tidak ada semester aktif');
        }

        $kelas = Kelas::where('semester_id', $semesterAktif->id)
            ->with('jurusan')
            ->orderBy('nama')
            ->get();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pembelajaran.jadwal-pelajaran.create', compact('kelas', 'hari'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_guru_id' => 'required|exists:mata_pelajaran_guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:50',
        ]);

        // Validasi bentrok
        $bentrok = $this->checkBentrok(
            $validated['mata_pelajaran_guru_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($bentrok) {
            return redirect()->back()
                ->withInput()
                ->with('error', $bentrok);
        }

        JadwalPelajaran::create($validated);

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal Pelajaran berhasil ditambahkan');
    }

    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load([
            'mataPelajaranGuru.mataPelajaranKelas.mataPelajaran',
            'mataPelajaranGuru.mataPelajaranKelas.kelas',
            'mataPelajaranGuru.guru'
        ]);

        return view('pembelajaran.jadwal-pelajaran.show', compact('jadwalPelajaran'));
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load([
            'mataPelajaranGuru.mataPelajaranKelas.kelas',
            'mataPelajaranGuru.mataPelajaranKelas.mataPelajaran',
            'mataPelajaranGuru.guru'
        ]);

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pembelajaran.jadwal-pelajaran.edit', compact('jadwalPelajaran', 'hari'));
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:50',
        ]);

        // Validasi bentrok (exclude jadwal ini)
        $bentrok = $this->checkBentrok(
            $jadwalPelajaran->mata_pelajaran_guru_id,
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $jadwalPelajaran->id
        );

        if ($bentrok) {
            return redirect()->back()
                ->withInput()
                ->with('error', $bentrok);
        }

        $jadwalPelajaran->update($validated);

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal Pelajaran berhasil diperbarui');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->delete();

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal Pelajaran berhasil dihapus');
    }

    /**
     * Check bentrok jadwal
     */
    private function checkBentrok($mataPelajaranGuruId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $mpg = MataPelajaranGuru::with([
            'mataPelajaranKelas.kelas',
            'guru'
        ])->find($mataPelajaranGuruId);

        if (!$mpg) return null;

        // 1. Check bentrok guru (guru tidak bisa mengajar 2 kelas di waktu sama)
        $bentrokGuru = JadwalPelajaran::whereHas('mataPelajaranGuru', function ($q) use ($mpg) {
            $q->where('guru_id', $mpg->guru_id);
        })
            ->where('hari', $hari)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                        $q2->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
            });

        if ($excludeId) {
            $bentrokGuru->where('id', '!=', $excludeId);
        }

        if ($bentrokGuru->exists()) {
            return "Guru {$mpg->guru->nama_lengkap} sudah mengajar di hari {$hari} pada jam {$jamMulai} - {$jamSelesai}";
        }

        // 2. Check bentrok kelas (kelas tidak bisa ada 2 mapel di waktu sama)
        $bentrokKelas = JadwalPelajaran::whereHas('mataPelajaranGuru.mataPelajaranKelas', function ($q) use ($mpg) {
            $q->where('kelas_id', $mpg->mataPelajaranKelas->kelas_id);
        })
            ->where('hari', $hari)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                        $q2->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
            });

        if ($excludeId) {
            $bentrokKelas->where('id', '!=', $excludeId);
        }

        if ($bentrokKelas->exists()) {
            return "Kelas {$mpg->mataPelajaranKelas->kelas->nama} sudah ada jadwal di hari {$hari} pada jam {$jamMulai} - {$jamSelesai}";
        }

        return null;
    }

    /**
     * View jadwal per kelas (table format)
     */
    public function viewByKelas(Kelas $kelas)
    {
        $jadwal = JadwalPelajaran::whereHas('mataPelajaranGuru.mataPelajaranKelas', function ($q) use ($kelas) {
            $q->where('kelas_id', $kelas->id);
        })
            ->with([
                'mataPelajaranGuru.mataPelajaranKelas.mataPelajaran',
                'mataPelajaranGuru.guru'
            ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Group by hari
        $jadwalPerHari = $jadwal->groupBy('hari');

        return view('pembelajaran.jadwal-pelajaran.by-kelas', compact('kelas', 'jadwalPerHari'));
    }

    /**
     * View jadwal per guru
     */
    public function viewByGuru($guruId)
    {
        $guru = \App\Models\Guru::findOrFail($guruId);

        $jadwal = JadwalPelajaran::whereHas('mataPelajaranGuru', function ($q) use ($guruId) {
            $q->where('guru_id', $guruId);
        })
            ->with([
                'mataPelajaranGuru.mataPelajaranKelas.mataPelajaran',
                'mataPelajaranGuru.mataPelajaranKelas.kelas'
            ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $jadwalPerHari = $jadwal->groupBy('hari');

        return view('pembelajaran.jadwal-pelajaran.by-guru', compact('guru', 'jadwalPerHari'));
    }

    /**
     * Get mata pelajaran guru by kelas (AJAX)
     */
    public function getMataPelajaranByKelas(Request $request)
    {
        $kelasId = $request->kelas_id;

        $mataPelajaranGuru = MataPelajaranGuru::whereHas('mataPelajaranKelas', function ($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })
            ->with([
                'mataPelajaranKelas.mataPelajaran',
                'guru'
            ])
            ->get()
            ->map(function ($mpg) {
                return [
                    'id' => $mpg->id,
                    'label' => $mpg->mataPelajaranKelas->mataPelajaran->nama . ' - ' . $mpg->guru->nama_lengkap,
                    'mapel' => $mpg->mataPelajaranKelas->mataPelajaran->nama,
                    'guru' => $mpg->guru->nama_lengkap,
                ];
            });

        return response()->json($mataPelajaranGuru);
    }
}
