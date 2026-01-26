<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Jurusan;
use App\Models\Guru;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Kelas::with(['semester.tahunAkademik', 'jurusan', 'waliKelas'])
                ->withCount(['siswaKelas as jumlah_siswa' => function ($q) {
                    $q->where('status', 'aktif');
                }]);

            // Filter by semester
            if ($request->filled('semester_id')) {
                $query->where('semester_id', $request->semester_id);
            } else {
                // Default: semua semester dari tahun akademik aktif
                $tahunAkademikAktif = TahunAkademik::active()->first();
                if ($tahunAkademikAktif) {
                    $query->whereHas('semester', function ($q) use ($tahunAkademikAktif) {
                        $q->where('tahun_akademik_id', $tahunAkademikAktif->id);
                    });
                }
            }

            // Filter by tingkat
            if ($request->filled('tingkat')) {
                $query->where('tingkat', $request->tingkat);
            }

            // Filter by jurusan
            if ($request->filled('jurusan_id')) {
                $query->where('jurusan_id', $request->jurusan_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('semester_info', function ($row) {
                    return $row->semester->nama . '<br><small class="text-muted">' . ($row->semester->tahunAkademik->nama ?? '') . '</small>';
                })
                ->addColumn('jurusan_nama', function ($row) {
                    return $row->jurusan->nama ?? '-';
                })
                ->addColumn('wali_kelas_nama', function ($row) {
                    return $row->waliKelas->nama_lengkap ?? '<span class="badge badge-warning">Belum Ada</span>';
                })
                ->addColumn('kuota_info', function ($row) {
                    $persentase = $row->kuota > 0 ? ($row->jumlah_siswa / $row->kuota) * 100 : 0;
                    $class = $persentase >= 90 ? 'danger' : ($persentase >= 70 ? 'warning' : 'success');
                    return '<div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-' . $class . '" role="progressbar" style="width: ' . $persentase . '%" aria-valuenow="' . $persentase . '" aria-valuemin="0" aria-valuemax="100">
                            ' . $row->jumlah_siswa . '/' . $row->kuota . '
                        </div>
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('kelas.show', $row->id) . '" class="btn btn-info btn-sm" title="Detail"><i class="simple-icon-eye"></i></a>';
                    $btn .= '<a href="' . route('kelas.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="simple-icon-pencil"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="simple-icon-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['semester_info', 'wali_kelas_nama', 'kuota_info', 'action'])
                ->make(true);
        }

        // Data untuk filter
        $semesterAktif = TahunAkademik::active()->first()->semester()->orderBy('tanggal_mulai', 'desc')->first()->id;
        $semester = Semester::orderBy('tanggal_mulai', 'desc')->get();
        $jurusan = Jurusan::active()->get();

        return view('master-data.kelas.index', compact('semester', 'jurusan'));
    }

    public function create()
    {
        $semester = Semester::orderBy('tanggal_mulai', 'desc')->get();
        $jurusan = Jurusan::active()->get();
        $guru = Guru::active()->orderBy('nama_lengkap')->get();

        return view('master-data.kelas.create', compact('semester', 'jurusan', 'guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:20|unique:kelas,kode',
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'kuota' => 'required|integer|min:1|max:50',
            'ruang_kelas' => 'nullable|string|max:50',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load([
            'semester.tahunAkademik',
            'jurusan',
            'waliKelas',
            'siswaKelas.siswa',
            'mataPelajaranKelas.mataPelajaran'
        ]);

        // Statistik
        $stats = [
            'total_siswa' => $kelas->siswaKelas()->where('status', 'aktif')->count(),
            'total_laki' => $kelas->siswaKelas()
                ->where('status', 'aktif')
                ->whereHas('siswa', fn($q) => $q->where('jenis_kelamin', 'L'))
                ->count(),
            'total_perempuan' => $kelas->siswaKelas()
                ->where('status', 'aktif')
                ->whereHas('siswa', fn($q) => $q->where('jenis_kelamin', 'P'))
                ->count(),
            'total_mapel' => $kelas->mataPelajaranKelas()->count(),
            'sisa_kuota' => $kelas->kuota - $kelas->siswaKelas()->where('status', 'aktif')->count(),
        ];

        return view('master-data.kelas.show', compact('kelas', 'stats'));
    }

    public function edit(Kelas $kelas)
    {
        $semester = Semester::orderBy('tanggal_mulai', 'desc')->get();
        $jurusan = Jurusan::active()->get();
        $guru = Guru::active()->orderBy('nama_lengkap')->get();

        return view('master-data.kelas.edit', compact('kelas', 'semester', 'jurusan', 'guru'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama' => 'required|string|max:50',
            'kode' => 'required|string|max:20|unique:kelas,kode,' . $kelas->id,
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'kuota' => 'required|integer|min:1|max:50',
            'ruang_kelas' => 'nullable|string|max:50',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        // Check jika masih ada siswa
        if ($kelas->siswaKelas()->where('status', 'aktif')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak dapat dihapus karena masih ada siswa aktif'
            ], 400);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus'
        ]);
    }

    /**
     * Assign wali kelas
     */
    public function assignWaliKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'required|exists:guru,id',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.show', $kelas)
            ->with('success', 'Wali kelas berhasil ditugaskan');
    }

    /**
     * Remove wali kelas
     */
    public function removeWaliKelas(Kelas $kelas)
    {
        $kelas->update(['wali_kelas_id' => null]);

        return redirect()->route('kelas.show', $kelas)
            ->with('success', 'Wali kelas berhasil dihapus');
    }

    /**
     * Get siswa in kelas (AJAX)
     */
    public function getSiswa(Kelas $kelas)
    {
        $siswa = $kelas->siswaKelas()
            ->with('siswa')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($sk) {
                return [
                    'id' => $sk->siswa->id,
                    'nisn' => $sk->siswa->nisn,
                    'nis' => $sk->siswa->nis,
                    'nama' => $sk->siswa->nama_lengkap,
                    'jenis_kelamin' => $sk->siswa->jenis_kelamin,
                    'tanggal_masuk' => $sk->tanggal_masuk->format('d/m/Y'),
                ];
            });

        return response()->json($siswa);
    }

    /**
     * Bulk create kelas untuk semester baru
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required|in:X,XI,XII',
            'jumlah_kelas' => 'required|integer|min:1|max:10',
            'kuota' => 'required|integer|min:1|max:50',
        ]);

        $semester = Semester::find($validated['semester_id']);
        $jurusan = Jurusan::find($validated['jurusan_id']);

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $validated['jumlah_kelas']; $i++) {
                $nama = "{$validated['tingkat']} {$jurusan->singkatan} {$i}";
                $kode = strtoupper("{$validated['tingkat']}-{$jurusan->kode}-{$i}-{$semester->kode}");

                Kelas::create([
                    'semester_id' => $validated['semester_id'],
                    'jurusan_id' => $validated['jurusan_id'],
                    'tingkat' => $validated['tingkat'],
                    'nama' => $nama,
                    'kode' => $kode,
                    'kuota' => $validated['kuota'],
                ]);
            }

            DB::commit();

            return redirect()->route('kelas.index')
                ->with('success', "{$validated['jumlah_kelas']} kelas berhasil ditambahkan");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Copy kelas dari semester sebelumnya
     */
    public function copyFromPrevious(Request $request)
    {
        $validated = $request->validate([
            'semester_tujuan_id' => 'required|exists:semester,id',
            'semester_asal_id' => 'required|exists:semester,id',
            'tingkat_asal' => 'required|in:X,XI,XII',
            'tingkat_tujuan' => 'required|in:X,XI,XII',
        ]);

        $semesterTujuan = Semester::find($validated['semester_tujuan_id']);
        $kelasAsal = Kelas::where('semester_id', $validated['semester_asal_id'])
            ->where('tingkat', $validated['tingkat_asal'])
            ->get();

        if ($kelasAsal->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada kelas di semester asal');
        }

        DB::beginTransaction();
        try {
            foreach ($kelasAsal as $kelas) {
                // Generate nama & kode baru
                $nama = str_replace($validated['tingkat_asal'], $validated['tingkat_tujuan'], $kelas->nama);
                $kode = str_replace($validated['tingkat_asal'], $validated['tingkat_tujuan'], $kelas->kode);
                $kode = str_replace($kelas->semester->kode, $semesterTujuan->kode, $kode);

                Kelas::create([
                    'semester_id' => $validated['semester_tujuan_id'],
                    'jurusan_id' => $kelas->jurusan_id,
                    'tingkat' => $validated['tingkat_tujuan'],
                    'nama' => $nama,
                    'kode' => $kode,
                    'wali_kelas_id' => $kelas->wali_kelas_id,
                    'kuota' => $kelas->kuota,
                    'ruang_kelas' => $kelas->ruang_kelas,
                ]);
            }

            DB::commit();

            return redirect()->route('kelas.index')
                ->with('success', count($kelasAsal) . ' kelas berhasil dicopy');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal copy kelas: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal copy kelas: ' . $e->getMessage());
        }
    }
    public function duplicateClassesFromPreviousYear(Request $request)
    {
        $semesterTujuan = Semester::findOrFail($request->semester_tujuan_id);
        $semesterSumber = Semester::findOrFail($request->semester_sumber_id);

        $kelasSumber = Kelas::where('semester_id', $semesterSumber->id)->get();

        foreach ($kelasSumber as $kelas) {
            Kelas::create([
                'semester_id' => $semesterTujuan->id,
                'jurusan_id' => $kelas->jurusan_id,
                'tingkat' => $kelas->tingkat,
                'nama' => $kelas->nama,
                'kode' => $this->generateKodeKelas($semesterTujuan, $kelas),
                'wali_kelas_id' => null, // Reset wali kelas, assign manual nanti
                'kuota' => $kelas->kuota,
                'ruang_kelas' => $kelas->ruang_kelas,
            ]);
        }

        return response()->json(['message' => 'Kelas berhasil diduplikasi']);
    }

    private function generateKodeKelas($semester, $kelasLama)
    {
        // Contoh: X-RPL-1-20252 (tingkat-jurusan-nomor-tahun+semester)
        // $tahunKode = substr($semester->tahunAkademik->kode, 0, 4); // 2025
        $tahunKode = $semester->tahunAkademik->kode; // 2025
        $semesterKode = $semester->nama == 'Ganjil' ? '1' : '2';

        return $kelasLama->tingkat . '-' .
            $kelasLama->jurusan->kode . '-' .
            $kelasLama->nama . '-' .
            $tahunKode . $semesterKode;
    }
}
