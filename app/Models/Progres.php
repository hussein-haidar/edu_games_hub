<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progres extends Model
{
    protected $fillable = ['user_id', 'game_id', 'skor', 'benar', 'salah', 'total'];

    protected $casts = [
        'skor' => 'integer',
        'benar' => 'integer',
        'salah' => 'integer',
        'total' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
