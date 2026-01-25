<?php

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

        try {
            // Jika set active, nonaktifkan yang lain
            if ($request->is_active) {
                Kurikulum::where('is_active', true)->update(['is_active' => false]);
            }

            Kurikulum::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Kurikulum berhasil ditambahkan',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan kurikulum: ' . $th->getMessage(),
            ], 500);
        }
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
        return response()->json([
            'status' => 'success',
            'data' => $kurikulum,
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Kurikulum berhasil diupdate',
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Kurikulum berhasil dihapus',
        ]);
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

            return response()->json([
                'status' => 'success',
                'message' => 'Kurikulum berhasil diaktifkan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'success',
                'message' => 'Kurikulum gagal diaktifkan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
