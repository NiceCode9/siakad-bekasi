<?php

namespace App\Http\Controllers;

use App\Models\JadwalUjian;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\BankSoal;
use App\Models\Semester;
use App\Models\Soal;
use App\Models\SoalUjian;
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
            $query = JadwalUjian::with(['mataPelajaranKelas.kelas', 'mataPelajaranKelas.mataPelajaran', 'bankSoal']);

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

        return view('pembelajaran.cbt.jadwal-ujian.create', compact('kelas', 'bankSoal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_kelas_id' => 'required|array',
            'mata_pelajaran_kelas_id.*' => 'exists:mata_pelajaran_kelas,id',
            'bank_soal_id' => 'required|exists:bank_soal,id',
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
            'bank_soal_id' => $validated['bank_soal_id'],
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
            $bank = BankSoal::withCount('soal')->findOrFail($validated['bank_soal_id']);
            if ($bank->soal_count < $validated['jumlah_soal']) {
                return back()->withInput()->with('error', "Bank soal hanya memiliki {$bank->soal_count} soal.");
            }

            // Loop through each selected class
            foreach ($validated['mata_pelajaran_kelas_id'] as $mpkId) {
                $data = $baseData;
                $data['mata_pelajaran_kelas_id'] = $mpkId;
                $data['token'] = strtoupper(Str::random(6));

                $jadwal = JadwalUjian::create($data);

                // Generate Questions
                $soals = Soal::where('bank_soal_id', $bank->id)
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

            DB::commit();
            return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil dibuat untuk semua kelas terpilih.');

        } catch (\Exception $e) {
            DB::rollBack();
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

        return view('pembelajaran.cbt.jadwal-ujian.edit', compact('jadwalUjian', 'kelas', 'bankSoal'));
    }

    public function update(Request $request, JadwalUjian $jadwalUjian)
    {
        if($jadwalUjian->status != 'draft') {
            return back()->with('error', 'Hanya jadwal berstatus Draft yang dapat diedit.');
        }

        $validated = $request->validate([
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
            'bank_soal_id' => 'required|exists:bank_soal,id',
            'jenis_ujian' => 'required|in:ulangan_harian,uts,uas,ujian_praktik,ujian_sekolah',
            'nama_ujian' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi' => 'required|integer|min:1',
            // 'jumlah_soal' => 'required|integer|min:1', // Changing question count implies simple re-gen or complex diff?
            // For simplicity, if bank or count changes, we RE-GENERATE questions.
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


    // --- Advanced Question Management ---

    public function manageSoal(JadwalUjian $jadwalUjian)
    {
        $jadwalUjian->load(['soalUjian.soal', 'bankSoal']);
        return view('pembelajaran.cbt.jadwal-ujian.manage-soal', compact('jadwalUjian'));
    }

    public function addSoal(Request $request, JadwalUjian $jadwalUjian)
    {
        $request->validate(['soal_id' => 'required|exists:soal,id']);
        
        // Count existing to determine order
        $maxOrder = $jadwalUjian->soalUjian()->max('urutan') ?? 0;

        SoalUjian::create([
            'jadwal_ujian_id' => $jadwalUjian->id,
            'soal_id' => $request->soal_id,
            'urutan' => $maxOrder + 1
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function removeSoal($id)
    {
        $soalUjian = SoalUjian::findOrFail($id);
        $soalUjian->delete();
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
}
