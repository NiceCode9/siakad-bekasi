<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'kurikulum';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'tahun_mulai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tahunAkademik()
    {
        return $this->hasMany(TahunAkademik::class);
    }

    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class);
    }

    public function komponenNilai()
    {
        return $this->hasMany(KomponenNilai::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
