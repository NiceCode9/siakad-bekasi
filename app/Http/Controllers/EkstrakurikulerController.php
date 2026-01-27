<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\Guru;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ekstrakurikuler::with('pembina');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('pembina_nama', function ($row) {
                    return $row->pembina->nama_lengkap ?? '-';
                })
                ->addColumn('waktu', function ($row) {
                    if ($row->hari) {
                        $jam = $row->jam_mulai ? $row->jam_mulai->format('H:i') : '';
                        $jam .= $row->jam_selesai ? ' - ' . $row->jam_selesai->format('H:i') : '';
                        return $row->hari . ' ' . $jam;
                    }
                    return '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . $row->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        $gurus = Guru::active()->orderBy('nama_lengkap')->get();
        return view('master-data.ekstrakurikuler.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'pembina_id' => 'nullable|exists:guru,id',
            'hari' => 'nullable|string|max:20',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Ekstrakurikuler::create($validated);

        return response()->json(['message' => 'Data berhasil disimpan']);
    }

    public function edit($id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);
        
        // Format time for form
        if($ekskul->jam_mulai) $ekskul->jam_mulai = $ekskul->jam_mulai->format('H:i');
        if($ekskul->jam_selesai) $ekskul->jam_selesai = $ekskul->jam_selesai->format('H:i');

        return response()->json($ekskul);
    }

    public function update(Request $request, $id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'pembina_id' => 'nullable|exists:guru,id',
            'hari' => 'nullable|string|max:20',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $ekskul->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);
        $ekskul->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
