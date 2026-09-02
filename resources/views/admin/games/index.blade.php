@extends('admin.layouts.admin')

@section('title', 'Game')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <h1 class="h3 fw-bold mb-0">Daftar Game</h1>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('admin.games.index') }}" class="d-flex gap-2">
                <select name="mapel_id" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="">Semua Mapel</option>
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.games.create') }}" class="btn btn-sm btn-primary fw-semibold">+ Tambah Game</a>
        </div>
    </div>

    <div class="card border">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Game</th>
                        <th>Mapel</th>
                        <th>Jenis</th>
                        <th>Soal</th>
                        <th>Status</th>
                        <th class="pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($games as $game)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $game->nama }}</td>
                            <td class="text-secondary">{{ $game->mapel->nama }}</td>
                            <td>
                                @switch($game->jenis)
                                    @case('kuis') Pilihan Ganda @break
                                    @case('bener_salah') Benar/Salah @break
                                    @case('teka_teki') Teka-Teki Jodohkan @break
                                    @default {{ $game->jenis }}
                                @endswitch
                            </td>
                            <td>{{ $game->soals_count }}</td>
                            <td><span class="badge {{ $game->aktif ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $game->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="pe-3">
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('admin.soals.index', $game) }}" class="btn btn-sm btn-info text-white">Soal</a>
                                    <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-sm btn-light border">Edit</a>
                                    <form method="POST" action="{{ route('admin.games.destroy', $game) }}" onsubmit="return confirm('Hapus game beserta soalnya?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada game.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $games->links() }}</div>
@endsection
