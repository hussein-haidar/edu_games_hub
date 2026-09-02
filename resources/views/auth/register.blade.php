@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <h2 class="h3 fw-bold text-center mb-4">Buat Akun</h2>

    @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Ulangi Password</label>
            <input type="password" name="password_confirmation" required class="form-control">
        </div>
        <button class="btn btn-primary w-100">Daftar</button>
    </form>

    <p class="text-center small text-secondary mt-4">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary">Masuk</a>
    </p>
@endsection
