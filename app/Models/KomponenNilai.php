<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenNilai extends Model
{
    use HasFactory;

    protected $table = 'komponen_nilai';

    protected $fillable = [
        'kurikulum_id',
        'kode',
        'nama',
        'kategori',
        'bobot',
        'keterangan',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
    ];

    // Relationships
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
