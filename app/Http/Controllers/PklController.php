<?php

namespace App\Http\Controllers;

use App\Models\Pkl;
use App\Models\PerusahaanPkl;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PklController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Pkl::with(['siswa.kelas', 'perusahaanPkl', 'pembimbingSekolah']);
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('siswa_nama', function ($row) {
                    return $row->siswa->nama . ' <br><small>' . ($row->siswa->kelas->nama ?? '-') . '</small>';
                })
                ->addColumn('perusahaan', function ($row) {
                    return $row->perusahaanPkl->nama;
                })
                ->addColumn('pembimbing', function ($row) {
                    return $row->pembimbingSekolah->nama ?? '-';
                })
                ->addColumn('periode', function ($row) {
                    return $row->tanggal_mulai && $row->tanggal_selesai ? 
                        $row->tanggal_mulai->format('d/m/Y') . ' - ' . $row->tanggal_selesai->format('d/m/Y') : '-';
                })
                ->addColumn('status_label', function ($row) {
                    $colors = [
                        'pengajuan' => 'secondary',
                        'disetujui' => 'info',
                        'aktif' => 'success',
                        'selesai' => 'primary',
                        'batal' => 'danger'
                    ];
                    return '<span class="badge badge-'.($colors[$row->status] ?? 'secondary').'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('pkl.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['siswa_nama', 'status_label', 'action'])
                ->make(true);
        }
        return view('pkl.index');
    }

    public function create()
    {
        $perusahaanPkl = PerusahaanPkl::active()->get();
        $gurus = Guru::where('status', 'aktif')->get();
        $semester = Semester::active()->first();
        $kelas = Kelas::where('semester_id', $semester->id)->orderBy('nama')->get();

        return view('pkl.create', compact('perusahaanPkl', 'gurus', 'kelas', 'semester'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'perusahaan_pkl_id' => 'required|exists:perusahaan_pkl,id',
            'pembimbing_sekolah_id' => 'nullable|exists:guru,id',
            'pembimbing_industri' => 'nullable|string|max:150',
            'jabatan_pembimbing_industri' => 'nullable|string|max:100',
            'telepon_pembimbing_industri' => 'nullable|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:pengajuan,disetujui,aktif,selesai,batal',
        ]);
        
        $validated['semester_id'] = Semester::active()->first()->id;

        Pkl::create($validated);

        return redirect()->route('pkl.index')->with('success', 'Data penempatan PKL berhasil disimpan.');
    }

    public function edit(Pkl $pkl)
    {
        $perusahaanPkl = PerusahaanPkl::active()->get();
        $gurus = Guru::where('status', 'aktif')->get();
        return view('pkl.edit', compact('pkl', 'perusahaanPkl', 'gurus'));
    }

    public function update(Request $request, Pkl $pkl)
    {
         $validated = $request->validate([
            'perusahaan_pkl_id' => 'required|exists:perusahaan_pkl,id',
            'pembimbing_sekolah_id' => 'nullable|exists:guru,id',
            'pembimbing_industri' => 'nullable|string|max:150',
            'jabatan_pembimbing_industri' => 'nullable|string|max:100',
            'telepon_pembimbing_industri' => 'nullable|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:pengajuan,disetujui,aktif,selesai,batal',
        ]);

        $pkl->update($validated);

        return redirect()->route('pkl.index')->with('success', 'Data penempatan PKL berhasil diperbarui.');
    }

    public function destroy(Pkl $pkl)
    {
        $pkl->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
