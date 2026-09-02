@extends('layouts.app')

@section('title', 'Hasil Langganan')

@section('content')
    <a href="{{ route('subscription') }}" class="small text-primary text-decoration-none">&larr; Kembali ke Langganan</a>

    <div class="card border mx-auto mt-4 p-4" style="max-width: 40rem;">
        <div class="card-body text-center">
            @if($subscription->isPaid())
                <div class="fs-1 mb-3">🎉</div>
                <h1 class="h3 fw-bold">Pembayaran Berhasil!</h1>
                <p class="text-secondary">Langganan kamu sudah aktif.</p>
                <div class="rounded bg-light p-3 text-start small mt-4">
                    <div class="d-flex justify-content-between py-1"><span class="text-secondary">Order ID</span><span class="fw-medium">{{ $subscription->order_id }}</span></div>
                    <div class="d-flex justify-content-between py-1"><span class="text-secondary">Paket</span><span class="fw-medium">{{ $subscription->plan?->nama }}</span></div>
                    <div class="d-flex justify-content-between py-1"><span class="text-secondary">Pembayaran</span><span class="fw-medium">{{ $subscription->payment_method }}</span></div>
                    <div class="d-flex justify-content-between py-1"><span class="text-secondary">Berlaku hingga</span><span class="fw-medium">{{ optional($subscription->ends_at)->format('d M Y') }}</span></div>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-4 px-4 fw-semibold">Mulai Bermain</a>
            @else
                <div class="fs-1 mb-3">⏳</div>
                <h1 class="h3 fw-bold">Pembayaran Belum Selesai</h1>
                <p class="text-secondary">Status: {{ $subscription->status }}</p>
                <a href="{{ route('subscription') }}" class="btn btn-primary mt-4 px-4 fw-semibold">Coba Lagi</a>
            @endif
        </div>
    </div>
@endsection
