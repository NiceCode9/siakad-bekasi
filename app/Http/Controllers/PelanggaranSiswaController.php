<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelanggaranSiswaController extends Controller
{
    public function index()
    {
        $this->authorize('view-pelanggaran');

        $pelanggaran = PelanggaranSiswa::with(['siswa.siswaKelas.kelas', 'pelapor'])
            ->latest('tanggal')
            ->get();

        return view('pelanggaran.index', compact('pelanggaran'));
    }

    public function create()
    {
        $this->authorize('laporkan-pelanggaran');

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
        $this->authorize('laporkan-pelanggaran');

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
            'kategori' => 'ringan',
            'poin' => 0,
            'kronologi' => $request->kronologi,
            'pelapor_id' => Auth::user()->guru ? Auth::user()->guru->id : null,
            'status' => 'proses',
        ]);

        return redirect()->route('pelanggaran-siswa.index')->with('success', 'Pelanggaran berhasil dilaporkan.');
    }

    public function edit(PelanggaranSiswa $pelanggaranSiswa)
    {
        $this->authorize('proses-pelanggaran');

        return view('pelanggaran.edit', compact('pelanggaranSiswa'));
    }

    public function update(Request $request, PelanggaranSiswa $pelanggaranSiswa)
    {
        $this->authorize('proses-pelanggaran');

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
        $this->authorize('proses-pelanggaran');
        $pelanggaranSiswa->delete();

        return redirect()->route('pelanggaran-siswa.index')->with('success', 'Data pelanggaran dihapus.');
    }

    /**
     * Display a resume of violations per student (for BK/Kesiswaan)
     */
    public function resume()
    {
        $this->authorize('view-resume-pelanggaran');

        $resume = Siswa::whereHas('pelanggaran')
            ->withCount('pelanggaran')
            ->withSum('pelanggaran', 'poin')
            ->with(['kelas' => function ($q) {
                $q->wherePivot('status', 'aktif');
            }])
            ->get();

        return view('pelanggaran.resume', compact('resume'));
    }

    /**
     * Display details of violations for a specific student
     */
    public function showStudent(Siswa $siswa)
    {
        $this->authorize('view-resume-pelanggaran');

        $siswa->load(['pelanggaran.pelapor', 'kelas' => function ($q) {
            $q->wherePivot('status', 'aktif');
        }]);

        return view('pelanggaran.show', compact('siswa'));
    }

    public function getStudents(Request $request)
    {
        $kelasId = $request->kelas_id;
        $students = Siswa::whereHas('kelas', function ($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->get();

        return response()->json($students);
    }
}
