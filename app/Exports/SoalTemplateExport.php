<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SoalTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
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
                '2'
            ],
            [
                'isian_singkat', 
                'Ibukota Jawa Timur adalah?', 
                '', '', '', '', '', 
                'Surabaya', 
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
            'bobot'
        ];
    }
}
