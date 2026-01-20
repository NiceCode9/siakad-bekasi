<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokMapel extends Model
{
    use HasFactory;

    protected $table = 'kelompok_mapel';

    protected $fillable = [
        'kode',
        'nama',
        'urutan',
    ];

    // Relationships
    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class);
    }
}
