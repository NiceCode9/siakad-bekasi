<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Raport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeggerExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $kelasId;

    protected $semesterId;

    public function __construct($kelasId, $semesterId)
    {
        $this->kelasId = $kelasId;
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        $allRaports = Raport::with(['siswa', 'raportDetail'])
            ->where('kelas_id', $this->kelasId)
            ->where('semester_id', $this->semesterId)
            ->get();

        $subjects = Kelas::with('mataPelajaranKelas.mataPelajaran')
            ->find($this->kelasId)
            ->mataPelajaranKelas->sortBy('id');

        $groupedBySiswa = $allRaports->groupBy('siswa_id');
        $aggregatedData = collect();

        foreach ($groupedBySiswa as $siswaId => $siswaRaports) {
            $firstRaport = $siswaRaports->first();
            $allDetails = $siswaRaports->pluck('raportDetail')->flatten();

            $totalNilaiSiswa = 0;
            $countMapelSiswa = 0;
            $mappedScores = [];

            foreach ($subjects as $mps) {
                $subjectDetails = $allDetails->where('mata_pelajaran_id', $mps->mata_pelajaran_id);
                $avgNilai = $subjectDetails->count() > 0 ? $subjectDetails->avg('nilai_akhir') : 0;
                $mappedScores[$mps->mata_pelajaran_id] = $avgNilai > 0 ? round($avgNilai) : '-';

                if ($avgNilai > 0) {
                    $totalNilaiSiswa += $avgNilai;
                    $countMapelSiswa++;
                }
            }

            $aggregatedData->push((object)[
                'id' => $siswaId,
                'siswa' => $firstRaport->siswa,
                'scores' => $mappedScores,
                'average_score' => $countMapelSiswa > 0 ? $totalNilaiSiswa / $countMapelSiswa : 0,
                'jumlah_sakit' => $siswaRaports->max('jumlah_sakit'),
                'jumlah_izin' => $siswaRaports->max('jumlah_izin'),
                'jumlah_alpha' => $siswaRaports->max('jumlah_alpha'),
            ]);
        }

        $sorted = $aggregatedData->sortByDesc('average_score')->values();
        foreach ($aggregatedData as $item) {
            $item->ranking = $sorted->search(fn ($s) => $s->id === $item->id) + 1;
        }

        return $aggregatedData;
    }

    public function headings(): array
    {
        $kelas = Kelas::with('mataPelajaranKelas.mataPelajaran')->find($this->kelasId);
        $subjects = $kelas->mataPelajaranKelas->sortBy('id')->pluck('mataPelajaran.kode')->toArray();

        return array_merge(['No', 'NIS', 'Nama Siswa'], $subjects, ['Rata-rata', 'Ranking', 'Sakit', 'Izin', 'Alpha']);
    }

    public function map($item): array
    {
        static $no = 1;

        return array_merge([
            $no++,
            $item->siswa->nis,
            $item->siswa->nama_lengkap,
        ], array_values($item->scores), [
            round($item->average_score, 2),
            $item->ranking,
            $item->jumlah_sakit,
            $item->jumlah_izin,
            $item->jumlah_alpha,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
