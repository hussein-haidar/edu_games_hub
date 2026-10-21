@extends('layouts.app')

@section('title', 'Hasil — '.$game->nama)

@section('content')
    <a href="{{ route('mapel.show', $game->mapel) }}" class="small text-primary text-decoration-none">&larr; Kembali ke {{ $game->mapel->nama }}</a>

    @php
        $rasio = $total > 0 ? $benar / $total : 0;
        $gradasi = $rasio >= 0.8
            ? 'linear-gradient(120deg, #10b981, #059669)'
            : ($rasio >= 0.5
                ? 'linear-gradient(120deg, #f59e0b, #f97316)'
                : 'linear-gradient(120deg, #8b5cf6, #6366f1)');
        $trofi = $rasio >= 0.8 ? '🏆' : ($rasio >= 0.5 ? '😊' : '💪');
        $pesan = $rasio >= 0.8
            ? 'Luar biasa! Kamu benar-benar menguasai materi ini. 🎉'
            : ($rasio >= 0.5
                ? 'Bagus! Terus berlatih ya, kamu pasti bisa lebih baik lagi.'
                : 'Jangan menyerah! Coba lagi dan baca penjelasannya baik-baik.');
    @endphp

    <div class="card border-0 shadow kado-hasil mx-auto mt-4 overflow-hidden" style="max-width: 40rem;">
        <div class="p-4 text-center pita-hasil" style="background: {{ $gradasi }}">
            <div class="konfeti" aria-hidden="true">
                <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="trofi-besar mb-2">{{ $trofi }}</div>
            <h1 class="h4 fw-bold text-white mb-1">Hasil Game: {{ $game->nama }}</h1>
            <p class="mb-0 text-white-50">{{ $pesan }}</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-4">
                    <div class="p-3 rounded bg-success-subtle text-center">
                        <div class="fs-2 fw-bold text-success">{{ $benar }}</div>
                        <div class="small text-secondary">Benar</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded bg-danger-subtle text-center">
                        <div class="fs-2 fw-bold text-danger">{{ $salah }}</div>
                        <div class="small text-secondary">Salah</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded bg-primary-subtle text-center">
                        <div class="fs-2 fw-bold text-primary">⭐ {{ $skor }}</div>
                        <div class="small text-secondary">Poin</div>
                    </div>
                </div>
            </div>

            <p class="text-muted small mt-3 mb-0 text-center">Dari {{ $total }} soal</p>

            <div class="d-flex gap-2 justify-content-center mt-4">
                <a href="{{ route('games.show', $game) }}" class="btn btn-primary fw-semibold rounded-pill px-4 shadow-sm">🔄 Main Lagi</a>
                <a href="{{ route('mapel.show', $game->mapel) }}" class="btn btn-outline-secondary fw-semibold rounded-pill">📚 Mapel Lain</a>
            </div>
        </div>
    </div>
@endsection
