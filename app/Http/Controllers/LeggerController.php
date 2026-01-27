<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Legger;
use App\Models\Raport;
use App\Models\MataPelajaran;
use App\Exports\LeggerExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LeggerController extends Controller
{
    public function index()
    {
        $classes = Kelas::all();
        $semesters = Semester::with('tahunAkademik')->get();
        $leggers = Legger::with(['kelas', 'semester'])->latest()->get();

        return view('legger.index', compact('classes', 'semesters', 'leggers'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
        ]);

        // Check if raport already exists for these students to ensure we have data
        $raportCount = Raport::where('kelas_id', $request->kelas_id)
            ->where('semester_id', $request->semester_id)
            ->count();

        if ($raportCount == 0) {
            return back()->with('error', 'Belum ada data raport yang di-generate untuk kelas dan semester ini.');
        }

        $legger = Legger::updateOrCreate(
            [
                'kelas_id' => $request->kelas_id,
                'semester_id' => $request->semester_id,
                'mata_pelajaran_id' => null, // Full class legger
            ],
            [
                'tanggal_generate' => now(),
                'generated_by' => Auth::user()->guru->id ?? null,
            ]
        );

        return redirect()->route('legger.show', $legger->id)->with('success', 'Legger berhasil di-generate.');
    }

    public function show($id)
    {
        $data = $this->getLeggerData($id);
        return view('legger.show', $data);
    }

    public function exportExcel($id)
    {
        $legger = Legger::findOrFail($id);
        $fileName = 'Legger_' . str_replace(' ', '_', $legger->kelas->nama) . '_' . str_replace('/', '-', $legger->semester->nama) . '.xlsx';
        
        return Excel::download(new LeggerExport($legger->kelas_id, $legger->semester_id), $fileName);
    }

    public function exportPdf($id)
    {
        $data = $this->getLeggerData($id);
        
        $pdf = Pdf::loadView('legger.pdf', $data)
            ->setPaper('a4', 'landscape');

        $fileName = 'Legger_' . str_replace(' ', '_', $data['legger']->kelas->nama) . '_' . str_replace('/', '-', $data['legger']->semester->nama) . '.pdf';
        
        return $pdf->download($fileName);
    }

    private function getLeggerData($id)
    {
        $legger = Legger::with(['kelas.mataPelajaranKelas.mataPelajaran', 'semester.tahunAkademik'])->findOrFail($id);
        
        $raports = Raport::with(['siswa', 'raportDetail.mataPelajaran'])
            ->where('kelas_id', $legger->kelas_id)
            ->where('semester_id', $legger->semester_id)
            ->get();

        $subjects = $legger->kelas->mataPelajaranKelas->sortBy('id');

        // Calculate averages
        foreach ($raports as $raport) {
            $totalNilai = 0;
            $countNilai = 0;
            foreach ($subjects as $mps) {
                $detail = $raport->raportDetail->where('mata_pelajaran_id', $mps->mata_pelajaran_id)->first();
                if ($detail && is_numeric($detail->nilai_akhir)) {
                    $totalNilai += $detail->nilai_akhir;
                    $countNilai++;
                }
            }
            $raport->average_score = $countNilai > 0 ? $totalNilai / $countNilai : 0;
        }

        // Assign rankings based on average_score
        $sortedRaports = $raports->sortByDesc('average_score')->values();
        
        foreach ($raports as $raport) {
            $raport->ranking = $sortedRaports->search(fn($item) => $item->id === $raport->id) + 1;
        }

        return [
            'legger' => $legger,
            'raports' => $raports,
            'subjects' => $subjects
        ];
    }
}
