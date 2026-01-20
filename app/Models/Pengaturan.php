<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'tipe',
        'kategori',
        'deskripsi',
    ];

    // Helpers
    public static function get($key, $default = null)
    {
        $setting = static::where('kunci', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->nilai, $setting->tipe);
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['kunci' => $key],
            ['nilai' => $value]
        );
    }

    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? +$value : 0;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
