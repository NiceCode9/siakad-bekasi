<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerusahaanPkl extends Model
{
    use HasFactory;

    protected $table = 'perusahaan_pkl';

    protected $fillable = [
        'nama',
        'bidang_usaha',
        'alamat',
        'telepon',
        'email',
        'nama_kontak',
        'jabatan_kontak',
        'telepon_kontak',
        'kuota',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function pkl()
    {
        return $this->hasMany(Pkl::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
