<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Require nama_lengkap, nisn, and nis
        $namaLengkap = trim($row['nama_lengkap'] ?? '');
        $nisn = trim($row['nisn'] ?? '');
        $nis = trim($row['nis'] ?? '');

        if (empty($namaLengkap) || empty($nisn) || empty($nis)) {
            return null; // Skip if mandatory fields are missing
        }

        // Generate Username and Email safely
        $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaLengkap));
        $username = substr($usernameBase, 0, 10) . rand(100, 999);
        
        while (User::query()->where('username', $username)->exists()) {
            $username = substr($usernameBase, 0, 10) . rand(1000, 9999);
        }

        $email = $username . '@siakad.test';
        if (!empty($row['email_siswa_opsional'])) {
            $email = trim($row['email_siswa_opsional']);
        }
        $password = 'password';

        // Clean enums and default values
        $jenisKelamin = strtoupper(trim($row['jenis_kelamin_lp'] ?? ''));
        if (!in_array($jenisKelamin, ['L', 'P'])) {
            $jenisKelamin = 'L'; // Default
        }

        $agamaInput = ucfirst(strtolower(trim($row['agama_islamkristenkatolikhindubuddhakonghucu'] ?? '')));
        $agamaValid = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
        $agama = in_array($agamaInput, $agamaValid) ? $agamaInput : 'Islam';
        
        $golonganDarah = strtoupper(trim($row['golongan_darah_abab_o_opsional'] ?? ''));
        if (!in_array($golonganDarah, ['A', 'B', 'AB', 'O'])) {
            $golonganDarah = null;
        }

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
        
        $tanggalMasuk = now()->format('Y-m-d');
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
            // Check if Siswa already exists by NISN or NIS, if exists we throw an error (handled by validate)
            // But we already use WithValidation, so we can assume it's clean here.

            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'siswa',
                'is_active' => true,
            ]);

            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nisn' => $nisn,
                'nis' => $nis,
                'nik' => $row['nik_opsional'] ?? null,
                'nama_lengkap' => $namaLengkap,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $row['tempat_lahir_opsional'] ?? null,
                'tanggal_lahir' => $tanggalLahir,
                'agama' => $agama,
                'anak_ke' => $row['anak_ke_opsional'] ?? null,
                'jumlah_saudara' => $row['jumlah_saudara_opsional'] ?? null,
                'alamat' => $row['alamat_opsional'] ?? null,
                'rt' => $row['rt_opsional'] ?? null,
                'rw' => $row['rw_opsional'] ?? null,
                'kelurahan' => $row['kelurahan_opsional'] ?? null,
                'kecamatan' => $row['kecamatan_opsional'] ?? null,
                'kota' => $row['kota_opsional'] ?? null,
                'provinsi' => $row['provinsi_opsional'] ?? null,
                'kode_pos' => $row['kode_pos_opsional'] ?? null,
                'telepon' => $row['telepon_siswa_opsional'] ?? null,
                'email' => $email,
                'asal_sekolah' => $row['asal_sekolah_opsional'] ?? null,
                'tahun_lulus_smp' => $row['tahun_lulus_smp_opsional'] ?? null,
                'tinggi_badan' => $row['tinggi_badan_opsional'] ?? null,
                'berat_badan' => $row['berat_badan_opsional'] ?? null,
                'golongan_darah' => $golonganDarah,
                'nama_ayah' => $row['nama_ayah_opsional'] ?? null,
                'pekerjaan_ayah' => $row['pekerjaan_ayah_opsional'] ?? null,
                'nama_ibu' => $row['nama_ibu_opsional'] ?? null,
                'pekerjaan_ibu' => $row['pekerjaan_ibu_opsional'] ?? null,
                'alamat_ortu' => $row['alamat_ortu_opsional'] ?? null,
                'telepon_ortu' => $row['telepon_ortu_opsional'] ?? null,
                'status' => 'aktif',
                'tanggal_masuk' => $tanggalMasuk,
            ]);

            // Assign Kelas
            $kodeKelas = trim($row['kode_kelas_opsional'] ?? '');
            if (!empty($kodeKelas)) {
                $kelas = Kelas::where('kode', $kodeKelas)->first();
                if ($kelas) {
                    SiswaKelas::create([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $kelas->id,
                        'tanggal_masuk' => $tanggalMasuk,
                        'status' => 'aktif',
                    ]);
                }
            }

            DB::commit();
            return $siswa;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required',
            'nisn' => 'required|unique:siswa,nisn',
            'nis' => 'required|unique:siswa,nis',
        ];
    }
}
