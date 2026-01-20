<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranSiswa extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_siswa';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jenis_pelanggaran',
        'kategori',
        'poin',
        'kronologi',
        'sanksi',
        'pelapor_id',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pelapor()
    {
        return $this->belongsTo(Guru::class, 'pelapor_id');
    }

    // Scopes
    public function scopeProses($query)
    {
        return $query->where('status', 'proses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}
