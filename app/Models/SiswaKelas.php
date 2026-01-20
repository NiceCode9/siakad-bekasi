<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaKelas extends Model
{
    use HasFactory;

    protected $table = 'siswa_kelas';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}
