<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;

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
}
