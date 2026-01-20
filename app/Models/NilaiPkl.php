<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPkl extends Model
{
    use HasFactory;

    protected $table = 'nilai_pkl';

    protected $fillable = [
        'pkl_id',
        'nilai_sikap_kerja',
        'nilai_keterampilan',
        'nilai_laporan',
        'nilai_dari_industri',
        'nilai_dari_sekolah',
        'nilai_akhir',
        'catatan_industri',
        'catatan_sekolah',
        'file_laporan',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'nilai_sikap_kerja' => 'decimal:2',
        'nilai_keterampilan' => 'decimal:2',
        'nilai_laporan' => 'decimal:2',
        'nilai_dari_industri' => 'decimal:2',
        'nilai_dari_sekolah' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'tanggal_penilaian' => 'date',
    ];

    // Relationships
    public function pkl()
    {
        return $this->belongsTo(Pkl::class);
    }

    // Helpers
    public function hitungNilaiAkhir()
    {
        // Formula: (Sikap 30%) + (Keterampilan 40%) + (Laporan 30%)
        if ($this->nilai_sikap_kerja && $this->nilai_keterampilan && $this->nilai_laporan) {
            $this->nilai_akhir =
                ($this->nilai_sikap_kerja * 0.30) +
                ($this->nilai_keterampilan * 0.40) +
                ($this->nilai_laporan * 0.30);
            $this->save();
        }
        return $this->nilai_akhir;
    }
}
