<?php

namespace App\Services\Question;

use App\Models\Game;

/**
 * Dasar bersama untuk driver AI berbasis HTTP (gemini, groq):
 * membangun prompt, mem-parse JSON respons, dan memvalidasi hasilnya
 * sesuai jenis game & struktur tabel `soals`.
 */
abstract class HttpQuestionGenerator implements QuestionGenerator
{
    public function generate(Game $game, string $topik, int $jumlah, string $kesulitan): array
    {
        $prompt = $this->buildPrompt($game, $topik, $jumlah, $kesulitan);
        $teks = $this->request($prompt);

        return $this->parse($teks, $game->jenis, $jumlah);
    }

    /**
     * Kirim prompt ke provider AI, kembalikan teks mentah respons.
     *
     * @param  array{system: string, user: string}  $prompt
     * @throws QuestionGenerationException
     */
    abstract protected function request(array $prompt): string;

    /**
     * @return array{system: string, user: string}
     */
    protected function buildPrompt(Game $game, string $topik, int $jumlah, string $kesulitan): array
    {
        $system = 'Anda adalah penulis soal latihan untuk siswa Sekolah Dasar (SD) di Indonesia. '
            .'Tulis soal berbahasa Indonesia baku, jelas, ramah anak, dan fakta-ilmiah sesuai kurikulum SD. '
            .'Respons WAJIB berupa JSON murni saja — tanpa markdown, tanpa komentar, tanpa teks apa pun di luar struktur JSON. '
            .'Jangan menulis anotasi seperti "(json)" di dalam nilai teks.';

        $format = match ($game->jenis) {
            'kuis' => 'Setiap objek wajib memiliki kunci: '
                .'"pertanyaan" (string), '
                .'"pilihan" (array JSON berisi tepat 4 string unik), '
                .'"jawaban_benar" (string yang persis sama dengan salah satu isi "pilihan"), '
                .'"penjelasan" (string, satu kalimat singkat). '
                .'Buat 3 pengecoh yang masuk akal namun jelas salah.',
            'bener_salah' => 'Setiap objek wajib memiliki kunci: '
                .'"pertanyaan" (string — berupa pernyataan yang bisa dinilai benar/salah), '
                .'"pilihan" (null), '
                .'"jawaban_benar" ("Benar" atau "Salah"), '
                .'"penjelasan" (string, satu kalimat). '
                .'Buat sekitar separuh pernyataan benar dan separuh salah.',
            'teka_teki' => 'Setiap objek wajib memiliki kunci: '
                .'"pertanyaan" (string — istilah/hal yang harus dipasangkan), '
                .'"pilihan" (null), '
                .'"jawaban_benar" (string — pasangan yang tepat dari pertanyaan), '
                .'"penjelasan" (string, satu kalimat alasan kecocokan).',
            'isian' => 'Setiap objek wajib memiliki kunci: '
                .'"pertanyaan" (string — kalimat yang mengandung titik-titik "...." untuk diisi siswa), '
                .'"pilihan" (null), '
                .'"jawaban_benar" (string — jawaban singkat 1–3 kata; boleh sertakan varian lain yang juga benar dipisah tanda |, mis. "paru-paru|paru paru"), '
                .'"penjelasan" (string, satu kalimat).',
            'urutkan' => 'Setiap objek wajib memiliki kunci: '
                .'"pertanyaan" (string — instruksi menyuruh siswa mengurutkan), '
                .'"urutan" (array JSON berisi 3–5 string item dalam URUTAN YANG BENAR, dari urutan pertama sampai terakhir), '
                .'"penjelasan" (string, satu kalimat). '
                .'Cocokkan dengan topik: kronologi kejadian, angka dari kecil ke besar, atau proses/prosedur yang berurutan.',
            'cocok_gambar' => 'Setiap objek wajib memiliki kunci: '
                .'"pertanyaan" (string — instruksi menjodohkan), '
                .'"pasangan" (array JSON berisi 3–5 pasangan; tiap pasangan berupa array [kiri, kanan] '
                .'dengan kiri = SATU emoji sederhana sebagai gambar (mis. "🍎") dan kanan = kata/keterangan yang tepat (mis. "Apel")), '
                .'"penjelasan" (string, satu kalimat). '
                .'Kiri maupun kanan harus unik antar pasangan dalam satu soal.',
            default => 'Setiap objek wajib memiliki kunci: pertanyaan, pilihan, jawaban_benar, penjelasan.',
        };

        $user = "Buat tepat {$jumlah} soal untuk game \"{$game->nama}\" (jenis: {$game->jenis}).\n"
            ."Mata pelajaran: {$game->mapel->nama}\n"
            ."Topik/materi: {$topik}\n"
            ."Tingkat kesulitan: {$kesulitan}\n\n"
            ."Format setiap soal: {$format}\n\n"
            ."Respons HARUS JSON murni berupa array [{\"pertanyaan\": ...}, ...] atau objek {\"soals\": [...]}, "
            ."berisi tepat {$jumlah} soal, tanpa teks lain.";

        return ['system' => $system, 'user' => $user];
    }

    /**
     * Parse teks respons AI menjadi array soal tervalidasi.
     *
     * @return array<int, array{pertanyaan: string, pilihan: ?array<int, string>, jawaban_benar: string, penjelasan: ?string}>
     * @throws QuestionGenerationException
     */
    protected function parse(string $teks, string $jenis, int $jumlah): array
    {
        $data = json_decode($this->extractJson($teks), true);

        // Beberapa model membungkus hasil: {"soals": [...]}
        if (is_array($data) && !array_is_list($data)) {
            foreach (['soals', 'questions', 'data', 'results'] as $kunci) {
                if (isset($data[$kunci]) && is_array($data[$kunci])) {
                    $data = $data[$kunci];
                    break;
                }
            }
        }

        if (!is_array($data)) {
            throw new QuestionGenerationException('Respons AI bukan JSON yang valid. Coba lagi atau ganti driver.');
        }

        $hasil = [];
        foreach ($data as $item) {
            if (! is_array($item)) {
                continue;
            }
            $soal = $this->sanitize($item, $jenis);
            if ($soal !== null) {
                $hasil[] = $soal;
            }
            if (count($hasil) >= $jumlah) {
                break;
            }
        }

        if ($hasil === []) {
            throw new QuestionGenerationException('AI tidak menghasilkan soal yang valid. Coba ubah topik atau ganti driver.');
        }

        return $hasil;
    }

    /**
     * Ambil potongan JSON dari teks (buang kode fence ```json ... ```).
     */
    protected function extractJson(string $teks): string
    {
        $teks = trim($teks);
        $teks = (string) preg_replace('/^```(?:json)?\s*/i', '', $teks);
        $teks = (string) preg_replace('/\s*```$/', '', $teks);

        if (json_decode($teks, true) !== null) {
            return $teks;
        }

        // Cadangan: ambil dari tanda [ pertama sampai ] terakhir
        $awal = strpos($teks, '[');
        $akhir = strrpos($teks, ']');
        if ($awal !== false && $akhir !== false && $akhir > $awal) {
            return substr($teks, $awal, $akhir - $awal + 1);
        }

        return $teks;
    }

    /**
     * Validasi & normalisasi satu item soal sesuai jenis game.
     * Kembalikan null jika tidak valid (akan difilter).
     *
     * @return array{pertanyaan: string, pilihan: ?array<int, string>, jawaban_benar: string, penjelasan: ?string}|null
     */
    protected function sanitize(array $item, string $jenis): ?array
    {
        $pertanyaan = trim((string) ($item['pertanyaan'] ?? ''));
        if ($pertanyaan === '') {
            return null;
        }

        $penjelasan = trim((string) ($item['penjelasan'] ?? ''));
        // Bersihkan artefak umum model, mis. ekor "(json)" yang menempel
        $penjelasan = (string) preg_replace('/\s*\((?:json|json object)\)\s*$/i', '', $penjelasan);
        $penjelasan = $penjelasan === '' ? null : $penjelasan;

        if ($jenis === 'kuis') {
            $pilihan = $item['pilihan'] ?? [];
            if (! is_array($pilihan)) {
                return null;
            }
            $pilihan = array_values(array_unique(array_filter(array_map(
                fn ($p) => mb_substr(trim((string) $p), 0, 255),
                $pilihan
            ), fn ($p) => $p !== '')));
            $pilihan = array_slice($pilihan, 0, 6);
            if (count($pilihan) < 2) {
                return null;
            }

            // Jawaban harus cocok dengan salah satu pilihan (case-insensitive)
            $jawaban = trim((string) ($item['jawaban_benar'] ?? ''));
            $cocok = null;
            foreach ($pilihan as $p) {
                if (mb_strtolower($p) === mb_strtolower($jawaban)) {
                    $cocok = $p;
                    break;
                }
            }
            if ($cocok === null) {
                return null;
            }

            return [
                'pertanyaan' => mb_substr($pertanyaan, 0, 255),
                'pilihan' => $pilihan,
                'jawaban_benar' => $cocok,
                'penjelasan' => $penjelasan,
            ];
        }

        if ($jenis === 'bener_salah') {
            $j = mb_strtolower(trim((string) ($item['jawaban_benar'] ?? '')));
            $jawaban = match (true) {
                in_array($j, ['benar', 'true', 'ya', 'iya'], true) => 'Benar',
                in_array($j, ['salah', 'false', 'tidak'], true) => 'Salah',
                default => null,
            };
            if ($jawaban === null) {
                return null;
            }

            return [
                'pertanyaan' => mb_substr($pertanyaan, 0, 255),
                'pilihan' => null,
                'jawaban_benar' => $jawaban,
                'penjelasan' => $penjelasan,
            ];
        }

        if ($jenis === 'isian') {
            $jawaban = trim((string) ($item['jawaban_benar'] ?? ''));
            if ($jawaban === '') {
                return null;
            }

            // Kumpulkan varian jawaban yang juga diterima (AI boleh mengisi "alternatif")
            $varian = [$jawaban];
            foreach ((array) ($item['alternatif'] ?? []) as $alt) {
                $alt = trim((string) $alt);
                if ($alt !== '') {
                    $varian[] = $alt;
                }
            }
            $varian = array_values(array_unique($varian));

            // Batasi total gabungan varian agar tetap wajar
            $gabung = '';
            $jaga = [];
            foreach ($varian as $v) {
                $v = mb_substr($v, 0, 100);
                $kandidat = $gabung === '' ? $v : $gabung.'|'.$v;
                if (mb_strlen($kandidat) > 255) {
                    break;
                }
                $jaga[] = $v;
                $gabung = $kandidat;
            }

            return [
                'pertanyaan' => mb_substr($pertanyaan, 0, 255),
                'pilihan' => null,
                'jawaban_benar' => $gabung,
                'penjelasan' => $penjelasan,
            ];
        }

        if ($jenis === 'urutkan') {
            $items = $item['urutan'] ?? $item['pilihan'] ?? [];
            if (! is_array($items)) {
                return null;
            }
            $items = array_values(array_unique(array_filter(array_map(
                fn ($p) => mb_substr(trim((string) $p), 0, 255),
                $items
            ), fn ($p) => $p !== '')));
            $items = array_slice($items, 0, 8);
            if (count($items) < 2) {
                return null;
            }

            return [
                'pertanyaan' => mb_substr($pertanyaan, 0, 255),
                'pilihan' => $items, // urutan benar
                'jawaban_benar' => implode(' → ', $items),
                'penjelasan' => $penjelasan,
            ];
        }

        if ($jenis === 'cocok_gambar') {
            $input = $item['pasangan'] ?? $item['pilihan'] ?? [];
            if (! is_array($input)) {
                return null;
            }

            $pasangan = [];
            foreach ($input as $p) {
                $kiri = null;
                $kanan = null;

                if (is_string($p) && str_contains($p, '|')) {
                    [$kiri, $kanan] = array_map('trim', explode('|', $p, 2));
                } elseif (is_array($p)) {
                    if (isset($p['kiri'], $p['kanan'])) {
                        $kiri = trim((string) $p['kiri']);
                        $kanan = trim((string) $p['kanan']);
                    } elseif (isset($p[0], $p[1])) {
                        $kiri = trim((string) $p[0]);
                        $kanan = trim((string) $p[1]);
                    }
                }

                if ($kiri === null || $kanan === null || $kiri === '' || $kanan === '') {
                    continue;
                }

                $pasangan[] = ['kiri' => mb_substr($kiri, 0, 64), 'kanan' => mb_substr($kanan, 0, 255)];
                if (count($pasangan) >= 6) {
                    break;
                }
            }

            if (count($pasangan) < 2) {
                return null;
            }

            return [
                'pertanyaan' => mb_substr($pertanyaan, 0, 255),
                'pilihan' => $pasangan,
                'jawaban_benar' => implode(', ', array_map(fn ($p) => $p['kiri'].' = '.$p['kanan'], $pasangan)),
                'penjelasan' => $penjelasan,
            ];
        }

        // teka_teki dan jenis lain
        $jawaban = trim((string) ($item['jawaban_benar'] ?? ''));
        if ($jawaban === '') {
            return null;
        }

        return [
            'pertanyaan' => mb_substr($pertanyaan, 0, 255),
            'pilihan' => null,
            'jawaban_benar' => mb_substr($jawaban, 0, 255),
            'penjelasan' => $penjelasan,
        ];
    }
}
