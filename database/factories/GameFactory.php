<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Mapel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        static $urutan = 0;

        return [
            'mapel_id' => Mapel::factory(),
            'nama' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'jenis' => 'kuis',
            'deskripsi' => fake()->sentence(),
            'icon' => 'calculator',
            'durasi_menit' => 5,
            'poin_per_benar' => 10,
            'aktif' => true,
            'urutan' => ++$urutan,
        ];
    }
}
