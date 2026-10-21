<?php

namespace App\Services\Question;

use App\Models\Game;

/**
 * Driver simulasi (mode demo/pengembangan) — tidak memanggil API mana pun.
 * Menghasilkan soal template dari topik supaya alur preview → simpan
 * bisa diuji tanpa internet maupun API key.
 */
class MockQuestionGenerator implements QuestionGenerator
{
    public function name(): string
    {
        return 'mock';
    }

    public function testKey(string $key = ''): string
    {
        return 'Driver mock selalu siap — tidak membutuhkan API key.';
    }

    public function generate(Game $game, string $topik, int $jumlah, string $kesulitan): array
    {
        $jumlah = max(1, min($jumlah, 20));
        $soals = [];

        for ($i = 1; $i <= $jumlah; $i++) {
            $soals[] = match ($game->jenis) {
                'kuis' => $this->kuis($topik, $i),
                'bener_salah' => $this->benerSalah($topik, $i),
                'isian' => $this->isian($topik, $i),
                'urutkan' => $this->urutkan($topik, $i),
                'cocok_gambar' => $this->cocokGambar($topik, $i),
                default => $this->tekaTeki($topik, $i),
            };
        }

        return $soals;
    }

    private function kuis(string $topik, int $i): array
    {
        $benar = "Contoh jawaban benar tentang {$topik} (no. {$i})";
        $pilihan = [
            $benar,
            "Pengecoh A tentang {$topik} (no. {$i})",
            "Pengecoh B tentang {$topik} (no. {$i})",
            "Pengecoh C tentang {$topik} (no. {$i})",
        ];
        // Geser posisi jawaban benar supaya tidak selalu di urutan pertama
        $geser = $i % 4;
        $pilihan = array_merge(array_slice($pilihan, $geser), array_slice($pilihan, 0, $geser));

        return [
            'pertanyaan' => "Soal contoh {$i} tentang {$topik}. Pernyataan mana yang benar?",
            'pilihan' => $pilihan,
            'jawaban_benar' => $benar,
            'penjelasan' => "Soal simulasi driver Mock tentang {$topik}. Gunakan driver Gemini/Groq untuk soal AI sungguhan.",
        ];
    }

    private function benerSalah(string $topik, int $i): array
    {
        $benar = $i % 2 === 1;

        return [
            'pertanyaan' => $benar
                ? "Mempelajari {$topik} bermanfaat untuk kehidupan sehari-hari (soal contoh {$i})."
                : "Topik {$topik} tidak perlu dipelajari di sekolah dasar (soal contoh {$i}).",
            'pilihan' => null,
            'jawaban_benar' => $benar ? 'Benar' : 'Salah',
            'penjelasan' => "Soal simulasi driver Mock tentang {$topik}.",
        ];
    }

    private function tekaTeki(string $topik, int $i): array
    {
        return [
            'pertanyaan' => "Istilah contoh {$i} dari topik {$topik}",
            'pilihan' => null,
            'jawaban_benar' => "Pasangan yang tepat untuk istilah contoh {$i}",
            'penjelasan' => "Soal simulasi driver Mock tentang {$topik}.",
        ];
    }

    private function isian(string $topik, int $i): array
    {
        return [
            'pertanyaan' => "Isian contoh {$i}: pelajari materi {$topik}, lalu isi titik-titik berikut ....",
            'pilihan' => null,
            'jawaban_benar' => "Jawaban contoh {$i} tentang {$topik}|varian jawaban no. {$i}",
            'penjelasan' => "Soal simulasi driver Mock tentang {$topik}. Gunakan driver Gemini/Groq untuk soal AI sungguhan.",
        ];
    }

    private function urutkan(string $topik, int $i): array
    {
        $items = [
            "Langkah pertama tentang {$topik}",
            "Langkah kedua tentang {$topik}",
            "Langkah ketiga tentang {$topik}",
            "Langkah keempat tentang {$topik}",
        ];

        return [
            'pertanyaan' => "Urutkan langkah {$i} tentang {$topik} dari awal sampai akhir.",
            'pilihan' => $items,
            'jawaban_benar' => implode(' → ', $items),
            'penjelasan' => "Soal simulasi driver Mock tentang {$topik}.",
        ];
    }

    private function cocokGambar(string $topik, int $i): array
    {
        $emojis = ['🍎', '🍌', '🍇', '🐘', '🦒', '🌻'];
        $pasangan = [];
        for ($n = 1; $n <= 3; $n++) {
            $pasangan[] = [
                'kiri' => $emojis[($i + $n) % count($emojis)],
                'kanan' => "Keterangan {$n} tentang {$topik} (no. {$i})",
            ];
        }

        return [
            'pertanyaan' => "Jodohkan gambar contoh {$i} dengan keterangan yang tepat tentang {$topik}.",
            'pilihan' => $pasangan,
            'jawaban_benar' => implode(', ', array_map(fn ($p) => $p['kiri'].' = '.$p['kanan'], $pasangan)),
            'penjelasan' => "Soal simulasi driver Mock tentang {$topik}.",
        ];
    }
}
