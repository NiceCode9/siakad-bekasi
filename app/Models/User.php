<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'username',
        'email',
        'password',
        // 'role',
        'is_active',
        'last_login',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function orangTua()
    {
        return $this->hasOne(OrangTua::class);
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKepalaSekolah()
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isGuru()
    {
        return $this->role === 'guru' || $this->role === 'wali_kelas';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    public function isOrangTua()
    {
        return $this->role === 'orang_tua';
    }

    public function hasAccessToMenu(Menu $menu): bool
    {
        // Cek apakah user punya role yang di-assign ke menu
        $hasRoleAccess = $this->roles()
            ->whereHas('menus', function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->exists();

        if ($hasRoleAccess) {
            return true;
        }

        // Cek apakah user punya permission yang di-assign ke menu
        $hasPermissionAccess = $this->permissions()
            ->whereHas('menus', function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->exists();

        return $hasPermissionAccess;
    }
}
