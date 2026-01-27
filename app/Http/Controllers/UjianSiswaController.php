<?php

namespace App\Http\Controllers;

use App\Models\JadwalUjian;
use App\Models\UjianSiswa;
use App\Models\JawabanSiswa;
use App\Models\SoalUjian;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UjianSiswaController extends Controller
{
    /**
     * List exams available for logged in student.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('siswa')) {
            abort(403, 'Anda bukan siswa aktif.');
        }

        // Get Student's Class (Active Semester)
        // Adjust logic if multiple active semesters allowed, but usually one.
        $siswaKelas = SiswaKelas::where('siswa_id', $user->siswa->id)
            ->where('status', 'aktif')
            ->latest()
            ->first();

        $ujianList = collect();
        if ($siswaKelas) {
            $ujianList = JadwalUjian::whereHas('mataPelajaranKelas', function ($q) use ($siswaKelas) {
                $q->where('kelas_id', $siswaKelas->kelas_id);
            })
            ->whereIn('status', ['aktif', 'selesai'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();
        }

        return view('pembelajaran.cbt.ujian.student.index', compact('ujianList'));
    }

    /**
     * Show intro/token page.
     */
    public function show($id)
    {
        $jadwal = JadwalUjian::findOrFail($id);
        $user = Auth::user();

        // Check active session
        $ujianSiswa = UjianSiswa::where('jadwal_ujian_id', $jadwal->id)
            ->where('siswa_id', $user->siswa->id)
            ->first();

        return view('pembelajaran.cbt.ujian.student.show', compact('jadwal', 'ujianSiswa'));
    }

    /**
     * Start the exam (Validate Token).
     */
    public function start(Request $request, $id)
    {
        $jadwal = JadwalUjian::findOrFail($id);
        
        // Validate Token
        if ($request->token !== $jadwal->token) {
            return back()->with('error', 'Token salah.');
        }

        // Check eligibility (Time)
        if (!now()->between($jadwal->tanggal_mulai, $jadwal->tanggal_selesai)) {
            return back()->with('error', 'Ujian belum dimulai atau sudah berakhir.');
        }

        $user = Auth::user();
        
        // Check if already started elsewhere?
        $existing = UjianSiswa::where('jadwal_ujian_id', $jadwal->id)
            ->where('siswa_id', $user->siswa->id)
            ->first();

        if ($existing && $existing->status != 'selesai') {
            // Update session_id to allow re-login
            $existing->update(['session_id' => session()->getId()]);
            $ujianSiswa = $existing;
        } else {
            // Create or Get UjianSiswa
            $ujianSiswa = UjianSiswa::firstOrCreate(
                [
                    'jadwal_ujian_id' => $jadwal->id,
                    'siswa_id' => $user->siswa->id,
                ],
                [
                    'waktu_mulai' => now(),
                    'status' => 'sedang_mengerjakan',
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'session_id' => session()->getId(),
                    'violation_count' => 0,
                ]
            );
        }

        return redirect()->route('ujian-siswa.take', $jadwal->id);
    }

    /**
     * The Exam Interface.
     */
    public function take($id)
    {
        $jadwal = JadwalUjian::with(['soalUjian.soal'])->findOrFail($id);
        $user = Auth::user();

        $ujianSiswa = UjianSiswa::where('jadwal_ujian_id', $jadwal->id)
            ->where('siswa_id', $user->siswa->id)
            ->firstOrFail();

        // 1. Session Security Check
        if ($ujianSiswa->session_id !== session()->getId()) {
             return redirect()->route('ujian-siswa.show', $id)->with('error', 'Sesi ujian tidak valid. Anda login di perangkat lain.');
        }

        if ($ujianSiswa->status == 'selesai') {
            return redirect()->route('ujian-siswa.show', $id)->with('info', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Calculate remaining time
        $endTime = $ujianSiswa->waktu_mulai->addMinutes($jadwal->durasi);
        if ($jadwal->tanggal_selesai < $endTime) {
            $endTime = $jadwal->tanggal_selesai;
        }
        
        // If time up
        if (now() > $endTime) {
            return redirect()->route('ujian-siswa.finish', $id);
        }

        $sisaDetik = now()->diffInSeconds($endTime, false);

        // Get Questions
        $soalList = $jadwal->soalUjian;
        
        if ($jadwal->acak_soal) {
            $soalList = $soalList->shuffle(getSeed($user->id . $jadwal->id)); 
        }

        // Load existing answers
        $jawaban = JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)
            ->pluck('jawaban', 'soal_ujian_id');

        return view('pembelajaran.cbt.ujian.student.take', compact('jadwal', 'ujianSiswa', 'soalList', 'sisaDetik', 'jawaban'));
    }

    /**
     * Save Answer (AJAX).
     */
    public function saveAnswer(Request $request)
    {
        $request->validate([
            'ujian_siswa_id' => 'required|exists:ujian_siswa,id',
            'soal_ujian_id' => 'required|exists:soal_ujian,id',
            'jawaban' => 'required'
        ]);

        $ujianSiswa = UjianSiswa::findOrFail($request->ujian_siswa_id);

        // Security Check
        if ($ujianSiswa->session_id !== session()->getId()) {
            return response()->json(['status' => 'error', 'message' => 'Sesi tidak valid'], 403);
        }

        if ($ujianSiswa->status == 'selesai') {
             return response()->json(['status' => 'error', 'message' => 'Ujian sudah selesai'], 403);
        }

        // Time Check
        $endTime = $ujianSiswa->waktu_mulai->addMinutes($ujianSiswa->jadwalUjian->durasi)->addMinutes(5); // 5 min buffer
        if (now() > $endTime) {
            return response()->json(['status' => 'error', 'message' => 'Waktu habis'], 403);
        }

        $jawaban = JawabanSiswa::updateOrCreate(
            [
                'ujian_siswa_id' => $request->ujian_siswa_id,
                'soal_ujian_id' => $request->soal_ujian_id,
            ],
            [
                'jawaban' => $request->jawaban,
                'waktu_jawab' => now()
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function logViolation(Request $request)
    {
         $request->validate(['ujian_siswa_id' => 'required|exists:ujian_siswa,id']);
         $ujianSiswa = UjianSiswa::findOrFail($request->ujian_siswa_id);
         
         if ($ujianSiswa->session_id !== session()->getId()) {
            return response()->json(['status' => 'error'], 403);
         }

         $ujianSiswa->increment('violation_count');
         
         return response()->json(['status' => 'recorded', 'count' => $ujianSiswa->violation_count]);
    }

    /**
     * Finish Exam.
     */
    public function finish(Request $request, $id)
    {
        $jadwal = JadwalUjian::findOrFail($id);
        $user = Auth::user();
        
        $ujianSiswa = UjianSiswa::where('jadwal_ujian_id', $jadwal->id)
            ->where('siswa_id', $user->siswa->id)
            ->firstOrFail();

        // Calculate Score
        $totalNilai = 0;
        $jawabans = JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)->get();
        
        DB::beginTransaction();
        try {
            foreach ($jawabans as $j) {
                // Trigger auto-grade
                if ($j->autoGrade()) {
                    $totalNilai += $j->nilai;
                }
            }
            
            // Normalize Score ? Usually raw sum of bobot, or percentage?
            // Let's assume Score = (Total Perolehan / Total Bobot Jadwal) * 100
            // But currently code adds raw bobot.
            // Let's stick to raw sum or handle scaling if needed.
            // For general CBT, often we want 0-100 scale.
            
            $maxScore = $jadwal->soalUjian->sum(function($su) {
                return $su->soal->bobot;
            });
            
            $finalScore = 0;
            if ($maxScore > 0) {
                $finalScore = ($totalNilai / $maxScore) * 100;
            }

            $ujianSiswa->update([
                'status' => 'selesai',
                'waktu_submit' => now(),
                'nilai' => $finalScore
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error calculating score: '.$e->getMessage());
        }

        return redirect()->route('ujian-siswa.show', $id);
    }
}

// Helper for consistent shuffle
function getSeed($val) {
    return crc32($val); 
}
