<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Edu Games Hub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="navbar-brand fw-bold text-primary">
                <span class="fs-4">🎮</span> Edu Games Hub
            </a>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(auth()->user()->isSubscribed())
                    <span class="badge rounded-pill text-bg-success">Premium</span>
                @endif
                <a href="{{ route('subscription') }}" class="btn btn-sm btn-link text-secondary text-decoration-none">Langganan</a>
                <span class="badge rounded-pill text-bg-primary">⭐ {{ auth()->user()->points }} poin</span>
                <a href="{{ route('profile') }}" class="fw-semibold text-dark text-decoration-none">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Keluar</button>
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