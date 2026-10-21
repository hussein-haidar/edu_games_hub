<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    /** @use HasFactory<\Database\Factories\MapelFactory> */
    use HasFactory;

    protected $fillable = ['nama', 'slug', 'kelas', 'deskripsi', 'icon', 'maskot', 'warna', 'aktif', 'urutan'];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Pemetaan nama icon → emoji, supaya konsisten di semua halaman
     * (sebelumnya di-hardcode sebagai rantai ternary di banyak view).
     */
    private const ICON_EMOJI = [
        'calculator' => '🧮',
        'flask'      => '🧪',
        'book'       => '📖',
        'globe'      => '🌍',
        'shield'     => '🛡️',
        'music'      => '🎵',
        'flag'       => '🚩',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class)->orderBy('urutan');
    }

    public function gamesAktif(): HasMany
    {
        return $this->hasMany(Game::class)->where('aktif', true)->orderBy('urutan');
    }

    /**
     * Emoji untuk icon mapel ini (fallback: 📚).
     */
    public function getEmojiAttribute(): string
    {
        return self::ICON_EMOJI[$this->icon] ?? '📚';
    }

    /**
     * Maskot mapel: pakai `maskot` jika diisi admin, jika tidak pakai emoji icon.
     */
    public function getKarakterAttribute(): string
    {
        return $this->maskot ?: $this->emoji;
    }
}
