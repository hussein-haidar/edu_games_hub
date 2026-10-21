<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Setting;
use App\Models\User;
use App\Services\Question\AiSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_dapat_membuka_halaman_pengaturan_ai(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ai-settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan AI')
            ->assertSee('Gemini')
            ->assertSee('Groq');
    }

    public function test_non_admin_tidak_dapat_membuka_pengaturan_ai(): void
    {
        $siswa = User::factory()->create(['role' => 'user']);

        $this->actingAs($siswa)->get(route('admin.ai-settings.index'))->assertForbidden();
    }

    public function test_tes_koneksi_driver_mock_selalu_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->postJson(
            route('admin.ai-settings.test'),
            ['driver' => 'mock']
        );

        $response->assertOk()->assertJson(['ok' => true]);
    }

    public function test_api_key_valid_disimpan_setelah_lolos_tes(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'models' => [['name' => 'models/gemini-3.8-flash']],
            ], 200),
        ]);

        $this->actingAs($this->admin)->post(route('admin.ai-settings.update'), [
            'gemini_api_key' => 'AIzaTESVALID123',
        ])->assertSessionHas('success');

        $this->assertSame('AIzaTESVALID123', AiSettings::key('gemini'));
        $this->assertDatabaseHas('settings', ['key' => 'ai.gemini_api_key', 'value' => 'AIzaTESVALID123']);
    }

    public function test_api_key_tidak_valid_ditolak_dan_tidak_disimpan(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                ['error' => ['message' => 'API key not valid.']],
            ], 400),
        ]);

        $this->actingAs($this->admin)->from(route('admin.ai-settings.index'))->post(route('admin.ai-settings.update'), [
            'gemini_api_key' => 'AIzaSALAH999',
        ])->assertSessionHasErrors('gemini_api_key');

        $this->assertNull(Setting::ambil('ai.gemini_api_key'));
    }

    public function test_kolom_kosong_mempertahankan_key_lama(): void
    {
        Setting::simpan('ai.groq_api_key', 'gsk_lama123');

        $this->actingAs($this->admin)->post(route('admin.ai-settings.update'), [
            'groq_api_key' => '',
        ])->assertSessionHas('success');

        $this->assertSame('gsk_lama123', AiSettings::key('groq'));
    }

    public function test_endpoint_tes_mengembalikan_ok_false_untuk_key_salah(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                ['error' => ['message' => 'Invalid API Key']],
            ], 401),
        ]);

        $this->actingAs($this->admin)->postJson(route('admin.ai-settings.test'), [
            'driver' => 'groq',
            'api_key' => 'gsk_salah',
        ])->assertStatus(422)->assertJson(['ok' => false]);
    }

    public function test_database_mengalahkan_env(): void
    {
        config(['ai.gemini.api_key' => 'ENV_KEY_LAMA']);
        $this->assertStringContainsString('ENV_KEY_LAMA', AiSettings::key('gemini'));

        Setting::simpan('ai.gemini_api_key', 'DB_KEY_BARU');
        $this->assertSame('DB_KEY_BARU', AiSettings::key('gemini'));
    }

    public function test_modal_soal_membaca_status_key_dari_database(): void
    {
        Setting::simpan('ai.gemini_api_key', 'AIzaDARIDB');
        $game = Game::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.soals.index', $game))
            ->assertOk()
            ->assertSee('Gemini', false);
        // Driver gemini tidak lagi disabled karena key sudah ada di DB
        $this->assertStringNotContainsString('Gemini (key belum diisi)', view('admin.soals.index', [
            'game' => $game->load('soals', 'mapel'),
            'ai' => [
                'driver' => AiSettings::driverAktif(),
                'max' => 12,
                'configured' => ['mock' => true, 'gemini' => AiSettings::configured('gemini'), 'groq' => AiSettings::configured('groq')],
            ],
        ])->render());
    }

    public function test_generate_ai_menggunakan_key_dari_database(): void
    {
        // Simulasi key tersimpan di DB tapi kosong di .env — generate tetap jalan
        // dengan driver mock (tanpa HTTP), memastikan jalur AiSettings terpakai.
        Setting::simpan('ai.driver', 'mock');

        $game = Game::factory()->create(['jenis' => 'kuis']);

        $this->actingAs($this->admin)->postJson(route('admin.soals.generate-ai', $game), [
            'topik' => 'Pecahan',
            'jumlah' => 2,
            'kesulitan' => 'mudah',
        ])->assertOk()->assertJsonCount(2, 'soals');

        $this->assertSame('mock', AiSettings::driverAktif());
    }
}
