@extends('layouts.app')

@section('title', $mapel->nama)

@section('content')
    <a href="{{ route('dashboard') }}" class="small text-primary text-decoration-none">&larr; Kembali ke Dashboard</a>

    <div class="card border overflow-hidden mt-3 shadow-sm">
        <div class="p-4 text-white d-flex justify-content-between align-items-center header-mapel" style="background: linear-gradient(135deg, {{ $mapel->warna }}, {{ $mapel->warna }}cc)">
            <div class="d-flex align-items-center gap-3">
                <span class="fs-1">{{ $mapel->karakter }}</span>
                <div>
                    <h1 class="h3 fw-bold mb-0">{{ $mapel->nama }}</h1>
                    <p class="mb-0 text-white-50">{{ $mapel->kelas }}</p>
                </div>
            </div>
            <span class="d-none d-sm-block" style="font-size: 3.5rem; opacity: .3; transform: rotate(10deg);">{{ $mapel->karakter }}</span>
        </div>
        <div class="card-body">
            <p class="text-secondary mb-0">{{ $mapel->deskripsi }}</p>
        </div>
    </div>

    @if(!auth()->user()->isSubscribed())
        <div class="kartu-cta-premium rounded-3 p-3 p-md-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mt-3">
            <div class="small">
                <strong class="d-block fs-6 mb-1">🔒 Kamu belum berlangganan</strong>
                <span class="text-secondary">Aktifkan premium untuk membuka semua game.</span>
            </div>
            <a href="{{ route('subscription') }}" class="btn btn-premium btn-sm fw-bold align-self-start">✨ Aktifkan Premium</a>
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
                    <div class="card h-100 border shadow-sm {{ auth()->user()->hasAccessTo($game) ? '' : 'kartu-terkunci' }}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="fs-1 emoji-game">{{ $game->emoji }}</span>
                                @if(auth()->user()->isSubscribed() || auth()->user()->isAdmin())
                                    @if($done)
                                        <span class="badge text-bg-success">🏆 Selesai · {{ $done->skor }} poin</span>
                                    @else
                                        <span class="badge text-bg-secondary">Belum dimainkan</span>
                                    @endif
                                @else
                                    <span class="badge-premium">🔒 Premium</span>
                                @endif
                            </div>
                            <h3 class="h6 fw-bold mt-3">{{ $game->nama }}</h3>
                            <p class="text-secondary small mb-2 flex-grow-1">{{ $game->deskripsi }}</p>
                            <div class="d-flex justify-content-between align-items-center small gap-2">
                                <span class="text-secondary">{{ $game->soals->count() }} soal · {{ $game->poin_per_benar }} poin/benar</span>
                                @if(auth()->user()->hasAccessTo($game))
                                    <a href="{{ route('games.show', $game) }}" class="btn btn-sm btn-primary fw-semibold rounded-pill">Main ▶</a>
                                @else
                                    <a href="{{ route('subscription') }}" class="btn btn-sm btn-premium fw-semibold rounded-pill">Daftar Premium</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
