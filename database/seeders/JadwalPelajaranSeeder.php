<?php

namespace Database\Seeders;

use App\Models\JadwalPelajaran;
use App\Models\MataPelajaranKelas;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class JadwalPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = Kelas::all();
        
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Time slots (jam pelajaran)
        $jamSlots = [
            ['07:00', '07:45'],
            ['07:45', '08:30'],
            ['08:30', '09:15'],
            ['09:15', '10:00'],
            ['10:15', '11:00'], // Break 10:00-10:15
            ['11:00', '11:45'],
            ['12:00', '12:45'], // Break 11:45-12:00
            ['12:45', '13:30'],
            ['13:30', '14:15'],
            ['14:15', '15:00'],
        ];

        foreach ($kelas as $k) {
            $mapelKelas = MataPelajaranKelas::where('kelas_id', $k->id)->get();
            
            // Create distribution plan
            $schedule = [];
            foreach ($mapelKelas as $mk) {
                $jamPerMinggu = $mk->jam_per_minggu;
                for ($i = 0; $i < $jamPerMinggu; $i++) {
                    $schedule[] = $mk->id;
                }
            }

            // Shuffle for variety
            shuffle($schedule);

            // Distribute across days
            $slotIndex = 0;
            foreach ($hariList as $hari) {
                $maxSlotsPerDay = 8; // Max 8 jam per hari (excluding breaks)
                $slotsUsed = 0;

                while ($slotsUsed < $maxSlotsPerDay && $slotIndex < count($schedule)) {
                    $mkId = $schedule[$slotIndex];
                    $slot = $jamSlots[$slotsUsed];

                    JadwalPelajaran::create([
                        'mata_pelajaran_kelas_id' => $mkId,
                        'hari' => $hari,
                        'jam_mulai' => $slot[0],
                        'jam_selesai' => $slot[1],
                        'ruang' => $k->ruang_kelas ?? 'R-' . $k->id,
                    ]);

                    $slotIndex++;
                    $slotsUsed++;
                }

                // Stop if all subjects are scheduled
                if ($slotIndex >= count($schedule)) {
                    break;
                }
            }
        }
    }
}
