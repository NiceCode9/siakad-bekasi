<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuruTemplateExport implements WithHeadings, WithTitle
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIP (Opsional)',
            'NUPTK (Opsional)',
            'Nama Lengkap',
            'Gelar Depan (Opsional)',
            'Gelar Belakang (Opsional)',
            'Jenis Kelamin (L/P)',
            'Tempat Lahir (Opsional)',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Agama (Islam/Kristen/Katolik/Hindu/Buddha/Konghucu)',
            'Alamat (Opsional)',
            'Telepon (Opsional)',
            'Status Kepegawaian (PNS/PPPK/GTY/GTT/Honorer)',
            'Tanggal Masuk (YYYY-MM-DD)',
        ];
    }

    public function title(): string
    {
        return 'Template Data Guru';
    }
}
