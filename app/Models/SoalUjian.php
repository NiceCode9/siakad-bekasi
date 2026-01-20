<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalUjian extends Model
{
    use HasFactory;

    protected $table = 'soal_ujian';

    protected $fillable = [
        'jadwal_ujian_id',
        'soal_id',
        'urutan',
    ];

    // Relationships
    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }
}
