<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'mata_pelajaran_kelas_id',
        'judul',
        'deskripsi',
        'file_lampiran',
        'tanggal_buat',
        'tanggal_deadline',
        'bobot',
        'is_published',
    ];

    protected $casts = [
        'tanggal_buat' => 'date',
        'tanggal_deadline' => 'datetime',
        'bobot' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    // Relationships
    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class);
    }


    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Helpers
    public function isTerlambat()
    {
        return now()->gt($this->tanggal_deadline);
    }
}
