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
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin" aria-controls="navAdmin" aria-expanded="false" aria-label="Buka menu admin">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navAdmin">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link nav-admin-tautan {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            📊 Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link nav-admin-tautan {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            👥 Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.mapels.index') }}" class="nav-link nav-admin-tautan {{ request()->routeIs('admin.mapels.*') ? 'active' : '' }}">
                            📚 Mapel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.games.index') }}" class="nav-link nav-admin-tautan {{ request()->routeIs('admin.games.*') ? 'active' : '' }}">
                            🎮 Game
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.ai-settings.index') }}" class="nav-link nav-admin-tautan {{ request()->routeIs('admin.ai-settings.*') ? 'active' : '' }}">
                            ⚙️ Pengaturan AI
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2 flex-wrap py-2 py-lg-0">
                    <span class="chip-admin">
                        <span class="avatar-user">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light rounded-pill">Keluar</button>
                    </form>
                </div>
            </div>
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