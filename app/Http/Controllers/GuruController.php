<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use App\Traits\HybridResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GuruController extends Controller
{
    use HybridResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Jika request DataTables (AJAX)
        if ($request->ajax()) {
            return $this->dataTable();
        }

        // Return view biasa
        return view('user-data.guru.index');
    }

    /**
     * DataTables AJAX
     */
    public function dataTable()
    {
        $guru = Guru::with('user')
            ->select('guru.*');

        return DataTables::of($guru)
            ->addIndexColumn()
            ->addColumn('nama_lengkap_gelar', function ($row) {
                return $row->nama_lengkap_gelar;
            })
            ->addColumn('status', function ($row) {
                $badge = $row->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-secondary">Nonaktif</span>';
                return $badge;
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="btn-group" role="group">
                        <a href="' . route('guru.show', $row->id) . '"
                           class="btn btn-sm btn-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button type="button"
                                class="btn btn-sm btn-warning btn-edit"
                                data-id="' . $row->id . '"
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-id="' . $row->id . '"
                                data-name="' . $row->nama_lengkap . '"
                                title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
                return $actions;
            })
            ->addColumn('toggle_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-active"
                               type="checkbox"
                               data-id="' . $row->id . '"
                               ' . $checked . '>
                    </div>
                ';
            })
            ->rawColumns(['status', 'action', 'toggle_active'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (request()->ajax()) {
            return view('user-data.guru.form', [
                'guru' => null,
                'action' => route('guru.store'),
                'method' => 'POST',
            ]);
        }

        return view('user-data.guru.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nip' => 'nullable|string|max:30|unique:guru,nip',
            'nuptk' => 'nullable|string|max:20|unique:guru,nuptk',
            'nama_lengkap' => 'required|string|max:100',
            'gelar_depan' => 'nullable|string|max:20',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email_guru' => 'nullable|email',
            'status_kepegawaian' => 'nullable|in:PNS,PPPK,GTY,GTT,Honorer',
            'tanggal_masuk' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Create User
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'guru',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Handle foto upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto-guru', 'public');
            }

            // Create Guru
            $guru = Guru::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'nuptk' => $validated['nuptk'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'gelar_depan' => $validated['gelar_depan'],
                'gelar_belakang' => $validated['gelar_belakang'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'alamat' => $validated['alamat'],
                'telepon' => $validated['telepon'],
                'email' => $validated['email_guru'] ?? $validated['email'],
                'status_kepegawaian' => $validated['status_kepegawaian'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'foto' => $fotoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return $this->successResponse(
                'Guru berhasil ditambahkan',
                'guru.index',
                $guru->load('user')
            );
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus foto jika ada
            if (isset($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }

            return $this->errorResponse('Gagal menambahkan guru: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Guru $guru)
    {
        $guru->load([
            'user',
            'kelasWali.semester.tahunAkademik',
            'mataPelajaranKelas.mataPelajaran',
            'mataPelajaranKelas.kelas',
        ]);

        // Statistik
        $stats = [
            'total_wali_kelas' => $guru->kelasWali()->count(),
            'total_mengajar' => $guru->mataPelajaranKelas()->count(),
            'total_bank_soal' => $guru->bankSoal()->count(),
        ];

        return view('user-data.guru.show', compact('guru', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guru $guru)
    {
        $guru->load('user');

        if (request()->ajax()) {
            return view('user-data.guru.form', [
                'guru' => $guru,
                'action' => route('guru.update', $guru),
                'method' => 'PUT',
            ]);
        }

        return view('user-data.guru.edit', compact('guru'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $guru->user_id,
            'email' => 'required|email|unique:users,email,' . $guru->user_id,
            'password' => 'nullable|string|min:6',
            'nip' => 'nullable|string|max:30|unique:guru,nip,' . $guru->id,
            'nuptk' => 'nullable|string|max:20|unique:guru,nuptk,' . $guru->id,
            'nama_lengkap' => 'required|string|max:100',
            'gelar_depan' => 'nullable|string|max:20',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email_guru' => 'nullable|email',
            'status_kepegawaian' => 'nullable|in:PNS,PPPK,GTY,GTT,Honorer',
            'tanggal_masuk' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $userData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'is_active' => $validated['is_active'] ?? true,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $guru->user->update($userData);

            // Handle foto upload
            $fotoPath = $guru->foto;
            if ($request->hasFile('foto')) {
                // Hapus foto lama
                if ($guru->foto) {
                    Storage::disk('public')->delete($guru->foto);
                }
                $fotoPath = $request->file('foto')->store('foto-guru', 'public');
            }

            // Update Guru
            $guru->update([
                'nip' => $validated['nip'],
                'nuptk' => $validated['nuptk'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'gelar_depan' => $validated['gelar_depan'],
                'gelar_belakang' => $validated['gelar_belakang'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'alamat' => $validated['alamat'],
                'telepon' => $validated['telepon'],
                'email' => $validated['email_guru'] ?? $validated['email'],
                'status_kepegawaian' => $validated['status_kepegawaian'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'foto' => $fotoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return $this->successResponse(
                'Guru berhasil diperbarui',
                'guru.index',
                $guru->load('user')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal memperbarui guru: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        // Check jika masih jadi wali kelas
        if ($guru->kelasWali()->exists()) {
            return $this->errorResponse('Guru tidak dapat dihapus karena masih menjadi wali kelas', 400);
        }

        // Check jika masih mengajar
        if ($guru->mataPelajaranKelas()->exists()) {
            return $this->errorResponse('Guru tidak dapat dihapus because masih mengajar', 400);
        }

        DB::beginTransaction();
        try {
            // Hapus foto
            if ($guru->foto) {
                Storage::disk('public')->delete($guru->foto);
            }

            // Hapus user
            $guru->user->delete();

            // Guru akan terhapus otomatis karena onDelete cascade di FK

            DB::commit();

            return $this->successResponse('Guru berhasil dihapus', 'guru.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menghapus guru: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Toggle active status (AJAX)
     */
    public function toggleActive(Guru $guru)
    {
        $guru->update(['is_active' => !$guru->is_active]);
        $guru->user->update(['is_active' => !$guru->user->is_active]);

        $status = $guru->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return $this->jsonSuccess(
            ['is_active' => $guru->is_active],
            "Guru berhasil {$status}"
        );
    }

    /**
     * Search guru (AJAX autocomplete)
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $guru = Guru::where('nama_lengkap', 'like', "%{$term}%")
            ->orWhere('nip', 'like', "%{$term}%")
            ->limit(10)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'text' => $g->nama_lengkap_gelar . ($g->nip ? " ({$g->nip})" : ''),
                    'nip' => $g->nip,
                    'nama' => $g->nama_lengkap,
                ];
            });

        return response()->json($guru);
    }

    /**
     * Get guru by ID (AJAX)
     */
    public function getById(Guru $guru)
    {
        $guru->load('user');
        return $this->jsonSuccess($guru);
    }

    /**
     * Export to Excel
     */
    public function export()
    {
        // TODO: Implement Excel export
        // return Excel::download(new GuruExport, 'guru.xlsx');
    }

    /**
     * Import from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        // TODO: Implement Excel import
        // Excel::import(new GuruImport, $request->file('file'));

        return $this->successResponse('Data guru berhasil diimport', 'guru.index');
    }
}
