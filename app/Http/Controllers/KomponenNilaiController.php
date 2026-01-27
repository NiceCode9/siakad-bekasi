<?php

namespace App\Http\Controllers;

use App\Models\KomponenNilai;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KomponenNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = KomponenNilai::with('kurikulum');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kurikulum_nama', function ($row) {
                    return $row->kurikulum->nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . $row->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $kurikulum = Kurikulum::all();
        return view('master-data.komponen-nilai.index', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'kode' => 'required|string|max:20|unique:komponen_nilai,kode',
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:pengetahuan,keterampilan',
            'bobot' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        KomponenNilai::create($validated);

        return response()->json(['message' => 'Komponen nilai berhasil ditambahkan']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $komponen = KomponenNilai::findOrFail($id);
        return response()->json($komponen);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $komponen = KomponenNilai::findOrFail($id);

        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'kode' => 'required|string|max:20|unique:komponen_nilai,kode,' . $id,
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:pengetahuan,keterampilan',
            'bobot' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $komponen->update($validated);

        return response()->json(['message' => 'Komponen nilai berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $komponen = KomponenNilai::findOrFail($id);
        
        // Check usage before delete if needed
        if ($komponen->nilai()->exists()) {
             return response()->json(['message' => 'Gagal! Komponen ini sudah digunakan dalam data nilai.'], 422);
        }

        $komponen->delete();

        return response()->json(['message' => 'Komponen nilai berhasil dihapus']);
    }
}
