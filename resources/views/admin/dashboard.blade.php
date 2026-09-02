@extends('admin.layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="h3 fw-bold mb-4">Dashboard Admin</h1>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card text-center border"><div class="card-body">
                <div class="fs-3 fw-bold text-primary">{{ $stats['users'] }}</div>
                <div class="small text-secondary">Total Pengguna</div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center border"><div class="card-body">
                <div class="fs-3 fw-bold text-success">{{ $stats['subscribed'] }}</div>
                <div class="small text-secondary">Langganan Aktif</div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center border"><div class="card-body">
                <div class="fs-3 fw-bold text-info">{{ $stats['mapels'] }}</div>
                <div class="small text-secondary">Mapel</div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center border"><div class="card-body">
                <div class="fs-3 fw-bold text-primary">{{ $stats['games'] }}</div>
                <div class="small text-secondary">Game</div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center border"><div class="card-body">
                <div class="fs-3 fw-bold text-warning">{{ $stats['progres'] }}</div>
                <div class="small text-secondary">Total Main</div>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="card h-100 text-decoration-none border shadow-sm"><div class="card-body">
                <div class="fs-1 mb-2">👥</div>
                <h2 class="h6 fw-bold">Kelola Pengguna</h2>
                <p class="text-secondary small mt-1 mb-0">Melihat daftar pengguna, mengaktifkan dan membatalkan langganan premium.</p>
            </div></a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.mapels.index') }}" class="card h-100 text-decoration-none border shadow-sm"><div class="card-body">
                <div class="fs-1 mb-2">📚</div>
                <h2 class="h6 fw-bold">Kelola Mapel</h2>
                <p class="text-secondary small mt-1 mb-0">Tambah dan kelola mata pelajaran.</p>
            </div></a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.games.index') }}" class="card h-100 text-decoration-none border shadow-sm"><div class="card-body">
                <div class="fs-1 mb-2">🎮</div>
                <h2 class="h6 fw-bold">Kelola Game</h2>
                <p class="text-secondary small mt-1 mb-0">Tambah, edit, dan hapus game beserta soal-soalnya.</p>
            </div></a>
        </div>
    </div>
@endsection
