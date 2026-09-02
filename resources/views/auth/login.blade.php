@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h2 class="h3 fw-bold text-center mb-4">Masuk</h2>

    @if($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>
        <button class="btn btn-primary w-100">Masuk</button>
    </form>

    <p class="text-center small text-secondary mt-4">
        Belum punya akun? <a href="{{ route('register') }}" class="text-primary">Daftar</a>
    </p>
    <p class="text-center small text-secondary">
        <a href="{{ route('home') }}" class="text-muted text-decoration-none">&larr; Kembali</a>
    </p>
@endsection
