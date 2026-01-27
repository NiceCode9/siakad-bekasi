<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guruRole = Role::where('name', 'guru')->first();

        $gurus = [
            [
                'nip' => '198501012010011001',
                'nuptk' => '1234567890123456',
                'nama_lengkap' => 'Budi Santoso',
                'gelar_belakang' => 'S.Kom',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-01-01',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_kepegawaian' => 'PNS',
                'telepon' => '081234567801',
                'email' => 'budi.santoso@smk.sch.id',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'foto' => null,
            ],
            [
                'nip' => '198702152011012002',
                'nuptk' => '2345678901234567',
                'nama_lengkap' => 'Siti Nurhaliza',
                'gelar_belakang' => 'S.Pd',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1987-02-15',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_kepegawaian' => 'PNS',
                'telepon' => '081234567802',
                'email' => 'siti.nurhaliza@smk.sch.id',
                'alamat' => 'Jl. Sudirman No. 45, Bandung',
                'foto' => null,
            ],
            [
                'nip' => '198903202012011003',
                'nuptk' => '3456789012345678',
                'nama_lengkap' => 'Ahmad Fauzi',
                'gelar_belakang' => 'S.T',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1989-03-20',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_kepegawaian' => 'GTY',
                'telepon' => '081234567803',
                'email' => 'ahmad.fauzi@smk.sch.id',
                'alamat' => 'Jl. Pemuda No. 78, Surabaya',
                'foto' => null,
            ],
            [
                'nip' => '199004102013012004',
                'nuptk' => '4567890123456789',
                'nama_lengkap' => 'Dewi Lestari',
                'gelar_belakang' => 'S.Kom',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1990-04-10',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_kepegawaian' => 'GTY',
                'telepon' => '081234567804',
                'email' => 'dewi.lestari@smk.sch.id',
                'alamat' => 'Jl. Malioboro No. 12, Yogyakarta',
                'foto' => null,
            ],
            [
                'nip' => '198805252014011005',
                'nuptk' => '5678901234567890',
                'nama_lengkap' => 'Rudi Hermawan',
                'gelar_belakang' => 'S.Pd',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1988-05-25',
                'jenis_kelamin' => 'L',
                'agama' => 'Kristen',
                'status_kepegawaian' => 'PNS',
                'telepon' => '081234567805',
                'email' => 'rudi.hermawan@smk.sch.id',
                'alamat' => 'Jl. Pahlawan No. 56, Semarang',
                'foto' => null,
            ],
            [
                'nip' => '199106302015012006',
                'nuptk' => '6789012345678901',
                'nama_lengkap' => 'Rina Wulandari',
                'gelar_belakang' => 'S.Sn',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1991-06-30',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_kepegawaian' => 'GTY',
                'telepon' => '081234567806',
                'email' => 'rina.wulandari@smk.sch.id',
                'alamat' => 'Jl. Slamet Riyadi No. 89, Solo',
                'foto' => null,
            ],
            [
                'nip' => '198607152016011007',
                'nuptk' => '7890123456789012',
                'nama_lengkap' => 'Hendra Wijaya',
                'gelar_belakang' => 'S.Kom',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1986-07-15',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_kepegawaian' => 'PNS',
                'telepon' => '081234567807',
                'email' => 'hendra.wijaya@smk.sch.id',
                'alamat' => 'Jl. Ijen No. 34, Malang',
                'foto' => null,
            ],
            [
                'nip' => '199208202017012008',
                'nuptk' => '8901234567890123',
                'nama_lengkap' => 'Maya Sari',
                'gelar_belakang' => 'S.E',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '1992-08-20',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_kepegawaian' => 'GTY',
                'telepon' => '081234567808',
                'email' => 'maya.sari@smk.sch.id',
                'alamat' => 'Jl. Cut Mutia No. 67, Bekasi',
                'foto' => null,
            ],
            [
                'nip' => '198509052018011009',
                'nuptk' => '9012345678901234',
                'nama_lengkap' => 'Agus Setiawan',
                'gelar_belakang' => 'S.Pd',
                'tempat_lahir' => 'Tangerang',
                'tanggal_lahir' => '1985-09-05',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_kepegawaian' => 'PNS',
                'telepon' => '081234567809',
                'email' => 'agus.setiawan@smk.sch.id',
                'alamat' => 'Jl. Gatot Subroto No. 90, Tangerang',
                'foto' => null,
            ],
            [
                'nip' => '199310122019012010',
                'nuptk' => '0123456789012345',
                'nama_lengkap' => 'Fitri Handayani',
                'gelar_belakang' => 'S.Pd',
                'tempat_lahir' => 'Depok',
                'tanggal_lahir' => '1993-10-12',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_kepegawaian' => 'GTY',
                'telepon' => '081234567810',
                'email' => 'fitri.handayani@smk.sch.id',
                'alamat' => 'Jl. Margonda Raya No. 123, Depok',
                'foto' => null,
            ],
            [
                'nip' => '198811252020011011',
                'nuptk' => '1234567890123457',
                'nama_lengkap' => 'Bambang Prasetyo',
                'gelar_belakang' => 'S.T',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1988-11-25',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'status_kepegawaian' => 'PNS',
                'telepon' => '081234567811',
                'email' => 'bambang.prasetyo@smk.sch.id',
                'alamat' => 'Jl. Pajajaran No. 45, Bogor',
                'foto' => null,
            ],
            [
                'nip' => '199112082021012012',
                'nuptk' => '2345678901234568',
                'nama_lengkap' => 'Lina Marlina',
                'gelar_belakang' => 'S.Pd',
                'tempat_lahir' => 'Cirebon',
                'tanggal_lahir' => '1991-12-08',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'status_kepegawaian' => 'GTY',
                'telepon' => '081234567812',
                'email' => 'lina.marlina@smk.sch.id',
                'alamat' => 'Jl. Siliwangi No. 78, Cirebon',
                'foto' => null,
            ],
        ];

        foreach ($gurus as $guruData) {
            // Create user account
            $user = User::create([
                'username' => strtolower(str_replace([' ', ',', '.'], '', $guruData['nama_lengkap'])),
                'email' => $guruData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Assign role
            $user->assignRole($guruRole);

            // Create guru record
            $guru = Guru::create(array_merge($guruData, [
                'user_id' => $user->id,
            ]));
        }
    }
}
