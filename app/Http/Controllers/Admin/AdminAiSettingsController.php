<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Question\AiSettings;
use App\Services\Question\QuestionGenerationException;
use App\Services\Question\QuestionGeneratorManager;
use Illuminate\Http\Request;

/**
 * Pengaturan AI untuk pembuat soal: input API key Gemini & Groq dari dashboard.
 * Key disimpan pada tabel `settings` (prioritas di atas .env) dan
 * selalu dites validitasnya sebelum disimpan.
 */
class AdminAiSettingsController extends Controller
{
    public function index()
    {
        $info = $this->info();

        return view('admin.ai-settings.index', $info);
    }

    /**
     * Tes koneksi satu driver TANPA menyimpan (dipakai tombol "🧪 Tes Koneksi").
     * Menerima key dari kolom input; jika kosong, tes key yang sudah tersimpan.
     */
    public function test(Request $request, QuestionGeneratorManager $manager)
    {
        $data = $request->validate([
            'driver' => ['required', 'in:mock,gemini,groq'],
            'api_key' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $pesan = $manager->generator($data['driver'])->testKey(trim($data['api_key'] ?? ''));
        } catch (QuestionGenerationException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true, 'message' => $pesan]);
    }

    /**
     * Simpan pengaturan. API key yang berubah WAJIB lolos tes dulu —
     * key tidak valid ditolak dan tidak tersimpan.
     */
    public function update(Request $request, QuestionGeneratorManager $manager)
    {
        $data = $request->validate([
            'gemini_api_key' => ['nullable', 'string', 'max:500'],
            'groq_api_key' => ['nullable', 'string', 'max:500'],
        ]);

        $tersimpan = [];

        foreach (['gemini', 'groq'] as $driver) {
            $baru = trim((string) ($data[$driver.'_api_key'] ?? ''));

            // Kolom kosong = pertahankan key lama
            if ($baru === '') {
                continue;
            }

            // Wajib lolos tes sebelum disimpan
            try {
                $manager->generator($driver)->testKey($baru);
            } catch (QuestionGenerationException $e) {
                return back()
                    ->withInput()
                    ->withErrors([$driver.'_api_key' => $e->getMessage()])
                    ->with('error', 'API key '.ucfirst($driver).' tidak jadi disimpan — '.$e->getMessage());
            }

            Setting::simpan("ai.{$driver}_api_key", $baru);
            $tersimpan[] = ucfirst($driver);
        }

        if ($tersimpan === []) {
            return back()->with('success', 'Tidak ada perubahan API key.');
        }

        return back()->with('success', 'API key '.implode(' & ', $tersimpan).' tervalidasi dan berhasil disimpan.');
    }

    /**
     * Data untuk view: status tiap driver + mask key.
     *
     * @return array<string, mixed>
     */
    private function info(): array
    {
        $drivers = [
            'gemini' => [
                'label' => 'Google Gemini',
                'hint' => 'Gratis di aistudio.google.com → Get API key',
                'model' => AiSettings::model('gemini'),
            ],
            'groq' => [
                'label' => 'Groq',
                'hint' => 'Gratis di console.groq.com → API Keys',
                'model' => AiSettings::model('groq'),
            ],
        ];

        foreach ($drivers as $key => &$info) {
            $info['key_masked'] = AiSettings::masked($key);
            $info['configured'] = AiSettings::configured($key);
        }
        unset($info);

        return [
            'drivers' => $drivers,
            'driver_aktif' => AiSettings::driverAktif(),
            'max_per_request' => (int) config('ai.max_per_request', 12),
        ];
    }
}
