<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->orderBy('created_at', 'desc');

        // Search/Filter by User
        if ($request->has('user') && $request->user != '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->user . '%');
            });
        }

        // Filter by Table
        if ($request->has('tabel') && $request->tabel != '') {
            $query->where('tabel', $request->tabel);
        }

        // Filter by Activity Type
        if ($request->has('aktivitas') && $request->aktivitas != '') {
            $query->where('aktivitas', 'like', '%' . $request->aktivitas . '%');
        }

        $logs = $query->paginate(20);
        $tables = LogAktivitas::select('tabel')->distinct()->pluck('tabel');

        return view('admin.log-aktivitas.index', compact('logs', 'tables'));
    }

    public function show($id)
    {
        $log = LogAktivitas::with('user')->findOrFail($id);
        return view('admin.log-aktivitas.show', compact('log'));
    }

    public function destroy($id)
    {
        $log = LogAktivitas::findOrFail($id);
        $log->delete();

        return redirect()->route('admin.log-aktivitas.index')->with('success', 'Log aktivitas berhasil dihapus');
    }

    public function clear()
    {
        LogAktivitas::truncate();
        return redirect()->route('admin.log-aktivitas.index')->with('success', 'Semua log aktivitas berhasil dibersihkan');
    }
}
