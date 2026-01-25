<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jurusan = Jurusan::withCount('kelas')->select('jurusan.*');

            return DataTables::of($jurusan)
                ->addIndexColumn()
                ->editColumn('kelas_count', function ($row) {
                    return $row->kelas_count . ' Kelas';
                })
                ->editColumn('is_active', function ($row) {
                    if ($row->is_active) {
                        return '<span class="badge badge-success">Aktif</span>';
                    } else {
                        return '<span class="badge badge-secondary">Nonaktif</span>';
                    }
                })
                ->addColumn('aksi', function ($row) {
                    $toggleBtn = $row->is_active ?
                        '<button class="btn btn-sm btn-warning btn-toggle" data-id="' . $row->id . '" title="Nonaktifkan"><i class="simple-icon-ban"></i></button>' :
                        '<button class="btn btn-sm btn-success btn-toggle" data-id="' . $row->id . '" title="Aktifkan"><i class="simple-icon-check"></i></button>';

                    return '
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-detail" data-id="' . $row->id . '" title="Detail">
                            <i class="simple-icon-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary btn-edit" data-id="' . $row->id . '" title="Edit">
                            <i class="simple-icon-pencil"></i>
                        </button>
                        ' . $toggleBtn . '
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '" title="Hapus">
                            <i class="simple-icon-trash"></i>
                        </button>
                    </div>
                ';
                })
                ->rawColumns(['is_active', 'aksi'])
                ->make(true);
        }

        return view('master-data.jurusan.index');
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

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil disimpan'
        ]);
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

        return response()->json([
            'jurusan' => $jurusan,
            'stats' => $stats,
        ]);
    }

    public function edit(Jurusan $jurusan)
    {
        return response()->json($jurusan);
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

        return response()->json([
            'success' => true,
            'messages' => 'Jurusan berhasil diperbarui'
        ]);
    }

    public function destroy(Jurusan $jurusan)
    {
        // Check jika masih ada kelas
        if ($jurusan->kelas()->exists()) {
            return response()->json([
                'success' => false,
                'messages' => 'Jurusan tidak dapat dihapus karena masih ada kelas',
            ]);
        }

        $jurusan->delete();

        return response()->json([
            'success' => true,
            'messages' => 'Jurusan berhasil dihapus'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Jurusan $jurusan)
    {
        $jurusan->update(['is_active' => !$jurusan->is_active]);

        $status = $jurusan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json(['success' => true, 'messages' => "Jurusan berhasil {$status}"]);
    }
}
