<?php

namespace App\Http\Controllers;

use App\Models\BukuInduk;
use App\Models\Siswa;
use App\Traits\HybridResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BukuIndukController extends Controller
{
    use HybridResponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }

        return view('user-data.buku-induk.index');
    }

    public function dataTable()
    {
        // We get students and check if they have buku induk data
        $siswa = Siswa::with('bukuInduk', 'kelasAktif')
            ->select('siswa.*');

        return DataTables::of($siswa)
            ->addIndexColumn()
            ->addColumn('nis_nisn', function ($row) {
                return $row->nis . ' / ' . $row->nisn;
            })
            ->addColumn('kelas', function ($row) {
                $kelas = $row->kelasAktif->first();
                return $kelas ? $kelas->nama : '-';
            })
            ->addColumn('status_data', function ($row) {
                return $row->bukuInduk 
                    ? '<span class="badge bg-success">Lengkap</span>' 
                    : '<span class="badge bg-warning">Belum Ada</span>';
            })
            ->addColumn('action', function ($row) {
                $btnClass = $row->bukuInduk ? 'btn-warning' : 'btn-primary';
                $icon = $row->bukuInduk ? 'bi-pencil' : 'bi-plus-lg';
                $title = $row->bukuInduk ? 'Edit Data Induk' : 'Input Data Induk';
                
                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('buku-induk.show', $row->id) . '"
                           class="btn btn-sm btn-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="' . route('buku-induk.edit', $row->id) . '"
                           class="btn btn-sm ' . $btnClass . '" title="' . $title . '">
                            <i class="bi ' . $icon . '"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['status_data', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $siswa = Siswa::with(['bukuInduk', 'kelasAktif'])->findOrFail($id);
        
        // Riwayat Kelas
        $riwayatKelas = \App\Models\SiswaKelas::with(['kelas.semester', 'kelas.tahunAkademik', 'kelas.jurusan'])
            ->where('siswa_id', $id)
            ->orderByDesc('tanggal_masuk')
            ->get();

        // Riwayat Mutasi
        $riwayatMutasi = \App\Models\MutasiSiswa::where('siswa_id', $id)
            ->orderByDesc('tanggal')
            ->get();

        // Riwayat Nilai (Group by Semester)
        $riwayatNilai = \App\Models\Nilai::with(['mataPelajaranKelas.mataPelajaran', 'semester'])
            ->where('siswa_id', $id)
            ->get()
            ->groupBy(function($item) {
                return $item->semester->nama ?? 'Semester Tidak Diketahui';
            });

        return view('user-data.buku-induk.show', compact('siswa', 'riwayatKelas', 'riwayatMutasi', 'riwayatNilai'));
    }

    public function edit($id)
    {
        $siswa = Siswa::with('bukuInduk')->findOrFail($id);
        $bukuInduk = $siswa->bukuInduk; // Can be null

        if (request()->ajax()) {
             return view('user-data.buku-induk.form', [
                'siswa' => $siswa,
                'bukuInduk' => $bukuInduk,
                'action' => route('buku-induk.update', $siswa->id),
                'method' => 'PUT',
            ]);
        }

        return view('user-data.buku-induk.edit', compact('siswa', 'bukuInduk'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nomor_induk' => 'nullable|string|max:50',
            'nomor_peserta_ujian' => 'nullable|string|max:50',
            'nomor_seri_ijazah' => 'nullable|string|max:50',
            'nomor_seri_skhun' => 'nullable|string|max:50',
            'tanggal_lulus' => 'nullable|date',
            'riwayat_pendidikan' => 'nullable|string',
            'riwayat_kesehatan' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $bukuInduk = $siswa->bukuInduk()->updateOrCreate(
                ['siswa_id' => $siswa->id],
                $validated
            );

            DB::commit();

            return $this->successResponse(
                'Data Buku Induk berhasil disimpan',
                'buku-induk.index',
                $bukuInduk
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }
}
