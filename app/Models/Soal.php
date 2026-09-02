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
}
