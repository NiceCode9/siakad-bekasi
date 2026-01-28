<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Kurikulum;
use App\Models\KelompokMapel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
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

            // Filter by status
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kurikulum_nama', function ($row) {
                    return $row->kurikulum->nama ?? '-';
                })
                ->addColumn('kelompok_mapel_nama', function ($row) {
                    return $row->kelompokMapel->nama ?? '-';
                })
                ->addColumn('jenis_badge', function ($row) {
                    $badges = [
                        'umum' => '<span class="badge badge-primary">Umum</span>',
                        'kejuruan' => '<span class="badge badge-success">Kejuruan</span>',
                        'muatan_lokal' => '<span class="badge badge-info">Muatan Lokal</span>',
                    ];
                    return $badges[$row->jenis] ?? '-';
                })
                ->addColumn('kategori_badge', function ($row) {
                    $badges = [
                        'wajib' => '<span class="badge badge-danger">Wajib</span>',
                        'peminatan' => '<span class="badge badge-warning">Peminatan</span>',
                        'lintas_minat' => '<span class="badge badge-secondary">Lintas Minat</span>',
                    ];
                    return $badges[$row->kategori] ?? '-';
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_active) {
                        return '<span class="badge badge-success">Aktif</span>';
                    }
                    return '<span class="badge badge-secondary">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('mata-pelajaran.show', $row->id) . '" class="btn btn-info btn-sm" title="Detail"><i class="simple-icon-eye"></i></a>';
                    $btn .= '<a href="' . route('mata-pelajaran.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="simple-icon-pencil"></i></a>';

                    // Toggle active button
                    if ($row->is_active) {
                        $btn .= '<button type="button" class="btn btn-secondary btn-sm btn-toggle-active" data-id="' . $row->id . '" title="Nonaktifkan"><i class="simple-icon-power"></i></button>';
                    } else {
                        $btn .= '<button type="button" class="btn btn-success btn-sm btn-toggle-active" data-id="' . $row->id . '" title="Aktifkan"><i class="simple-icon-check"></i></button>';
                    }

                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="simple-icon-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['jenis_badge', 'kategori_badge', 'status', 'action'])
                ->make(true);
        }

        // Data untuk filter
        $kurikulum = Kurikulum::all();
        $kelompokMapel = KelompokMapel::orderBy('urutan')->get();

        return view('master-data.mata-pelajaran.index', compact('kurikulum', 'kelompokMapel'));
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

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

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
            'mataPelajaranKelas.guru'
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

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $mataPelajaran->update($validated);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata Pelajaran berhasil diperbarui');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        // Check jika masih digunakan
        if ($mataPelajaran->mataPelajaranKelas()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Mata Pelajaran tidak dapat dihapus karena masih digunakan di kelas'
            ], 400);
        }

        $mataPelajaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata Pelajaran berhasil dihapus'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->update(['is_active' => !$mataPelajaran->is_active]);

        $status = $mataPelajaran->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Mata Pelajaran berhasil {$status}",
            'is_active' => $mataPelajaran->is_active
        ]);
    }

    /**
     * Assign ke kelas
     */
    public function assignToKelas(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|array',
            'kelas_id.*' => 'exists:kelas,id',
            'guru_id' => 'required|exists:guru,id',
            'jam_per_minggu' => 'required|integer|min:1|max:20',
        ]);

       foreach ($validated['kelas_id'] as $kelasId) {
            // Buat atau update MataPelajaranKelas
            $mataPelajaran->mataPelajaranKelas()->updateOrCreate(
                ['kelas_id' => $kelasId],
                [
                    'guru_id' => $validated['guru_id'],
                    'jam_per_minggu' => $validated['jam_per_minggu']
                ]
            );
        }

        return redirect()->route('mata-pelajaran.show', $mataPelajaran)
            ->with('success', 'Mata Pelajaran berhasil ditugaskan ke kelas');
    }
}
