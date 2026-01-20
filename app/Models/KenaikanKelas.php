<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenaikanKelas extends Model
{
    use HasFactory;

    protected $table = 'kenaikan_kelas';

    protected $fillable = [
        'tahun_akademik_id',
        'tanggal_proses',
        'status',
        'total_siswa',
        'total_naik',
        'total_tidak_naik',
        'processed_by',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_proses' => 'date',
    ];

    // Relationships
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function kenaikanKelasDetail()
    {
        return $this->hasMany(KenaikanKelasDetail::class);
    }
}
