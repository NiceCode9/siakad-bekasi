<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenaikanKelasDetail extends Model
{
    use HasFactory;

    protected $table = 'kenaikan_kelas_detail';

    protected $fillable = [
        'kenaikan_kelas_id',
        'siswa_id',
        'kelas_asal_id',
        'kelas_tujuan_id',
        'status_kenaikan',
        'rata_rata_nilai',
        'jumlah_mapel_remidi',
        'total_absensi',
        'nilai_sikap',
        'alasan_tidak_naik',
    ];

    protected $casts = [
        'rata_rata_nilai' => 'decimal:2',
        'nilai_sikap' => 'decimal:2',
    ];

    // Relationships
    public function kenaikanKelas()
    {
        return $this->belongsTo(KenaikanKelas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelasAsal()
    {
        return $this->belongsTo(Kelas::class, 'kelas_asal_id');
    }

    public function kelasTujuan()
    {
        return $this->belongsTo(Kelas::class, 'kelas_tujuan_id');
    }
}
