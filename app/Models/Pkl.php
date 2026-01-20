<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pkl extends Model
{
    use HasFactory;

    protected $table = 'pkl';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'perusahaan_pkl_id',
        'pembimbing_sekolah_id',
        'pembimbing_industri',
        'jabatan_pembimbing_industri',
        'telepon_pembimbing_industri',
        'tanggal_mulai',
        'tanggal_selesai',
        'posisi',
        'divisi',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
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

    public function perusahaanPkl()
    {
        return $this->belongsTo(PerusahaanPkl::class);
    }

    public function pembimbingSekolah()
    {
        return $this->belongsTo(Guru::class, 'pembimbing_sekolah_id');
    }

    public function monitoringPkl()
    {
        return $this->hasMany(MonitoringPkl::class);
    }

    public function nilaiPkl()
    {
        return $this->hasOne(NilaiPkl::class);
    }

    public function sertifikatPkl()
    {
        return $this->hasOne(SertifikatPkl::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}
