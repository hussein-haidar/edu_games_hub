<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Soal;
use Illuminate\Http\Request;

class AdminSoalController extends Controller
{
    public function index(Game $game)
    {
        $game->load('soals', 'mapel');

        return view('admin.soals.index', compact('game'));
    }

    public function store(Request $request, Game $game)
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'pilihan' => ['nullable', 'array'],
            'pilihan.*' => ['nullable', 'string', 'max:255'],
            'jawaban_benar' => ['required', 'string', 'max:255'],
            'penjelasan' => ['nullable', 'string'],
        ]);

        $data['pilihan'] = array_values(array_filter($data['pilihan'] ?? []));

        $game->soals()->create($data);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, Game $game, Soal $soal)
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'pilihan' => ['nullable', 'array'],
            'pilihan.*' => ['nullable', 'string', 'max:255'],
            'jawaban_benar' => ['required', 'string', 'max:255'],
            'penjelasan' => ['nullable', 'string'],
        ]);

        $data['pilihan'] = array_values(array_filter($data['pilihan'] ?? []));

        $soal->update($data);

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Game $game, Soal $soal)
    {
        $soal->delete();

        return back()->with('success', 'Soal dihapus.');
    }
}
