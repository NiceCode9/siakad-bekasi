<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAkademikController extends Controller
{
    public function index()
    {
        $tahunAkademik = TahunAkademik::with(['kurikulum', 'semester'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('master-data.tahun-akademik.index', compact('tahunAkademik'));
    }

    public function create()
    {
        $kurikulum = Kurikulum::active()->get();

        return view('master-data.tahun-akademik.create', compact('kurikulum'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:tahun_akademik,kode',
            'nama' => 'required|string|max:50',
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Jika set active, nonaktifkan yang lain
            if ($request->is_active) {
                TahunAkademik::where('is_active', true)->update(['is_active' => false]);
            }

            $tahunAkademik = TahunAkademik::create($validated);

            // Auto-create 2 semester (Ganjil & Genap)
            $this->createDefaultSemester($tahunAkademik);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tahun Ajaran Berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan Tahun Akademik: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(TahunAkademik $tahunAkademik)
    {
        $tahunAkademik->load(['kurikulum', 'semester.kelas']);
        return response()->json(["data" => $tahunAkademik]);
    }

    public function edit(TahunAkademik $tahunAkademik)
    {
        return response()->json([
            'data' => [
                'id' => $tahunAkademik->id,
                'kode' => $tahunAkademik->kode,
                'nama' => $tahunAkademik->nama,
                'kurikulum_id' => $tahunAkademik->kurikulum_id,
                'tanggal_mulai' => $tahunAkademik->tanggal_mulai?->format('Y-m-d'),
                'tanggal_selesai' => $tahunAkademik->tanggal_selesai?->format('Y-m-d'),
                'is_active' => $tahunAkademik->is_active,
            ]
        ]);
    }

    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:tahun_akademik,kode,' . $tahunAkademik->id,
            'nama' => 'required|string|max:50',
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        // Jika set active, nonaktifkan yang lain
        if ($request->is_active) {
            TahunAkademik::where('is_active', true)
                ->where('id', '!=', $tahunAkademik->id)
                ->update(['is_active' => false]);
            
            // Activation preferred via setActive to specify semester.
        } else {
            // Jika dinonaktifkan, nonaktifkan semua semesternya
            $tahunAkademik->semester()->update(['is_active' => false]);
        }

        $tahunAkademik->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Tahun Akademik berhasil diupdate',
        ]);
    }

    public function destroy(TahunAkademik $tahunAkademik)
    {
        if ($tahunAkademik->semester()->whereHas('kelas')->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tahun Akademik tidak dapat dihapus karena masih ada kelas'
            ], 422);
        }

        $tahunAkademik->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menghapus tahun akademik'
        ]);
    }

    public function setActive(Request $request, TahunAkademik $tahunAkademik, $semesterId = null)
    {
        $semesterId = $semesterId ?: $request->semester_id;

        if (!$semesterId) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Semester harus dipilih'
            ], 422);
        }

        DB::beginTransaction();
        try {
            TahunAkademik::where('id', '!=', $tahunAkademik->id)->update(['is_active' => false]);
            $tahunAkademik->update(['is_active' => true]);

            \App\Models\Semester::where('is_active', true)->update(['is_active' => false]);

            $semester = \App\Models\Semester::where('tahun_akademik_id', $tahunAkademik->id)
                ->where('id', $semesterId)
                ->first();

            if (!$semester) {
                throw new \Exception("Semester tidak ditemukan di Tahun Akademik ini.");
            }

            $semester->update(['is_active' => true]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mengaktifkan Tahun Akademik: ' . $tahunAkademik->nama . ' (' . $semester->nama . ')'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengaktifkan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSemesters(TahunAkademik $tahunAkademik)
    {
        $semesters = $tahunAkademik->semester()->get();
        return response()->json(['data' => $semesters]);
    }

    private function createDefaultSemester(TahunAkademik $tahunAkademik)
    {
        $tanggalMulai = \Carbon\Carbon::parse($tahunAkademik->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($tahunAkademik->tanggal_selesai);
        $tengah = $tanggalMulai->copy()->addMonths(6);

        $tahunAkademik->semester()->create([
            'nama' => 'Ganjil',
            'kode' => $tahunAkademik->kode . '-1',
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tengah->copy()->subDay(),
            'is_active' => false,
        ]);

        $tahunAkademik->semester()->create([
            'nama' => 'Genap',
            'kode' => $tahunAkademik->kode . '-2',
            'tanggal_mulai' => $tengah,
            'tanggal_selesai' => $tanggalSelesai,
            'is_active' => false,
        ]);
    }

    public function getKurikulum()
    {
        $data = Kurikulum::active()->get();
        return response()->json(['data' => $data]);
    }
}
