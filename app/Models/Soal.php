<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Soal extends Model
{
    protected $fillable = ['game_id', 'pertanyaan', 'pilihan', 'jawaban_benar', 'penjelasan', 'urutan'];

    protected $casts = [
        'pilihan' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Apakah "gambar" kiri soal ini berupa file hasil upload (bukan emoji/teks)?
     */
    public static function gambarTerupload(string $kiri): bool
    {
        return str_starts_with($kiri, 'gambar-soal/');
    }

    /**
     * Hapus semua file gambar hasil upload yang dipakai soal ini (jika ada).
     * Dipanggil saat soal/game dihapus supaya tidak ada file yatim.
     */
    public function hapusGambarTerupload(): void
    {
        foreach ($this->pilihan ?? [] as $pasangan) {
            if (is_array($pasangan) && isset($pasangan['kiri']) && self::gambarTerupload((string) $pasangan['kiri'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pasangan['kiri']);
            }
        }
    }
}
