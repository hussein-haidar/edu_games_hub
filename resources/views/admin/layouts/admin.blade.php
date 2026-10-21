<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Edu Games Hub</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top">
        <div class="container-fluid">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand fw-bold">
                <span class="logo-utama">🎮</span> Admin Edu Games Hub
            </a>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-white-50 small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-light rounded-pill">Keluar</button>
                </form>
            </div>
        </div>
        <div class="container-fluid pb-2 d-flex flex-wrap gap-1">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm rounded-pill {{ request()->routeIs('admin.dashboard') ? 'btn-light text-dark' : 'btn-outline-light' }}">Dashboard</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm rounded-pill {{ request()->routeIs('admin.users.*') ? 'btn-light text-dark' : 'btn-outline-light' }}">Pengguna & Langganan</a>
            <a href="{{ route('admin.mapels.index') }}" class="btn btn-sm rounded-pill {{ request()->routeIs('admin.mapels.*') ? 'btn-light text-dark' : 'btn-outline-light' }}">Mata Pelajaran</a>
            <a href="{{ route('admin.games.index') }}" class="btn btn-sm rounded-pill {{ request()->routeIs('admin.games.*') ? 'btn-light text-dark' : 'btn-outline-light' }}">Game</a>
            <a href="{{ route('admin.ai-settings.index') }}" class="btn btn-sm rounded-pill {{ request()->routeIs('admin.ai-settings.*') ? 'btn-light text-dark' : 'btn-outline-light' }}">⚙️ Pengaturan AI</a>
        </div>
    </nav>

    <main class="flex-grow-1 w-100 py-4">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>