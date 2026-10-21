@extends('admin.layouts.admin')

@section('title', 'Edit Game')

@section('content')
    <div class="text-center mb-4">
        <a href="{{ route('admin.games.index') }}" class="small text-primary text-decoration-none d-block mb-2">&larr; Kembali</a>
        <h1 class="h3 fw-bold mb-0">Edit Game: {{ $game->nama }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.games.update', $game) }}" class="card border p-4 mx-auto" style="max-width: 40rem;">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label small fw-medium">Mata Pelajaran</label>
            <select name="mapel_id" required class="form-select">
                @foreach($mapels as $m)
                    <option value="{{ $m->id }}" {{ $game->mapel_id == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Nama Game</label>
            <input type="text" name="nama" value="{{ $game->nama }}" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Jenis Game</label>
            <select name="jenis" required class="form-select">
                <option value="kuis" {{ $game->jenis == 'kuis' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="bener_salah" {{ $game->jenis == 'bener_salah' ? 'selected' : '' }}>Benar / Salah</option>
                <option value="teka_teki" {{ $game->jenis == 'teka_teki' ? 'selected' : '' }}>Teka-Teki Jodohkan</option>
                <option value="isian" {{ $game->jenis == 'isian' ? 'selected' : '' }}>Isian Singkat</option>
                <option value="urutkan" {{ $game->jenis == 'urutkan' ? 'selected' : '' }}>Mengurutkan</option>
                <option value="cocok_gambar" {{ $game->jenis == 'cocok_gambar' ? 'selected' : '' }}>Menjodohkan Gambar</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Deskripsi</label>
            <textarea name="deskripsi" rows="2" class="form-control">{{ $game->deskripsi }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Icon</label>
            <select name="icon" class="form-select">
                @foreach(['calculator' => '🧮 Kalkulator', 'flask' => '🧪 Labu', 'check-badge' => '✅ Centang', 'puzzle' => '🧩 Puzzle'] as $val => $label)
                    <option value="{{ $val }}" {{ $game->icon == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label small fw-medium">Durasi (menit)</label>
                <input type="number" name="durasi_menit" value="{{ $game->durasi_menit }}" min="1" max="120" class="form-control">
            </div>
            <div class="col-6">
                <label class="form-label small fw-medium">Poin per Benar</label>
                <input type="number" name="poin_per_benar" value="{{ $game->poin_per_benar }}" min="1" max="100" required class="form-control">
            </div>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="aktif" value="1" {{ $game->aktif ? 'checked' : '' }} class="form-check-input" id="aktif">
            <label class="form-check-label" for="aktif">Aktif</label>
        </div>
        <button class="btn btn-primary w-100 fw-semibold">Simpan Perubahan</button>
    </form>
@endsection
