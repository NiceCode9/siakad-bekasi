<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class SoalTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SoalTemplateDataSheet(),
            new SoalTemplatePetunjukSheet(),
        ];
    }
}

class SoalTemplateDataSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return new Collection([
            [
                'pilihan_ganda',
                'Contoh Pertanyaan PG?',
                'Opsi A',
                'Opsi B',
                'Opsi C',
                'Opsi D',
                'Opsi E',
                'A',
                'mudah',
                '2'
            ],
            [
                'isian_singkat',
                'Ibukota Jawa Timur adalah?',
                '', '', '', '', '',
                'Surabaya',
                'mudah',
                '2'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'tipe_soal',
            'pertanyaan',
            'opsi_a',
            'opsi_b',
            'opsi_c',
            'opsi_d',
            'opsi_e',
            'kunci_jawaban',
            'tingkat_kesulitan',
            'bobot'
        ];
    }

    public function title(): string
    {
        return 'Template Soal';
    }
}

class SoalTemplatePetunjukSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return new Collection([
            ['tipe_soal', "Harus diisi dengan salah satu nilai berikut:\n - pilihan_ganda\n - isian_singkat\n - uraian"],
            ['pertanyaan', 'Tuliskan teks soal atau pertanyaan di sini.'],
            ['opsi_a s/d opsi_e', 'Opsi pilihan jawaban (Hanya wajib diisi jika tipe_soal adalah pilihan_ganda).'],
            ['kunci_jawaban', "Untuk pilihan ganda: Ketikkan A, B, C, D, atau E.\nUntuk isian singkat: Ketikkan teks jawaban benarnya."],
            ['tingkat_kesulitan', "PENTING! Harus diisi persis dengan salah satu nilai berikut (huruf kecil semua):\n - mudah\n - sedang\n - sulit"],
            ['bobot', 'Angka desimal/bulat untuk bobot skor soal (Misal: 1, 2, atau 2.5). Jika dikosongkan, defaultnya adalah 1.'],
        ]);
    }

    public function headings(): array
    {
        return [
            'NAMA KOLOM',
            'PETUNJUK PENGISIAN PADA SHEET "Template Soal"'
        ];
    }

    public function title(): string
    {
        return 'Petunjuk Pengisian';
    }
}
