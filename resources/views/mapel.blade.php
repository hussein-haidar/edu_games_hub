@extends('layouts.app')

@section('title', $mapel->nama)

@section('content')
    <a href="{{ route('dashboard') }}" class="small text-primary text-decoration-none">&larr; Kembali ke Dashboard</a>

    <div class="card border overflow-hidden mt-3">
        <div class="p-4 text-white" style="background: {{ $mapel->warna }}">
            <div class="d-flex align-items-center gap-3">
                <span class="fs-1">{{ $mapel->icon === 'calculator' ? '🧮' : ($mapel->icon === 'flask' ? '🧪' : '📚') }}</span>
                <div>
                    <h1 class="h3 fw-bold mb-0">{{ $mapel->nama }}</h1>
                    <p class="mb-0 text-white-50">{{ $mapel->kelas }}</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p class="text-secondary mb-0">{{ $mapel->deskripsi }}</p>
        </div>
    </div>

    @if(!auth()->user()->isSubscribed())
        <div class="alert alert-warning small mt-3 mb-0">
            Kamu belum berlangganan. <a href="{{ route('subscription') }}" class="fw-semibold">Aktifkan premium</a> untuk membuka semua game.
        </div>
    @endif

    <h2 class="h5 fw-bold mt-4 mb-3">Game Tersedia</h2>
    @if($mapel->gamesAktif->isEmpty())
        <div class="text-center text-secondary py-5">Belum ada game.</div>
    @else
        <div class="row g-4">
            @foreach($mapel->gamesAktif as $game)
                @php $done = $game->progres->first(); @endphp
                <div class="col-md-6">
                    <div class="card h-100 border">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="fs-1">{{ $game->icon === 'calculator' ? '🧮' : ($game->icon === 'flask' ? '🧪' : ($game->icon === 'check-badge' ? '✅' : '🧩')) }}</span>
                                @if(auth()->user()->isSubscribed() || auth()->user()->isAdmin())
                                    @if($done)
                                        <span class="badge text-bg-success">Selesai · {{ $done->skor }} poin</span>
                                    @else
                                        <span class="badge text-bg-secondary">Belum dimainkan</span>
                                    @endif
                                @else
                                    <span class="badge text-bg-warning">🔒 Premium</span>
                                @endif
                            </div>
                            <h3 class="h6 fw-bold mt-3">{{ $game->nama }}</h3>
                            <p class="text-secondary small mb-2 flex-grow-1">{{ $game->deskripsi }}</p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-secondary">{{ $game->soals->count() }} soal · {{ $game->poin_per_benar }} poin/benar</span>
                                @if(auth()->user()->hasAccessTo($game))
                                    <a href="{{ route('games.show', $game) }}" class="btn btn-sm btn-primary fw-semibold">Main</a>
                                @else
                                    <a href="{{ route('subscription') }}" class="btn btn-sm btn-secondary fw-semibold">Daftar Premium</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
