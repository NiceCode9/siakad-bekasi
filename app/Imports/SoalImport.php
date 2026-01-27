<?php

namespace App\Imports;

use App\Models\Soal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SoalImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $bankSoalId;

    public function __construct($bankSoalId)
    {
        $this->bankSoalId = $bankSoalId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Headers handled by WithHeadingRow (slugged usually, but let's assume keys match)
        // Template headers: tipe_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot
        
        return new Soal([
            'bank_soal_id'  => $this->bankSoalId,
            'tipe_soal'     => strtolower($row['tipe_soal']),
            'pertanyaan'    => $row['pertanyaan'],
            'opsi_a'        => $row['opsi_a'] ?? null,
            'opsi_b'        => $row['opsi_b'] ?? null,
            'opsi_c'        => $row['opsi_c'] ?? null,
            'opsi_d'        => $row['opsi_d'] ?? null,
            'opsi_e'        => $row['opsi_e'] ?? null,
            'kunci_jawaban' => $row['kunci_jawaban'],
            'bobot'         => is_numeric($row['bobot']) ? $row['bobot'] : 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'tipe_soal' => 'required|in:pilihan_ganda,isian_singkat,uraian',
            'pertanyaan' => 'required',
            'bobot' => 'numeric',
            // 'kunci_jawaban' => 'required_if:tipe_soal,pilihan_ganda' -- logic bit complex for excel validation, let's keep simple
        ];
    }
}
