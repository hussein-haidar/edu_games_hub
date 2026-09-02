<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Progres;
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
            if ($jawabanUser !== null && trim($jawabanUser) === trim($soal->jawaban_benar)) {
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
}
