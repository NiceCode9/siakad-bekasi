<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaportDetail extends Model
{
    use HasFactory;

    protected $table = 'raport_detail';

    protected $fillable = [
        'raport_id',
        'mata_pelajaran_id',
        'nilai_pengetahuan',
        'nilai_keterampilan',
        'nilai_akhir',
        'predikat',
        'deskripsi',
        'jumlah_pertemuan',
        'jumlah_hadir',
        'persentase_kehadiran',
        'nilai_akhir_manual',
        'is_manual_override',
        'override_reason',
    ];

    protected $casts = [
        'nilai_pengetahuan' => 'decimal:2',
        'nilai_keterampilan' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'nilai_akhir_manual' => 'decimal:2',
        'is_manual_override' => 'boolean',
    ];

    // Relationships
    public function raport()
    {
        return $this->belongsTo(Raport::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // Helpers
    public function hitungNilaiAkhir()
    {
        if ($this->nilai_pengetahuan && $this->nilai_keterampilan) {
            $this->nilai_akhir = ($this->nilai_pengetahuan + $this->nilai_keterampilan) / 2;
            $this->predikat = $this->konversiPredikat($this->nilai_akhir);
            $this->save();
        }
        return $this->nilai_akhir;
    }

    private function konversiPredikat($nilai)
    {
        $kkm = MataPelajaranKelas::where('kelas_id', $this->raport->kelas_id)
            ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
            ->value('kkm') ?? 70;

        $interval = (100 - $kkm) / 3;

        if ($nilai >= (100 - $interval)) return 'A';
        if ($nilai >= ($kkm + $interval)) return 'B';
        if ($nilai >= $kkm) return 'C';
        return 'D';
    }
}
