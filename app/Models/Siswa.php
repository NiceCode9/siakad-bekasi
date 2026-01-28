<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Siswa extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'orang_tua_id',
        'nisn',
        'nis',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'anak_ke',
        'jumlah_saudara',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'asal_sekolah',
        'tahun_lulus_smp',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'foto',
        'status',
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tinggi_badan' => 'decimal:2',
        'berat_badan' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class);
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'siswa_kelas')
            ->withPivot('tanggal_masuk', 'tanggal_keluar', 'status')
            ->withTimestamps();
    }

    public function kelasAktif()
    {
        return $this->belongsToMany(Kelas::class, 'siswa_kelas')
            ->wherePivot('status', 'aktif')
            ->withPivot('tanggal_masuk', 'tanggal_keluar', 'status')
            ->withTimestamps();
    }

    public function bukuInduk()
    {
        return $this->hasOne(BukuInduk::class);
    }

    public function prestasi()
    {
        return $this->hasMany(PrestasiSiswa::class);
    }

    public function pelanggaran()
    {
        return $this->hasMany(PelanggaranSiswa::class);
    }

    public function mutasi()
    {
        return $this->hasMany(MutasiSiswa::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function nilaiSikap()
    {
        return $this->hasMany(NilaiSikap::class);
    }

    public function nilaiEkstrakurikuler()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class);
    }

    public function ujianSiswa()
    {
        return $this->hasMany(UjianSiswa::class);
    }

    public function raport()
    {
        return $this->hasMany(Raport::class);
    }

    public function pkl()
    {
        return $this->hasMany(Pkl::class);
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeLulus($query)
    {
        return $query->where('status', 'lulus');
    }
}
