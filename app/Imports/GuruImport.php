<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Require nama_lengkap length check
        $namaLengkap = trim($row['nama_lengkap']);
        if (empty($namaLengkap)) {
            return null;
        }

        // Generate Username and Email safely
        $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaLengkap));
        $username = substr($usernameBase, 0, 10) . rand(100, 999);
        
        // Ensure uniqueness
        while (User::query()->where('username', $username)->exists()) {
            $username = substr($usernameBase, 0, 10) . rand(1000, 9999);
        }

        $email = $username . '@siakad.test';
        $password = 'password';

        // Clean enums and default values
        $jenisKelamin = strtoupper(trim($row['jenis_kelamin_lp'] ?? ''));
        if (!in_array($jenisKelamin, ['L', 'P'])) {
            $jenisKelamin = 'L'; // Default
        }

        $agamaInput = ucfirst(strtolower(trim($row['agama_islamkristenkatolikhindubuddhakonghucu'] ?? '')));
        $agamaValid = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
        $agama = in_array($agamaInput, $agamaValid) ? $agamaInput : 'Islam';

        $statusKepegawaian = strtoupper(trim($row['status_kepegawaian_pnspppkgtygtthonorer'] ?? ''));
        $statusValid = ['PNS', 'PPPK', 'GTY', 'GTT', 'Honorer'];
        $status_kepegawaian = in_array($statusKepegawaian, $statusValid) ? $statusKepegawaian : 'GTY';

        $tanggalLahir = null;
        if (!empty($row['tanggal_lahir_yyyy_mm_dd'])) {
            try {
                if (is_numeric($row['tanggal_lahir_yyyy_mm_dd'])) {
                    $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir_yyyy_mm_dd'])->format('Y-m-d');
                } else {
                    $tanggalLahir = date('Y-m-d', strtotime($row['tanggal_lahir_yyyy_mm_dd']));
                }
            } catch (\Exception $e) { }
        }
        
        $tanggalMasuk = null;
        if (!empty($row['tanggal_masuk_yyyy_mm_dd'])) {
            try {
                if (is_numeric($row['tanggal_masuk_yyyy_mm_dd'])) {
                    $tanggalMasuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_masuk_yyyy_mm_dd'])->format('Y-m-d');
                } else {
                    $tanggalMasuk = date('Y-m-d', strtotime($row['tanggal_masuk_yyyy_mm_dd']));
                }
            } catch (\Exception $e) { }
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'guru',
                'is_active' => true,
            ]);

            $guru = Guru::create([
                'user_id' => $user->id,
                'nip' => $row['nip_opsional'] ?? null,
                'nuptk' => $row['nuptk_opsional'] ?? null,
                'nama_lengkap' => $namaLengkap,
                'gelar_depan' => $row['gelar_depan_opsional'] ?? null,
                'gelar_belakang' => $row['gelar_belakang_opsional'] ?? null,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $row['tempat_lahir_opsional'] ?? null,
                'tanggal_lahir' => $tanggalLahir,
                'agama' => $agama,
                'alamat' => $row['alamat_opsional'] ?? null,
                'telepon' => $row['telepon_opsional'] ?? null,
                'status_kepegawaian' => $status_kepegawaian,
                'tanggal_masuk' => $tanggalMasuk,
                'email' => $email,
                'is_active' => true,
            ]);

            DB::commit();
            return $guru;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required',
        ];
    }
}
