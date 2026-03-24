<?php

namespace App\Http\Controllers;

use App\Exports\LeggerExport;
use App\Models\Kelas;
use App\Models\Legger;
use App\Models\Raport;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
                'generated_by' => Auth::user()->guru->id ?? Auth::user()->id,
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
        $fileName = 'Legger_'.str_replace(' ', '_', $legger->kelas->nama).'_'.str_replace('/', '-', $legger->semester->nama).'.xlsx';

        return Excel::download(new LeggerExport($legger->kelas_id, $legger->semester_id), $fileName);
    }

    public function exportPdf($id)
    {
        $data = $this->getLeggerData($id);

        $pdf = Pdf::loadView('legger.pdf', $data)
            ->setPaper('a4', 'landscape');

        $fileName = 'Legger_'.str_replace(' ', '_', $data['legger']->kelas->nama).'_'.str_replace('/', '-', $data['legger']->semester->nama).'.pdf';

        return $pdf->download($fileName);
    }

    private function getLeggerData($id)
    {
        $legger = Legger::with(['kelas.mataPelajaranKelas.mataPelajaran', 'semester.tahunAkademik'])->findOrFail($id);

        $allRaports = Raport::with(['siswa', 'raportDetail'])
            ->where('kelas_id', $legger->kelas_id)
            ->where('semester_id', $legger->semester_id)
            ->get();

        $subjects = $legger->kelas->mataPelajaranKelas->sortBy('id');

        // Group by student to merge components
        $groupedBySiswa = $allRaports->groupBy('siswa_id');
        $aggregatedData = collect();

        foreach ($groupedBySiswa as $siswaId => $siswaRaports) {
            $firstRaport = $siswaRaports->first();
            $siswa = $firstRaport->siswa;

            // Merge details across all components for this student
            $allDetails = $siswaRaports->pluck('raportDetail')->flatten();

            $mappedGrades = [];
            $totalNilaiSiswa = 0;
            $countMapelSiswa = 0;

            foreach ($subjects as $mps) {
                // Average scores for the same subject across different components
                $subjectDetails = $allDetails->where('mata_pelajaran_id', $mps->mata_pelajaran_id);
                $avgNilai = $subjectDetails->count() > 0 ? $subjectDetails->avg('nilai_akhir') : 0;

                $mappedGrades[$mps->mata_pelajaran_id] = [
                    'nilai' => $avgNilai > 0 ? round($avgNilai) : '-',
                    'predikat' => $subjectDetails->first()?->predikat ?? '-',
                ];

                if ($avgNilai > 0) {
                    $totalNilaiSiswa += $avgNilai;
                    $countMapelSiswa++;
                }
            }

            // Consolidate attendance (taking max/latest available)
            $aggregatedData->push((object)[
                'id' => $siswaId, // Use student ID as surrogate for uniqueness
                'siswa' => $siswa,
                'grades' => $mappedGrades,
                'average_score' => $countMapelSiswa > 0 ? $totalNilaiSiswa / $countMapelSiswa : 0,
                'jumlah_sakit' => $siswaRaports->max('jumlah_sakit'),
                'jumlah_izin' => $siswaRaports->max('jumlah_izin'),
                'jumlah_alpha' => $siswaRaports->max('jumlah_alpha'),
            ]);
        }

        // Assign rankings
        $sorted = $aggregatedData->sortByDesc('average_score')->values();
        foreach ($aggregatedData as $item) {
            $item->ranking = $sorted->search(fn ($s) => $s->id === $item->id) + 1;
        }

        return [
            'legger' => $legger,
            'raports' => $aggregatedData,
            'subjects' => $subjects,
        ];
    }
}
