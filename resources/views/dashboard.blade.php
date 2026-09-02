@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 fw-bold mb-1">Halo, {{ auth()->user()->name }}! 👋</h1>
    <p class="text-secondary mb-4">Pilih mata pelajaran dan mulailah bermain hari ini.</p>

    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="card text-center border"><div class="card-body py-3">
                <div class="fs-4 fw-bold text-primary">{{ $totalGame }}</div>
                <div class="small text-secondary">Game Dimainkan</div>
            </div></div>
        </div>
        <div class="col-4">
            <div class="card text-center border"><div class="card-body py-3">
                <div class="fs-4 fw-bold text-warning">⭐ {{ $totalPoin }}</div>
                <div class="small text-secondary">Total Poin</div>
            </div></div>
        </div>
        <div class="col-4">
            <div class="card text-center border"><div class="card-body py-3">
                <div class="fs-4 fw-bold text-success">{{ $mapels->count() }}</div>
                <div class="small text-secondary">Mata Pelajaran</div>
            </div></div>
        </div>
    </div>

    @if(!auth()->user()->isSubscribed())
        <div class="alert alert-warning small">
            Kamu belum berlangganan. Kamu bisa melihat mapel, tapi untuk memainkan game butuh <a href="{{ route('subscription') }}" class="fw-semibold">langganan premium</a>.
        </div>
    @endif

    <h2 class="h5 fw-bold mb-3">Mata Pelajaran</h2>
    @if($mapels->isEmpty())
        <div class="text-center text-secondary py-5">Belum ada mata pelajaran.</div>
    @else
        <div class="row g-4">
            @foreach($mapels as $mapel)
                <div class="col-md-6">
                    <a href="{{ route('mapel.show', $mapel) }}" class="card h-100 text-decoration-none border overflow-hidden shadow-sm">
                        <div class="p-4 text-white d-flex justify-content-between align-items-center" style="background: {{ $mapel->warna }}">
                            <span class="fs-1">{{ $mapel->icon === 'calculator' ? '🧮' : ($mapel->icon === 'flask' ? '🧪' : '📚') }}</span>
                            <span class="badge text-bg-light">{{ $mapel->kelas }}</span>
                        </div>
                        <div class="card-body">
                            <h3 class="h5 fw-bold">{{ $mapel->nama }}</h3>
                            <p class="text-secondary small mb-2">{{ $mapel->deskripsi }}</p>
                            <div class="d-flex justify-content-between small">
                                <span class="text-secondary">{{ $mapel->gamesAktif->count() }} game</span>
                                <span class="text-primary fw-semibold">Lihat &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
