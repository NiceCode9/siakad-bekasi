<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPkl;
use App\Models\Pkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class JurnalPklController extends Controller
{
    /**
     * Display a listing of journals for the logged-in student.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('siswa')) {
             return redirect()->route('jurnal-pkl.pembimbing');
        }

        $siswaId = $user->siswa->id;
        $pkl = Pkl::where('siswa_id', $siswaId)->where('status', 'aktif')->first();

        if ($request->ajax()) {
            $query = MonitoringPkl::where('pkl_id', $pkl->id)->orderBy('tanggal_monitoring', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('foto_preview', function ($row) {
                    if ($row->foto) {
                        return '<img src="' . asset('storage/' . $row->foto) . '" width="50" class="img-thumbnail" style="cursor:pointer" onclick="viewImage(\''.asset('storage/' . $row->foto).'\')">';
                    }
                    return '-';
                })
                ->addColumn('status_label', function ($row) {
                    $badges = [
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger'
                    ];
                    return '<span class="badge badge-'.$badges[$row->status].'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">';
                    if ($row->status == 'pending' || $row->status == 'ditolak') {
                        $btn .= '<a href="' . route('jurnal-pkl.edit', $row->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
                        $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['foto_preview', 'status_label', 'action'])
                ->make(true);
        }

        return view('pkl.jurnal.index', compact('pkl'));
    }

    public function create()
    {
        $user = Auth::user();
        $pkl = Pkl::where('siswa_id', $user->siswa->id)->where('status', 'aktif')->firstOrFail();
        return view('pkl.jurnal.create', compact('pkl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pkl_id' => 'required|exists:pkl,id',
            'tanggal_monitoring' => 'required|date',
            'kegiatan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pkl/jurnal', 'public');
        }

        $data['status'] = 'pending';
        MonitoringPkl::create($data);

        return redirect()->route('jurnal-pkl.index')->with('success', 'Jurnal (monitoring) berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jurnalPkl = MonitoringPkl::findOrFail($id);
        if ($jurnalPkl->status == 'disetujui') {
            return back()->with('error', 'Jurnal yang sudah disetujui tidak dapat diedit.');
        }
        return view('pkl.jurnal.edit', compact('jurnalPkl'));
    }

    public function update(Request $request, $id)
    {
        $jurnalPkl = MonitoringPkl::findOrFail($id);
        if ($jurnalPkl->status == 'disetujui') {
            return back()->with('error', 'Jurnal yang sudah disetujui tidak dapat diedit.');
        }

        $request->validate([
            'tanggal_monitoring' => 'required|date',
            'kegiatan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto')) {
            if ($jurnalPkl->foto) Storage::disk('public')->delete($jurnalPkl->foto);
            $data['foto'] = $request->file('foto')->store('pkl/jurnal', 'public');
        }

        $data['status'] = 'pending'; 
        $jurnalPkl->update($data);

        return redirect()->route('jurnal-pkl.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurnalPkl = MonitoringPkl::findOrFail($id);
        if ($jurnalPkl->status == 'disetujui') {
             return response()->json(['message' => 'Jurnal disetujui tidak bisa dihapus'], 422);
        }
        if ($jurnalPkl->foto) Storage::disk('public')->delete($jurnalPkl->foto);
        $jurnalPkl->delete();
        return response()->json(['message' => 'Jurnal dihapus']);
    }

    /**
     * Dashboard for mentor to see their students' journals.
     */
    public function pembimbingIndex(Request $request)
    {
        $user = Auth::user();
        if ($request->ajax()) {
            $query = MonitoringPkl::with(['pkl.siswa.kelas'])
                ->whereHas('pkl', function($q) use ($user) {
                    $q->where('pembimbing_sekolah_id', $user->guru->id ?? 0);
                })
                ->orderBy('status', 'asc') // Pending first
                ->orderBy('tanggal_monitoring', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('siswa_info', function ($row) {
                    return $row->pkl->siswa->nama . '<br><small>' . ($row->pkl->siswa->kelas->nama ?? '-') . '</small>';
                })
                ->addColumn('foto_preview', function ($row) {
                    if ($row->foto) {
                        return '<img src="' . asset('storage/' . $row->foto) . '" width="60" class="img-thumbnail" style="cursor:pointer" onclick="viewImage(\''.asset('storage/' . $row->foto).'\')">';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    if ($row->status == 'pending') {
                        return '<button class="btn btn-success btn-sm mr-1" onclick="approve('.$row->id.')"><i class="fas fa-check"></i> Setujui</button>' .
                               '<button class="btn btn-danger btn-sm" onclick="reject('.$row->id.')"><i class="fas fa-times"></i> Tolak</button>';
                    }
                    return '<span class="text-muted">No Action</span>';
                })
                ->rawColumns(['siswa_info', 'foto_preview', 'action'])
                ->make(true);
        }

        return view('pkl.jurnal.pembimbing');
    }

    public function setStatus(Request $request, $id)
    {
        $jurnal = MonitoringPkl::findOrFail($id);
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $jurnal->update([
            'status' => $request->status,
            'catatan_pembimbing' => $request->catatan
        ]);

        return response()->json(['status' => 'success', 'message' => 'Status jurnal diperbarui.']);
    }
}
