<?php

namespace App\Http\Controllers;

use App\Models\NilaiPkl;
use App\Models\Pkl;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class NilaiPklController extends Controller
{
    /**
     * List students for assessment (Mentor View).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($request->ajax()) {
            $query = Pkl::with(['siswa.kelas', 'perusahaanPkl', 'nilaiPkl'])
                ->whereIn('status', ['aktif', 'selesai']);

            if (!$user->hasRole(['admin', 'super-admin'])) {
                // Ensure user is Guru
                if (!$user->guru) {
                    return response()->json(['data' => []]);
                }
                
                // Filter students: Wali Kelas of the student OR Pembimbing Sekolah of the PKL
                $query->where(function($q) use ($user) {
                    $q->whereHas('siswa.kelas', function($sq) use ($user) {
                        $sq->where('wali_kelas_id', $user->guru->id ?? 0);
                    })->orWhere('pembimbing_sekolah_id', $user->guru->id ?? 0);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('siswa_info', function ($row) {
                    return $row->siswa->nama . '<br><small>' . ($row->siswa->kelas->nama ?? '-') . '</small>';
                })
                ->addColumn('nilai_akhir', function ($row) {
                    return $row->nilaiPkl->nilai_akhir ?? '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('pkl-nilai.edit', $row->id) . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Input Nilai</a>';
                })
                ->rawColumns(['siswa_info', 'nilai_akhir', 'action'])
                ->make(true);
        }

        return view('pkl.nilai.index');
    }

    public function edit($pklId)
    {
        $user = Auth::user();
        $pkl = Pkl::with(['siswa.kelas', 'perusahaanPkl', 'nilaiPkl'])->findOrFail($pklId);
        
        // Authorization check
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $isWali = Kelas::where('id', $pkl->siswa->kelas->id ?? 0)
                ->where('wali_kelas_id', $user->guru->id ?? 0)
                ->exists();
            
            $isPembimbing = ($pkl->pembimbing_sekolah_id == ($user->guru->id ?? 0));
            
            if (!$isWali && !$isPembimbing) {
                return redirect()->route('pkl-nilai.index')->with('error', 'Anda tidak memiliki akses untuk menilai siswa ini.');
            }
        }

        $nilai = $pkl->nilaiPkl ?? new NilaiPkl();
        
        return view('pkl.nilai.edit', compact('pkl', 'nilai'));
    }

    public function update(Request $request, $pklId)
    {
        $user = Auth::user();
        $pkl = Pkl::with('siswa.kelas')->findOrFail($pklId);

        // Authorization check
        if (!$user->hasRole(['admin', 'super-admin'])) {
            $isWali = Kelas::where('id', $pkl->siswa->kelas->id ?? 0)
                ->where('wali_kelas_id', $user->guru->id ?? 0)
                ->exists();
                
            $isPembimbing = ($pkl->pembimbing_sekolah_id == ($user->guru->id ?? 0));
            
            if (!$isWali && !$isPembimbing) {
                return redirect()->route('pkl-nilai.index')->with('error', 'Gagal menyimpan: Anda tidak memiliki akses untuk menilai siswa ini.');
            }
        }

        $request->validate([
            'nilai_sikap_kerja' => 'required|numeric|min:0|max:100',
            'nilai_keterampilan' => 'required|numeric|min:0|max:100',
            'nilai_laporan' => 'required|numeric|min:0|max:100',
            'nilai_dari_sekolah' => 'required|numeric|min:0|max:100',
            'catatan_industri' => 'nullable|string',
            'catatan_sekolah' => 'nullable|string',
            'tanggal_penilaian' => 'required|date',
        ]);

        $nilai = NilaiPkl::updateOrCreate(
            ['pkl_id' => $pklId],
            [
                'nilai_sikap_kerja' => $request->nilai_sikap_kerja,
                'nilai_keterampilan' => $request->nilai_keterampilan,
                'nilai_laporan' => $request->nilai_laporan,
                'nilai_dari_sekolah' => $request->nilai_dari_sekolah,
                'catatan_industri' => $request->catatan_industri,
                'catatan_sekolah' => $request->catatan_sekolah,
                'tanggal_penilaian' => $request->tanggal_penilaian,
            ]
        );

        $nilai->hitungNilaiAkhir();

        return redirect()->route('pkl-nilai.index')->with('success', 'Nilai PKL berhasil disimpan.');
    }
}
