<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'mata_pelajaran_guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruang',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    // Relationships
    public function mataPelajaranGuru()
    {
        return $this->belongsTo(MataPelajaranGuru::class);
    }

    public function jurnalMengajar()
    {
        return $this->hasMany(JurnalMengajar::class);
    }

    // Scopes
    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }
}
