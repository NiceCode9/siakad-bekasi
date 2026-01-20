<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiSiswa extends Model
{
    use HasFactory;

    protected $table = 'prestasi_siswa';

    protected $fillable = [
        'siswa_id',
        'jenis',
        'nama_prestasi',
        'tingkat',
        'peringkat',
        'penyelenggara',
        'tanggal',
        'keterangan',
        'file_sertifikat',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
