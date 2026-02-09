<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PerusahaanPkl;
use App\Models\Pkl;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    $kelasNama = $row->siswa->kelas->first()->nama ?? '-';

                    return $row->siswa->nama_lengkap.' <br><small>'.$kelasNama.'</small>';
                })
                ->addColumn('perusahaan', function ($row) {
                    return $row->perusahaanPkl->nama;
                })
                ->addColumn('pembimbing', function ($row) {
                    return $row->pembimbingSekolah->nama_lengkap ?? '-';
                })
                ->addColumn('periode', function ($row) {
                    return $row->tanggal_mulai && $row->tanggal_selesai ?
                        $row->tanggal_mulai->format('d/m/Y').' - '.$row->tanggal_selesai->format('d/m/Y') : '-';
                })
                ->addColumn('status_label', function ($row) {
                    $colors = [
                        'pengajuan' => 'secondary',
                        'disetujui' => 'info',
                        'aktif' => 'success',
                        'selesai' => 'primary',
                        'batal' => 'danger',
                    ];

                    return '<span class="badge badge-'.($colors[$row->status] ?? 'secondary').'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="'.route('pkl.edit', $row->id).'" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'" title="Hapus"><i class="fas fa-trash"></i></button>';
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
        $semesterAktif = Semester::active()->first();
        $kelas = Kelas::where('semester_id', $semesterAktif->id)->get();
        $perusahaanPkl = PerusahaanPkl::active()->get();
        $gurus = Guru::active()->get();

        return view('pkl.create', compact('kelas', 'perusahaanPkl', 'gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'required|exists:siswa,id',
            'perusahaan_pkl_id' => 'required|exists:perusahaan_pkl,id',
            'pembimbing_sekolah_id' => 'nullable|exists:guru,id',
            'pembimbing_industri' => 'nullable|string|max:150',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:pending,aktif,selesai,batal',
        ]);

        $semesterAktif = Semester::active()->first();

        DB::transaction(function () use ($validated, $semesterAktif) {
            foreach ($validated['siswa_id'] as $siswaId) {
                Pkl::create([
                    'siswa_id' => $siswaId,
                    'semester_id' => $semesterAktif->id,
                    'perusahaan_pkl_id' => $validated['perusahaan_pkl_id'],
                    'pembimbing_sekolah_id' => $validated['pembimbing_sekolah_id'],
                    'pembimbing_industri' => $validated['pembimbing_industri'],
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_selesai' => $validated['tanggal_selesai'],
                    'status' => $validated['status'],
                ]);
            }
        });

        return redirect()->route('pkl.index')->with('success', count($validated['siswa_id']).' data penempatan PKL berhasil ditambahkan.');
    }

    public function edit(Pkl $pkl)
    {
        $perusahaanPkl = PerusahaanPkl::active()->get();
        $gurus = Guru::where('is_active', true)->get();

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
