<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\MataPelajaranKelas;

class ApiController extends Controller
{
    /**
     * Get available gurus (helper for select2)
     */
    public function getGurus(Request $request)
    {
        $term = $request->get('term');
        $gurus = Guru::active()
            ->where('nama_lengkap', 'like', "%{$term}%")
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get()
            ->map(function($guru) {
                return [
                    'id' => $guru->id,
                    'text' => $guru->nama_lengkap . ' (' . ($guru->nip ?? '-') . ')'
                ];
            });

        return response()->json($gurus);
    }

    /**
     * Get mata pelajaran guru by kelas (AJAX)
     */
    public function getMataPelajaranByKelas(Request $request)
    {
        $kelasId = $request->kelas_id;

        $mataPelajaranKelas = MataPelajaranKelas::where('kelas_id', $kelasId)
            ->with([
                'mataPelajaran',
                'guru'
            ])
            ->get()
            ->map(function ($mpk) {
                return [
                    'id' => $mpk->id,
                    'label' => $mpk->mataPelajaran->nama . ' - ' . ($mpk->guru->nama_lengkap ?? 'Belum ada guru'),
                    'mapel' => $mpk->mataPelajaran->nama,
                    'guru' => $mpk->guru->nama_lengkap ?? 'Belum ada guru',
                ];
            });

        return response()->json($mataPelajaranKelas);
    }
}
