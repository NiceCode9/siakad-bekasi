<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\MataPelajaranKelas;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $semesterAktif = Semester::active()->first();
            $query = JadwalPelajaran::whereHas('mataPelajaranKelas.kelas', function($q) use ($semesterAktif) {
                if ($semesterAktif) {
                    $q->where('semester_id', $semesterAktif->id);
                }
            })->with([
                'mataPelajaranKelas.mataPelajaran',
                'mataPelajaranKelas.kelas',
                'mataPelajaranKelas.guru'
            ]);

            // Filter by kelas
            if ($request->filled('kelas_id')) {
                $query->whereHas('mataPelajaranKelas', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            // Filter by hari
            if ($request->filled('hari')) {
                $query->where('hari', $request->hari);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kelas_nama', function ($row) {
                    return $row->mataPelajaranKelas->kelas->nama ?? '-';
                })
                ->addColumn('mapel_nama', function ($row) {
                    return $row->mataPelajaranKelas->mataPelajaran->nama ?? '-';
                })
                ->addColumn('guru_nama', function ($row) {
                    return $row->mataPelajaranKelas->guru->nama_lengkap ?? '-';
                })
                ->addColumn('waktu', function ($row) {
                    return $row->jam_mulai->format('H:i') . ' - ' . $row->jam_selesai->format('H:i');
                })
                ->addColumn('hari_badge', function ($row) {
                    $colors = [
                        'Senin' => 'primary',
                        'Selasa' => 'success',
                        'Rabu' => 'warning',
                        'Kamis' => 'info',
                        'Jumat' => 'danger',
                        'Sabtu' => 'secondary'
                    ];
                    $color = $colors[$row->hari] ?? 'primary';
                    return '<span class="badge badge-' . $color . '">' . $row->hari . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a href="' . route('jadwal-pelajaran.show', $row->id) . '" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>';
                    $btn .= '<a href="' . route('jadwal-pelajaran.edit', $row->id) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->orderColumn('hari', function ($query, $order) {
                    $hariOrder = "FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')";
                    $query->orderByRaw("$hariOrder $order");
                })
                ->orderColumn('waktu', 'jam_mulai $1')
                ->rawColumns(['hari_badge', 'action'])
                ->make(true);
        }

        // Data untuk filter
        $semesterAktif = Semester::active()->first();
        $kelas = $semesterAktif
            ? Kelas::where('semester_id', $semesterAktif->id)->orderBy('nama')->get()
            : collect();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pembelajaran.jadwal-pelajaran.index', compact('kelas', 'hari'));
    }

    public function create()
    {
        $semesterAktif = Semester::active()->first();

        if (!$semesterAktif) {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Tidak ada semester aktif');
        }

        $kelas = Kelas::where('semester_id', $semesterAktif->id)
            ->with('jurusan')
            ->orderBy('nama')
            ->get();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pembelajaran.jadwal-pelajaran.create', compact('kelas', 'hari'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_kelas_id' => 'required|exists:mata_pelajaran_kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:50',
        ]);

        // Validasi bentrok
        $bentrok = $this->checkBentrok(
            $validated['mata_pelajaran_kelas_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($bentrok) {
            return redirect()->back()
                ->withInput()
                ->with('error', $bentrok);
        }

        JadwalPelajaran::create($validated);

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal Pelajaran berhasil ditambahkan');
    }

    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load([
            'mataPelajaranKelas.mataPelajaran',
            'mataPelajaranKelas.kelas',
            'mataPelajaranKelas.guru'
        ]);

        return view('pembelajaran.jadwal-pelajaran.show', compact('jadwalPelajaran'));
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load([
            'mataPelajaranKelas.kelas',
            'mataPelajaranKelas.mataPelajaran',
            'mataPelajaranKelas.guru'
        ]);

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pembelajaran.jadwal-pelajaran.edit', compact('jadwalPelajaran', 'hari'));
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:50',
        ]);

        // Validasi bentrok (exclude jadwal ini)
        $bentrok = $this->checkBentrok(
            $jadwalPelajaran->mata_pelajaran_kelas_id,
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $jadwalPelajaran->id
        );

        if ($bentrok) {
            return redirect()->back()
                ->withInput()
                ->with('error', $bentrok);
        }

        $jadwalPelajaran->update($validated);

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal Pelajaran berhasil diperbarui');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal Pelajaran berhasil dihapus'
        ]);
    }

    /**
     * Check bentrok jadwal
     */
    private function checkBentrok($mataPelajaranKelasId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $mpk = MataPelajaranKelas::with([
            'kelas',
            'guru'
        ])->find($mataPelajaranKelasId);

        if (!$mpk) return null;

        // 1. Check bentrok guru (guru tidak bisa mengajar 2 kelas di waktu sama)
        $bentrokGuru = JadwalPelajaran::whereHas('mataPelajaranKelas', function ($q) use ($mpk) {
            $q->where('guru_id', $mpk->guru_id);
        })
            ->where('hari', $hari)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                        $q2->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
            });

        if ($excludeId) {
            $bentrokGuru->where('id', '!=', $excludeId);
        }

        if ($bentrokGuru->exists()) {
            return "Guru {$mpk->guru->nama_lengkap} sudah mengajar di hari {$hari} pada jam {$jamMulai} - {$jamSelesai}";
        }

        // 2. Check bentrok kelas (kelas tidak bisa ada 2 mapel di waktu sama)
        $bentrokKelas = JadwalPelajaran::whereHas('mataPelajaranKelas', function ($q) use ($mpk) {
            $q->where('kelas_id', $mpk->kelas_id);
        })
            ->where('hari', $hari)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                        $q2->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
            });

        if ($excludeId) {
            $bentrokKelas->where('id', '!=', $excludeId);
        }

        if ($bentrokKelas->exists()) {
            return "Kelas {$mpk->kelas->nama} sudah ada jadwal di hari {$hari} pada jam {$jamMulai} - {$jamSelesai}";
        }

        return null;
    }

    /**
     * View jadwal per kelas (table format)
     */
    public function viewByKelas(Kelas $kelas)
    {
        $jadwal = JadwalPelajaran::whereHas('mataPelajaranKelas', function ($q) use ($kelas) {
            $q->where('kelas_id', $kelas->id);
        })
            ->with([
                'mataPelajaranKelas.mataPelajaran',
                'mataPelajaranKelas.guru'
            ])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->get();

        // Group by hari
        $jadwalPerHari = $jadwal->groupBy('hari');

        return view('pembelajaran.jadwal-pelajaran.by-kelas', compact('kelas', 'jadwalPerHari'));
    }

    /**
     * View jadwal per guru
     */
    public function viewByGuru($guruId)
    {
        $guru = \App\Models\Guru::findOrFail($guruId);

        $semesterAktif = \App\Models\Semester::active()->first();
        $jadwal = JadwalPelajaran::whereHas('mataPelajaranKelas', function ($q) use ($guruId, $semesterAktif) {
            $q->where('guru_id', $guruId);
            if ($semesterAktif) {
                $q->whereHas('kelas', function($qk) use ($semesterAktif) {
                    $qk->where('semester_id', $semesterAktif->id);
                });
            }
        })
            ->with([
                'mataPelajaranKelas.mataPelajaran',
                'mataPelajaranKelas.kelas'
            ])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->get();

        $jadwalPerHari = $jadwal->groupBy('hari');

        return view('pembelajaran.jadwal-pelajaran.by-guru', compact('guru', 'jadwalPerHari'));
    }
}

