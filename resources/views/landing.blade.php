<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edu Games Hub — Belajar Sambil Bermain</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-white">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a href="/" class="navbar-brand fw-bold text-primary">
                <span class="fs-4">🎮</span> Edu Games Hub
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Daftar Gratis</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <section class="text-white text-center" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
            <div class="container py-5">
                <h1 class="display-4 fw-bold">Belajar Sambil Bermain</h1>
                <p class="lead mx-auto" style="max-width: 40rem;">Kumpulan game edukasi seru untuk anak SD. Kuis Matematika, IPA dan banyak lagi — tanpa iklan mengganggu.</p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold text-primary">Daftar Sekarang</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg fw-bold">Masuk</a>
                </div>
            </div>
        </section>

        <section class="container py-5">
            <h2 class="fw-bold text-center mb-4">Fitur Edu Games Hub</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body text-center">
                            <div class="fs-1 mb-2">🧠</div>
                            <h3 class="card-title h5 fw-bold">Game Edukasi</h3>
                            <p class="card-text text-secondary">Berbagai jenis game: pilihan ganda, benar/salah, teka-teki, isian singkat, mengurutkan, dan menjodohkan gambar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body text-center">
                            <div class="fs-1 mb-2">🚫</div>
                            <h3 class="card-title h5 fw-bold">Tanpa Iklan</h3>
                            <p class="card-text text-secondary">Fokus belajar tanpa gangguan. Pengalaman bersih untuk anak-anak.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body text-center">
                            <div class="fs-1 mb-2">⭐</div>
                            <h3 class="card-title h5 fw-bold">Poin & Premium</h3>
                            <p class="card-text text-secondary">Kumpulkan poin dari setiap game. Buka semua game lewat langganan premium.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white border-top py-3 text-center small text-secondary">
        Edu Games Hub &copy; {{ date('Y') }}
    </footer>
</body>
</html>