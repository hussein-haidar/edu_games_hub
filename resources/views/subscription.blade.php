@extends('layouts.app')

@section('title', 'Langganan Premium')

@section('content')
    <h1 class="h3 fw-bold mb-1">Langganan Premium</h1>
    <p class="text-secondary mb-4">Buka semua game edukasi tanpa iklan. Pilih paket dan selesaikan pembayaranmu.</p>

    @if(auth()->user()->isSubscribed())
        <div class="alert alert-success border-success mb-4">
            <div class="fs-1 mb-2">✅</div>
            <h2 class="h5 fw-bold">Kamu Sudah Premium!</h2>
            <p class="mb-1">Langganan aktif hingga <strong>{{ auth()->user()->subscribed_until->format('d M Y') }}</strong>.</p>
            <p class="small mb-3">Kamu bisa memperpanjang kapan saja.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-success">Mulai Bermain</a>
        </div>
    @endif

    @if(config('payment.driver') === 'mock')
        <div class="alert alert-primary small">
            ⚙️ <strong>Mode demo (mock payment):</strong> pembayaran disimulasikan. Setelah checkout, kamu akan melihat halaman pembayaran simulasi dan bisa langsung mengonfirmasinya. Untuk menyalakan pembayaran asli (Midtrans), ubah <code>PAYMENT_DRIVER=midtrans</code> dan isi kunci Midtrans di <code>.env</code>.
        </div>
    @endif

    @if($plans->isEmpty())
        <div class="text-center text-secondary py-5 bg-white border rounded">Belum ada paket langganan.</div>
    @else
        <div class="row g-4">
            @foreach($plans as $plan)
                @php $pop = $plan->durasi_bulan == 3; @endphp
                <div class="col-md-3">
                    <div class="card h-100 {{ $pop ? 'border-primary border-2' : '' }} position-relative">
                        @if($pop)
                            <span class="position-absolute badge text-bg-primary" style="top:-0.7rem; left:1rem;">POPULER</span>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h3 class="h6 fw-bold">{{ $plan->nama }}</h3>
                            <div class="fs-3 fw-bold {{ $pop ? 'text-primary' : '' }}">
                                {{ $plan->getHargaFormattedAttribute() }}<span class="fs-6 text-secondary">/{{ $plan->durasi_bulan }} bln</span>
                            </div>
                            <p class="text-secondary small mt-2 flex-grow-1">{{ $plan->deskripsi }}</p>
                            <form method="POST" action="{{ route('subscription.checkout', $plan) }}" class="mt-3">
                                @csrf
                                <button class="btn w-100 {{ $pop ? 'btn-primary' : 'btn-light border' }} fw-semibold">
                                    {{ auth()->user()->isSubscribed() ? 'Perpanjang' : 'Langganan Sekarang' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="alert alert-light border small mt-4 mb-0">
        <strong>Butuh bantuan?</strong> Hubungi pengelola untuk aktivasi manual atau paket khusus sekolah.
        Admin bisa mengaktifkan langganan secara manual di panel admin.
    </div>
@endsection
