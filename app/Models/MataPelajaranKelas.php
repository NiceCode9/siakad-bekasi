<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaranKelas extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran_kelas';

    protected $fillable = [
        'mata_pelajaran_id',
        'kelas_id',
        'guru_id',
        'jam_per_minggu',
    ];

    // Relationships
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function materiAjar()
    {
        return $this->hasMany(MateriAjar::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function forumDiskusi()
    {
        return $this->hasMany(ForumDiskusi::class);
    }


    public function jadwalUjian()
    {
        return $this->hasMany(JadwalUjian::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
