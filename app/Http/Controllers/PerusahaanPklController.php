<?php

namespace App\Http\Controllers;

use App\Models\PerusahaanPkl;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PerusahaanPklController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PerusahaanPkl::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('perusahaan-pkl.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('status_label', function ($row) {
                    return $row->is_active 
                        ? '<span class="badge badge-success">Aktif</span>' 
                        : '<span class="badge badge-secondary">Non-Aktif</span>';
                })
                ->rawColumns(['action', 'status_label'])
                ->make(true);
        }
        return view('pkl.perusahaan.index');
    }

    public function create()
    {
        return view('pkl.perusahaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bidang_usaha' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nama_kontak' => 'nullable|string|max:100',
            'jabatan_kontak' => 'nullable|string|max:100',
            'telepon_kontak' => 'nullable|string|max:20',
            'kuota' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');

        PerusahaanPkl::create($validated);

        return redirect()->route('perusahaan-pkl.index')->with('success', 'Data perusahaan PKL berhasil ditambahkan.');
    }

    public function edit(PerusahaanPkl $perusahaanPkl)
    {
        return view('pkl.perusahaan.edit', compact('perusahaanPkl'));
    }

    public function update(Request $request, PerusahaanPkl $perusahaanPkl)
    {
         $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bidang_usaha' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nama_kontak' => 'nullable|string|max:100',
            'jabatan_kontak' => 'nullable|string|max:100',
            'telepon_kontak' => 'nullable|string|max:20',
            'kuota' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active'); // checkbox

        $perusahaanPkl->update($validated);

        return redirect()->route('perusahaan-pkl.index')->with('success', 'Data perusahaan PKL berhasil diperbarui.');
    }

    public function destroy(PerusahaanPkl $perusahaanPkl)
    {
        try {
            $perusahaanPkl->delete();
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data. Mungkin sedang digunakan.'], 500);
        }
    }
}
