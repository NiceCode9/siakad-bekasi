<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected $fillable = [
        'tahun_akademik_id',
        'nama',
        'kode',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function jadwalUjian()
    {
        return $this->hasMany(JadwalUjian::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function raport()
    {
        return $this->hasMany(Raport::class);
    }

    public function pkl()
    {
        return $this->hasMany(Pkl::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGanjil($query)
    {
        return $query->where('nama', 'Ganjil');
    }

    public function scopeGenap($query)
    {
        return $query->where('nama', 'Genap');
    }
}
