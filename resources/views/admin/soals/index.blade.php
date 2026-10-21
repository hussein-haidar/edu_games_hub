@extends('admin.layouts.admin')

@section('title', 'Soal — '.$game->nama)

@section('content')
    <a href="{{ route('admin.games.index') }}" class="small text-primary text-decoration-none">&larr; Kembali ke Game</a>
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-2 mb-1">
        <h1 class="h3 fw-bold mb-0">Soal Game: {{ $game->nama }}</h1>
        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#aiModal">
            ✨ Buat Soal dengan AI
        </button>
    </div>
    <p class="text-secondary small">{{ $game->mapel->nama }} · {{ $game->soals->count() }} soal · {{ $game->jenis_label }}</p>

    {{-- ===== Modal AI ===== --}}
    <div class="modal fade" id="aiModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">✨ Buat Soal dengan AI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="aiAlert" class="alert d-none py-2 small" role="alert"></div>

                <form id="aiForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium">Topik / Materi Ajar</label>
                            <textarea name="topik" rows="2" required maxlength="500" class="form-control form-control-sm"
                                placeholder="cth: Pecahan sederhana untuk kelas 4 SD, pencatatan waktu, atau tokoh pahlawan Indonesia">{{ old('topik') }}</textarea>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-medium">Jumlah</label>
                            <input type="number" name="jumlah" value="5" min="1" max="{{ $ai['max'] }}" required class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-medium">Kesulitan</label>
                            <select name="kesulitan" class="form-select form-select-sm">
                                <option value="mudah">Mudah</option>
                                <option value="sedang" selected>Sedang</option>
                                <option value="sulit">Sulit</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-medium">Driver AI</label>
                            <select name="driver" class="form-select form-select-sm">
                                <option value="mock" {{ $ai['driver'] === 'mock' ? 'selected' : '' }}>🧪 Mock (simulasi)</option>
                                <option value="gemini" {{ $ai['driver'] === 'gemini' ? 'selected' : '' }} {{ $ai['configured']['gemini'] ? '' : 'disabled' }}>
                                    🟦 Gemini {{ $ai['configured']['gemini'] ? '' : '(key belum diisi)' }}
                                </option>
                                <option value="groq" {{ $ai['driver'] === 'groq' ? 'selected' : '' }} {{ $ai['configured']['groq'] ? '' : 'disabled' }}>
                                    🟧 Groq {{ $ai['configured']['groq'] ? '' : '(key belum diisi)' }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" id="aiGenerateBtn" class="btn btn-primary btn-sm fw-semibold mt-3">
                        🪄 Buatkan Soal
                    </button>
                    <span class="small text-secondary ms-2">Maks. {{ $ai['max'] }} soal per request</span>
                </form>

                <hr class="my-4">

                <div id="aiPreviewWrap" class="d-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="h6 fw-bold mb-0">👀 Tinjau hasil AI (centang yang ingin disimpan)</h3>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="aiCheckAll" checked>
                            <label class="form-check-label small" for="aiCheckAll">Semua</label>
                        </div>
                    </div>
                    <div id="aiPreview" class="small"></div>
                    <button type="button" id="aiSaveBtn" class="btn btn-success btn-sm fw-semibold mt-3 w-100">
                        💾 Simpan Soal Terpilih
                    </button>
                </div>
            </div>
        </div></div>
    </div>


    <div class="mb-4">
        <div class="card border mx-auto" style="max-width: 44rem;">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">+ Tambah Soal</h2>
                @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.soals.store', $game) }}" @if($game->jenis === 'cocok_gambar') enctype="multipart/form-data" @endif>
                    @csrf
                    @if($game->jenis === 'cocok_gambar')
                        <script type="application/json" id="pairOldData">{!! json_encode(old('pair', [])) !!}</script>
                    @endif
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Pertanyaan</label>
                        <input type="text" name="pertanyaan" value="{{ old('pertanyaan') }}" required class="form-control form-control-sm">
                    </div>

                    @if($game->jenis === 'kuis')
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Pilihan Jawaban</label>
                            <div class="row g-2">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="col-6">
                                        <input type="text" name="pilihan[]" value="{{ old('pilihan.'.$i) }}" placeholder="Pilihan {{ $i+1 }}" class="form-control form-control-sm">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Jawaban Benar</label>
                        @if($game->jenis === 'bener_salah')
                            <select name="jawaban_benar" required class="form-select form-select-sm">
                                <option value="Benar">Benar</option>
                                <option value="Salah">Salah</option>
                            </select>
                        @elseif($game->jenis === 'isian')
                            <input type="text" name="jawaban_benar" value="{{ old('jawaban_benar') }}" required class="form-control form-control-sm">
                            <div class="form-text">Tidak peka huruf besar/kecil. Boleh beberapa jawaban yang diterima, pisahkan dengan <code>|</code> — mis. <code>paru-paru|paru paru</code>.</div>
                        @elseif(in_array($game->jenis, ['urutkan', 'cocok_gambar']))
                            {{-- Diisi otomatis dari input di bawah --}}
                        @else
                            <input type="text" name="jawaban_benar" value="{{ old('jawaban_benar') }}" required class="form-control form-control-sm">
                        @endif
                    </div>

                    @if($game->jenis === 'urutkan')
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Item yang Diurutkan</label>
                            <textarea name="urutan_items" rows="4" required class="form-control form-control-sm" placeholder="Satu item per baris, urut dari yang benar:&#10;Bangun tidur&#10;Gosok gigi&#10;Sarapan&#10;Berangkat sekolah">{{ old('urutan_items') }}</textarea>
                            <div class="form-text">Baris pertama = urutan pertama (benar). Minimal 2, maksimal 8 item.</div>
                        </div>
                    @endif

                    @if($game->jenis === 'cocok_gambar')
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Pasangan Gambar &amp; Keterangan</label>
                            <div id="pairRows"></div>
                            <button type="button" id="pairAdd" class="btn btn-sm btn-outline-primary mt-2">+ Tambah baris</button>
                            <div class="form-text">Isi emoji (mis. 🍎) ATAU upload foto (JPG/PNG/WebP, maks. 1 MB), lalu keterangannya. Minimal 2 pasangan.</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Penjelasan (opsional)</label>
                        <input type="text" name="penjelasan" value="{{ old('penjelasan') }}" class="form-control form-control-sm">
                    </div>

                    <button class="btn btn-sm btn-primary w-100 fw-semibold">Tambahkan Soal</button>
                </form>
            </div>
        </div>
    </div>

    <div class="mx-auto" style="max-width: 44rem;">
        @forelse($game->soals as $soal)
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold">{{ $soal->urutan }}. {{ $soal->pertanyaan }}</div>
                            @if($game->jenis === 'urutkan' && $soal->pilihan)
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach($soal->pilihan as $p)
                                        <span class="badge text-bg-light border">{{ $loop->iteration }}. {{ $p }}</span>
                                    @endforeach
                                </div>
                            @elseif($game->jenis === 'cocok_gambar' && $soal->pilihan)
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach($soal->pilihan as $p)
                                        <span class="badge text-bg-light border d-inline-flex align-items-center gap-1">
                                            @if(\App\Models\Soal::gambarTerupload($p['kiri']))
                                                <img src="{{ asset('storage/'.$p['kiri']) }}" alt="" style="height:1.4em;width:1.4em;object-fit:cover;border-radius:.25rem;">
                                            @else
                                                <span style="font-size:1.1em;">{{ $p['kiri'] }}</span>
                                            @endif
                                            <span>= {{ $p['kanan'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @elseif($soal->pilihan)
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach($soal->pilihan as $p)
                                        <span class="badge {{ $p === $soal->jawaban_benar ? 'text-bg-success' : 'text-bg-light border' }}">{{ $p }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @unless($game->jenis === 'urutkan' || $game->jenis === 'cocok_gambar')
                                <div class="small mt-2">
                                    <span class="text-success fw-medium">Jawaban: {{ $game->jenis === 'isian' ? str_replace('|', ' atau ', $soal->jawaban_benar) : $soal->jawaban_benar }}</span>
                                    @if($soal->penjelasan)
                                        <span class="text-secondary"> — {{ $soal->penjelasan }}</span>
                                    @endif
                                </div>
                            @else
                                <div class="small mt-2">
                                    @if($soal->penjelasan)
                                        <span class="text-secondary">💡 {{ $soal->penjelasan }}</span>
                                    @endif
                                </div>
                            @endunless
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editSoal-{{ $soal->id }}">Edit</button>
                            <form method="POST" action="{{ route('admin.soals.destroy', [$game, $soal]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger w-100">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editSoal-{{ $soal->id }}" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Edit Soal #{{ $soal->urutan }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.soals.update', [$game, $soal]) }}" @if($game->jenis === 'cocok_gambar') enctype="multipart/form-data" @endif>
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Pertanyaan</label>
                                <input type="text" name="pertanyaan" value="{{ $soal->pertanyaan }}" required class="form-control form-control-sm">
                            </div>
                            @if($game->jenis === 'kuis')
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Pilihan</label>
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="mb-2">
                                            <input type="text" name="pilihan[]" value="{{ $soal->pilihan[$i] ?? '' }}" placeholder="Pilihan {{ $i+1 }}" class="form-control form-control-sm">
                                        </div>
                                    @endfor
                                </div>
                            @endif
                            @if($game->jenis === 'urutkan')
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Item yang Diurutkan</label>
                                    <textarea name="urutan_items" rows="4" required class="form-control form-control-sm">{{ old('urutan_items', implode("\n", $soal->pilihan ?? [])) }}</textarea>
                                    <div class="form-text">Satu item per baris, urut dari yang benar.</div>
                                </div>
                            @endif
                            @if($game->jenis === 'cocok_gambar')
                                <script type="application/json" class="pair-data">{!! json_encode($soal->pilihan ?? []) !!}</script>
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Pasangan Gambar &amp; Keterangan</label>
                                    <div class="pair-rows"></div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2 pair-add">+ Tambah baris</button>
                                    <div class="form-text">Emoji/teks ATAU upload foto baru (maks. 1 MB). Kosongkan kolom emoji bila mempertahankan foto lama.</div>
                                </div>
                            @endif
                            @unless($game->jenis === 'urutkan' || $game->jenis === 'cocok_gambar')
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Jawaban benar</label>
                                    <input type="text" name="jawaban_benar" value="{{ $soal->jawaban_benar }}" required class="form-control form-control-sm">
                                    @if($game->jenis === 'isian')
                                        <div class="form-text">Beberapa varian jawaban dipisah <code>|</code>.</div>
                                    @endif
                                </div>
                            @endunless
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Penjelasan</label>
                                <input type="text" name="penjelasan" value="{{ $soal->penjelasan }}" class="form-control form-control-sm">
                            </div>
                            <button class="btn btn-sm btn-primary w-100 fw-semibold">Simpan</button>
                        </form>
                    </div>
                </div></div>
            </div>
        @empty
            <p class="text-center text-secondary py-4 bg-white border rounded">Belum ada soal. Tambahkan di atas.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('aiForm');
            const alertBox = document.getElementById('aiAlert');
            const btnGenerate = document.getElementById('aiGenerateBtn');
            const btnSave = document.getElementById('aiSaveBtn');
            const previewWrap = document.getElementById('aiPreviewWrap');
            const previewBox = document.getElementById('aiPreview');
            const checkAll = document.getElementById('aiCheckAll');
            let hasilAI = [];

            const csrf = document.querySelector('#aiForm input[name=_token]').value;
            const urlGenerate = @json(route('admin.soals.generate-ai', $game));
            const urlSave = @json(route('admin.soals.save-ai', $game));
            const jenisGame = @json($game->jenis);

            function showAlert(tipe, pesan) {
                alertBox.className = 'alert alert-' + tipe + ' py-2 small';
                alertBox.textContent = pesan;
            }

            function sembunyikanAlert() {
                alertBox.className = 'alert d-none py-2 small';
            }

            function esc(str) {
                const d = document.createElement('div');
                d.textContent = str ?? '';
                return d.innerHTML;
            }

            // ===== Generate =====
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                sembunyikanAlert();
                btnGenerate.disabled = true;
                btnGenerate.textContent = '⏳ AI sedang menulis soal...';
                previewWrap.classList.add('d-none');

                try {
                    const res = await fetch(urlGenerate, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(form))),
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        showAlert('danger', data.message || 'Terjadi kesalahan saat membuat soal.');
                        return;
                    }

                    hasilAI = data.soals;
                    renderPreview();
                    showAlert('success', hasilAI.length + ' soal dibuat. Tinjau lalu simpan.');
                } catch (err) {
                    showAlert('danger', 'Gagal menghubungi server. Coba lagi.');
                } finally {
                    btnGenerate.disabled = false;
                    btnGenerate.textContent = '🪄 Buatkan Soal';
                }
            });

            // ===== Render preview =====
            function renderPreview() {
                previewBox.innerHTML = '';
                hasilAI.forEach(function (soal, i) {
                    const card = document.createElement('div');
                    card.className = 'border rounded p-3 mb-2 bg-white';

                    let html = '<div class="form-check mb-1">' +
                        '<input type="checkbox" class="form-check-input ai-pick" data-i="' + i + '" checked id="aiPick' + i + '">' +
                        '<label class="form-check-label fw-semibold" for="aiPick' + i + '">' + esc(soal.pertanyaan) + '</label></div>';

                    if (jenisGame === 'cocok_gambar' && soal.pilihan && soal.pilihan.length) {
                        html += '<div class="d-flex flex-wrap gap-1 ms-4">';
                        soal.pilihan.forEach(function (p) {
                            html += '<span class="badge text-bg-light border">' + esc(p.kiri) + ' = ' + esc(p.kanan) + '</span>';
                        });
                        html += '</div>';
                    } else if (jenisGame === 'urutkan' && soal.pilihan && soal.pilihan.length) {
                        html += '<div class="d-flex flex-wrap gap-1 ms-4">';
                        soal.pilihan.forEach(function (p, n) {
                            html += '<span class="badge text-bg-light border">' + (n + 1) + '. ' + esc(p) + '</span>';
                        });
                        html += '</div>';
                    } else if (soal.pilihan && soal.pilihan.length) {
                        html += '<div class="d-flex flex-wrap gap-1 ms-4">';
                        soal.pilihan.forEach(function (p) {
                            const benar = p === soal.jawaban_benar;
                            html += '<span class="badge ' + (benar ? 'text-bg-success' : 'text-bg-light border') + '">' + esc(p) + '</span>';
                        });
                        html += '</div>';
                    } else {
                        html += '<div class="ms-4"><span class="badge text-bg-success">' + esc(soal.jawaban_benar) + '</span></div>';
                    }

                    if (soal.penjelasan) {
                        html += '<div class="small text-secondary ms-4 mt-1">💡 ' + esc(soal.penjelasan) + '</div>';
                    }

                    card.innerHTML = html;
                    previewBox.appendChild(card);
                });
                previewWrap.classList.remove('d-none');
            }

            // ===== Centang semua =====
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('.ai-pick').forEach(function (cb) {
                    cb.checked = checkAll.checked;
                });
            });

            // ===== Simpan terpilih =====
            btnSave.addEventListener('click', async function () {
                sembunyikanAlert();
                const terpilih = hasilAI.filter(function (_, i) {
                    const cb = document.getElementById('aiPick' + i);
                    return cb && cb.checked;
                });

                if (terpilih.length === 0) {
                    showAlert('warning', 'Centang minimal satu soal untuk disimpan.');
                    return;
                }

                btnSave.disabled = true;
                btnSave.textContent = '⏳ Menyimpan...';

                try {
                    const res = await fetch(urlSave, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ soals: terpilih }),
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        showAlert('danger', data.message || 'Gagal menyimpan soal.');
                        return;
                    }

                    // Sukses → muat ulang halaman supaya daftar soal segar
                    window.location.reload();
                } catch (err) {
                    showAlert('danger', 'Gagal menghubungi server. Coba lagi.');
                    btnSave.disabled = false;
                    btnSave.textContent = '💾 Simpan Soal Terpilih';
                }
            });

            // ===== Baris pasangan (game menjodohkan gambar) =====
            function escAtr(s) { return (s ?? '').toString(); }

            function buatBarisPasangan(container, index, data) {
                data = data || {};
                const gambarLama = (data.kiri || '').indexOf('gambar-soal/') === 0 ? data.kiri : null;

                const row = document.createElement('div');
                row.className = 'pair-row d-flex gap-2 align-items-start mb-2';

                const kolomKiri = document.createElement('div');
                kolomKiri.style.cssText = 'flex: 0 0 5.5rem;';
                const inputKiri = document.createElement('input');
                inputKiri.type = 'text';
                inputKiri.name = 'pair[' + index + '][kiri]';
                inputKiri.placeholder = '🍎';
                inputKiri.className = 'form-control form-control-sm';
                kolomKiri.appendChild(inputKiri);
                if (gambarLama) {
                    const thumb = document.createElement('img');
                    thumb.src = '{{ asset('storage') }}/' + gambarLama;
                    thumb.style.cssText = 'width:2.5rem;height:2.5rem;object-fit:cover;border-radius:.35rem;margin-top:.25rem;';
                    kolomKiri.appendChild(thumb);
                    const lama = document.createElement('input');
                    lama.type = 'hidden';
                    lama.name = 'pair[' + index + '][lama]';
                    lama.value = gambarLama;
                    kolomKiri.appendChild(lama);
                }

                const kolomFile = document.createElement('div');
                kolomFile.style.cssText = 'flex: 0 0 9.5rem;';
                const inputFile = document.createElement('input');
                inputFile.type = 'file';
                inputFile.name = 'pair[' + index + '][gambar]';
                inputFile.accept = 'image/jpeg,image/png,image/webp';
                inputFile.className = 'form-control form-control-sm';
                kolomFile.appendChild(inputFile);

                const kolomKanan = document.createElement('div');
                kolomKanan.className = 'flex-grow-1';
                const inputKanan = document.createElement('input');
                inputKanan.type = 'text';
                inputKanan.name = 'pair[' + index + '][kanan]';
                inputKanan.placeholder = 'Keterangan';
                inputKanan.className = 'form-control form-control-sm';
                kolomKanan.appendChild(inputKanan);

                const btnHapus = document.createElement('button');
                btnHapus.type = 'button';
                btnHapus.className = 'btn btn-sm btn-outline-danger';
                btnHapus.textContent = '×';
                btnHapus.addEventListener('click', function () { row.remove(); });

                // Set lewat properti .value supaya aman dari tanda kutip
                inputKiri.value = gambarLama ? '' : escAtr(data.kiri);
                inputKanan.value = escAtr(data.kanan);

                row.append(kolomKiri, kolomFile, kolomKanan, btnHapus);
                container.appendChild(row);
            }

            function siapkanBarisPasangan(container, addBtn, dataAwal) {
                let next = 0;
                const tambah = function (data) { buatBarisPasangan(container, next++, data); };
                (dataAwal && dataAwal.length ? Object.values(dataAwal) : [null, null, null, null]).forEach(tambah);
                if (addBtn) addBtn.addEventListener('click', function () { tambah(null); });
            }

            const pairRowsStore = document.getElementById('pairRows');
            if (pairRowsStore) {
                let oldPair = [];
                try { oldPair = JSON.parse(document.getElementById('pairOldData').textContent) || []; } catch (e) { oldPair = []; }
                siapkanBarisPasangan(pairRowsStore, document.getElementById('pairAdd'), oldPair);
            }

            document.querySelectorAll('.pair-rows').forEach(function (box) {
                const dataEl = box.parentElement.querySelector('.pair-data');
                let data = [];
                try { data = JSON.parse(dataEl.textContent) || []; } catch (e) { data = []; }
                siapkanBarisPasangan(box, box.parentElement.querySelector('.pair-add'), data);
            });
        });
    </script>
@endpush
