<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    use HasFactory;

    protected $table = 'tahun_akademik';

    protected $fillable = [
        'kode',
        'nama',
        'kurikulum_id',
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
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function semester()
    {
        return $this->hasMany(Semester::class);
    }

    public function kenaikanKelas()
    {
        return $this->hasMany(KenaikanKelas::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
