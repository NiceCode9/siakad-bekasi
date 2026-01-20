<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuInduk extends Model
{
    use HasFactory;

    protected $table = 'buku_induk';

    protected $fillable = [
        'siswa_id',
        'nomor_induk',
        'nomor_peserta_ujian',
        'nomor_seri_ijazah',
        'nomor_seri_skhun',
        'tanggal_lulus',
        'riwayat_pendidikan',
        'riwayat_kesehatan',
        'catatan_khusus',
    ];

    protected $casts = [
        'tanggal_lulus' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
