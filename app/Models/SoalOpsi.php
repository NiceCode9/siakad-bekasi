<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalOpsi extends Model
{
    use HasFactory;

    protected $table = 'soal_opsi';

    protected $fillable = [
        'soal_id',
        'tipe',
        'urutan',
        'teks',
        'is_benar',
        'pasangan_id',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    // Relationships
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    // Untuk menjodohkan: relasi ke pasangan
    public function pasangan()
    {
        return $this->belongsTo(SoalOpsi::class, 'pasangan_id');
    }

    // Scopes
    public function scopePernyataan($query)
    {
        return $query->where('tipe', 'pernyataan');
    }

    public function scopeKolomKiri($query)
    {
        return $query->where('tipe', 'kolom_kiri');
    }

    public function scopeKolomKanan($query)
    {
        return $query->where('tipe', 'kolom_kanan');
    }
}
