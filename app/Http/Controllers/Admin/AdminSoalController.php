<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Soal;
use App\Services\Question\AiSettings;
use App\Services\Question\QuestionGenerationException;
use App\Services\Question\QuestionGeneratorManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSoalController extends Controller
{
    public function index(Game $game)
    {
        $game->load('soals', 'mapel');

        $ai = [
            'driver' => AiSettings::driverAktif(),
            'max' => (int) config('ai.max_per_request', 12),
            'configured' => [
                'mock' => true,
                'gemini' => AiSettings::configured('gemini'),
                'groq' => AiSettings::configured('groq'),
            ],
        ];

        return view('admin.soals.index', compact('game', 'ai'));
    }

    /**
     * Buat soal via AI → kembalikan preview JSON (belum disimpan).
     * Admin wajib meninjau hasilnya sebelum menyimpan.
     */
    public function generateAi(Request $request, Game $game, QuestionGeneratorManager $manager)
    {
        $data = $request->validate([
            'topik' => ['required', 'string', 'max:500'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:'.(int) config('ai.max_per_request', 12)],
            'kesulitan' => ['required', 'in:mudah,sedang,sulit'],
            'driver' => ['nullable', 'in:mock,gemini,groq'],
        ]);

        try {
            $soals = $manager->generator($data['driver'] ?? null)->generate(
                $game,
                $data['topik'],
                (int) $data['jumlah'],
                $data['kesulitan']
            );
        } catch (QuestionGenerationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['soals' => $soals]);
    }

    /**
     * Simpan soal hasil preview AI (hanya yang dicentang admin).
     */
    public function saveAi(Request $request, Game $game)
    {
        $jenis = $game->jenis;
        $panjangJawaban = in_array($jenis, ['urutkan', 'cocok_gambar'], true) ? 2048 : 255;

        $rules = [
            'soals' => ['required', 'array', 'min:1', 'max:'.(int) config('ai.max_per_request', 12)],
            'soals.*.pertanyaan' => ['required', 'string', 'max:255'],
            'soals.*.jawaban_benar' => ['required', 'string', 'max:'.$panjangJawaban],
            'soals.*.penjelasan' => ['nullable', 'string'],
        ];

        if ($jenis === 'cocok_gambar') {
            $rules['soals.*.pilihan'] = ['required', 'array', 'min:2', 'max:8'];
            $rules['soals.*.pilihan.*.kiri'] = ['required', 'string', 'max:255'];
            $rules['soals.*.pilihan.*.kanan'] = ['required', 'string', 'max:255'];
        } elseif ($jenis === 'urutkan') {
            $rules['soals.*.pilihan'] = ['required', 'array', 'min:2', 'max:8'];
            $rules['soals.*.pilihan.*'] = ['required', 'string', 'max:255'];
        } else {
            $rules['soals.*.pilihan'] = ['nullable', 'array'];
            $rules['soals.*.pilihan.*'] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules, [
            'soals.required' => 'Pilih minimal satu soal untuk disimpan.',
        ]);

        $urutan = (int) $game->soals()->max('urutan');
        $tersimpan = 0;
        $dilewati = 0;

        foreach ($data['soals'] as $s) {
            $pilihan = array_values(array_filter($s['pilihan'] ?? []));

            // Pertahanan tambahan: kuis wajib punya jawaban di dalam pilihan
            if ($jenis === 'kuis') {
                $cocok = collect($pilihan)->contains(
                    fn ($p) => mb_strtolower(trim($p)) === mb_strtolower(trim($s['jawaban_benar']))
                );
                if (! $cocok) {
                    $dilewati++;
                    continue;
                }
            }

            $game->soals()->create([
                'pertanyaan' => $s['pertanyaan'],
                'pilihan' => $pilihan ?: null,
                'jawaban_benar' => $s['jawaban_benar'],
                'penjelasan' => $s['penjelasan'] ?? null,
                'urutan' => ++$urutan,
            ]);
            $tersimpan++;
        }

        if ($tersimpan === 0) {
            return response()->json(['message' => 'Tidak ada soal yang tersimpan — semua tidak valid.'], 422);
        }

        $pesan = "{$tersimpan} soal AI berhasil disimpan.";
        if ($dilewati > 0) {
            $pesan .= " {$dilewati} soal dilewati (jawaban tidak cocok dengan pilihan).";
        }

        return response()->json(['message' => $pesan, 'count' => $tersimpan]);
    }

    public function store(Request $request, Game $game)
    {
        $hasil = $this->validasiDanSusun($request, $game);

        if ($hasil instanceof \Illuminate\Http\RedirectResponse) {
            return $hasil;
        }

        $game->soals()->create($hasil + ['urutan' => (int) $game->soals()->max('urutan') + 1]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, Game $game, Soal $soal)
    {
        $hasil = $this->validasiDanSusun($request, $game, $soal);

        if ($hasil instanceof \Illuminate\Http\RedirectResponse) {
            return $hasil;
        }

        $soal->update($hasil);

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Game $game, Soal $soal)
    {
        $soal->hapusGambarTerupload();
        $soal->delete();

        return back()->with('success', 'Soal dihapus.');
    }

    /**
     * Validasi & susun data soal sesuai jenis game.
     *
     * - urutkan      : textarea "urutan_items" (satu item per baris, urut dari
     *                  awal) → kolom pilihan berisi urutan benar,
     *                  jawaban_benar berupa ringkasan untuk ditampilkan.
     * - cocok_gambar : baris "pair[i]" (emoji/teks atau foto upload + label)
     *                  → pilihan berisi pasangan {kiri, kanan},
     *                  kiri bisa emoji/teks atau path file "gambar-soal/...".
     * - lainnya      : form klasik (pilihan + jawaban_benar).
     *
     * Kembalikan array data siap simpan, atau RedirectResponse jika gagal.
     */
    private function validasiDanSusun(Request $request, Game $game, ?Soal $soalLama = null): array|\Illuminate\Http\RedirectResponse
    {
        if ($game->jenis === 'urutkan') {
            $data = $request->validate([
                'pertanyaan' => ['required', 'string', 'max:255'],
                'urutan_items' => ['required', 'string'],
                'penjelasan' => ['nullable', 'string'],
            ], [
                'urutan_items.required' => 'Isi item yang harus diurutkan — satu item per baris.',
            ]);

            $items = $this->barisKeArray($data['urutan_items']);

            if (count($items) < 2) {
                return back()->withErrors(['urutan_items' => 'Isi minimal 2 item — satu item per baris, urut dari yang benar.'])->withInput();
            }
            if (count($items) > 8) {
                return back()->withErrors(['urutan_items' => 'Maksimal 8 item.'])->withInput();
            }
            if (count($items) !== count(array_unique($items))) {
                return back()->withErrors(['urutan_items' => 'Ada item yang sama persis — setiap item urutan harus unik.'])->withInput();
            }

            return [
                'pertanyaan' => $data['pertanyaan'],
                'pilihan' => $items,
                'jawaban_benar' => implode(' → ', $items),
                'penjelasan' => $data['penjelasan'] ?? null,
            ];
        }

        if ($game->jenis === 'cocok_gambar') {
            return $this->susunPasangan($request, $soalLama);
        }

        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'pilihan' => ['nullable', 'array'],
            'pilihan.*' => ['nullable', 'string', 'max:255'],
            'jawaban_benar' => ['required', 'string', 'max:255'],
            'penjelasan' => ['nullable', 'string'],
        ]);

        $data['pilihan'] = array_values(array_filter($data['pilihan'] ?? []));

        return $data;
    }

    /**
     * Susun data soal menjodohkan dari baris pasangan (emoji/teks + foto opsional).
     */
    private function susunPasangan(Request $request, ?Soal $soalLama = null): array|\Illuminate\Http\RedirectResponse
    {
        // Buang baris yang sepenuhnya kosong supaya validasi tidak mengeluh
        $bersih = [];
        foreach ($request->input('pair', []) as $i => $p) {
            $adaIsi = trim((string) ($p['kiri'] ?? '')) !== ''
                || trim((string) ($p['kanan'] ?? '')) !== ''
                || $request->file("pair.{$i}.gambar") !== null
                || ($p['lama'] ?? null) !== null;
            if ($adaIsi) {
                $bersih[$i] = $p;
            }
        }
        $request->merge(['pair' => $bersih]);

        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'pair' => ['required', 'array', 'min:2', 'max:6'],
            'pair.*.kiri' => ['nullable', 'string', 'max:64'],
            'pair.*.kanan' => ['required', 'string', 'max:255'],
            'pair.*.gambar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'pair.*.lama' => ['nullable', 'string', 'max:255'],
            'penjelasan' => ['nullable', 'string'],
        ], [
            'pair.min' => 'Isi minimal 2 pasangan.',
            'pair.max' => 'Maksimal 6 pasangan per soal.',
            'pair.*.kanan.required' => 'Setiap baris wajib diisi keterangannya (kolom paling kanan).',
            'pair.*.gambar.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'pair.*.gambar.max' => 'Ukuran gambar maksimal 1 MB.',
        ]);

        $pasangan = [];
        $galat = [];

        foreach ($data['pair'] as $i => $p) {
            $kiri = trim((string) ($p['kiri'] ?? ''));
            $kanan = trim((string) ($p['kanan'] ?? ''));
            $lama = $p['lama'] ?? null;
            $file = $request->file("pair.{$i}.gambar");

            if ($file !== null && $file->isValid()) {
                if ($lama && Soal::gambarTerupload($lama)) {
                    Storage::disk('public')->delete($lama);
                }
                $kiri = $file->store('gambar-soal', 'public');
            } elseif ($kiri === '' && $lama) {
                $kiri = $lama; // biarkan gambar lama tetap dipakai
            } elseif ($lama && $kiri !== '' && Soal::gambarTerupload($lama)) {
                Storage::disk('public')->delete($lama); // diganti jadi emoji/teks
            }

            if ($kiri === '' || $kanan === '') {
                $galat[] = 'Baris '.($i + 1).': isi gambarnya (emoji/teks atau upload foto) dan keterangannya.';
                continue;
            }

            $pasangan[] = ['kiri' => mb_substr($kiri, 0, 255), 'kanan' => mb_substr($kanan, 0, 255)];
        }

        if (count($pasangan) < 2) {
            $galat[] = 'Isi minimal 2 pasangan yang lengkap.';
        }

        $kiriRendah = array_map(fn ($p) => mb_strtolower($p['kiri']), $pasangan);
        if (count($kiriRendah) !== count(array_unique($kiriRendah))) {
            $galat[] = 'Ada gambar/emoji yang duplikat — setiap gambar harus unik.';
        }
        $kananRendah = array_map(fn ($p) => mb_strtolower($p['kanan']), $pasangan);
        if (count($kananRendah) !== count(array_unique($kananRendah))) {
            $galat[] = 'Ada keterangan yang duplikat — setiap keterangan harus unik.';
        }

        if ($galat !== []) {
            return back()->withErrors($galat)->withInput();
        }

        return [
            'pertanyaan' => $data['pertanyaan'],
            'pilihan' => $pasangan,
            'jawaban_benar' => implode(', ', array_map(fn ($p) => $p['kiri'].' = '.$p['kanan'], $pasangan)),
            'penjelasan' => $data['penjelasan'] ?? null,
        ];
    }

    /**
     * Ubah teks multi-baris menjadi array item (buang baris kosong).
     *
     * @return array<int, string>
     */
    private function barisKeArray(string $teks): array
    {
        $baris = preg_split('/\R/u', $teks) ?: [];

        return array_values(array_filter(array_map('trim', $baris), fn ($b) => $b !== ''));
    }
}
