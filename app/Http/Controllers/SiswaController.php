<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use App\Models\OrangTua;
use App\Models\Kelas;
use App\Models\SiswaKelas;
use App\Traits\HybridResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SiswaController extends Controller
{
    use HybridResponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable($request);
        }

        // Data untuk filter
        $kelas = Kelas::with('semester')->orderBy('nama')->get();
        $status = ['aktif', 'lulus', 'pindah', 'keluar', 'DO'];

        return view('user-data.siswa.index', compact('kelas', 'status'));
    }

    public function dataTable(Request $request)
    {
        $query = Siswa::with(['user', 'orangTua', 'kelasAktif'])
            ->select('siswa.*');

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('kelasAktif', function ($q) use ($request) {
                $q->where('kelas.id', $request->kelas_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kelas_aktif', function ($row) {
                $kelas = $row->kelasAktif->first();
                return $kelas ? $kelas->nama : '-';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'aktif' => 'success',
                    'lulus' => 'info',
                    'pindah' => 'warning',
                    'keluar' => 'secondary',
                    'DO' => 'danger',
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('jk', function ($row) {
                return $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('siswa.show', $row->id) . '"
                           class="btn btn-sm btn-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button type="button"
                                class="btn btn-sm btn-warning btn-edit"
                                data-id="' . $row->id . '">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-primary btn-assign-kelas"
                                data-id="' . $row->id . '"
                                data-name="' . $row->nama_lengkap . '">
                            <i class="bi bi-door-open"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-id="' . $row->id . '"
                                data-name="' . $row->nama_lengkap . '">
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
        $orangTua = OrangTua::all();

        if (request()->ajax()) {
            return view('user-data.siswa.form', [
                'siswa' => null,
                'orangTua' => $orangTua,
                'action' => route('siswa.store'),
                'method' => 'POST',
            ]);
        }

        return view('user-data.siswa.create', compact('orangTua'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'orang_tua_id' => 'nullable|exists:orang_tua,id',
            'nisn' => 'required|string|size:10|unique:siswa,nisn',
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'nik' => 'nullable|string|size:16|unique:siswa,nik',
            'nama_lengkap' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'anak_ke' => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:50',
            'kecamatan' => 'nullable|string|max:50',
            'kota' => 'nullable|string|max:50',
            'provinsi' => 'nullable|string|max:50',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email_siswa' => 'nullable|email',
            'asal_sekolah' => 'nullable|string|max:100',
            'tahun_lulus_smp' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'berat_badan' => 'nullable|numeric|min:0|max:200',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_masuk' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // Create User
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'siswa',
                'is_active' => true,
            ]);

            // Handle foto upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto-siswa', 'public');
            }

            // Create Siswa
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'orang_tua_id' => $validated['orang_tua_id'],
                'nisn' => $validated['nisn'],
                'nis' => $validated['nis'],
                'nik' => $validated['nik'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'anak_ke' => $validated['anak_ke'],
                'jumlah_saudara' => $validated['jumlah_saudara'],
                'alamat' => $validated['alamat'],
                'rt' => $validated['rt'],
                'rw' => $validated['rw'],
                'kelurahan' => $validated['kelurahan'],
                'kecamatan' => $validated['kecamatan'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
                'kode_pos' => $validated['kode_pos'],
                'telepon' => $validated['telepon'],
                'email' => $validated['email_siswa'] ?? $validated['email'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'tahun_lulus_smp' => $validated['tahun_lulus_smp'],
                'tinggi_badan' => $validated['tinggi_badan'],
                'berat_badan' => $validated['berat_badan'],
                'golongan_darah' => $validated['golongan_darah'],
                'foto' => $fotoPath,
                'status' => 'aktif',
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? now(),
            ]);

            DB::commit();

            return $this->successResponse(
                'Siswa berhasil ditambahkan',
                'siswa.index',
                $siswa->load('user', 'orangTua')
            );
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }

            return $this->errorResponse('Gagal menambahkan siswa: ' . $e->getMessage(), 500);
        }
    }

    public function show(Siswa $siswa)
    {
        $siswa->load([
            'user',
            'orangTua',
            'kelasAktif',
            'siswaKelas.kelas.semester',
            'bukuInduk',
            'prestasi' => fn($q) => $q->latest()->limit(5),
            'pelanggaran' => fn($q) => $q->latest()->limit(5),
        ]);

        // Statistik
        $stats = [
            'total_prestasi' => $siswa->prestasi()->count(),
            'total_pelanggaran' => $siswa->pelanggaran()->count(),
            'total_nilai' => $siswa->nilai()->count(),
        ];

        return view('user-data.siswa.show', compact('siswa', 'stats'));
    }

    public function edit(Siswa $siswa)
    {
        $siswa->load('user', 'orangTua');
        $orangTua = OrangTua::all();

        if (request()->ajax()) {
            return view('user-data.siswa.form', [
                'siswa' => $siswa,
                'orangTua' => $orangTua,
                'action' => route('siswa.update', $siswa),
                'method' => 'PUT',
            ]);
        }

        return view('user-data.siswa.edit', compact('siswa', 'orangTua'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $siswa->user_id,
            'email' => 'required|email|unique:users,email,' . $siswa->user_id,
            'password' => 'nullable|string|min:6',
            'orang_tua_id' => 'nullable|exists:orang_tua,id',
            'nisn' => 'required|string|size:10|unique:siswa,nisn,' . $siswa->id,
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $siswa->id,
            'nik' => 'nullable|string|size:16|unique:siswa,nik,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'anak_ke' => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:50',
            'kecamatan' => 'nullable|string|max:50',
            'kota' => 'nullable|string|max:50',
            'provinsi' => 'nullable|string|max:50',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email_siswa' => 'nullable|email',
            'asal_sekolah' => 'nullable|string|max:100',
            'tahun_lulus_smp' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'berat_badan' => 'nullable|numeric|min:0|max:200',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:aktif,lulus,pindah,keluar,DO',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $userData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $siswa->user->update($userData);

            // Handle foto upload
            $fotoPath = $siswa->foto;
            if ($request->hasFile('foto')) {
                if ($siswa->foto) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $fotoPath = $request->file('foto')->store('foto-siswa', 'public');
            }

            // Update Siswa
            $siswa->update([
                'orang_tua_id' => $validated['orang_tua_id'],
                'nisn' => $validated['nisn'],
                'nis' => $validated['nis'],
                'nik' => $validated['nik'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'anak_ke' => $validated['anak_ke'],
                'jumlah_saudara' => $validated['jumlah_saudara'],
                'alamat' => $validated['alamat'],
                'rt' => $validated['rt'],
                'rw' => $validated['rw'],
                'kelurahan' => $validated['kelurahan'],
                'kecamatan' => $validated['kecamatan'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
                'kode_pos' => $validated['kode_pos'],
                'telepon' => $validated['telepon'],
                'email' => $validated['email_siswa'] ?? $validated['email'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'tahun_lulus_smp' => $validated['tahun_lulus_smp'],
                'tinggi_badan' => $validated['tinggi_badan'],
                'berat_badan' => $validated['berat_badan'],
                'golongan_darah' => $validated['golongan_darah'],
                'foto' => $fotoPath,
                'status' => $validated['status'] ?? $siswa->status,
            ]);

            DB::commit();

            return $this->successResponse(
                'Siswa berhasil diperbarui',
                'siswa.index',
                $siswa->load('user', 'orangTua')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal memperbarui siswa: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Siswa $siswa)
    {
        // Check jika masih ada di kelas aktif
        if ($siswa->kelasAktif()->exists()) {
            return $this->errorResponse('Siswa tidak dapat dihapus karena masih terdaftar di kelas', 400);
        }

        DB::beginTransaction();
        try {
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }

            $siswa->user->delete();

            DB::commit();

            return $this->successResponse('Siswa berhasil dihapus', 'siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menghapus siswa: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Assign siswa ke kelas (AJAX)
     */
    public function assignKelas(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_masuk' => 'nullable|date',
        ]);

        // Check kuota kelas
        $kelas = Kelas::find($validated['kelas_id']);
        $jumlahSiswa = $kelas->siswaKelas()->where('status', 'aktif')->count();

        if ($jumlahSiswa >= $kelas->kuota) {
            return $this->errorResponse('Kelas sudah penuh (kuota: ' . $kelas->kuota . ')', 400);
        }

        // Check jika sudah ada di kelas lain yang aktif
        $kelasAktif = $siswa->kelasAktif()->first();
        if ($kelasAktif && $kelasAktif->id != $validated['kelas_id']) {
            return $this->errorResponse('Siswa masih terdaftar di kelas ' . $kelasAktif->nama, 400);
        }

        DB::beginTransaction();
        try {
            // Jika sudah ada di kelas yang sama, skip
            $existing = SiswaKelas::where('siswa_id', $siswa->id)
                ->where('kelas_id', $validated['kelas_id'])
                ->where('status', 'aktif')
                ->first();

            if ($existing) {
                return $this->errorResponse('Siswa sudah terdaftar di kelas ini', 400);
            }

            // Create siswa_kelas
            SiswaKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $validated['kelas_id'],
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? now(),
                'status' => 'aktif',
            ]);

            DB::commit();

            return $this->jsonSuccess(
                ['kelas' => $kelas->load('semester', 'jurusan')],
                'Siswa berhasil ditugaskan ke kelas ' . $kelas->nama
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal assign kelas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove siswa dari kelas (AJAX)
     */
    public function removeKelas(Siswa $siswa, Kelas $kelas)
    {
        $siswaKelas = SiswaKelas::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->first();

        if (!$siswaKelas) {
            return $this->errorResponse('Siswa tidak terdaftar di kelas ini', 404);
        }

        $siswaKelas->update([
            'status' => 'pindah',
            'tanggal_keluar' => now(),
        ]);

        return $this->jsonSuccess(null, 'Siswa berhasil dikeluarkan dari kelas');
    }

    /**
     * Check NISN duplicate (AJAX validation)
     */
    public function checkNisn(Request $request)
    {
        $nisn = $request->get('nisn');
        $excludeId = $request->get('exclude_id');

        $exists = Siswa::where('nisn', $nisn)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'NISN sudah digunakan' : 'NISN tersedia',
        ]);
    }

    /**
     * Search siswa (AJAX autocomplete)
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $siswa = Siswa::where('nama_lengkap', 'like', "%{$term}%")
            ->orWhere('nisn', 'like', "%{$term}%")
            ->orWhere('nis', 'like', "%{$term}%")
            ->limit(10)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'text' => $s->nama_lengkap . " ({$s->nisn})",
                    'nisn' => $s->nisn,
                    'nis' => $s->nis,
                    'nama' => $s->nama_lengkap,
                ];
            });

        return response()->json($siswa);
    }
}
