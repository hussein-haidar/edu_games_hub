<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Progres;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $mapels = Mapel::with(['gamesAktif', 'gamesAktif.progres' => fn ($q) => $q->where('user_id', Auth::id())])
            ->where('aktif', true)
            ->orderBy('urutan')
            ->get();

        $progres = Progres::where('user_id', Auth::id())->get();
        $totalGame = $progres->count();
        $totalPoin = $progres->sum('skor');

        return view('dashboard', compact('mapels', 'progres', 'totalGame', 'totalPoin'));
    }

    public function mapel(Mapel $mapel)
    {
        abort_unless($mapel->aktif, 404);

        $mapel->load(['gamesAktif', 'gamesAktif.progres' => fn ($q) => $q->where('user_id', Auth::id())]);

        return view('mapel', compact('mapel'));
    }
}
