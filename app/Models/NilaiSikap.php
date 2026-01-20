<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSikap extends Model
{
    use HasFactory;

    protected $table = 'nilai_sikap';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'kelas_id',
        'aspek',
        'nilai',
        'predikat',
        'deskripsi',
        'penginput_id',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function penginput()
    {
        return $this->belongsTo(Guru::class, 'penginput_id');
    }

    // Scopes
    public function scopeSpiritual($query)
    {
        return $query->where('aspek', 'spiritual');
    }

    public function scopeSosial($query)
    {
        return $query->where('aspek', 'sosial');
    }
}
