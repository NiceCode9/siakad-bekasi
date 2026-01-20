<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifikatPkl extends Model
{
    use HasFactory;

    protected $table = 'sertifikat_pkl';

    protected $fillable = [
        'pkl_id',
        'nomor_sertifikat',
        'tanggal_terbit',
        'file_sertifikat',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    // Relationships
    public function pkl()
    {
        return $this->belongsTo(Pkl::class);
    }
}
