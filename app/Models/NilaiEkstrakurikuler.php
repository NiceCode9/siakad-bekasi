<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiEkstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'nilai_ekstrakurikuler';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'nama_ekskul',
        'nilai',
        'predikat',
        'keterangan',
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
}
