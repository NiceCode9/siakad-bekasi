<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiMapel extends Model
{
    use HasFactory;

    protected $table = 'presensi_mapel';

    protected $fillable = [
        'jurnal_mengajar_id',
        'siswa_id',
        'status',
        'keterangan',
    ];

    // Relationships
    public function jurnalMengajar()
    {
        return $this->belongsTo(JurnalMengajar::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
