<?php

namespace App\Services\Question;

use App\Models\Game;

interface QuestionGenerator
{
    /**
     * Nama unik driver (mock, gemini, groq).
     */
    public function name(): string;

    /**
     * Tes validitas sebuah API key dengan memanggil endpoint ringan provider
     * (hanya daftar model — tidak menghasilkan token/biaya).
     *
     * @param  string  $key  API key yang akan dites. Kosongkan untuk memakai key aktif dari AiSettings.
     * @return string  Pesan sukses (mis. "Koneksi berhasil — model tersedia ...").
     *
     * @throws QuestionGenerationException  Jika key tidak valid / tidak ada koneksi.
     */
    public function testKey(string $key = ''): string;

    /**
     * Buat soal baru berdasarkan topik.
     *
     * @param  Game    $game     Game tujuan (jenis & mapel dipakai untuk merancang prompt/soal)
     * @param  string  $topik    Topik/materi ajar dari admin
     * @param  int     $jumlah   Jumlah soal yang diminta
     * @param  string  $kesulitan mudah | sedang | sulit
     * @return array<int, array{pertanyaan: string, pilihan: ?array<int, string>, jawaban_benar: string, penjelasan: ?string}>
     *
     * @throws QuestionGenerationException
     */
    public function generate(Game $game, string $topik, int $jumlah, string $kesulitan): array;
}
