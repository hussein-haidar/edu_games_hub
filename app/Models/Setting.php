<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Penyimpanan pengaturan aplikasi (key-value).
 * Dipakai untuk API key AI yang diinput admin dari dashboard,
 * dengan prioritas: database > .env (config).
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai pengaturan. Kembalikan $default jika belum diatur.
     */
    public static function ambil(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Simpan (create/update) nilai pengaturan.
     */
    public static function simpan(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
