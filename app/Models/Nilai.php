<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Nilai extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'nilai';

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_kelas_id',
        'komponen_nilai_id',
        'semester_id',
        'jenis_nilai',
        'nilai',
        'keterangan',
        'ujian_siswa_id',
        'penginput_id',
        'tanggal_input',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'tanggal_input' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class);
    }

    public function komponenNilai()
    {
        return $this->belongsTo(KomponenNilai::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class);
    }

    public function penginput()
    {
        return $this->belongsTo(Guru::class, 'penginput_id');
    }

    // Scopes
    public function scopeUlanganHarian($query)
    {
        return $query->where('jenis_nilai', 'ulangan_harian');
    }

    public function scopeUts($query)
    {
        return $query->where('jenis_nilai', 'uts');
    }

    public function scopeUas($query)
    {
        return $query->where('jenis_nilai', 'uas');
    }
}
