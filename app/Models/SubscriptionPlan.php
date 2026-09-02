<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['nama', 'slug', 'harga', 'durasi_bulan', 'deskripsi', 'aktif', 'urutan'];

    protected $casts = [
        'harga' => 'float',
        'durasi_bulan' => 'integer',
        'aktif' => 'boolean',
    ];

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp'.number_format($this->harga, 0, ',', '.');
    }
}
