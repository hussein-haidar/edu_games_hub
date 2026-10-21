@extends('admin.layouts.admin')

@section('title', 'Tambah Game')

@section('content')
    <div class="text-center mb-4">
        <a href="{{ route('admin.games.index') }}" class="small text-primary text-decoration-none d-block mb-2">&larr; Kembali</a>
        <h1 class="h3 fw-bold mb-0">Tambah Game</h1>
    </div>

    <form method="POST" action="{{ route('admin.games.store') }}" class="card border p-4 mx-auto" style="max-width: 40rem;">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-medium">Mata Pelajaran</label>
            <select name="mapel_id" required class="form-select">
                @foreach($mapels as $m)
                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Nama Game</label>
            <input type="text" name="nama" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Jenis Game</label>
            <select name="jenis" required class="form-select">
                <option value="kuis">Pilihan Ganda</option>
                <option value="bener_salah">Benar / Salah</option>
                <option value="teka_teki">Teka-Teki Jodohkan</option>
                <option value="isian">Isian Singkat</option>
                <option value="urutkan">Mengurutkan</option>
                <option value="cocok_gambar">Menjodohkan Gambar</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Deskripsi</label>
            <textarea name="deskripsi" rows="2" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-medium">Icon</label>
            <select name="icon" class="form-select">
                <option value="calculator">🧮 Kalkulator</option>
                <option value="flask">🧪 Labu</option>
                <option value="check-badge">✅ Centang</option>
                <option value="puzzle">🧩 Puzzle</option>
            </select>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label small fw-medium">Durasi (menit)</label>
                <input type="number" name="durasi_menit" min="1" max="120" class="form-control">
            </div>
            <div class="col-6">
                <label class="form-label small fw-medium">Poin per Benar</label>
                <input type="number" name="poin_per_benar" value="10" min="1" max="100" required class="form-control">
            </div>
        </div>
        <button class="btn btn-primary w-100 fw-semibold">Simpan Game</button>
    </form>
@endsection
