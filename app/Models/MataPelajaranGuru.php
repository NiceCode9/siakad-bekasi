<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaranGuru extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran_guru';

    protected $fillable = [
        'mata_pelajaran_kelas_id',
        'guru_id',
    ];

    // Relationships
    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class);
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
}
