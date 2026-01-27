<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MataPelajaranKelas;
use App\Models\MataPelajaranGuru;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MataPelajaranKelasController extends Controller
{
    /**
     * Display list of subjects for a specific class.
     */
    public function index(Request $request, Kelas $kelas)
    {
        if ($request->ajax()) {
            $query = MataPelajaranKelas::with(['mataPelajaran', 'mataPelajaranGuru.guru'])
                ->where('kelas_id', $kelas->id);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kode_mapel', function ($row) {
                    return $row->mataPelajaran->kode;
                })
                ->addColumn('nama_mapel', function ($row) {
                    return $row->mataPelajaran->nama;
                })
                ->addColumn('guru_pengajar', function ($row) {
                    if ($row->mataPelajaranGuru->isEmpty()) {
                        return '<span class="badge badge-warning">Belum ada guru</span>';
                    }
                    
                    $gurus = $row->mataPelajaranGuru->map(function ($mpg) {
                        return '<span class="badge badge-info">' . $mpg->guru->nama_lengkap . '</span>';
                    })->implode(' ');
                    
                    return $gurus;
                })
                ->addColumn('jam_per_minggu', function ($row) {
                    return $row->jam_per_minggu . ' JP';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm btn-assign-guru" data-id="' . $row->id . '" title="Atur Guru"><i class="fas fa-chalkboard-teacher"></i></button>';
                    $btn .= '<button type="button" class="btn btn-warning btn-sm btn-edit-mapel" data-id="' . $row->id . '" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete-mapel" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['guru_pengajar', 'action'])
                ->make(true);
        }

        $mataPelajaran = MataPelajaran::active()->orderBy('nama')->get();
        return view('master-data.kelas.mata-pelajaran.index', compact('kelas', 'mataPelajaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'jam_per_minggu' => 'required|integer|min:1|max:10',
        ]);

        // Check duplicates
        $exists = MataPelajaranKelas::where('kelas_id', $kelas->id)
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Mata pelajaran sudah ada di kelas ini'], 422);
        }

        MataPelajaranKelas::create([
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'jam_per_minggu' => $validated['jam_per_minggu'],
        ]);

        return response()->json(['message' => 'Mata pelajaran berhasil ditambahkan']);
    }

    /**
     * Get details for editing
     */
    public function show($id) 
    {
        $mpk = MataPelajaranKelas::with('mataPelajaran', 'mataPelajaranGuru.guru')->findOrFail($id);
        return response()->json($mpk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $mpk = MataPelajaranKelas::findOrFail($id);
        
        $validated = $request->validate([
            'jam_per_minggu' => 'required|integer|min:1|max:10',
        ]);

        $mpk->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mpk = MataPelajaranKelas::findOrFail($id);
        
        // Optional: Check constraint if needed (e.g. if already used in schedule)
        
        $mpk->delete();

        return response()->json(['message' => 'Mata pelajaran berhasil dihapus dari kelas']);
    }

    /**
     * Assign teacher to subject in class
     */
    public function assignGuru(Request $request, $id)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:guru,id',
        ]);

        $mpk = MataPelajaranKelas::findOrFail($id);

        // Check if guru already assigned
        $exists = MataPelajaranGuru::where('mata_pelajaran_kelas_id', $id)
            ->where('guru_id', $validated['guru_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Guru sudah ditugaskan untuk mata pelajaran ini'], 422);
        }

        MataPelajaranGuru::create([
            'mata_pelajaran_kelas_id' => $id,
            'guru_id' => $validated['guru_id'],
        ]);

        return response()->json(['message' => 'Guru berhasil ditugaskan']);
    }

    /**
     * Remove teacher from subject in class
     */
    public function removeGuru($id, $guruId)
    {
        $mpg = MataPelajaranGuru::where('mata_pelajaran_kelas_id', $id)
            ->where('guru_id', $guruId)
            ->firstOrFail();

        $mpg->delete();

        return response()->json(['message' => 'Guru berhasil dihapus dari pengajar']);
    }

    /**
     * Get available gurus (helper for select2)
     */
    public function getGurus(Request $request)
    {
        $term = $request->get('term');
        $gurus = Guru::active()
            ->where('nama_lengkap', 'like', "%{$term}%")
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get()
            ->map(function($guru) {
                return [
                    'id' => $guru->id,
                    'text' => $guru->nama_lengkap . ' (' . ($guru->nip ?? '-') . ')'
                ];
            });

        return response()->json($gurus);
    }
}
