<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\JadwalPelajaran;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RollOverController extends Controller
{
    public function index()
    {
        $semesters = Semester::with('tahunAkademik')->latest()->get();
        return view('admin.roll-over.index', compact('semesters'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'from_semester_id' => 'required|exists:semester,id',
            'to_semester_id' => 'required|exists:semester,id|different:from_semester_id',
            'copy_kelas' => 'nullable|boolean',
            'copy_mapel' => 'nullable|boolean',
            'copy_jadwal' => 'nullable|boolean',
        ]);

        $fromIdx = $request->from_semester_id;
        $toIdx = $request->to_semester_id;

        DB::beginTransaction();
        try {
            $report = [];
            $kelasMap = []; // Mapping old class ID to new class ID

            // 1. Copy Kelas
            if ($request->copy_kelas) {
                $oldClasses = Kelas::where('semester_id', $fromIdx)->get();
                $countKelas = 0;
                
                foreach ($oldClasses as $oldKelas) {
                    // Check if class with same name already exists in target semester
                    $existing = Kelas::where('semester_id', $toIdx)
                        ->where('nama', $oldKelas->nama)
                        ->first();
                    
                    if ($existing) {
                        $kelasMap[$oldKelas->id] = $existing->id;
                        continue;
                    }

                    $newKelas = $oldKelas->replicate();
                    $newKelas->semester_id = $toIdx;
                    
                    // Regenerate code if it contains semester info
                    $fromSemester = Semester::find($fromIdx);
                    $toSemester = Semester::find($toIdx);
                    $newKode = str_replace($fromSemester->kode, $toSemester->kode, $oldKelas->kode);
                    
                    // If code remains same or clashes globally, make it unique
                    if ($newKode === $oldKelas->kode || Kelas::where('kode', $newKode)->exists()) {
                        // Append target semester id or a short suffix to ensure uniqueness
                        $newKode = substr($oldKelas->kode, 0, 15) . '-' . $toIdx;
                        
                        // Last resort if still exists (very unlikely but for robustness)
                        if (Kelas::where('kode', $newKode)->exists()) {
                            $newKode = substr($oldKelas->kode, 0, 14) . '-' . $toIdx . Str::random(3);
                        }
                    }

                    $newKelas->kode = $newKode;
                    $newKelas->save();

                    $kelasMap[$oldKelas->id] = $newKelas->id;
                    $countKelas++;
                }
                $report[] = "$countKelas Kelas berhasil disalin/disesuaikan.";
            } else {
                // If not copying classes, we need to map existing classes by name for Mapel/Jadwal
                $oldClasses = Kelas::where('semester_id', $fromIdx)->get();
                foreach ($oldClasses as $oldKelas) {
                    $existing = Kelas::where('semester_id', $toIdx)
                        ->where('nama', $oldKelas->nama)
                        ->first();
                    if ($existing) {
                        $kelasMap[$oldKelas->id] = $existing->id;
                    }
                }
            }

            // 2. Copy Mata Pelajaran Kelas (Teacher Assignments)
            $mapelMap = []; // Mapping old MPK ID to new MPK ID
            if ($request->copy_mapel) {
                $countMapel = 0;
                foreach ($kelasMap as $oldKelasId => $newKelasId) {
                    $oldMapels = MataPelajaranKelas::where('kelas_id', $oldKelasId)->get();
                    foreach ($oldMapels as $oldMapel) {
                        // Check if already exists in target
                        $existing = MataPelajaranKelas::where('kelas_id', $newKelasId)
                            ->where('mata_pelajaran_id', $oldMapel->mata_pelajaran_id)
                            ->first();

                        if ($existing) {
                            $mapelMap[$oldMapel->id] = $existing->id;
                            continue;
                        }

                        $newMapel = $oldMapel->replicate();
                        $newMapel->kelas_id = $newKelasId;
                        $newMapel->save();

                        $mapelMap[$oldMapel->id] = $newMapel->id;
                        $countMapel++;
                    }
                }
                $report[] = "$countMapel Penugasan Guru berhasil disalin.";
            }

            // 3. Copy Jadwal Pelajaran
            if ($request->copy_jadwal) {
                $countJadwal = 0;
                // We need the mapping from step 2
                if (empty($mapelMap)) {
                    // If mapel wasn't copied in this request, try to find matches
                    foreach ($kelasMap as $oldKelasId => $newKelasId) {
                        $oldMapels = MataPelajaranKelas::where('kelas_id', $oldKelasId)->get();
                        foreach ($oldMapels as $oldMapel) {
                             $match = MataPelajaranKelas::where('kelas_id', $newKelasId)
                                ->where('mata_pelajaran_id', $oldMapel->mata_pelajaran_id)
                                ->first();
                             if ($match) {
                                 $mapelMap[$oldMapel->id] = $match->id;
                             }
                        }
                    }
                }

                foreach ($mapelMap as $oldMpkId => $newMpkId) {
                    $oldSchedules = JadwalPelajaran::where('mata_pelajaran_kelas_id', $oldMpkId)->get();
                    foreach ($oldSchedules as $oldSchedule) {
                        // Check for conflict in target semester
                        $conflict = JadwalPelajaran::whereHas('mataPelajaranKelas', function($q) use ($toIdx) {
                                $q->whereHas('kelas', function($qk) use ($toIdx) {
                                    $qk->where('semester_id', $toIdx);
                                });
                            })
                            ->where('hari', $oldSchedule->hari)
                            ->where(function($q) use ($oldSchedule) {
                                $q->whereBetween('jam_mulai', [$oldSchedule->jam_mulai, $oldSchedule->jam_selesai])
                                  ->orWhereBetween('jam_selesai', [$oldSchedule->jam_mulai, $oldSchedule->jam_selesai]);
                            })
                            // we filter further to target class to avoid false positives if we only care about class-level conflict
                            // but usually it's teacher conflict too. For simplicity, we filter by the new MPK (which is class specific)
                            ->where('mata_pelajaran_kelas_id', $newMpkId)
                            ->exists();

                        if (!$conflict) {
                            $newSchedule = $oldSchedule->replicate();
                            $newSchedule->mata_pelajaran_kelas_id = $newMpkId;
                            $newSchedule->save();
                            $countJadwal++;
                        }
                    }
                }
                $report[] = "$countJadwal Jadwal Pelajaran berhasil disalin.";
            }

            DB::commit();
            return redirect()->route('admin.roll-over.index')
                ->with('success', 'Roll-over berhasil: ' . implode(' ', $report));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal roll-over: ' . $e->getMessage());
        }
    }
}
