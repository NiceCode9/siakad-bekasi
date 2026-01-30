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
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }
}
