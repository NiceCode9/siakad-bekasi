<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Raport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeggerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        $raports = Raport::with(['siswa', 'raportDetail.mataPelajaran'])
            ->where('kelas_id', $this->kelasId)
            ->where('semester_id', $this->semesterId)
            ->get();

        $subjects = Kelas::with('mataPelajaranKelas.mataPelajaran')
            ->find($this->kelasId)
            ->mataPelajaranKelas->pluck('mata_pelajaran_id')
            ->toArray();

        foreach ($raports as $raport) {
            $total = 0;
            $count = 0;
            foreach ($subjects as $subjectId) {
                $detail = $raport->raportDetail->where('mata_pelajaran_id', $subjectId)->first();
                $nilai = $detail ? round($detail->nilai_akhir) : 0;
                if ($nilai > 0) {
                    $total += $nilai;
                    $count++;
                }
            }
            $raport->average_score = $count > 0 ? $total / $count : 0;
        }

        $sorted = $raports->sortByDesc('average_score')->values();
        foreach ($raports as $raport) {
            $raport->ranking = $sorted->search(fn($item) => $item->id === $raport->id) + 1;
        }

        return $raports;
    }

    public function headings(): array
    {
        $kelas = Kelas::with('mataPelajaranKelas.mataPelajaran')->find($this->kelasId);
        $subjects = $kelas->mataPelajaranKelas->pluck('mataPelajaran.nama')->toArray();

        return array_merge(['No', 'NIS', 'Nama Siswa'], $subjects, ['Rata-rata', 'Ranking', 'Sakit', 'Izin', 'Alpha']);
    }

    public function map($raport): array
    {
        static $no = 1;
        $subjects = Kelas::with('mataPelajaranKelas.mataPelajaran')
            ->find($this->kelasId)
            ->mataPelajaranKelas->pluck('mata_pelajaran_id')
            ->toArray();

        $scores = [];
        foreach ($subjects as $subjectId) {
            $detail = $raport->raportDetail->where('mata_pelajaran_id', $subjectId)->first();
            $scores[] = $detail ? round($detail->nilai_akhir) : '-';
        }

        return array_merge([
            $no++,
            $raport->siswa->nis,
            $raport->siswa->nama,
        ], $scores, [
            round($raport->average_score, 2),
            $raport->ranking,
            $raport->jumlah_sakit,
            $raport->jumlah_izin,
            $raport->jumlah_alpha
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
