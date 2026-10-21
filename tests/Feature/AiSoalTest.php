<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiSoalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->game = Game::factory()->create(['jenis' => 'kuis', 'aktif' => true]);
    }

    public function test_admin_dapat_membuat_preview_soal_ai_dengan_driver_mock(): void
    {
        $response = $this->actingAs($this->admin)->postJson(
            route('admin.soals.generate-ai', $this->game),
            ['topik' => 'Pecahan kelas 4', 'jumlah' => 3, 'kesulitan' => 'sedang', 'driver' => 'mock']
        );

        $response->assertOk()
            ->assertJsonStructure(['soals' => [['pertanyaan', 'pilihan', 'jawaban_benar', 'penjelasan']]])
            ->assertJsonCount(3, 'soals');

        // Kuis: jawaban wajib ada di dalam pilihan
        foreach ($response->json('soals') as $soal) {
            $this->assertContains($soal['jawaban_benar'], $soal['pilihan']);
        }
    }

    public function test_generate_ai_menolak_jumlah_melebihi_batas(): void
    {
        $this->actingAs($this->admin)->postJson(
            route('admin.soals.generate-ai', $this->game),
            ['topik' => 'topik', 'jumlah' => 999, 'kesulitan' => 'sedang', 'driver' => 'mock']
        )->assertStatus(422);
    }

    public function test_generate_ai_ditolak_bagi_non_admin(): void
    {
        $siswa = User::factory()->create(['role' => 'user']);

        $this->actingAs($siswa)->postJson(
            route('admin.soals.generate-ai', $this->game),
            ['topik' => 'topik', 'jumlah' => 2, 'kesulitan' => 'sedang', 'driver' => 'mock']
        )->assertForbidden();
    }

    public function test_save_ai_menyimpan_soal_terpilih(): void
    {
        $soals = [
            [
                'pertanyaan' => 'Hasil dari 2 + 3 = ?',
                'pilihan' => ['4', '5', '6'],
                'jawaban_benar' => '5',
                'penjelasan' => '2 + 3 = 5',
            ],
            [
                'pertanyaan' => 'Hasil dari 10 - 4 = ?',
                'pilihan' => ['5', '6', '7'],
                'jawaban_benar' => '6',
                'penjelasan' => null,
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.soals.save-ai', $this->game),
            ['soals' => $soals]
        );

        $response->assertOk()->assertJson(['count' => 2]);
        $this->assertDatabaseHas('soals', ['game_id' => $this->game->id, 'pertanyaan' => 'Hasil dari 2 + 3 = ?']);
        $this->assertSame(2, $this->game->soals()->count());
    }

    public function test_save_ai_melewati_soal_kuis_dengan_jawaban_di_luar_pilihan(): void
    {
        $soals = [
            ['pertanyaan' => 'Valid', 'pilihan' => ['A', 'B'], 'jawaban_benar' => 'A', 'penjelasan' => null],
            ['pertanyaan' => 'Tidak valid', 'pilihan' => ['A', 'B'], 'jawaban_benar' => 'Z', 'penjelasan' => null],
        ];

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.soals.save-ai', $this->game),
            ['soals' => $soals]
        );

        $response->assertOk()->assertJson(['count' => 1]);
        $this->assertSame(1, $this->game->soals()->count());
    }

    public function test_save_ai_wajib_memilih_minimal_satu_soal(): void
    {
        $this->actingAs($this->admin)->postJson(
            route('admin.soals.save-ai', $this->game),
            ['soals' => []]
        )->assertStatus(422);
    }
}
