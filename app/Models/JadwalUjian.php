<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUjian extends Model
{
    use HasFactory;

    protected $table = 'jadwal_ujian';

    protected $fillable = [
        'semester_id',
        'mata_pelajaran_kelas_id',
        'bank_soal_id',
        'jenis_ujian',
        'nama_ujian',
        'keterangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'jumlah_soal',
        'acak_soal',
        'acak_opsi',
        'tampilkan_nilai',
        'token',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'acak_soal' => 'boolean',
        'acak_opsi' => 'boolean',
        'tampilkan_nilai' => 'boolean',
    ];

    // Relationships
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class);
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function soalUjian()
    {
        return $this->hasMany(SoalUjian::class);
    }

    public function ujianSiswa()
    {
        return $this->hasMany(UjianSiswa::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Helpers
    public function isAktif()
    {
        return $this->status === 'aktif' &&
            now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }
}
