<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Guru extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nuptk',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'telepon',
        'email',
        'status_kepegawaian',
        'tanggal_masuk',
        'foto',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function mataPelajaranKelas()
    {
        return $this->hasMany(MataPelajaranKelas::class);
    }


    public function bankSoal()
    {
        return $this->hasMany(BankSoal::class, 'pembuat_id');
    }

    public function pelanggaranDilaporkan()
    {
        return $this->hasMany(PelanggaranSiswa::class, 'pelapor_id');
    }

    public function pklDibimbing()
    {
        return $this->hasMany(Pkl::class, 'pembimbing_sekolah_id');
    }

    public function nilaiDiinput()
    {
        return $this->hasMany(Nilai::class, 'penginput_id');
    }

    public function nilaiSikapDiinput()
    {
        return $this->hasMany(NilaiSikap::class, 'penginput_id');
    }

    public function leggerGenerate()
    {
        return $this->hasMany(Legger::class, 'generated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getNamaLengkapGelarAttribute()
    {
        $nama = '';
        if ($this->gelar_depan) {
            $nama .= $this->gelar_depan . ' ';
        }
        $nama .= $this->nama_lengkap;
        if ($this->gelar_belakang) {
            $nama .= ', ' . $this->gelar_belakang;
        }
        return $nama;
    }
}
