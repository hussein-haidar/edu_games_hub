@extends('admin.layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard Admin</h1>
            <p class="text-secondary mb-0 small">Ringkasan aktivitas Edu Games Hub hari ini.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl">
            <div class="kartu-stat stat-biru">
                <div class="kartu-stat-ikon">👥</div>
                <div>
                    <div class="kartu-stat-angka">{{ $stats['users'] }}</div>
                    <div class="kartu-stat-label">Total Pengguna</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="kartu-stat stat-emas">
                <div class="kartu-stat-ikon">👑</div>
                <div>
                    <div class="kartu-stat-angka">{{ $stats['subscribed'] }}</div>
                    <div class="kartu-stat-label">Langganan Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="kartu-stat stat-ungu">
                <div class="kartu-stat-ikon">📚</div>
                <div>
                    <div class="kartu-stat-angka">{{ $stats['mapels'] }}</div>
                    <div class="kartu-stat-label">Mapel</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="kartu-stat stat-sian">
                <div class="kartu-stat-ikon">🎮</div>
                <div>
                    <div class="kartu-stat-angka">{{ $stats['games'] }}</div>
                    <div class="kartu-stat-label">Game</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="kartu-stat stat-hijau">
                <div class="kartu-stat-ikon">▶️</div>
                <div>
                    <div class="kartu-stat-angka">{{ $stats['progres'] }}</div>
                    <div class="kartu-stat-label">Total Main</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.users.index') }}" class="card h-100 text-decoration-none border shadow-sm kartu-aksi">
                <div class="card-body">
                    <div class="ikon-aksi ikon-aksi-biru">👥</div>
                    <h2 class="h6 fw-bold">Kelola Pengguna</h2>
                    <p class="text-secondary small mt-1 mb-0">Melihat daftar pengguna, mengaktifkan dan membatalkan langganan premium.</p>
                    <div class="small text-primary fw-semibold mt-3">Kelola &rarr;</div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.mapels.index') }}" class="card h-100 text-decoration-none border shadow-sm kartu-aksi">
                <div class="card-body">
                    <div class="ikon-aksi ikon-aksi-hijau">📚</div>
                    <h2 class="h6 fw-bold">Kelola Mapel</h2>
                    <p class="text-secondary small mt-1 mb-0">Tambah dan kelola mata pelajaran.</p>
                    <div class="small text-primary fw-semibold mt-3">Kelola &rarr;</div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.games.index') }}" class="card h-100 text-decoration-none border shadow-sm kartu-aksi">
                <div class="card-body">
                    <div class="ikon-aksi ikon-aksi-ungu">🎮</div>
                    <h2 class="h6 fw-bold">Kelola Game</h2>
                    <p class="text-secondary small mt-1 mb-0">Tambah, edit, dan hapus game beserta soal-soalnya.</p>
                    <div class="small text-primary fw-semibold mt-3">Kelola &rarr;</div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.ai-settings.index') }}" class="card h-100 text-decoration-none border shadow-sm kartu-aksi">
                <div class="card-body">
                    <div class="ikon-aksi ikon-aksi-emas">⚙️</div>
                    <h2 class="h6 fw-bold">Pengaturan AI</h2>
                    <p class="text-secondary small mt-1 mb-0">Atur API key Gemini & Groq untuk pembuatan soal otomatis.</p>
                    <div class="small text-primary fw-semibold mt-3">Atur &rarr;</div>
                </div>
            </a>
        </div>
    </div>
@endsection
