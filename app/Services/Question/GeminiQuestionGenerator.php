<?php

namespace App\Services\Question;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Driver Google Gemini (generativelanguage.googleapis.com).
 * API key diambil dari database (input admin) dengan fallback .env — lihat AiSettings.
 */
class GeminiQuestionGenerator extends HttpQuestionGenerator
{
    public function name(): string
    {
        return 'gemini';
    }

    public function testKey(string $key = ''): string
    {
        $key = trim($key !== '' ? $key : AiSettings::key('gemini'));
        if ($key === '') {
            throw new QuestionGenerationException('API key Gemini belum diisi.');
        }

        try {
            $response = Http::timeout((int) config('ai.timeout', 45))
                ->withHeaders(['x-goog-api-key' => $key])
                ->get('https://generativelanguage.googleapis.com/v1beta/models');
        } catch (ConnectionException $e) {
            throw new QuestionGenerationException('Tidak bisa terhubung ke Gemini. Periksa koneksi internet. ('.$e->getMessage().')');
        }

        if ($response->failed()) {
            $pesan = $response->json('error.message') ?? 'HTTP '.$response->status();
            throw new QuestionGenerationException('API key Gemini tidak valid: '.$pesan);
        }

        $model = AiSettings::model('gemini');
        $namaModel = array_column($response->json('models') ?? [], 'name');
        $ada = in_array("models/{$model}", $namaModel, true);

        return 'Koneksi berhasil ✓ — key valid.'
            .($ada ? " Model \"{$model}\" tersedia." : " Perhatian: model \"{$model}\" tidak terdaftar pada key ini.");
    }

    protected function request(array $prompt): string
    {
        $key = AiSettings::key('gemini');
        if ($key === '') {
            throw new QuestionGenerationException('API key Gemini belum diisi. Atur pada menu Pengaturan AI atau file .env.');
        }

        $model = AiSettings::model('gemini') ?: 'gemini-3.8-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout((int) config('ai.timeout', 45))
                ->withHeaders(['x-goog-api-key' => $key, 'Content-Type' => 'application/json'])
                ->post($url, [
                    'systemInstruction' => ['parts' => [['text' => $prompt['system']]]],
                    'contents' => [['role' => 'user', 'parts' => [['text' => $prompt['user']]]]],
                    'generationConfig' => [
                        'temperature' => 0.9,
                        'responseMimeType' => 'application/json',
                    ],
                ]);
        } catch (ConnectionException $e) {
            throw new QuestionGenerationException('Tidak bisa terhubung ke Gemini. Periksa koneksi internet. ('.$e->getMessage().')');
        }

        if ($response->failed()) {
            $pesan = $response->json('error.message') ?? 'HTTP '.$response->status();
            throw new QuestionGenerationException('Gemini menolak permintaan: '.$pesan);
        }

        $teks = $response->json('candidates.0.content.parts.0.text');
        if (! is_string($teks) || trim($teks) === '') {
            throw new QuestionGenerationException('Gemini tidak mengembalikan teks (kemungkinan konten ditolak filter).');
        }

        return $teks;
    }
}
