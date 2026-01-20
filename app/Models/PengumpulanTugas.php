<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'file_path',
        'jawaban',
        'tanggal_submit',
        'status',
        'nilai',
        'feedback',
        'tanggal_dinilai',
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
        'nilai' => 'decimal:2',
        'tanggal_dinilai' => 'date',
    ];

    // Relationships
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
