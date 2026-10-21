<?php

namespace App\Services\Question;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Driver Groq (API kompatibel OpenAI) — model Llama/GPT-OSS.
 * API key diambil dari database (input admin) dengan fallback .env — lihat AiSettings.
 */
class GroqQuestionGenerator extends HttpQuestionGenerator
{
    public function name(): string
    {
        return 'groq';
    }

    public function testKey(string $key = ''): string
    {
        $key = trim($key !== '' ? $key : AiSettings::key('groq'));
        if ($key === '') {
            throw new QuestionGenerationException('API key Groq belum diisi.');
        }

        try {
            $response = Http::timeout((int) config('ai.timeout', 45))
                ->withHeaders(['Authorization' => 'Bearer '.$key])
                ->get('https://api.groq.com/openai/v1/models');
        } catch (ConnectionException $e) {
            throw new QuestionGenerationException('Tidak bisa terhubung ke Groq. Periksa koneksi internet. ('.$e->getMessage().')');
        }

        if ($response->failed()) {
            $pesan = $response->json('error.message') ?? 'HTTP '.$response->status();
            throw new QuestionGenerationException('API key Groq tidak valid: '.$pesan);
        }

        $model = AiSettings::model('groq');
        $namaModel = array_column($response->json('data') ?? [], 'id');
        $ada = in_array($model, $namaModel, true);

        return 'Koneksi berhasil ✓ — key valid.'
            .($ada ? " Model \"{$model}\" tersedia." : " Perhatian: model \"{$model}\" tidak terdaftar pada key ini.");
    }

    protected function request(array $prompt): string
    {
        $key = AiSettings::key('groq');
        if ($key === '') {
            throw new QuestionGenerationException('API key Groq belum diisi. Atur pada menu Pengaturan AI atau file .env.');
        }

        $model = AiSettings::model('groq') ?: 'openai/gpt-oss-120b';

        try {
            $response = Http::timeout((int) config('ai.timeout', 45))
                ->withHeaders(['Authorization' => 'Bearer '.$key, 'Content-Type' => 'application/json'])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'temperature' => 0.8,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $prompt['system']],
                        ['role' => 'user', 'content' => $prompt['user']],
                    ],
                ]);
        } catch (ConnectionException $e) {
            throw new QuestionGenerationException('Tidak bisa terhubung ke Groq. Periksa koneksi internet. ('.$e->getMessage().')');
        }

        if ($response->failed()) {
            $pesan = $response->json('error.message') ?? 'HTTP '.$response->status();
            throw new QuestionGenerationException('Groq menolak permintaan: '.$pesan);
        }

        $teks = $response->json('choices.0.message.content');
        if (! is_string($teks) || trim($teks) === '') {
            throw new QuestionGenerationException('Groq tidak mengembalikan konten yang valid.');
        }

        return $teks;
    }
}
