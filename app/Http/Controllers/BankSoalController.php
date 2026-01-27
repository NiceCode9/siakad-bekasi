<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;
use App\Exports\SoalTemplateExport;

class BankSoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankSoal::with(['mataPelajaran', 'pembuat', 'soal']);

            if (Auth::user()->hasRole('guru')) {
                $query->where('pembuat_id', Auth::user()->guru_id);
            }
            
            // Filter
            if ($request->filled('mata_pelajaran_id')) {
                $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
            }
            if ($request->filled('tingkat_kesulitan')) {
                $query->where('tingkat_kesulitan', $request->tingkat_kesulitan);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('mapel_nama', function ($row) {
                    return $row->mataPelajaran->nama ?? '-';
                })
                ->addColumn('pembuat_nama', function ($row) {
                    return $row->pembuat->nama_lengkap ?? '-';
                })
                ->addColumn('jumlah_soal', function ($row) {
                    return $row->soal->count();
                })
                ->addColumn('status', function ($row) {
                    return $row->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('bank-soal.show', $row->id) . '" class="btn btn-info btn-sm" title="Kelola Soal"><i class="fas fa-list"></i> Soal</a>';
                    $btn .= '<a href="' . route('bank-soal.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    // Duplicate Button
                    $btn .= '<form action="'.route('bank-soal.duplicate', $row->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Duplicate bank soal ini?\')">'.csrf_field().'<button type="submit" class="btn btn-secondary btn-sm" title="Duplicate"><i class="fas fa-copy"></i></button></form>';
                    
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $mapel = MataPelajaran::orderBy('nama')->get();
        return view('pembelajaran.cbt.bank-soal.index', compact('mapel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mapel = MataPelajaran::orderBy('nama')->get();
        return view('pembelajaran.cbt.bank-soal.create', compact('mapel'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kode' => 'required|string|max:50|unique:bank_soal,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tingkat_kesulitan' => 'nullable|in:mudah,sedang,sulit',
            'is_active' => 'boolean',
        ]);

        $validated['pembuat_id'] = Auth::user()->guru_id ?? 1; // Fallback for dev if not linked
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        BankSoal::create($validated);

        return redirect()->route('bank-soal.index')->with('success', 'Bank Soal berhasil dibuat');
    }

    /**
     * Display the specified resource (Manage Soal).
     */
    public function show(BankSoal $bankSoal)
    {
        $bankSoal->load(['mataPelajaran', 'soal']);
        return view('pembelajaran.cbt.bank-soal.show', compact('bankSoal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankSoal $bankSoal)
    {
        $mapel = MataPelajaran::orderBy('nama')->get();
        return view('pembelajaran.cbt.bank-soal.edit', compact('bankSoal', 'mapel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankSoal $bankSoal)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kode' => 'required|string|max:50|unique:bank_soal,kode,' . $bankSoal->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tingkat_kesulitan' => 'nullable|in:mudah,sedang,sulit',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $bankSoal->update($validated);

        return redirect()->route('bank-soal.index')->with('success', 'Bank Soal berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankSoal $bankSoal)
    {
        $bankSoal->delete();
        return response()->json(['message' => 'Bank Soal berhasil dihapus']);
    }

    // --- New Features ---

    public function duplicate($id)
    {
        $original = BankSoal::with('soal')->findOrFail($id);
        
        $new = $original->replicate();
        $new->kode = $original->kode . '-COPY-' . time();
        $new->nama = $original->nama . ' (Copy)';
        $new->is_active = false;
        $new->created_at = now();
        $new->save();

        foreach ($original->soal as $soal) {
            $newSoal = $soal->replicate();
            $newSoal->bank_soal_id = $new->id;
            $newSoal->created_at = now();
            $newSoal->save();
        }

        return back()->with('success', 'Bank Soal berhasil diduplikasi.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SoalTemplateExport, 'template_soal.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:csv,txt,xlsx,xls',
            'bank_soal_id' => 'required|exists:bank_soal,id'
        ]);

        try {
            Excel::import(new SoalImport($request->bank_soal_id), $request->file('file_import'));
            return back()->with('success', 'Import berhasil.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $msg = 'Gagal import. ';
             foreach ($failures as $failure) {
                 $msg .= 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '. ';
             }
             return back()->with('error', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
