<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = ['mapel_id', 'nama', 'slug', 'jenis', 'deskripsi', 'icon', 'durasi_menit', 'poin_per_benar', 'aktif', 'urutan'];

    protected $casts = [
        'aktif' => 'boolean',
        'durasi_menit' => 'integer',
        'poin_per_benar' => 'integer',
    ];

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
