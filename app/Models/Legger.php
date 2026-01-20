<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legger extends Model
{
    use HasFactory;

    protected $table = 'legger';

    protected $fillable = [
        'kelas_id',
        'semester_id',
        'mata_pelajaran_id',
        'tanggal_generate',
        'generated_by',
        'file_path',
    ];

    protected $casts = [
        'tanggal_generate' => 'date',
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(Guru::class, 'generated_by');
    }
}
