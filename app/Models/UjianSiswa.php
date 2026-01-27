<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianSiswa extends Model
{
    use HasFactory;

    protected $table = 'ujian_siswa';

    protected $fillable = [
        'jadwal_ujian_id',
        'siswa_id',
        'token_siswa',
        'waktu_mulai',
        'waktu_selesai',
        'waktu_submit',
        'sisa_waktu',
        'status',
        'nilai',
        'ip_address',
        'user_agent',
        'session_id',
        'violation_count',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'waktu_submit' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    // Relationships
    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    // Scopes
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeSedangMengerjakan($query)
    {
        return $query->where('status', 'sedang_mengerjakan');
    }
}
