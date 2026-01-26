<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\User;
use App\Traits\HybridResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class OrangTuaController extends Controller
{
    use HybridResponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTable();
        }

        return view('user-data.orang-tua.index');
    }

    public function dataTable()
    {
        $orangTua = OrangTua::with('user')
            ->withCount('siswa')
            ->select('orang_tua.*');

        return DataTables::of($orangTua)
            ->addIndexColumn()
            ->addColumn('nama_lengkap', function ($row) {
                return $row->nama_ayah ?? $row->nama_ibu ?? $row->nama_wali ?? '-';
            })
            ->addColumn('telepon', function ($row) {
                return $row->telepon_ayah ?? $row->telepon_ibu ?? $row->telepon_wali ?? '-';
            })
            ->addColumn('jumlah_anak', function ($row) {
                return $row->siswa_count;
            })
            ->addColumn('has_account', function ($row) {
                if ($row->user_id) {
                    return '<span class="badge bg-success">Ya</span>';
                }
                return '<span class="badge bg-secondary">Tidak</span>';
            })
            ->addColumn('action', function ($row) {
                $createAccount = '';
                if (!$row->user_id) {
                    $createAccount = '
                        <button type="button"
                                class="btn btn-sm btn-success btn-create-account"
                                data-id="' . $row->id . '"
                                title="Buat Akun">
                            <i class="bi bi-person-plus"></i>
                        </button>
                    ';
                }

                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('orang-tua.show', $row->id) . '"
                           class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button type="button"
                                class="btn btn-sm btn-warning btn-edit"
                                data-id="' . $row->id . '">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ' . $createAccount . '
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['has_account', 'action'])
            ->make(true);
    }

    public function create()
    {
        if (request()->ajax()) {
            return view('user-data.orang-tua.form', [
                'orangTua' => null,
                'action' => route('orang-tua.store'),
                'method' => 'POST',
            ]);
        }

        return view('user-data.orang-tua.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Data Ayah
            'nik_ayah' => 'nullable|string|size:16',
            'nama_ayah' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:50',
            'pendidikan_ayah' => 'nullable|string|max:50',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'telepon_ayah' => 'nullable|string|max:20',

            // Data Ibu
            'nik_ibu' => 'nullable|string|size:16',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:50',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'penghasilan_ibu' => 'nullable|string|max:50',
            'telepon_ibu' => 'nullable|string|max:20',

            // Data Wali (opsional)
            'nama_wali' => 'nullable|string|max:100',
            'pekerjaan_wali' => 'nullable|string|max:50',
            'telepon_wali' => 'nullable|string|max:20',

            // Alamat
            'alamat' => 'nullable|string',

            // User account (opsional)
            'create_account' => 'nullable|boolean',
            'username' => 'required_if:create_account,true|nullable|string|max:50|unique:users,username',
            'email' => 'required_if:create_account,true|nullable|email|unique:users,email',
            'password' => 'required_if:create_account,true|nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $userId = null;

            // Create User jika diminta
            if ($request->create_account) {
                $user = User::create([
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'orang_tua',
                    'is_active' => true,
                ]);
                $userId = $user->id;
            }

            // Create Orang Tua
            $orangTua = OrangTua::create([
                'user_id' => $userId,
                'nik_ayah' => $validated['nik_ayah'],
                'nama_ayah' => $validated['nama_ayah'],
                'pekerjaan_ayah' => $validated['pekerjaan_ayah'],
                'pendidikan_ayah' => $validated['pendidikan_ayah'],
                'penghasilan_ayah' => $validated['penghasilan_ayah'],
                'telepon_ayah' => $validated['telepon_ayah'],
                'nik_ibu' => $validated['nik_ibu'],
                'nama_ibu' => $validated['nama_ibu'],
                'pekerjaan_ibu' => $validated['pekerjaan_ibu'],
                'pendidikan_ibu' => $validated['pendidikan_ibu'],
                'penghasilan_ibu' => $validated['penghasilan_ibu'],
                'telepon_ibu' => $validated['telepon_ibu'],
                'nama_wali' => $validated['nama_wali'],
                'pekerjaan_wali' => $validated['pekerjaan_wali'],
                'telepon_wali' => $validated['telepon_wali'],
                'alamat' => $validated['alamat'],
            ]);

            DB::commit();

            return $this->successResponse(
                'Data orang tua berhasil ditambahkan',
                'orang-tua.index',
                $orangTua->load('user')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menambahkan data: ' . $e->getMessage(), 500);
        }
    }

    public function show(OrangTua $orangTua)
    {
        $orangTua->load(['user', 'siswa.kelasAktif']);

        return view('user-data.orang-tua.show', compact('orangTua'));
    }

    public function edit(OrangTua $orangTua)
    {
        $orangTua->load('user');

        if (request()->ajax()) {
            return view('user-data.orang-tua.form', [
                'orangTua' => $orangTua,
                'action' => route('orang-tua.update', $orangTua),
                'method' => 'PUT',
            ]);
        }

        return view('user-data.orang-tua.edit', compact('orangTua'));
    }

    public function update(Request $request, OrangTua $orangTua)
    {
        $validated = $request->validate([
            // Data Ayah
            'nik_ayah' => 'nullable|string|size:16',
            'nama_ayah' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:50',
            'pendidikan_ayah' => 'nullable|string|max:50',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'telepon_ayah' => 'nullable|string|max:20',

            // Data Ibu
            'nik_ibu' => 'nullable|string|size:16',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:50',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'penghasilan_ibu' => 'nullable|string|max:50',
            'telepon_ibu' => 'nullable|string|max:20',

            // Data Wali
            'nama_wali' => 'nullable|string|max:100',
            'pekerjaan_wali' => 'nullable|string|max:50',
            'telepon_wali' => 'nullable|string|max:20',

            // Alamat
            'alamat' => 'nullable|string',

            // Update User (jika ada)
            'username' => 'nullable|string|max:50|unique:users,username,' . ($orangTua->user_id ?? 'NULL'),
            'email' => 'nullable|email|unique:users,email,' . ($orangTua->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            // Update User jika ada
            if ($orangTua->user) {
                $userData = [];

                if ($request->filled('username')) {
                    $userData['username'] = $validated['username'];
                }
                if ($request->filled('email')) {
                    $userData['email'] = $validated['email'];
                }
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                if (!empty($userData)) {
                    $orangTua->user->update($userData);
                }
            }

            // Update Orang Tua
            $orangTua->update([
                'nik_ayah' => $validated['nik_ayah'],
                'nama_ayah' => $validated['nama_ayah'],
                'pekerjaan_ayah' => $validated['pekerjaan_ayah'],
                'pendidikan_ayah' => $validated['pendidikan_ayah'],
                'penghasilan_ayah' => $validated['penghasilan_ayah'],
                'telepon_ayah' => $validated['telepon_ayah'],
                'nik_ibu' => $validated['nik_ibu'],
                'nama_ibu' => $validated['nama_ibu'],
                'pekerjaan_ibu' => $validated['pekerjaan_ibu'],
                'pendidikan_ibu' => $validated['pendidikan_ibu'],
                'penghasilan_ibu' => $validated['penghasilan_ibu'],
                'telepon_ibu' => $validated['telepon_ibu'],
                'nama_wali' => $validated['nama_wali'],
                'pekerjaan_wali' => $validated['pekerjaan_wali'],
                'telepon_wali' => $validated['telepon_wali'],
                'alamat' => $validated['alamat'],
            ]);

            DB::commit();

            return $this->successResponse(
                'Data orang tua berhasil diperbarui',
                'orang-tua.index',
                $orangTua->load('user')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal memperbarui data: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(OrangTua $orangTua)
    {
        // Check jika masih punya anak
        if ($orangTua->siswa()->exists()) {
            return $this->errorResponse('Orang tua tidak dapat dihapus karena masih memiliki anak terdaftar', 400);
        }

        DB::beginTransaction();
        try {
            // Delete user jika ada
            if ($orangTua->user) {
                $orangTua->user->delete();
            }

            // OrangTua akan terhapus otomatis karena cascade

            DB::commit();

            return $this->successResponse('Data orang tua berhasil dihapus', 'orang-tua.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create user account untuk orang tua (AJAX)
     */
    public function createAccount(Request $request, OrangTua $orangTua)
    {
        if ($orangTua->user_id) {
            return $this->errorResponse('Orang tua sudah memiliki akun', 400);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'orang_tua',
                'is_active' => true,
            ]);

            $orangTua->update(['user_id' => $user->id]);

            DB::commit();

            return $this->jsonSuccess(
                ['user' => $user],
                'Akun berhasil dibuat'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal membuat akun: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Search orang tua (AJAX autocomplete)
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $orangTua = OrangTua::where(function ($q) use ($term) {
            $q->where('nama_ayah', 'like', "%{$term}%")
                ->orWhere('nama_ibu', 'like', "%{$term}%")
                ->orWhere('nama_wali', 'like', "%{$term}%")
                ->orWhere('telepon_ayah', 'like', "%{$term}%")
                ->orWhere('telepon_ibu', 'like', "%{$term}%");
        })
            ->limit(10)
            ->get()
            ->map(function ($ot) {
                $nama = $ot->nama_ayah ?? $ot->nama_ibu ?? $ot->nama_wali;
                $telepon = $ot->telepon_ayah ?? $ot->telepon_ibu ?? $ot->telepon_wali;

                return [
                    'id' => $ot->id,
                    'text' => $nama . ($telepon ? " ({$telepon})" : ''),
                    'nama' => $nama,
                    'telepon' => $telepon,
                ];
            });

        return response()->json($orangTua);
    }
}
