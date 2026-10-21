<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Edu Games Hub')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-utama sticky-top">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <span class="logo-utama">🎮</span>
                <span class="brand-teks">Edu Games Hub</span>
            </a>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(auth()->user()->isSubscribed())
                    <span class="badge-premium">👑 Premium</span>
                    <a href="{{ route('subscription') }}" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">Langganan</a>
                @else
                    <a href="{{ route('subscription') }}" class="btn btn-sm btn-premium rounded-pill">✨ Coba Premium</a>
                @endif
                <a href="{{ route('profile') }}" class="poin-pill">⭐ {{ auth()->user()->points }} poin</a>
                <a href="{{ route('profile') }}" class="chip-user">
                    <span class="avatar-user">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    {{ auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary rounded-pill">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 w-100 py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-top py-3 text-center small text-secondary">
        Edu Games Hub &copy; {{ date('Y') }} — Belajar sambil bermain
    </footer>

    @stack('scripts')
</body>
</html>