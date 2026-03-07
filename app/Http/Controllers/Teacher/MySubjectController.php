<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaranKelas;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MySubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan');
        }

        $tahunAkademiks = TahunAkademik::orderBy('nama', 'desc')->get();

        $query = MataPelajaranKelas::with(['mataPelajaran', 'kelas.semester.tahunAkademik'])
            ->where('guru_id', $guru->id);

        if ($request->has('tahun_akademik_id') && $request->tahun_akademik_id != '') {
            $query->whereHas('kelas.semester', function($q) use ($request) {
                $q->where('tahun_akademik_id', $request->tahun_akademik_id);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('mataPelajaran', function($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%')
                       ->orWhere('kode', 'like', '%' . $search . '%');
                })->orWhereHas('kelas', function($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                });
            });
        }

        $assignmentsList = $query->paginate(10);

        $assignments = $assignmentsList->groupBy(function($item) {
            return $item->kelas->semester->tahunAkademik->nama . ' - ' . $item->kelas->semester->nama;
        });

        return view('teacher.my-subject.index', compact('assignments', 'assignmentsList', 'tahunAkademiks', 'guru'));
    }

    public function updateKkm(Request $request, $id)
    {
        $validated = $request->validate([
            'kkm' => 'required|numeric|min:0|max:100',
            'capaian_kompetensi_a' => 'nullable|string',
            'capaian_kompetensi_b' => 'nullable|string',
            'capaian_kompetensi_c' => 'nullable|string',
            'capaian_kompetensi_d' => 'nullable|string',
        ]);

        $mpk = MataPelajaranKelas::findOrFail($id);

        // Ensure the teacher is authorized to update this specific assignment
        $user = Auth::user();
        if ($user->guru->id !== $mpk->guru_id && !$user->hasRole(['admin', 'super-admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $mpk->update([
            'kkm' => $validated['kkm'],
            'capaian_kompetensi_a' => $validated['capaian_kompetensi_a'],
            'capaian_kompetensi_b' => $validated['capaian_kompetensi_b'],
            'capaian_kompetensi_c' => $validated['capaian_kompetensi_c'],
            'capaian_kompetensi_d' => $validated['capaian_kompetensi_d'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KKM dan Capaian Kompetensi berhasil diperbarui',
            'kkm' => $mpk->kkm,
            'capaian_kompetensi_a' => $mpk->capaian_kompetensi_a,
            'capaian_kompetensi_b' => $mpk->capaian_kompetensi_b,
            'capaian_kompetensi_c' => $mpk->capaian_kompetensi_c,
            'capaian_kompetensi_d' => $mpk->capaian_kompetensi_d,
        ]);
    }
}
