<?php

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
            return response()->json([
                'success' => false,
                'message' => 'Semester ' . $request->nama . ' sudah ada di Tahun Akademik ini'
            ]);
        }

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        Semester::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Semester berhasil ditambahkan'
        ]);
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
        return response()->json($stats);
    }

    public function edit(Semester $semester)
    {
        $data = [
            'id' => $semester->id,
            'tahun_akademik_id' => $semester->tahun_akademik_id,
            'nama' => $semester->nama,
            'kode' => $semester->kode,
            'tanggal_mulai' => $semester->tanggal_mulai?->format('Y-m-d'),
            'tanggal_selesai' => $semester->tanggal_selesai?->format('Y-m-d'),
            'is_active' => $semester->is_active,
        ];
        return response()->json($data);
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
            return response()->json([
                'success' => false,
                'message' => 'Semester ' . $request->nama . ' sudah ada di Tahun Akademik ini'
            ]);
        }

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            Semester::where('is_active', true)
                ->where('id', '!=', $semester->id)
                ->update(['is_active' => false]);
        }

        $semester->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Semester berhasil diperbarui'
        ]);
    }

    public function destroy(Semester $semester)
    {
        // Check jika masih ada kelas
        if ($semester->kelas()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Semester tidak dapat dihapus karena masih ada kelas'
            ]);
        }

        $semester->delete();

        return response()->json([
            'status' => true,
            'message' => 'Semester berhasil dihapus'
        ]);
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

            return response()->json([
                'status' => true,
                'message' => 'Semester berhasil diaktifkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengaktifkan semester: ' . $e->getMessage()
            ]);
        }
    }

    public function getTahunAkademik()
    {
        $data = TahunAkademik::orderBy('kode', 'DESC')->get();
        return response()->json($data);
    }
}
