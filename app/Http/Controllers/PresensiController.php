<?php

namespace App\Http\Controllers;

use App\Models\PresensiSiswa;
use App\Models\Kelas;
use App\Models\SiswaKelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresensiController extends Controller
{
    /**
     * Display presensi dashboard/filter.
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $semesterAktif = Semester::active()->first();
        
        $kelas = $semesterAktif 
            ? Kelas::where('semester_id', $semesterAktif->id)->orderBy('nama')->get() 
            : collect();

        // If filter applied
        $rekap = null;
        if ($request->filled('kelas_id') && $request->filled('tanggal')) {
            $kelasId = $request->kelas_id;
            $tanggal = $request->tanggal;

            // Get students in class
            $siswaList = SiswaKelas::with('siswa')
                ->where('kelas_id', $kelasId)
                ->where('status', 'aktif')
                ->get();

            // Get presensi records
            $presensi = PresensiSiswa::where('kelas_id', $kelasId)
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->keyBy('siswa_id');

            $rekap = [
                'siswa' => $siswaList,
                'presensi' => $presensi,
                'total_h' => $presensi->where('status', 'H')->count(),
                'total_i' => $presensi->where('status', 'I')->count(),
                'total_s' => $presensi->where('status', 'S')->count(),
                'total_a' => $presensi->where('status', 'A')->count(),
                'not_recorded' => $siswaList->count() - $presensi->count()
            ];
        }

        return view('pembelajaran.presensi.index', compact('kelas', 'tanggal', 'rekap'));
    }

    /**
     * Show form for bulk input.
     */
    public function create(Request $request)
    {
        if (!$request->filled('kelas_id') || !$request->filled('tanggal')) {
            return redirect()->route('presensi.index')
                ->with('error', 'Silakan pilih Kelas dan Tanggal terlebih dahulu.');
        }

        $kelas = Kelas::with(['semester', 'jurusan'])->findOrFail($request->kelas_id);
        $tanggal = $request->tanggal;
        
        // Check if date is future
        if (strtotime($tanggal) > time()) {
            return redirect()->back()->with('error', 'Tidak dapat menginput presensi untuk tanggal yang akan datang.');
        }

        // Get students
        $siswa = SiswaKelas::with('siswa')
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('siswa.nama_lengkap') // Actually order by rel is harder, assume relation loaded
            ->get()
            ->sortBy(function($sk) {
                return $sk->siswa->nama_lengkap;
            });

        // Get existing data if any (Edit Mode)
        $existing = PresensiSiswa::where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        return view('pembelajaran.presensi.create', compact('kelas', 'tanggal', 'siswa', 'existing'));
    }

    /**
     * Store bulk presensi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.status' => 'required|in:H,I,S,A',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $kelasId = $validated['kelas_id'];
        $tanggal = $validated['tanggal'];
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            foreach ($validated['presensi'] as $siswaId => $data) {
                PresensiSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'kelas_id' => $kelasId,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'user_id' => $userId,
                    ]
                );
            }
            DB::commit();

            return redirect()->route('presensi.index', ['kelas_id' => $kelasId, 'tanggal' => $tanggal])
                ->with('success', 'Data presensi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
        }
    }

    /**
     * Monthly Rekap View.
     */
    public function rekap(Request $request)
    {
        $semesterAktif = Semester::active()->first();
        $kelas = $semesterAktif ? Kelas::where('semester_id', $semesterAktif->id)->get() : collect();
        
        $kelasId = $request->input('kelas_id');
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $dataRekap = null;
        $selectedKelas = null;

        if ($kelasId) {
            $selectedKelas = Kelas::find($kelasId);
            $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
            
            // Siswa
            $siswa = SiswaKelas::with('siswa')
                ->where('kelas_id', $kelasId)
                ->where('status', 'aktif')
                ->get()
                ->sortBy(fn($sk) => $sk->siswa->nama_lengkap);

            // Presensi
            $presensi = PresensiSiswa::where('kelas_id', $kelasId)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->get()
                ->groupBy('siswa_id'); // Group by siswa, then keyed by date usually handled in view

            $dataRekap = [
                'siswa' => $siswa,
                'presensi' => $presensi,
                'days' => $daysInMonth
            ];
        }

        return view('pembelajaran.presensi.rekap', compact('kelas', 'kelasId', 'bulan', 'tahun', 'dataRekap', 'selectedKelas'));
    }
}
