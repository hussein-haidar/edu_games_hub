<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Progres;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function show(Game $game)
    {
        abort_unless($game->aktif, 404);
        $game->load('mapel', 'soals');

        if (!Auth::user()->hasAccessTo($game)) {
            return redirect()->route('subscription')
                ->with('error', 'Kamu perlu berlangganan untuk memainkan game ini.');
        }

        // Ambil progres sebelumnya jika ada
        $progres = Progres::where('user_id', Auth::id())
            ->where('game_id', $game->id)
            ->first();

        return view('games.show', compact('game', 'progres'));
    }

    public function submit(Request $request, Game $game)
    {
        abort_unless(Auth::user()->hasAccessTo($game), 403);

        $game->load('soals');

        $data = $request->validate([
            'jawaban' => ['required', 'string'],
        ]);

        $jawaban = json_decode($data['jawaban'], true);
        if (! is_array($jawaban)) {
            $jawaban = [];
        }
        $benar = 0;
        $skor = 0;
        $total = $game->soals->count();

        foreach ($game->soals as $soal) {
            $jawabanUser = $jawaban[$soal->id] ?? null;
            if ($this->dijawabBenar($game->jenis, $soal, $jawabanUser)) {
                $benar++;
                $skor += $game->poin_per_benar;
            }
        }

        $salah = $total - $benar;
        $gamePlayed = ($total > 0);

        if ($gamePlayed) {
            $progres = Progres::updateOrCreate(
                ['user_id' => Auth::id(), 'game_id' => $game->id],
                ['skor' => $skor, 'benar' => $benar, 'salah' => $salah, 'total' => $total]
            );

            // Tambahkan poin ke user (skor terbaru)
            Auth::user()->update(['points' => Auth::user()->points + $skor]);
        } else {
            $progres = null;
        }

        return redirect()->route('games.result', [$game->id, 'benar' => $benar, 'salah' => $salah, 'skor' => $skor, 'total' => $total]);
    }

    public function result(Game $game)
    {
        $benar = (int) request('benar', 0);
        $salah = (int) request('salah', 0);
        $skor = (int) request('skor', 0);
        $total = (int) request('total', 0);

        $game->load('mapel');
        $progres = Progres::where('user_id', Auth::id())
            ->where('game_id', $game->id)
            ->first();

        return view('games.result', compact('game', 'benar', 'salah', 'skor', 'total', 'progres'));
    }

    /**
     * Penilaian satu jawaban siswa sesuai jenis game.
     *
     * - kuis / bener_salah / teka_teki → teks yang sama dengan jawaban_benar
     * - isian      → teks, tidak peka huruf besar/kecil & spasi;
     *                jawaban_benar boleh memuat beberapa varian dipisah "|"
     * - urutkan    → array urutan item siswa, dibandingkan dengan pilihan
     *                (pilihan menyimpan urutan yang benar)
     * - cocok_gambar → array pasangan [kiri, kanan], dibandingkan dengan
     *                pilihan (array objek {kiri, kanan})
     */
    private function dijawabBenar(string $jenis, Soal $soal, mixed $user): bool
    {
        if ($user === null) {
            return false;
        }

        switch ($jenis) {
            case 'isian':
                if (! is_string($user)) {
                    return false;
                }
                $norm = fn ($s) => mb_strtolower((string) preg_replace('/\s+/u', ' ', trim((string) $s)));
                $kunci = array_values(array_filter(array_map($norm, explode('|', (string) $soal->jawaban_benar))));
                $jawaban = $norm($user);

                return $jawaban !== '' && in_array($jawaban, $kunci, true);

            case 'urutkan':
                if (! is_array($user)) {
                    return false;
                }
                $benar = array_values($soal->pilihan ?? []);
                if ($benar === [] || count($user) !== count($benar)) {
                    return false;
                }
                foreach ($benar as $i => $item) {
                    if (mb_strtolower(trim((string) ($user[$i] ?? ''))) !== mb_strtolower(trim((string) $item))) {
                        return false;
                    }
                }

                return true;

            case 'cocok_gambar':
                if (! is_array($user)) {
                    return false;
                }
                $kunci = [];
                foreach ($soal->pilihan ?? [] as $pasangan) {
                    if (is_array($pasangan) && isset($pasangan['kiri'], $pasangan['kanan'])) {
                        $kunci[mb_strtolower(trim($pasangan['kiri']))] = trim($pasangan['kanan']);
                    }
                }
                if ($kunci === [] || count($user) !== count($kunci)) {
                    return false;
                }
                foreach ($user as $pasangan) {
                    if (! is_array($pasangan)) {
                        return false;
                    }
                    $k = mb_strtolower(trim((string) ($pasangan[0] ?? '')));
                    $v = trim((string) ($pasangan[1] ?? ''));
                    if ($k === '' || ! isset($kunci[$k]) || $kunci[$k] !== $v) {
                        return false;
                    }
                }

                return true;

            default: // kuis, bener_salah, teka_teki
                return is_string($user) && trim($user) === trim($soal->jawaban_benar);
        }
    }
}
