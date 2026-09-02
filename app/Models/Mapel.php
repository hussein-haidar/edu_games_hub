<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    protected $fillable = ['nama', 'slug', 'kelas', 'deskripsi', 'icon', 'warna', 'aktif', 'urutan'];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class)->orderBy('urutan');
    }

    public function gamesAktif(): HasMany
    {
        return $this->hasMany(Game::class)->where('aktif', true)->orderBy('urutan');
    }
}
