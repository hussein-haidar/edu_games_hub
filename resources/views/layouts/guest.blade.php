<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Edu Games Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center p-3" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
    <div class="w-100" style="max-width: 26rem;">
        <div class="text-center mb-4">
            <span class="fs-1">🎮</span>
            <h1 class="fw-bold text-white mt-2">Edu Games Hub</h1>
        </div>
        <div class="card shadow-lg border-0 p-4">
            @yield('content')
        </div>
    </div>
</body>
</html>