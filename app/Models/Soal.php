<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $table = 'soal';

    protected $fillable = [
        'bank_soal_id',
        'tipe_soal',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'bobot',
        'pembahasan',
        'gambar',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
    ];

    // Relationships
    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function soalUjian()
    {
        return $this->hasMany(SoalUjian::class);
    }

    // Helpers
    public function isPilihanGanda()
    {
        return $this->tipe_soal === 'pilihan_ganda';
    }

    public function isEssay()
    {
        return $this->tipe_soal === 'essay';
    }
}
