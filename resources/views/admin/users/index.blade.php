@extends('admin.layouts.admin')

@section('title', 'Pengguna & Langganan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <h1 class="h3 fw-bold mb-0">Pengguna & Langganan</h1>
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/email..." class="form-control form-control-sm">
            <button class="btn btn-sm btn-primary">Cari</button>
        </form>
    </div>

    <div class="card border">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nama</th>
                        <th>Email</th>
                        <th>Poin</th>
                        <th>Status Langganan</th>
                        <th class="pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $user->name }}</td>
                            <td class="text-secondary">{{ $user->email }}</td>
                            <td>⭐ {{ $user->points }}</td>
                            <td>
                                @if($user->isSubscribed())
                                    <span class="badge text-bg-success">Aktif s/d {{ $user->subscribed_until->format('d M Y') }}</span>
                                @else
                                    <span class="badge text-bg-secondary">Tidak aktif</span>
                                @endif
                            </td>
                            <td class="pe-3">
                                @if(!$user->isSubscribed())
                                    <form method="POST" action="{{ route('admin.users.subscription', $user) }}" class="d-flex gap-1 align-items-center">
                                        @csrf
                                        <select name="durasi" class="form-select form-select-sm" style="width:auto;">
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}">{{ $m }} bln</option>
                                            @endfor
                                        </select>
                                        <button class="btn btn-sm btn-success">Aktifkan</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.cancel-subscription', $user) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">Batalkan</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Tidak ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
