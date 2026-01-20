<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'semester_id',
        'jurusan_id',
        'tingkat',
        'nama',
        'kode',
        'wali_kelas_id',
        'kuota',
        'ruang_kelas',
    ];

    // Relationships
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_kelas')
            ->withPivot('tanggal_masuk', 'tanggal_keluar', 'status')
            ->withTimestamps();
    }

    public function mataPelajaranKelas()
    {
        return $this->hasMany(MataPelajaranKelas::class);
    }

    public function raport()
    {
        return $this->hasMany(Raport::class);
    }

    public function nilaiSikap()
    {
        return $this->hasMany(NilaiSikap::class);
    }

    public function legger()
    {
        return $this->hasMany(Legger::class);
    }

    // Scopes
    public function scopeTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }
}
