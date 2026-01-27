<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAkademiks = TahunAkademik::all();

        foreach ($tahunAkademiks as $ta) {
            // Extract years from kode (e.g., "2024/2025")
            $years = explode('/', $ta->kode);
            $year1 = $years[0];
            $year2 = $years[1];

            // Semester Ganjil (Odd - July to December)
            Semester::create([
                'tahun_akademik_id' => $ta->id,
                'nama' => 'Ganjil',
                'kode' => $ta->kode . '-1',
                'tanggal_mulai' => $year1 . '-07-15',
                'tanggal_selesai' => $year1 . '-12-23',
                'is_active' => $ta->is_active ? true : false,
            ]);

            // Semester Genap (Even - January to June)
            Semester::create([
                'tahun_akademik_id' => $ta->id,
                'nama' => 'Genap',
                'kode' => $ta->kode . '-2',
                'tanggal_mulai' => $year2 . '-01-06',
                'tanggal_selesai' => $year2 . '-06-30',
                'is_active' => false,
            ]);
        }
    }
}
