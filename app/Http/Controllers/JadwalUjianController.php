<?php

namespace App\Http\Controllers;

use App\Models\JadwalUjian;
use App\Models\Kelas;
use App\Models\BankSoal;
use App\Models\Semester;
use App\Models\Soal;
use App\Models\SoalUjian;
use App\Models\KomponenNilai;
use App\Models\UjianSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class JadwalUjianController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JadwalUjian::with(['mataPelajaranKelas.kelas', 'mataPelajaranKelas.mataPelajaran', 'mataPelajaranKelas.guru','bankSoal'])->where('semester_id', Semester::active()->first()->id);

            if(auth()->user()->hasRole('guru')){
                $query->whereHas('mataPelajaranKelas', function($query){
                    $query->where('guru_id', auth()->user()->guru->id);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kelas_mapel', function ($row) {
                    $kelas = $row->mataPelajaranKelas->kelas->nama ?? '-';
                    $mapel = $row->mataPelajaranKelas->mataPelajaran->nama ?? '-';
                    return $kelas . ' - ' . $mapel;
                })
                ->addColumn('waktu', function ($row) {
                    return $row->tanggal_mulai->format('d/m/Y H:i') . '<br>s/d<br>' . $row->tanggal_selesai->format('d/m/Y H:i');
                })
                ->addColumn('status', function ($row) {
                    if (!$row->bank_soal_id) {
                        return '<span class="badge badge-warning">Belum Ada Soal</span>';
                    }
                    $badges = [
                        'draft' => 'secondary',
                        'aktif' => 'success',
                        'selesai' => 'dark'
                    ];
                    return '<span class="badge badge-'.$badges[$row->status].'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('jadwal-ujian.show', $row->id) . '" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>';
                    if($row->status == 'aktif') {
                        $btn .= '<a href="' . route('jadwal-ujian.monitor', $row->id) . '" class="btn btn-dark btn-sm" title="Monitor"><i class="fas fa-desktop"></i></a>';
                    }
                    if($row->status == 'draft') {
                        $btn .= '<a href="' . route('jadwal-ujian.manage-soal', $row->id) . '" class="btn btn-primary btn-sm" title="Kelola Soal"><i class="fas fa-tasks"></i></a>';
                        $btn .= '<a href="' . route('jadwal-ujian.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                        $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['waktu', 'status', 'action'])
                ->make(true);
        }

        return view('pembelajaran.cbt.jadwal-ujian.index');
    }

    public function create()
    {
        $semester = Semester::active()->first();
        // Get Classes that have subjects
        // Group by Class for easier selection? Or just list all MPK?
        // Let's iterate Classes -> Subjects
        $kelas = Kelas::where('semester_id', $semester->id)
            ->with(['mataPelajaranKelas.mataPelajaran'])
            ->orderBy('nama')
            ->get();

        // Bank Soal loaded via AJAX based on selected Subject usually, but for simple MVP let's load all active
        $bankSoal = BankSoal::active()->with('mataPelajaran')->get();

        $components = KomponenNilai::where('kurikulum_id', $semester->tahunAkademik->kurikulum_id ?? 0)->get();

        return view('pembelajaran.cbt.jadwal-ujian.create', compact('kelas', 'bankSoal', 'components'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_kelas_id' => 'required|array',
            'mata_pelajaran_kelas_id.*' => 'exists:mata_pelajaran_kelas,id',
            'bank_soal_id' => 'nullable|exists:bank_soal,id',
            'komponen_nilai_id' => 'nullable|exists:komponen_nilai,id',
            'jenis_ujian' => 'required|in:ulangan_harian,uts,uas,ujian_praktik,ujian_sekolah',
            'nama_ujian' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi' => 'required|integer|min:1',
            'jumlah_soal' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $baseData = [
            'semester_id' => Semester::active()->first()->id,
            'status' => 'draft',
            'bank_soal_id' => $validated['bank_soal_id'] ?? null,
            'komponen_nilai_id' => $validated['komponen_nilai_id'] ?? null,
            'jenis_ujian' => $validated['jenis_ujian'],
            'nama_ujian' => $validated['nama_ujian'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'durasi' => $validated['durasi'],
            'jumlah_soal' => $validated['jumlah_soal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'acak_soal' => $request->has('acak_soal'),
            'acak_opsi' => $request->has('acak_opsi'),
            'tampilkan_nilai' => $request->has('tampilkan_nilai'),
        ];

        DB::beginTransaction();
        try {
            if ($validated['bank_soal_id']) {
                $bank = BankSoal::withCount('soal')->findOrFail($validated['bank_soal_id']);
                if ($bank->soal_count < (int) $validated['jumlah_soal']) {
                    return back()->with('error', "Bank soal hanya memiliki {$bank->soal_count} soal.")->withInput();
                }
            }

            // Loop through each selected class
            foreach ($validated['mata_pelajaran_kelas_id'] as $mpkId) {
                $data = $baseData;
                $data['mata_pelajaran_kelas_id'] = $mpkId;
                $data['token'] = strtoupper(Str::random(6));

                $jadwal = JadwalUjian::create($data);

                // Generate Questions only if bank_soal_id is provided
                if ($validated['bank_soal_id']) {
                    $soals = Soal::where('bank_soal_id', $validated['bank_soal_id'])
                        ->inRandomOrder()
                        ->take($validated['jumlah_soal'])
                        ->get();

                    foreach ($soals as $index => $soal) {
                        SoalUjian::create([
                            'jadwal_ujian_id' => $jadwal->id,
                            'soal_id' => $soal->id,
                            'urutan' => $index + 1,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil dibuat untuk semua kelas terpilih.');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(JadwalUjian $jadwalUjian)
    {
        $jadwalUjian->load(['mataPelajaranKelas.kelas', 'mataPelajaranKelas.mataPelajaran', 'bankSoal', 'soalUjian.soal']);
        return view('pembelajaran.cbt.jadwal-ujian.show', compact('jadwalUjian'));
    }

    public function edit(JadwalUjian $jadwalUjian)
    {
        if($jadwalUjian->status != 'draft') {
            return back()->with('error', 'Hanya jadwal berstatus Draft yang dapat diedit.');
        }

        $semester = Semester::active()->first();
        $kelas = Kelas::where('semester_id', $semester->id)
            ->with(['mataPelajaranKelas.mataPelajaran'])
            ->orderBy('nama')
            ->get();

        $bankSoal = BankSoal::active()->with('mataPelajaran')->get();

        $components = KomponenNilai::where('kurikulum_id', $semester->tahunAkademik->kurikulum_id ?? 0)->get();

        return view('pembelajaran.cbt.jadwal-ujian.edit', compact('jadwalUjian', 'kelas', 'bankSoal', 'components'));
    }

    public function update(Request $request, JadwalUjian $jadwalUjian)
    {
        if($jadwalUjian->status != 'draft') {
            return back()->with('error', 'Hanya jadwal berstatus Draft yang dapat diedit.');
        }

        $validated = $request->validate([
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
            'bank_soal_id' => 'nullable|exists:bank_soal,id',
            'komponen_nilai_id' => 'nullable|exists:komponen_nilai,id',
            'jenis_ujian' => 'required|in:ulangan_harian,uts,uas,ujian_praktik,ujian_sekolah',
            'nama_ujian' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi' => 'required|integer|min:1',
            'jumlah_soal' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // If Bank or Count changed, regenerate questions
            $regenerate = false;

            if ($jadwalUjian->bank_soal_id != $validated['bank_soal_id'] || $jadwalUjian->jumlah_soal != $validated['jumlah_soal']) {
                $regenerate = true;
            }

            // Update Basic Info
            $jadwalUjian->update([
                'mata_pelajaran_kelas_id' => $validated['mata_pelajaran_kelas_id'],
                'bank_soal_id' => $validated['bank_soal_id'],
                'komponen_nilai_id' => $validated['komponen_nilai_id'] ?? null,
                'jenis_ujian' => $validated['jenis_ujian'],
                'nama_ujian' => $validated['nama_ujian'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'durasi' => $validated['durasi'],
                'jumlah_soal' => $validated['jumlah_soal'],
                'keterangan' => $validated['keterangan'] ?? null,
                'acak_soal' => $request->has('acak_soal'),
                'acak_opsi' => $request->has('acak_opsi'),
                'tampilkan_nilai' => $request->has('tampilkan_nilai'),
            ]);

            if ($regenerate || $request->has('regenerate_soal')) {
                // Check bank
                $bank = BankSoal::withCount('soal')->findOrFail($validated['bank_soal_id']);
                if ($bank->soal_count < $validated['jumlah_soal']) {
                     throw new \Exception("Bank soal hanya memiliki {$bank->soal_count} soal.");
                }

                // Delete old
                SoalUjian::where('jadwal_ujian_id', $jadwalUjian->id)->delete();

                // Create new
                $soals = Soal::where('bank_soal_id', $bank->id)
                    ->inRandomOrder()
                    ->take($validated['jumlah_soal'])
                    ->get();

                foreach ($soals as $index => $soal) {
                    SoalUjian::create([
                        'jadwal_ujian_id' => $jadwalUjian->id,
                        'soal_id' => $soal->id,
                        'urutan' => $index + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(JadwalUjian $jadwalUjian)
    {
        if ($jadwalUjian->status != 'draft') {
            return response()->json(['message' => 'Hanya status Draft yang bisa dihapus'], 422);
        }
        $jadwalUjian->delete();
        return response()->json(['message' => 'Jadwal dihapus']);
    }


    public function monitor($id)
    {
        $jadwalUjian = JadwalUjian::with([
            'mataPelajaranKelas.kelas.siswa',
            'ujianSiswa' => function ($q) {
                $q->with('siswa');
            }
        ])->findOrFail($id);

        // Match students with their exam status
        $students = $jadwalUjian->mataPelajaranKelas->kelas->siswa;
        $exams = $jadwalUjian->ujianSiswa->keyBy('siswa_id');

        $data = $students->map(function ($s) use ($exams) {
            $exam = $exams->get($s->id);
            return [
                'id' => $s->id,
                'ujian_siswa_id' => $exam ? $exam->id : null,
                'nama' => $s->nama_lengkap,
                'nis' => $s->nis,
                'status' => $exam ? $exam->status : 'belum_mulai',
                'waktu_mulai' => $exam && $exam->waktu_mulai ? $exam->waktu_mulai->format('H:i:s') : '-',
                'waktu_submit' => $exam && $exam->waktu_submit ? $exam->waktu_submit->format('H:i:s') : '-',
                'nilai' => $exam ? $exam->nilai : '-',
                'pelanggaran' => $exam ? $exam->violation_count : 0,
                'last_seen' => $exam ? $exam->updated_at->diffForHumans() : '-',
                'is_blocked' => $exam ? (bool)$exam->is_blocked : false,
            ];
        });

        if (request()->ajax()) {
            return response()->json([
                'data' => $data,
                'stats' => [
                    'total' => $students->count(),
                    'mengerjakan' => $exams->where('status', 'sedang_mengerjakan')->count(),
                    'selesai' => $exams->where('status', 'selesai')->count(),
                    'belum' => $students->count() - $exams->count(),
                ]
            ]);
        }

        return view('pembelajaran.cbt.jadwal-ujian.monitor', compact('jadwalUjian', 'data'));
    }

    // --- Advanced Question Management ---

    public function manageSoal(JadwalUjian $jadwalUjian)
    {
        $jadwalUjian->load(['soalUjian.soal', 'bankSoal', 'mataPelajaranKelas']);

        $bankSoals = collect([]);
        if (!$jadwalUjian->bank_soal_id) {
            $query = BankSoal::active()->with('mataPelajaran');

            // Filter by Mata Pelajaran
            if ($jadwalUjian->mataPelajaranKelas) {
                $query->where('mata_pelajaran_id', $jadwalUjian->mataPelajaranKelas->mata_pelajaran_id);
            }

            // Filter by Guru (Pembuat)
            if (auth()->user()->hasRole('guru')) {
                $query->where('pembuat_id', auth()->user()->guru->id ?? 0);
            }

            $bankSoals = $query->get();
        }

        return view('pembelajaran.cbt.jadwal-ujian.manage-soal', compact('jadwalUjian', 'bankSoals'));
    }

    public function linkBankSoal(Request $request, JadwalUjian $jadwalUjian)
    {
        $request->validate([
            'bank_soal_id' => 'required|exists:bank_soal,id',
            'generate_auto' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            $jadwalUjian->update(['bank_soal_id' => $request->bank_soal_id]);

            if ($request->generate_auto) {
                $bank = BankSoal::withCount('soal')->findOrFail($request->bank_soal_id);
                $count = min($bank->soal_count, $jadwalUjian->jumlah_soal);

                $soals = Soal::where('bank_soal_id', $bank->id)
                    ->inRandomOrder()
                    ->take($count)
                    ->get();

                foreach ($soals as $index => $soal) {
                    SoalUjian::create([
                        'jadwal_ujian_id' => $jadwalUjian->id,
                        'soal_id' => $soal->id,
                        'urutan' => $index + 1,
                    ]);
                }

                // If count was lower than expected, update the schedule count?
                // For now, just generate what's available.
            }

            DB::commit();
            return back()->with('success', 'Bank soal berhasil dikaitkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function addSoal(Request $request, JadwalUjian $jadwalUjian)
    {
        $request->validate(['soal_id' => 'required|exists:soal,id']);

        // Count existing to determine order
        $maxOrder = $jadwalUjian->soalUjian()->max('urutan') ?? 0;

        $soalUjian = SoalUjian::create([
            'jadwal_ujian_id' => $jadwalUjian->id,
            'soal_id' => $request->soal_id,
            'urutan' => $maxOrder + 1
        ]);

        if ($request->ajax()) {
            // Load necessary relations to render the new row
            $soalUjian->load('soal');
            return response()->json([
                'status' => 'success',
                'message' => 'Soal berhasil ditambahkan.',
                'data' => $soalUjian
            ]);
        }

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function removeSoal(Request $request, $id)
    {
        $soalUjian = SoalUjian::findOrFail($id);
        $soalUjian->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Soal dihapus dari jadwal.'
            ]);
        }

        return back()->with('success', 'Soal dihapus dari jadwal.');
    }

    public function reorderSoal(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach($request->order as $index => $id) {
            SoalUjian::where('id', $id)->update(['urutan' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }

    public function getAvailableSoal(Request $request, JadwalUjian $jadwalUjian)
    {
        if (!$jadwalUjian->bank_soal_id) {
            return response()->json(['data' => [], 'links' => '']);
        }

        $existingIds = $jadwalUjian->soalUjian->pluck('soal_id')->toArray();
        $query = $jadwalUjian->bankSoal->soal()->whereNotIn('id', $existingIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('tingkat_kesulitan', 'like', "%{$search}%")
                  ->orWhere('tipe_soal', 'like', "%{$search}%");
            });
        }

        $available = $query->paginate(10); // 10 items per page

        // Generate HTML for items
        $html = '';
        foreach ($available as $s) {
            $badgeColorInfo = $s->tingkat_kesulitan == 'mudah' ? 'success' : ($s->tingkat_kesulitan == 'sedang' ? 'warning' : 'danger');

            $tipeLabel = 'Uraian';
            if ($s->tipe_soal == 'pilihan_ganda') $tipeLabel = 'PG';
            if ($s->tipe_soal == 'isian_singkat') $tipeLabel = 'Isian';

            $pertanyaan = Str::limit(strip_tags($s->pertanyaan), 150);
            $addRoute = route('jadwal-ujian.add-soal', $jadwalUjian->id);
            $csrf = csrf_field();

            $html .= <<<HTML
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-4 py-3">
                <div class="pr-3">
                    <div class="mb-1">
                        <span class="badge badge-{$badgeColorInfo} mr-1 text-capitalize">{$s->tingkat_kesulitan}</span>
                        <span class="badge badge-light border">{$tipeLabel}</span>
                    </div>
                    <div class="text-dark line-height-normal">{$pertanyaan}</div>
                </div>
                <form action="{$addRoute}" method="POST" class="add-soal-form">
                    {$csrf}
                    <input type="hidden" name="soal_id" value="{$s->id}">
                    <button type="submit" class="btn btn-outline-primary shadow-sm rounded-pill px-4">Pilih</button>
                </form>
            </div>
HTML;
        }

        if ($available->count() == 0) {
            $html = '<div class="p-5 text-center"><h5 class="text-muted"><i class="fas fa-search-minus fa-2x d-block mb-3"></i>Tidak ada soal yang ditemukan atau semua soal sudah dipilih.</h5></div>';
        }

        return response()->json([
            'html' => $html,
            'pagination' => (string) $available->appends($request->all())->links('pagination::bootstrap-4')
        ]);
    }

    public function regenerateSoalByDifficulty(Request $request, JadwalUjian $jadwalUjian)
    {
        $request->validate([
            'jml_mudah' => 'required|integer|min:0',
            'jml_sedang' => 'required|integer|min:0',
            'jml_sulit' => 'required|integer|min:0',
        ]);

        $totalReq = $request->jml_mudah + $request->jml_sedang + $request->jml_sulit;
        if ($totalReq == 0) {
            return back()->with('error', 'Jumlah total soal tidak boleh 0.');
        }

        DB::beginTransaction();
        try {
            // Check availability in Bank
            $bankId = $jadwalUjian->bank_soal_id;

            $mudah = Soal::where('bank_soal_id', $bankId)->where('tingkat_kesulitan', 'mudah')->inRandomOrder()->take($request->jml_mudah)->get();
            $sedang = Soal::where('bank_soal_id', $bankId)->where('tingkat_kesulitan', 'sedang')->inRandomOrder()->take($request->jml_sedang)->get();
            $sulit = Soal::where('bank_soal_id', $bankId)->where('tingkat_kesulitan', 'sulit')->inRandomOrder()->take($request->jml_sulit)->get();

            if ($mudah->count() < $request->jml_mudah) throw new \Exception("Kurang soal Mudah (Tersedia: {$mudah->count()}).");
            if ($sedang->count() < $request->jml_sedang) throw new \Exception("Kurang soal Sedang (Tersedia: {$sedang->count()}).");
            if ($sulit->count() < $request->jml_sulit) throw new \Exception("Kurang soal Sulit (Tersedia: {$sulit->count()}).");

            // Delete existing
            $jadwalUjian->soalUjian()->delete();

            // Insert new
            $urutan = 1;

            // Merge all
            $allSoal = $mudah->merge($sedang)->merge($sulit);

            if ($request->has('acak_urutan')) {
                $allSoal = $allSoal->shuffle();
            }

            foreach($allSoal as $soal) {
                SoalUjian::create([
                    'jadwal_ujian_id' => $jadwalUjian->id,
                    'soal_id' => $soal->id,
                    'urutan' => $urutan++
                ]);
            }

            // Update jumlah_soal in parent
            $jadwalUjian->update(['jumlah_soal' => $totalReq]);

            DB::commit();
            return back()->with('success', 'Soal berhasil di-generate ulang sesuai komposisi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Additional methods for Publish/Start/Finish
    public function setStatus(Request $request, JadwalUjian $jadwalUjian)
    {
        $status = $request->status;
        if (in_array($status, ['draft', 'aktif', 'selesai'])) {
            $jadwalUjian->update(['status' => $status]);
            return back()->with('success', 'Status ujian diperbarui');
        }
        return back()->with('error', 'Status tidak valid');
    }

    /**
     * Manual Block/Unblock student from an exam
     */
    public function toggleBlockStudent(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $ujSiswa = SoalUjian::where('jadwal_ujian_id', $id)->first(); // Just to check if exam exists? No, should check JadwalUjian
        $jadwal = JadwalUjian::findOrFail($id);

        $ujianSiswa = UjianSiswa::firstOrCreate(
            ['jadwal_ujian_id' => $id, 'siswa_id' => $request->siswa_id],
            ['status' => 'belum_mulai']
        );

        $ujianSiswa->is_blocked = !$ujianSiswa->is_blocked;
        $ujianSiswa->save();

        return response()->json([
            'status' => 'success',
            'is_blocked' => $ujianSiswa->is_blocked,
            'message' => $ujianSiswa->is_blocked ? 'Siswa berhasil diblokir.' : 'Blokir siswa dibuka.'
        ]);
    }
}
