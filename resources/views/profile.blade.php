@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <h1 class="h3 fw-bold mb-4">Profil & Statistik</h1>

    <div class="row g-4">
        {{-- Kiri: Form edit profil --}}
        <div class="col-md-4">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width:3.5rem; height:3.5rem;">
                            <span class="fs-4 fw-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <div class="small text-secondary">{{ $user->email }}</div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama</label>
                            <input type="text" name="name" value="{{ $user->name }}" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Password Baru (opsional)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Ulangi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <button class="btn btn-primary w-100">Simpan Profil</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kanan: Statistik & riwayat --}}
        <div class="col-md-8">
            <div class="card border mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">Status Langganan</h2>
                    @if($user->isSubscribed())
                        <div class="d-flex justify-content-between align-items-center p-3 rounded bg-success-subtle border-success">
                            <div>
                                <div class="fw-semibold">✅ Premium Aktif</div>
                                <div class="small mt-1">Berlaku hingga <strong>{{ $user->subscribed_until->format('d M Y') }}</strong></div>
                            </div>
                            <a href="{{ route('subscription') }}" class="btn btn-sm btn-success">Perpanjang</a>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center p-3 rounded bg-warning-subtle border-warning">
                            <div>
                                <div class="fw-semibold">Belum Berlangganan</div>
                                <div class="small mt-1">Aktifkan premium untuk membuka semua game.</div>
                            </div>
                            <a href="{{ route('subscription') }}" class="btn btn-sm btn-warning">Langganan</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-3">
                    <div class="card text-center border"><div class="card-body py-3">
                        <div class="fs-4 fw-bold text-primary">⭐ {{ $user->points }}</div><div class="small text-secondary">Total Poin</div>
                    </div></div>
                </div>
                <div class="col-3">
                    <div class="card text-center border"><div class="card-body py-3">
                        <div class="fs-4 fw-bold text-success">{{ $totalBenar }}</div><div class="small text-secondary">Jawaban Benar</div>
                    </div></div>
                </div>
                <div class="col-3">
                    <div class="card text-center border"><div class="card-body py-3">
                        <div class="fs-4 fw-bold text-danger">{{ $totalSalah }}</div><div class="small text-secondary">Jawaban Salah</div>
                    </div></div>
                </div>
                <div class="col-3">
                    <div class="card text-center border"><div class="card-body py-3">
                        <div class="fs-4 fw-bold text-primary">{{ $totalGame }}</div><div class="small text-secondary">Game Dimainkan</div>
                    </div></div>
                </div>
            </div>

            <div class="card border mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">Riwayat Progres</h2>
                    @if($user->progres->isEmpty())
                        <div class="text-center text-secondary py-4">Belum ada game yang dimainkan.</div>
                    @else
                        @foreach($user->progres as $p)
                            <div class="d-flex justify-content-between align-items-center p-3 rounded border mb-2 bg-light">
                                <div>
                                    <div class="fw-semibold">{{ $p->game->nama }}</div>
                                    <div class="small text-secondary">{{ $p->game->mapel->nama }} · {{ $p->updated_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-3 small">
                                    <span class="text-success fw-medium">{{ $p->benar }} benar</span>
                                    <span class="text-danger">{{ $p->salah }} salah</span>
                                    <span class="fw-bold text-primary">⭐ {{ $p->skor }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            @if($subscriptions->isNotEmpty())
            <div class="card border">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">Riwayat Langganan</h2>
                    @foreach($subscriptions as $sub)
                        <div class="d-flex justify-content-between align-items-center p-3 rounded border mb-2 bg-light">
                            <div>
                                <div class="fw-semibold">{{ $sub->plan?->nama ?? 'Langganan' }}</div>
                                <div class="small text-secondary">{{ $sub->order_id }} · {{ $sub->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ $sub->plan?->getHargaFormattedAttribute() }}</div>
                                @if($sub->isPaid())
                                    <span class="badge text-bg-success">Lunas</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ ucfirst($sub->status) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
