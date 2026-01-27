<?php

namespace App\Http\Controllers;

use App\Models\PrestasiSiswa;
use App\Models\Siswa;
use App\Traits\HybridResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PrestasiSiswaController extends Controller
{
    use HybridResponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }

        return view('user-data.prestasi.index');
    }

    public function dataTable()
    {
        $prestasi = PrestasiSiswa::with('siswa')
            ->select('prestasi_siswa.*');

        return DataTables::of($prestasi)
            ->addIndexColumn()
            ->addColumn('nama_siswa', function ($row) {
                return $row->siswa ? $row->siswa->nama_lengkap : '-';
            })
            ->addColumn('file_link', function ($row) {
                if ($row->file_sertifikat) {
                    return '<a href="' . Storage::url($row->file_sertifikat) . '" target="_blank" class="btn btn-xs btn-outline-info"><i class="bi bi-file-earmark-pdf"></i> Lihat</a>';
                }
                return '-';
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
                                data-name="' . $row->nama_prestasi . '"
                                title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['file_link', 'action'])
            ->make(true);
    }

    public function create()
    {
        if (request()->ajax()) {
            return view('user-data.prestasi.form', [
                'prestasi' => null,
                'action' => route('prestasi-siswa.store'),
                'method' => 'POST',
            ]);
        }
        return abort(404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis' => 'required|in:Akademik,Non-Akademik',
            'nama_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string|max:50',
            'peringkat' => 'required|string|max:50',
            'penyelenggara' => 'nullable|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $filePath = null;
            if ($request->hasFile('file_sertifikat')) {
                $filePath = $request->file('file_sertifikat')->store('sertifikat-prestasi', 'public');
            }

            $prestasi = PrestasiSiswa::create([
                'siswa_id' => $validated['siswa_id'],
                'jenis' => $validated['jenis'],
                'nama_prestasi' => $validated['nama_prestasi'],
                'tingkat' => $validated['tingkat'],
                'peringkat' => $validated['peringkat'],
                'penyelenggara' => $validated['penyelenggara'],
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
                'file_sertifikat' => $filePath,
            ]);

            DB::commit();

            return $this->successResponse('Prestasi berhasil ditambahkan', 'prestasi-siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return $this->errorResponse('Gagal menambahkan prestasi: ' . $e->getMessage(), 500);
        }
    }

    public function edit(PrestasiSiswa $prestasiSiswa)
    {
        $prestasiSiswa->load('siswa');
        if (request()->ajax()) {
            return view('user-data.prestasi.form', [
                'prestasi' => $prestasiSiswa,
                'action' => route('prestasi-siswa.update', $prestasiSiswa->id),
                'method' => 'PUT',
            ]);
        }
        return abort(404);
    }

    public function update(Request $request, PrestasiSiswa $prestasiSiswa)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis' => 'required|in:Akademik,Non-Akademik',
            'nama_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string|max:50',
            'peringkat' => 'required|string|max:50',
            'penyelenggara' => 'nullable|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $filePath = $prestasiSiswa->file_sertifikat;
            if ($request->hasFile('file_sertifikat')) {
                if ($prestasiSiswa->file_sertifikat) {
                    Storage::disk('public')->delete($prestasiSiswa->file_sertifikat);
                }
                $filePath = $request->file('file_sertifikat')->store('sertifikat-prestasi', 'public');
            }

            $prestasiSiswa->update([
                'siswa_id' => $validated['siswa_id'],
                'jenis' => $validated['jenis'],
                'nama_prestasi' => $validated['nama_prestasi'],
                'tingkat' => $validated['tingkat'],
                'peringkat' => $validated['peringkat'],
                'penyelenggara' => $validated['penyelenggara'],
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
                'file_sertifikat' => $filePath,
            ]);

            DB::commit();

            return $this->successResponse('Prestasi berhasil diperbarui', 'prestasi-siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal memperbarui prestasi: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(PrestasiSiswa $prestasiSiswa)
    {
        try {
            if ($prestasiSiswa->file_sertifikat) {
                Storage::disk('public')->delete($prestasiSiswa->file_sertifikat);
            }
            $prestasiSiswa->delete();
            return $this->successResponse('Prestasi berhasil dihapus', 'prestasi-siswa.index');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus prestasi: ' . $e->getMessage(), 500);
        }
    }
}
