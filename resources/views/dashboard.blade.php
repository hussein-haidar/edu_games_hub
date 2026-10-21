@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card border-0 shadow-sm hero-sapa mb-4">
        <div class="card-body p-4 d-flex align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h4 fw-bold text-white mb-1">Halo, {{ auth()->user()->name }}! 👋</h1>
                <p class="mb-0 text-white-50">Pilih mata pelajaran dan mulailah bermain hari ini.</p>
            </div>
            <span class="display-3">🎮</span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="kartu-stat stat-biru">
                <div class="kartu-stat-ikon">🎮</div>
                <div>
                    <div class="kartu-stat-angka">{{ $totalGame }}</div>
                    <div class="kartu-stat-label">Game Dimainkan</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="kartu-stat stat-emas">
                <div class="kartu-stat-ikon">⭐</div>
                <div>
                    <div class="kartu-stat-angka">{{ $totalPoin }}</div>
                    <div class="kartu-stat-label">Total Poin</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="kartu-stat stat-hijau">
                <div class="kartu-stat-ikon">📚</div>
                <div>
                    <div class="kartu-stat-angka">{{ $mapels->count() }}</div>
                    <div class="kartu-stat-label">Mata Pelajaran</div>
                </div>
            </div>
        </div>
    </div>

    @if(!auth()->user()->isSubscribed())
        <div class="kartu-cta-premium rounded-3 p-3 p-md-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div class="small">
                <strong class="d-block fs-6 mb-1">🔓 Buka semua game dengan Premium</strong>
                <span class="text-secondary">Kamu bisa melihat mapel, tapi untuk memainkan game butuh langganan premium.</span>
            </div>
            <a href="{{ route('subscription') }}" class="btn btn-premium btn-sm fw-bold align-self-start">✨ Lihat Paket Premium</a>
        </div>
    @endif

    <h2 class="h5 fw-bold mb-3">📚 Mata Pelajaran</h2>
    @if($mapels->isEmpty())
        <div class="text-center text-secondary py-5">Belum ada mata pelajaran.</div>
    @else
        <div class="row g-4">
            @foreach($mapels as $mapel)
                <div class="col-md-6">
                    <a href="{{ route('mapel.show', $mapel) }}" class="card h-100 text-decoration-none border overflow-hidden shadow-sm">
                        <div class="p-4 text-white d-flex justify-content-between align-items-center header-mapel" style="background: linear-gradient(135deg, {{ $mapel->warna }}, {{ $mapel->warna }}cc)">
                            <span class="fs-1">{{ $mapel->karakter }}</span>
                            <span class="badge text-bg-light">{{ $mapel->kelas }}</span>
                        </div>
                        <div class="card-body">
                            <h3 class="h5 fw-bold">{{ $mapel->nama }}</h3>
                            <p class="text-secondary small mb-2">{{ $mapel->deskripsi }}</p>
                            <div class="d-flex justify-content-between small align-items-center">
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
