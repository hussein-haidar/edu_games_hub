<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::withCount('games')->orderBy('urutan')->get();

        return view('admin.mapels.index', compact('mapels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'warna' => ['nullable', 'string', 'max:50'],
        ]);

        Mapel::create($data + ['slug' => Str::slug($data['nama']).'-'.Str::random(4)]);

        return back()->with('success', 'Mapel berhasil ditambahkan.');
    }

    public function update(Request $request, Mapel $mapel)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'warna' => ['nullable', 'string', 'max:50'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['aktif'] = $request->boolean('aktif');
        $mapel->update($data);

        return back()->with('success', 'Mapel berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return back()->with('success', 'Mapel beserta game dan soal dihapus.');
    }
}
