@extends('admin.layouts.admin')

@section('title', 'Pengaturan AI')

@section('content')
    <div class="mx-auto" style="max-width: 46rem;">
        <div class="text-center mb-3">
            <a href="{{ route('admin.dashboard') }}" class="small text-primary text-decoration-none">← Kembali ke Dashboard</a>
            <h1 class="h3 fw-bold mt-2 mb-1">⚙️ Pengaturan AI</h1>
            <p class="text-secondary small mx-auto" style="max-width: 40rem;">
                API key dipakai oleh fitur <strong>✨ Buat Soal dengan AI</strong>. Key disimpan di server (tidak pernah
                dikirim ke browser) dan selalu dites sebelum disimpan. Kosongkan kolom jika tidak ingin mengubah key lama.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.ai-settings.update') }}" class="mt-3">
            @csrf

            <div class="mx-auto" style="max-width: 60rem;">
                @foreach ($drivers as $id => $info)
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h2 class="h6 fw-bold mb-0">
                                    {{ $id === 'gemini' ? '🤖' : '⚡' }} {{ $info['label'] }}
                                    @if ($info['configured'])
                                        <span class="badge text-bg-success ms-1">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary ms-1">Belum diatur</span>
                                    @endif
                                </h2>
                                <span class="small text-secondary">Model saat ini: <code>{{ $info['model'] ?: '—' }}</code></span>
                            </div>

                            <label class="form-label small fw-medium mt-3">API Key</label>
                            <div class="input-group input-group-sm mx-auto" style="max-width: 34rem;">
                                <input type="password"
                                       name="{{ $id }}_api_key"
                                       class="form-control"
                                       autocomplete="off"
                                       maxlength="500"
                                       placeholder="{{ $info['key_masked'] !== '' ? 'Tersimpan: '.$info['key_masked'].' — masukkan key baru untuk mengganti' : 'Belum ada key — paste di sini' }}"
                                       value="{{ old($id.'_api_key') }}">
                                <button type="button"
                                        class="btn btn-outline-secondary ai-test-btn"
                                        data-driver="{{ $id }}">
                                    🔌 Tes Koneksi
                                </button>
                            </div>
                            @error($id.'_api_key')
                                <div class="small text-danger mt-1 text-center">{{ $message }}</div>
                            @enderror
                            <div class="small mt-1 ai-test-result text-center" data-driver="{{ $id }}"></div>
                            <div class="small text-secondary mt-1 text-center">💡 {{ $info['hint'] }}</div>
                        </div>
                    </div>
                @endforeach

                <div class="text-center">
                    <button type="submit" class="btn btn-primary fw-semibold">💾 Simpan Pengaturan</button>
                    <div class="small text-secondary mt-2">Key baru hanya tersimpan jika lolos tes koneksi.</div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = document.querySelector('meta[name=csrf-token]').content;
            const urlUji = @json(route('admin.ai-settings.test'));

            document.querySelectorAll('.ai-test-btn').forEach(function (btn) {
                btn.addEventListener('click', async function () {
                    const driver = btn.dataset.driver;
                    const input = document.querySelector('input[name="' + driver + '_api_key"]');
                    const hasil = document.querySelector('.ai-test-result[data-driver="' + driver + '"]');

                    hasil.textContent = '⏳ Menguji koneksi...';
                    hasil.className = 'small ai-test-result text-secondary text-center';
                    btn.disabled = true;

                    try {
                        const res = await fetch(urlUji, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({ driver: driver, api_key: input.value }),
                        });
                        const data = await res.json();
                        hasil.textContent = (data.ok ? '✅ ' : '❌ ') + data.message;
                        hasil.className = 'small ai-test-result ' + (data.ok ? 'text-success' : 'text-danger') + ' text-center';
                    } catch (err) {
                        hasil.textContent = '❌ Gagal menghubungi server. Coba lagi.';
                        hasil.className = 'small ai-test-result text-danger text-center';
                    } finally {
                        btn.disabled = false;
                    }
                });
            });
        });
    </script>
@endpush
