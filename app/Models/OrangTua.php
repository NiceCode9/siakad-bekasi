<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orang_tua';

    protected $fillable = [
        'user_id',
        'nik_ayah',
        'nama_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'penghasilan_ayah',
        'telepon_ayah',
        'nik_ibu',
        'nama_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'penghasilan_ibu',
        'telepon_ibu',
        'nama_wali',
        'pekerjaan_wali',
        'telepon_wali',
        'alamat',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
