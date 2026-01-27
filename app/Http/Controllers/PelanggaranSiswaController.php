<?php

namespace App\Http\Controllers;

use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Traits\HybridResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PelanggaranSiswaController extends Controller
{
    use HybridResponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }

        return view('user-data.pelanggaran.index');
    }

    public function dataTable()
    {
        $pelanggaran = PelanggaranSiswa::with(['siswa', 'pelapor'])
            ->select('pelanggaran_siswa.*');

        return DataTables::of($pelanggaran)
            ->addIndexColumn()
            ->addColumn('nama_siswa', function ($row) {
                return $row->siswa ? $row->siswa->nama_lengkap . ' (' . $row->siswa->kelasAktif->first()?->nama . ')' : '-';
            })
            ->addColumn('nama_pelapor', function ($row) {
                return $row->pelapor ? $row->pelapor->nama_lengkap : '-';
            })
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal ? $row->tanggal->format('d/m/Y') : '-';
            })
            ->addColumn('status_badge', function ($row) {
                $color = $row->status == 'selesai' ? 'success' : 'warning';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group" role="group">
                         <button type="button"
                                class="btn btn-sm btn-warning btn-edit"
                                data-id="' . $row->id . '"
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-id="' . $row->id . '"
                                data-name="' . $row->jenis_pelanggaran . '"
                                title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function create()
    {
        if (request()->ajax()) {
            return view('user-data.pelanggaran.form', [
                'pelanggaran' => null,
                'action' => route('pelanggaran-siswa.store'),
                'method' => 'POST',
            ]);
        }
        return abort(404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'kategori' => 'required|in:Ringan,Sedang,Berat',
            'poin' => 'required|integer|min:0',
            'kronologi' => 'required|string',
            'sanksi' => 'nullable|string',
            'pelapor_id' => 'nullable|exists:guru,id',
            'status' => 'required|in:proses,selesai',
        ]);

        DB::beginTransaction();
        try {
            PelanggaranSiswa::create($validated);
            DB::commit();
            return $this->successResponse('Pelanggaran berhasil dicatat', 'pelanggaran-siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal mencatat pelanggaran: ' . $e->getMessage(), 500);
        }
    }

    public function edit(PelanggaranSiswa $pelanggaranSiswa)
    {
        $pelanggaranSiswa->load('siswa', 'pelapor');
        if (request()->ajax()) {
            return view('user-data.pelanggaran.form', [
                'pelanggaran' => $pelanggaranSiswa,
                'action' => route('pelanggaran-siswa.update', $pelanggaranSiswa->id),
                'method' => 'PUT',
            ]);
        }
        return abort(404);
    }

    public function update(Request $request, PelanggaranSiswa $pelanggaranSiswa)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'kategori' => 'required|in:Ringan,Sedang,Berat',
            'poin' => 'required|integer|min:0',
            'kronologi' => 'required|string',
            'sanksi' => 'nullable|string',
            'pelapor_id' => 'nullable|exists:guru,id',
            'status' => 'required|in:proses,selesai',
        ]);

        DB::beginTransaction();
        try {
            $pelanggaranSiswa->update($validated);
            DB::commit();
            return $this->successResponse('Data pelanggaran berhasil diperbarui', 'pelanggaran-siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal memperbarui data pelanggaran: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(PelanggaranSiswa $pelanggaranSiswa)
    {
        try {
            $pelanggaranSiswa->delete();
            return $this->successResponse('Data pelanggaran berhasil dihapus', 'pelanggaran-siswa.index');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus data pelanggaran: ' . $e->getMessage(), 500);
        }
    }
}
