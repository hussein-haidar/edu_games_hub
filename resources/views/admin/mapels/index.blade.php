@extends('admin.layouts.admin')

@section('title', 'Mata Pelajaran')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.mapel-edit-modal').forEach(function (modal) {
                modal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    if (!btn) return;
                    const form = modal.querySelector('form');
                    form.querySelector('[name=nama]').value = btn.dataset.nama;
                    form.querySelector('[name=kelas]').value = btn.dataset.kelas;
                    form.querySelector('[name=deskripsi]').value = btn.dataset.deskripsi;
                    form.querySelector('[name=warna]').value = btn.dataset.warna;
                    form.querySelector('[name=maskot]').value = btn.dataset.maskot || '';
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="d-flex align-items-center justify-content-between gap-3 mb-3 flex-wrap">
        <h1 class="h3 fw-bold mb-0">Mata Pelajaran</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahMapel">+ Tambah Mapel</button>
    </div>

    <div class="modal fade" id="tambahMapel" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">+ Tambah Mapel</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.mapels.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama</label>
                        <input type="text" name="nama" required class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Kelas</label>
                        <input type="text" name="kelas" value="SD" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" class="form-control form-control-sm"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Icon</label>
                        <select name="icon" class="form-select form-select-sm">
                            <option value="calculator">🧮 Kalkulator</option>
                            <option value="flask">🧪 Labu</option>
                            <option value="book">📚 Buku</option>
                            <option value="globe">🌍 Globe</option>
                            <option value="shield">🛡️ Perisai</option>
                            <option value="music">🎵 Musik</option>
                            <option value="flag">🚩 Bendera</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Maskot (emoji, opsional)</label>
                        <input type="text" name="maskot" maxlength="4" placeholder="cth: 🦎" class="form-control form-control-sm">
                        <div class="form-text">Karakter khas mapel ini. Kosongkan untuk memakai icon.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Warna (hex)</label>
                        <input type="text" name="warna" value="#4f46e5" class="form-control form-control-sm">
                    </div>
                    <button class="btn btn-primary btn-sm w-100 fw-semibold">Simpan</button>
                </form>
            </div>
        </div></div>
    </div>

    <div class="row g-4">
        @forelse($mapels as $mapel)
            <div class="col-md-6">
                <div class="card h-100 border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3">
                                <div class="rounded d-flex align-items-center justify-content-center text-white fs-3" style="width:3rem; height:3rem; background: {{ $mapel->warna }}">
                                    {{ $mapel->karakter }}
                                </div>
                                <div>
                                    <h3 class="h6 fw-bold mb-0">{{ $mapel->nama }}</h3>
                                    <p class="small text-secondary mb-0">{{ $mapel->kelas }} · {{ $mapel->games_count }} game</p>
                                </div>
                            </div>
                            <span class="badge {{ $mapel->aktif ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $mapel->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <p class="small text-secondary mt-3">{{ $mapel->deskripsi }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-light border"
                                data-bs-toggle="modal" data-bs-target="#editMapel-{{ $mapel->id }}"
                                data-nama="{{ $mapel->nama }}" data-kelas="{{ $mapel->kelas }}"
                                data-deskripsi="{{ $mapel->deskripsi }}" data-warna="{{ $mapel->warna }}" data-maskot="{{ $mapel->maskot }}">Edit</button>
                            <form method="POST" action="{{ route('admin.mapels.destroy', $mapel) }}" onsubmit="return confirm('Hapus mapel ini beserta semua game & soalnya?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade mapel-edit-modal" id="editMapel-{{ $mapel->id }}" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Edit: {{ $mapel->nama }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <form id="editMapelForm-{{ $mapel->id }}" method="POST" action="{{ route('admin.mapels.update', $mapel) }}">
                                @csrf @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Nama</label>
                                    <input type="text" name="nama" value="{{ $mapel->nama }}" required class="form-control form-control-sm">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Kelas</label>
                                    <input type="text" name="kelas" value="{{ $mapel->kelas }}" class="form-control form-control-sm">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Deskripsi</label>
                                    <textarea name="deskripsi" rows="2" class="form-control form-control-sm">{{ $mapel->deskripsi }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Warna</label>
                                    <input type="text" name="warna" value="{{ $mapel->warna }}" class="form-control form-control-sm">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Maskot (emoji, opsional)</label>
                                    <input type="text" name="maskot" value="{{ $mapel->maskot }}" maxlength="4" placeholder="cth: 🦎" class="form-control form-control-sm">
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="aktif" value="1" {{ $mapel->aktif ? 'checked' : '' }} class="form-check-input" id="aktif-{{ $mapel->id }}">
                                    <label class="form-check-label small" for="aktif-{{ $mapel->id }}">Aktif</label>
                                </div>
                                <button class="btn btn-primary btn-sm w-100 fw-semibold">Simpan</button>
                            </form>
                        </div>
                    </div></div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-center text-secondary py-4 bg-white border rounded">Belum ada mapel.</p></div>
        @endforelse
    </div>
@endsection
