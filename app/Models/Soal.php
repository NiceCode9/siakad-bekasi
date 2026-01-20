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
        'metadata',
];

    protected $casts = [
        'bobot' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function soalOpsi()
    {
        return $this->hasMany(SoalOpsi::class);
    }

    public function soalUjian()
    {
        return $this->hasMany(SoalUjian::class);
    }

    // Relasi khusus untuk PG Kompleks
    public function pernyataan()
    {
        return $this->hasMany(SoalOpsi::class)->where('tipe', 'pernyataan')->orderBy('urutan');
    }

    // Relasi khusus untuk Menjodohkan
    public function kolomKiri()
    {
        return $this->hasMany(SoalOpsi::class)->where('tipe', 'kolom_kiri')->orderBy('urutan');
    }

    public function kolomKanan()
    {
        return $this->hasMany(SoalOpsi::class)->where('tipe', 'kolom_kanan')->orderBy('urutan');
    }

    // Scopes
    public function scopePilihanGanda($query)
    {
        return $query->where('tipe_soal', 'pilihan_ganda');
    }

    public function scopePilihanGandaKompleks($query)
    {
        return $query->where('tipe_soal', 'pilihan_ganda_kompleks');
    }

    public function scopeMenjodohkan($query)
    {
        return $query->where('tipe_soal', 'menjodohkan');
    }

    public function scopeIsianSingkat($query)
    {
        return $query->where('tipe_soal', 'isian_singkat');
    }

    public function scopeUraian($query)
    {
        return $query->where('tipe_soal', 'uraian');
    }

    // Helper Methods
    public function isPilihanGanda()
    {
        return $this->tipe_soal === 'pilihan_ganda';
    }

    public function isPilihanGandaKompleks()
    {
        return $this->tipe_soal === 'pilihan_ganda_kompleks';
    }

    public function isMenjodohkan()
    {
        return $this->tipe_soal === 'menjodohkan';
    }

    public function isIsianSingkat()
    {
        return $this->tipe_soal === 'isian_singkat';
    }

    public function isUraian()
    {
        return $this->tipe_soal === 'uraian';
    }

    public function isAutoGrading()
    {
        return in_array($this->tipe_soal, [
            'pilihan_ganda',
            'pilihan_ganda_kompleks',
            'menjodohkan',
            'isian_singkat'
        ]);
    }

    public function needsManualGrading()
    {
        return $this->tipe_soal === 'uraian';
    }

    // Get Kunci Jawaban (parsed)
    public function getKunciJawabanParsed()
    {
        if (empty($this->kunci_jawaban)) {
            return null;
        }

        // Coba parse sebagai JSON
        $decoded = json_decode($this->kunci_jawaban, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Jika bukan JSON, return as string
        return $this->kunci_jawaban;
    }

    // Check Jawaban Benar (untuk auto-grading)
    public function checkJawaban($jawabanSiswa)
    {
        switch ($this->tipe_soal) {
            case 'pilihan_ganda':
                return $this->checkPilihanGanda($jawabanSiswa);

            case 'pilihan_ganda_kompleks':
                return $this->checkPilihanGandaKompleks($jawabanSiswa);

            case 'menjodohkan':
                return $this->checkMenjodohkan($jawabanSiswa);

            case 'isian_singkat':
                return $this->checkIsianSingkat($jawabanSiswa);

            case 'uraian':
                return null; // Perlu koreksi manual

            default:
                return false;
        }
    }

    // Check Pilihan Ganda
    private function checkPilihanGanda($jawaban)
    {
        return strtoupper(trim($jawaban)) === strtoupper(trim($this->kunci_jawaban));
    }

    // Check Pilihan Ganda Kompleks
    private function checkPilihanGandaKompleks($jawaban)
    {
        $kunci = $this->getKunciJawabanParsed();
        $jawabanArray = is_array($jawaban) ? $jawaban : json_decode($jawaban, true);

        if (!is_array($kunci) || !is_array($jawabanArray)) {
            return false;
        }

        // Sort untuk perbandingan
        sort($kunci);
        sort($jawabanArray);

        return $kunci === $jawabanArray;
    }

    // Check Menjodohkan
    private function checkMenjodohkan($jawaban)
    {
        $kunci = $this->getKunciJawabanParsed();
        $jawabanArray = is_array($jawaban) ? $jawaban : json_decode($jawaban, true);

        if (!is_array($kunci) || !is_array($jawabanArray)) {
            return false;
        }

        // Hitung berapa yang benar
        $benar = 0;
        $total = count($kunci);

        foreach ($kunci as $key => $value) {
            if (isset($jawabanArray[$key]) && $jawabanArray[$key] == $value) {
                $benar++;
            }
        }

        // Return persentase kebenaran
        return $total > 0 ? ($benar / $total) : 0;
    }

    // Check Isian Singkat
    private function checkIsianSingkat($jawaban)
    {
        $kunci = $this->getKunciJawabanParsed();

        // Jika kunci array (beberapa alternatif jawaban)
        if (is_array($kunci)) {
            foreach ($kunci as $alternatif) {
                if ($this->compareText($jawaban, $alternatif)) {
                    return true;
                }
            }
            return false;
        }

        // Jika kunci string tunggal
        return $this->compareText($jawaban, $kunci);
    }

    // Compare text (case-insensitive, trim)
    private function compareText($text1, $text2)
    {
        $t1 = strtolower(trim($text1));
        $t2 = strtolower(trim($text2));

        // Exact match
        if ($t1 === $t2) {
            return true;
        }

        // Similarity check (optional - untuk toleransi typo)
        similar_text($t1, $t2, $percent);
        return $percent >= 85; // 85% similarity = benar
    }

    // Get Opsi untuk display (PG standar)
    public function getOpsiArray()
    {
        $opsi = [];

        if ($this->opsi_a) $opsi['A'] = $this->opsi_a;
        if ($this->opsi_b) $opsi['B'] = $this->opsi_b;
        if ($this->opsi_c) $opsi['C'] = $this->opsi_c;
        if ($this->opsi_d) $opsi['D'] = $this->opsi_d;
        if ($this->opsi_e) $opsi['E'] = $this->opsi_e;

        return $opsi;
    }
}
