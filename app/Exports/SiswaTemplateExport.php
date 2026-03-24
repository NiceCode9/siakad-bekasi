<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SiswaTemplateExport implements WithHeadings, WithTitle
{
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NISN',
            'NIS',
            'NIK (Opsional)',
            'Nama Lengkap',
            'Jenis Kelamin (L/P)',
            'Tempat Lahir (Opsional)',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Agama (Islam/Kristen/Katolik/Hindu/Buddha/Konghucu)',
            'Anak Ke (Opsional)',
            'Jumlah Saudara (Opsional)',
            'Alamat (Opsional)',
            'RT (Opsional)',
            'RW (Opsional)',
            'Kelurahan (Opsional)',
            'Kecamatan (Opsional)',
            'Kota (Opsional)',
            'Provinsi (Opsional)',
            'Kode Pos (Opsional)',
            'Telepon Siswa (Opsional)',
            'Email Siswa (Opsional)',
            'Asal Sekolah (Opsional)',
            'Tahun Lulus SMP (Opsional)',
            'Tinggi Badan (Opsional)',
            'Berat Badan (Opsional)',
            'Golongan Darah (A/B/AB/O) (Opsional)',
            'Nama Ayah (Opsional)',
            'Pekerjaan Ayah (Opsional)',
            'Nama Ibu (Opsional)',
            'Pekerjaan Ibu (Opsional)',
            'Alamat Ortu (Opsional)',
            'Telepon Ortu (Opsional)',
            'Tanggal Masuk (YYYY-MM-DD)',
            'Kode Kelas (Opsional)',
        ];
    }

    public function title(): string
    {
        return 'Template Data Siswa';
    }
}
