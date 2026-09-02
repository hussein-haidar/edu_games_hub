@extends('admin.layouts.admin')

@section('title', 'Soal — '.$game->nama)

@section('content')
    <a href="{{ route('admin.games.index') }}" class="small text-primary text-decoration-none">&larr; Kembali ke Game</a>
    <h1 class="h3 fw-bold mt-2">Soal Game: {{ $game->nama }}</h1>
    <p class="text-secondary small">{{ $game->mapel->nama }} · {{ $game->soals->count() }} soal · {{ $game->jenis }}</p>

    <div class="mb-4">
        <div class="card border" style="max-width: 44rem;">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">+ Tambah Soal</h2>
                @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.soals.store', $game) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Pertanyaan</label>
                        <input type="text" name="pertanyaan" value="{{ old('pertanyaan') }}" required class="form-control form-control-sm">
                    </div>

                    @if($game->jenis === 'kuis')
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Pilihan Jawaban</label>
                            <div class="row g-2">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="col-6">
                                        <input type="text" name="pilihan[]" value="{{ old('pilihan.'.$i) }}" placeholder="Pilihan {{ $i+1 }}" class="form-control form-control-sm">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Jawaban Benar</label>
                        @if($game->jenis === 'bener_salah')
                            <select name="jawaban_benar" required class="form-select form-select-sm">
                                <option value="Benar">Benar</option>
                                <option value="Salah">Salah</option>
                            </select>
                        @else
                            <input type="text" name="jawaban_benar" value="{{ old('jawaban_benar') }}" required class="form-control form-control-sm">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Penjelasan (opsional)</label>
                        <input type="text" name="penjelasan" value="{{ old('penjelasan') }}" class="form-control form-control-sm">
                    </div>

                    <button class="btn btn-sm btn-primary w-100 fw-semibold">Tambahkan Soal</button>
                </form>
            </div>
        </div>
    </div>

    <div style="max-width: 44rem;">
        @forelse($game->soals as $soal)
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold">{{ $soal->urutan }}. {{ $soal->pertanyaan }}</div>
                            @if($soal->pilihan)
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach($soal->pilihan as $p)
                                        <span class="badge {{ $p === $soal->jawaban_benar ? 'text-bg-success' : 'text-bg-light border' }}">{{ $p }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="small mt-2">
                                <span class="text-success fw-medium">Jawaban: {{ $soal->jawaban_benar }}</span>
                                @if($soal->penjelasan)
                                    <span class="text-secondary"> — {{ $soal->penjelasan }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editSoal-{{ $soal->id }}">Edit</button>
                            <form method="POST" action="{{ route('admin.soals.destroy', [$game, $soal]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger w-100">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editSoal-{{ $soal->id }}" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Edit Soal #{{ $soal->urutan }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.soals.update', [$game, $soal]) }}">
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Pertanyaan</label>
                                <input type="text" name="pertanyaan" value="{{ $soal->pertanyaan }}" required class="form-control form-control-sm">
                            </div>
                            @if($game->jenis === 'kuis')
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Pilihan</label>
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="mb-2">
                                            <input type="text" name="pilihan[]" value="{{ $soal->pilihan[$i] ?? '' }}" placeholder="Pilihan {{ $i+1 }}" class="form-control form-control-sm">
                                        </div>
                                    @endfor
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Jawaban benar</label>
                                <input type="text" name="jawaban_benar" value="{{ $soal->jawaban_benar }}" required class="form-control form-control-sm">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Penjelasan</label>
                                <input type="text" name="penjelasan" value="{{ $soal->penjelasan }}" class="form-control form-control-sm">
                            </div>
                            <button class="btn btn-sm btn-primary w-100 fw-semibold">Simpan</button>
                        </form>
                    </div>
                </div></div>
            </div>
        @empty
            <p class="text-center text-secondary py-4 bg-white border rounded">Belum ada soal. Tambahkan di atas.</p>
        @endforelse
    </div>
@endsection
