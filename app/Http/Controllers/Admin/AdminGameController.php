<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminGameController extends Controller
{
    public function index(Request $request)
    {
        $mapels = Mapel::orderBy('urutan')->get();

        $query = Game::with('mapel', 'soals')->withCount('soals');

        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }

        $games = $query->orderBy('mapel_id')->orderBy('urutan')->paginate(15)->withQueryString();

        return view('admin.games.index', compact('games', 'mapels'));
    }

    public function create()
    {
        $mapels = Mapel::orderBy('urutan')->get();

        return view('admin.games.create', compact('mapels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mapel_id' => ['required', 'exists:mapels,id'],
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:kuis,bener_salah,teka_teki,drag_drop'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'durasi_menit' => ['nullable', 'integer', 'min:1', 'max:120'],
            'poin_per_benar' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        Game::create($data + ['slug' => Str::slug($data['nama']).'-'.Str::random(4)]);

        return redirect()->route('admin.games.index')->with('success', 'Game berhasil ditambahkan.');
    }

    public function edit(Game $game)
    {
        $mapels = Mapel::orderBy('urutan')->get();

        return view('admin.games.edit', compact('game', 'mapels'));
    }

    public function update(Request $request, Game $game)
    {
        $data = $request->validate([
            'mapel_id' => ['required', 'exists:mapels,id'],
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:kuis,bener_salah,teka_teki,drag_drop'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'durasi_menit' => ['nullable', 'integer', 'min:1', 'max:120'],
            'poin_per_benar' => ['required', 'integer', 'min:1', 'max:100'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['aktif'] = $request->boolean('aktif');
        $game->update($data);

        return back()->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return back()->with('success', 'Game beserta soalnya dihapus.');
    }
}
