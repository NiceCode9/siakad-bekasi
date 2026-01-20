<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    use HasFactory;

    protected $table = 'jawaban_siswa';

    protected $fillable = [
        'ujian_siswa_id',
        'soal_ujian_id',
        'jawaban',
        'jawaban_detail',
        'is_benar',
        'nilai',
        'waktu_jawab',
        'ragu_ragu',
        'catatan_koreksi',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
        'nilai' => 'decimal:2',
        'waktu_jawab' => 'datetime',
        'ragu_ragu' => 'boolean',
        'jawaban_detail' => 'array',
    ];

    // Relationships
    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class);
    }

    public function soalUjian()
    {
        return $this->belongsTo(SoalUjian::class);
    }

    // Auto-grade jawaban
    public function autoGrade()
    {
        $soal = $this->soalUjian->soal;

        if (!$soal->isAutoGrading()) {
            return false; // Perlu koreksi manual
        }

        $result = $soal->checkJawaban($this->jawaban);

        if (is_bool($result)) {
            // Pilihan Ganda, PG Kompleks, Isian Singkat
            $this->is_benar = $result;
            $this->nilai = $result ? $soal->bobot : 0;
        } elseif (is_numeric($result)) {
            // Menjodohkan (partial scoring)
            $this->is_benar = $result >= 1;
            $this->nilai = $soal->bobot * $result;
        }

        $this->save();
        return true;
    }

    // Get jawaban parsed (untuk tipe kompleks)
    public function getJawabanParsed()
    {
        if (empty($this->jawaban)) {
            return null;
        }

        $decoded = json_decode($this->jawaban, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $this->jawaban;
    }
}
