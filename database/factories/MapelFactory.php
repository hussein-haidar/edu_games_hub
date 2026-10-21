<?php

namespace Database\Factories;

use App\Models\Mapel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mapel>
 */
class MapelFactory extends Factory
{
    protected $model = Mapel::class;

    public function definition(): array
    {
        static $urutan = 0;

        return [
            'nama' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(3),
            'kelas' => 'SD',
            'deskripsi' => fake()->sentence(),
            'icon' => 'calculator',
            'maskot' => '🦎',
            'warna' => '#4f46e5',
            'aktif' => true,
            'urutan' => ++$urutan,
        ];
    }
}
