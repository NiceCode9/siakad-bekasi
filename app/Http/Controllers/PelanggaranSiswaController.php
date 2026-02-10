<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelanggaranSiswaController extends Controller
{
    public function index()
    {
        $this->authorize('view-violations');

        // Fetch violations based on role/permissions? 
        // For now, list all. Maybe filter by academic year if needed.
        $pelanggaran = PelanggaranSiswa::with(['siswa.siswaKelas.kelas', 'pelapor'])
            ->latest('tanggal')
            ->get();

        return view('pelanggaran.index', compact('pelanggaran'));
    }

    public function create()
    {
        $this->authorize('report-violations');

        $activeTahun = TahunAkademik::active()->first();
        $kelas = collect([]);
        
        if ($activeTahun) {
            $kelas = Kelas::whereHas('semester', function ($q) use ($activeTahun) {
                $q->where('tahun_akademik_id', $activeTahun->id);
            })->get();
        }

        return view('pelanggaran.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $this->authorize('report-violations');

        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'kronologi' => 'nullable|string',
        ]);

        PelanggaranSiswa::create([
            'siswa_id' => $request->siswa_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'kategori' => 'ringan', // Default, updated by Kesiswaan later
            'poin' => 0, // Default, updated by Kesiswaan later
            'kronologi' => $request->kronologi,
            'pelapor_id' => Auth::user()->guru ? Auth::user()->guru->id : null, // If user is guru
            'status' => 'proses',
        ]);

        return redirect()->route('pelanggaran-siswa.index')->with('success', 'Pelanggaran berhasil dilaporkan.');
    }

    public function edit(PelanggaranSiswa $pelanggaranSiswa)
    {
        $this->authorize('process-violations'); // Only Kesiswaan/BK can edit/process

        return view('pelanggaran.edit', compact('pelanggaranSiswa'));
    }

    public function update(Request $request, PelanggaranSiswa $pelanggaranSiswa)
    {
        $this->authorize('process-violations');

        $request->validate([
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin' => 'required|integer|min:0',
            'sanksi' => 'required|string',
            'status' => 'required|in:proses,selesai',
        ]);

        $pelanggaranSiswa->update([
            'kategori' => $request->kategori,
            'poin' => $request->poin,
            'sanksi' => $request->sanksi,
            'status' => $request->status,
        ]);

        return redirect()->route('pelanggaran-siswa.index')->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy(PelanggaranSiswa $pelanggaranSiswa)
    {
        $this->authorize('process-violations'); // Only process role can delete
        $pelanggaranSiswa->delete();
        return redirect()->route('pelanggaran-siswa.index')->with('success', 'Data pelanggaran dihapus.');
    }
}
