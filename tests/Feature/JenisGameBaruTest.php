<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Progres;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JenisGameBaruTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ===== Admin: input manual =====

    public function test_admin_bisa_menyimpan_soal_urutkan_dari_textarea(): void
    {
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'urutkan']);

        $this->actingAs($admin)->post(route('admin.soals.store', $game), [
            'pertanyaan' => 'Urutkan hari dari awal minggu.',
            'urutan_items' => "Senin\nSelasa\nRabu",
            'penjelasan' => 'Minggu hari pertama.',
        ])->assertSessionHas('success');

        $soal = $game->soals()->first();
        $this->assertSame(['Senin', 'Selasa', 'Rabu'], $soal->pilihan);
        $this->assertSame('Senin → Selasa → Rabu', $soal->jawaban_benar);
    }

    public function test_soal_urutkan_wajib_minimal_dua_item(): void
    {
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'urutkan']);

        $this->actingAs($admin)->post(route('admin.soals.store', $game), [
            'pertanyaan' => 'Urutkan apa saja.',
            'urutan_items' => 'Cuma satu',
        ])->assertSessionHasErrors();

        $this->assertSame(0, $game->soals()->count());
    }

    public function test_admin_bisa_menyimpan_soal_cocok_gambar_dengan_emoji(): void
    {
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'cocok_gambar']);

        $this->actingAs($admin)->post(route('admin.soals.store', $game), [
            'pertanyaan' => 'Jodohkan gambar buah dengan namanya.',
            'pair' => [
                ['kiri' => '🍎', 'kanan' => 'Apel'],
                ['kiri' => '🍌', 'kanan' => 'Pisang'],
            ],
        ])->assertSessionHas('success');

        $soal = $game->soals()->first();
        $this->assertSame([
            ['kiri' => '🍎', 'kanan' => 'Apel'],
            ['kiri' => '🍌', 'kanan' => 'Pisang'],
        ], $soal->pilihan);
        $this->assertSame('🍎 = Apel, 🍌 = Pisang', $soal->jawaban_benar);
    }

    public function test_admin_bisa_menyimpan_soal_cocok_gambar_dengan_upload_foto(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'cocok_gambar']);

        $this->actingAs($admin)->post(route('admin.soals.store', $game), [
            'pertanyaan' => 'Jodohkan gambar dengan namanya.',
            'pair' => [
                ['kiri' => '', 'kanan' => 'Apel', 'gambar' => UploadedFile::fake()->create('apel.jpg', 50, 'image/jpeg')],
                ['kiri' => '🍌', 'kanan' => 'Pisang'],
            ],
        ])->assertSessionHas('success');

        $soal = $game->soals()->first();
        $this->assertStringStartsWith('gambar-soal/', $soal->pilihan[0]['kiri']);
        $this->assertSame('Apel', $soal->pilihan[0]['kanan']);
        Storage::disk('public')->assertExists($soal->pilihan[0]['kiri']);
    }

    public function test_soal_cocok_gambar_menolak_baris_tanpa_keterangan(): void
    {
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'cocok_gambar']);

        $this->actingAs($admin)->post(route('admin.soals.store', $game), [
            'pertanyaan' => 'Jodohkan gambar.',
            'pair' => [
                ['kiri' => '🍎', 'kanan' => 'Apel'],
                ['kiri' => '🍌', 'kanan' => ''], // kosong → galat
            ],
        ])->assertSessionHasErrors();

        $this->assertSame(0, $game->soals()->count());
    }

    // ===== Penilaian siswa =====

    public function test_submit_isian_tidak_peka_huruf_besar_dan_spasi(): void
    {
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);
        $game = Game::factory()->create(['jenis' => 'isian']);
        $soal = Soal::create([
            'game_id' => $game->id,
            'pertanyaan' => 'Alat pernapasan manusia adalah ....',
            'jawaban_benar' => 'paru-paru',
            'urutan' => 1,
        ]);

        $this->actingAs($user)->post(route('games.submit', $game), [
            'jawaban' => json_encode([$soal->id => '  Paru-Paru ']),
        ])->assertRedirect();

        $this->assertDatabaseHas('progres', [
            'user_id' => $user->id,
            'game_id' => $game->id,
            'benar' => 1,
        ]);
    }

    public function test_submit_isian_menerima_varian_jawaban(): void
    {
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);
        $game = Game::factory()->create(['jenis' => 'isian']);
        $soal = Soal::create([
            'game_id' => $game->id,
            'pertanyaan' => 'Alat pernapasan manusia adalah ....',
            'jawaban_benar' => 'paru-paru|paru paru',
            'urutan' => 1,
        ]);

        $this->actingAs($user)->post(route('games.submit', $game), [
            'jawaban' => json_encode([$soal->id => 'paru paru']),
        ])->assertRedirect();

        $this->assertSame(1, Progres::where('user_id', $user->id)->first()->benar);
    }

    public function test_submit_urutkan_urutan_benar_dihitung_benar(): void
    {
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);
        $game = Game::factory()->create(['jenis' => 'urutkan']);
        $soal = Soal::create([
            'game_id' => $game->id,
            'pertanyaan' => 'Urutkan hari.',
            'pilihan' => ['Senin', 'Selasa', 'Rabu'],
            'jawaban_benar' => 'Senin → Selasa → Rabu',
            'urutan' => 1,
        ]);

        $this->actingAs($user)->post(route('games.submit', $game), [
            'jawaban' => json_encode([$soal->id => ['Senin', 'Selasa', 'Rabu']]),
        ])->assertRedirect();

        $this->assertSame(1, Progres::where('user_id', $user->id)->first()->benar);
    }

    public function test_submit_urutkan_urutan_salah_dihitung_salah(): void
    {
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);
        $game = Game::factory()->create(['jenis' => 'urutkan']);
        $soal = Soal::create([
            'game_id' => $game->id,
            'pertanyaan' => 'Urutkan hari.',
            'pilihan' => ['Senin', 'Selasa', 'Rabu'],
            'jawaban_benar' => 'Senin → Selasa → Rabu',
            'urutan' => 1,
        ]);

        $this->actingAs($user)->post(route('games.submit', $game), [
            'jawaban' => json_encode([$soal->id => ['Rabu', 'Selasa', 'Senin']]),
        ])->assertRedirect();

        $this->assertSame(0, Progres::where('user_id', $user->id)->first()->benar);
    }

    public function test_submit_cocok_gambar_pasangan_benar_dihitung_benar(): void
    {
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);
        $game = Game::factory()->create(['jenis' => 'cocok_gambar']);
        $soal = Soal::create([
            'game_id' => $game->id,
            'pertanyaan' => 'Jodohkan gambar buah.',
            'pilihan' => [
                ['kiri' => '🍎', 'kanan' => 'Apel'],
                ['kiri' => '🍌', 'kanan' => 'Pisang'],
            ],
            'jawaban_benar' => '🍎 = Apel, 🍌 = Pisang',
            'urutan' => 1,
        ]);

        // Tukar urutan pasangan sekalipun tetap benar (yang penting kiri cocok kanan)
        $this->actingAs($user)->post(route('games.submit', $game), [
            'jawaban' => json_encode([$soal->id => [['🍌', 'Pisang'], ['🍎', 'Apel']]]),
        ])->assertRedirect();

        $this->assertSame(1, Progres::where('user_id', $user->id)->first()->benar);
    }

    public function test_submit_cocok_gambar_pasangan_salah_dihitung_salah(): void
    {
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);
        $game = Game::factory()->create(['jenis' => 'cocok_gambar']);
        $soal = Soal::create([
            'game_id' => $game->id,
            'pertanyaan' => 'Jodohkan gambar buah.',
            'pilihan' => [
                ['kiri' => '🍎', 'kanan' => 'Apel'],
                ['kiri' => '🍌', 'kanan' => 'Pisang'],
            ],
            'jawaban_benar' => '🍎 = Apel, 🍌 = Pisang',
            'urutan' => 1,
        ]);

        $this->actingAs($user)->post(route('games.submit', $game), [
            'jawaban' => json_encode([$soal->id => [['🍎', 'Pisang'], ['🍌', 'Apel']]]),
        ])->assertRedirect();

        $this->assertSame(0, Progres::where('user_id', $user->id)->first()->benar);
    }

    // ===== AI (driver mock) =====

    public function test_ai_mock_menghasilkan_struktur_valid_untuk_jenis_baru(): void
    {
        $admin = $this->admin();

        $urutkan = Game::factory()->create(['jenis' => 'urutkan']);
        $res = $this->actingAs($admin)->post(route('admin.soals.generate-ai', $urutkan), [
            'topik' => 'kronologi', 'jumlah' => 2, 'kesulitan' => 'sedang', 'driver' => 'mock',
        ])->assertOk()->json('soals');
        $this->assertIsArray($res[0]['pilihan']);
        $this->assertGreaterThanOrEqual(2, count($res[0]['pilihan']));
        $this->assertStringContainsString(' → ', $res[0]['jawaban_benar']);

        $isian = Game::factory()->create(['jenis' => 'isian']);
        $res = $this->actingAs($admin)->post(route('admin.soals.generate-ai', $isian), [
            'topik' => 'tumbuhan', 'jumlah' => 2, 'kesulitan' => 'mudah', 'driver' => 'mock',
        ])->assertOk()->json('soals');
        $this->assertNull($res[0]['pilihan']);
        $this->assertNotEmpty($res[0]['jawaban_benar']);

        $cocok = Game::factory()->create(['jenis' => 'cocok_gambar']);
        $res = $this->actingAs($admin)->post(route('admin.soals.generate-ai', $cocok), [
            'topik' => 'hewan', 'jumlah' => 2, 'kesulitan' => 'mudah', 'driver' => 'mock',
        ])->assertOk()->json('soals');
        $this->assertArrayHasKey('kiri', $res[0]['pilihan'][0]);
        $this->assertArrayHasKey('kanan', $res[0]['pilihan'][0]);
    }

    public function test_save_ai_cocok_gambar_menyimpan_pasangan(): void
    {
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'cocok_gambar']);

        $this->actingAs($admin)->postJson(route('admin.soals.save-ai', $game), [
            'soals' => [
                [
                    'pertanyaan' => 'Jodohkan gambar hewan.',
                    'pilihan' => [
                        ['kiri' => '🐘', 'kanan' => 'Gajah'],
                        ['kiri' => '🦒', 'kanan' => 'Jerapah'],
                    ],
                    'jawaban_benar' => '🐘 = Gajah, 🦒 = Jerapah',
                    'penjelasan' => 'Gajah dan jerapah sama-sama hewan darat besar.',
                ],
            ],
        ])->assertOk();

        $this->assertSame(1, $game->soals()->count());
        $this->assertSame('🐘', $game->soals()->first()->pilihan[0]['kiri']);
    }

    public function test_save_ai_urutkan_menyimpan_urutan(): void
    {
        $admin = $this->admin();
        $game = Game::factory()->create(['jenis' => 'urutkan']);

        $this->actingAs($admin)->postJson(route('admin.soals.save-ai', $game), [
            'soals' => [
                [
                    'pertanyaan' => 'Urutkan langkah.',
                    'pilihan' => ['Langkah 1', 'Langkah 2', 'Langkah 3'],
                    'jawaban_benar' => 'Langkah 1 → Langkah 2 → Langkah 3',
                    'penjelasan' => 'Urutan kegiatan sehari-hari.',
                ],
            ],
        ])->assertOk();

        $this->assertSame(['Langkah 1', 'Langkah 2', 'Langkah 3'], $game->soals()->first()->pilihan);
    }

    // ===== Label =====

    public function test_game_memiliki_label_jenis_yang_ramah(): void
    {
        $this->assertSame('Isian Singkat', Game::factory()->create(['jenis' => 'isian'])->jenis_label);
        $this->assertSame('Mengurutkan', Game::factory()->create(['jenis' => 'urutkan'])->jenis_label);
        $this->assertSame('Menjodohkan Gambar', Game::factory()->create(['jenis' => 'cocok_gambar'])->jenis_label);
    }

    // ===== Render halaman =====

    public function test_halaman_admin_soal_dan_halaman_main_tampil_untuk_jenis_baru(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['subscribed_until' => now()->addDay()]);

        foreach (['isian', 'urutkan', 'cocok_gambar'] as $jenis) {
            $game = Game::factory()->create(['jenis' => $jenis]);

            $data = match ($jenis) {
                'isian' => ['jawaban_benar' => 'paru-paru|paru paru'],
                'urutkan' => ['pilihan' => ['Senin', 'Selasa'], 'jawaban_benar' => 'Senin → Selasa'],
                'cocok_gambar' => [
                    'pilihan' => [
                        ['kiri' => '🍎', 'kanan' => 'Apel'],
                        ['kiri' => '🍌', 'kanan' => 'Pisang'],
                    ],
                    'jawaban_benar' => '🍎 = Apel, 🍌 = Pisang',
                ],
            };

            Soal::create([
                'game_id' => $game->id,
                'pertanyaan' => "Tes {$jenis} ...",
                'urutan' => 1,
            ] + $data);

            // Halaman input soal admin (form + modal edit per-jenis)
            $this->actingAs($admin)->get(route('admin.soals.index', $game))->assertOk();

            // Halaman main siswa (JS mode per-jenis)
            $this->actingAs($user)->get(route('games.show', $game))->assertOk();
        }
    }
}
