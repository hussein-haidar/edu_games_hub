@extends('layouts.app')

@section('title', 'Hasil — '.$game->nama)

@section('content')
    <a href="{{ route('mapel.show', $game->mapel) }}" class="small text-primary text-decoration-none">&larr; Kembali ke {{ $game->mapel->nama }}</a>

    <div class="card border mx-auto mt-4 p-4" style="max-width: 40rem;">
        <div class="card-body text-center">
            <div class="fs-1 mb-3">{{ $skor >= 40 ? '🏆' : ($skor >= 20 ? '😊' : '💪') }}</div>
            <h1 class="h3 fw-bold">Hasil Game: {{ $game->nama }}</h1>
            <p class="text-secondary">Berikut hasil permainanmu:</p>

            <div class="row g-3 mt-3">
                <div class="col-4">
                    <div class="p-3 rounded bg-success-subtle"><div class="fs-3 fw-bold text-success">{{ $benar }}</div><div class="small text-secondary">Benar</div></div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded bg-danger-subtle"><div class="fs-3 fw-bold text-danger">{{ $salah }}</div><div class="small text-secondary">Salah</div></div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded bg-primary-subtle"><div class="fs-3 fw-bold text-primary">⭐ {{ $skor }}</div><div class="small text-secondary">Poin</div></div>
                </div>
            </div>

            <p class="text-muted small mt-3">Dari {{ $total }} soal</p>

            <div class="d-flex gap-2 justify-content-center mt-4">
                <a href="{{ route('games.show', $game) }}" class="btn btn-primary fw-semibold">Main Lagi</a>
                <a href="{{ route('mapel.show', $game->mapel) }}" class="btn btn-light fw-semibold border">Mapel Lain</a>
            </div>
        </div>
    </div>
@endsection
