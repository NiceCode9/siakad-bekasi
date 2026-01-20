<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kurikulum_id',
        'kelompok_mapel_id',
        'kode',
        'nama',
        'jenis',
        'kategori',
        'kkm',
        'is_active',
    ];

    protected $casts = [
        'kkm' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function kelompokMapel()
    {
        return $this->belongsTo(KelompokMapel::class);
    }

    public function mataPelajaranKelas()
    {
        return $this->hasMany(MataPelajaranKelas::class);
    }

    public function bankSoal()
    {
        return $this->hasMany(BankSoal::class);
    }

    public function raportDetail()
    {
        return $this->hasMany(RaportDetail::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKejuruan($query)
    {
        return $query->where('jenis', 'kejuruan');
    }

    public function scopeUmum($query)
    {
        return $query->where('jenis', 'umum');
    }
}
