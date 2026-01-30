<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jurnal_mengajar';

    protected $fillable = [
        'jadwal_pelajaran_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'metode_pembelajaran',
        'hambatan',
        'solusi',
        'catatan',
        'jumlah_hadir',
        'jumlah_tidak_hadir',
        'is_approved',
        'approved_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'is_approved' => 'boolean',
    ];

    // Relationships
    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function presensiMapel()
    {
        return $this->hasMany(PresensiMapel::class);
    }
}
