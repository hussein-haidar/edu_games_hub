<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $fillable = ['mapel_id', 'nama', 'slug', 'jenis', 'deskripsi', 'icon', 'durasi_menit', 'poin_per_benar', 'aktif', 'urutan'];

    protected $casts = [
        'aktif' => 'boolean',
        'durasi_menit' => 'integer',
        'poin_per_benar' => 'integer',
    ];

    /**
     * Pemetaan nama icon → emoji, supaya konsisten di semua halaman.
     */
    private const ICON_EMOJI = [
        'calculator'  => '🧮',
        'flask'       => '🧪',
        'check-badge' => '✅',
        'puzzle'      => '🧩',
        'book'        => '📖',
        'globe'       => '🌍',
        'music'       => '🎵',
    ];

    /**
     * Pemetaan jenis game → nama yang ditampilkan ke manusia.
     */
    public const JENIS_LABEL = [
        'kuis' => 'Pilihan Ganda',
        'bener_salah' => 'Benar / Salah',
        'teka_teki' => 'Teka-Teki Jodohkan',
        'isian' => 'Isian Singkat',
        'urutkan' => 'Mengurutkan',
        'cocok_gambar' => 'Menjodohkan Gambar',
    ];

    /**
     * Emoji untuk icon game ini (fallback: 🎮).
     */
    public function getEmojiAttribute(): string
    {
        return self::ICON_EMOJI[$this->icon] ?? '🎮';
    }

    /**
     * Nama ramah untuk jenis game (fallback: kode jenis apa adanya).
     */
    public function getJenisLabelAttribute(): string
    {
        return self::JENIS_LABEL[$this->jenis] ?? $this->jenis;
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class)->orderBy('urutan');
    }

    public function progres(): HasMany
    {
        return $this->hasMany(Progres::class);
    }
}
