@extends('layouts.app')

@section('title', $game->nama)

@section('content')
    <a href="{{ route('mapel.show', $game->mapel) }}" class="small text-primary text-decoration-none">&larr; Kembali ke {{ $game->mapel->nama }}</a>

    <div class="card border overflow-hidden mt-3">
        <div class="p-4 text-white" style="background: {{ $game->mapel->warna }}">
            <div class="d-flex align-items-center gap-3">
                <span class="fs-1">{{ $game->mapel->karakter }}</span>
                <div>
                    <div class="small opacity-75 mb-1">{{ $game->mapel->nama }}</div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="h4 fw-bold mb-0">{{ $game->nama }}</h1>
                        <span class="badge bg-white text-dark">{{ $game->jenis_label }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            @if($game->soals->isEmpty())
                <p class="text-center text-secondary py-4">Belum ada soal untuk game ini.</p>
            @else
                @if($progres)
                    <div class="alert alert-info py-2 small mb-3">
                        🏆 Skor terbaikmu sebelumnya: <strong>{{ $progres->skor }}</strong>
                        ({{ $progres->benar }}/{{ $progres->total }} benar)
                    </div>
                @endif

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="small text-secondary">
                    <span>Soal <span id="qCurrent">1</span>/{{ $game->soals->count() }}</span>
                    <span id="qSkor" class="fw-semibold"></span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div id="qProgress" class="progress-bar" role="progressbar"
                         style="width:{{ 100 / $game->soals->count() }}%; background: {{ $game->mapel->warna }};"></div>
                </div>
            </div>

            <div id="qPertanyaan" class="h5 fw-semibold mb-4"></div>

            <div id="qAnswers" class="d-grid gap-2"></div>

            <div id="qFeedback" class="alert mt-3 d-none animasi-muncul" role="alert"></div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button id="btnPrev" class="btn btn-light d-none">Sebelumnya</button>
                <button id="btnNext" class="btn px-4 fw-semibold"
                        style="background: {{ $game->mapel->warna }}; border-color: {{ $game->mapel->warna }}; color: #fff;">Lanjut</button>
            </div>
            @endif
        </div>

        <form id="submitForm" method="POST" action="{{ route('games.submit', $game) }}" class="d-none">
            @csrf
            <textarea name="jawaban" id="jawabanJson" class="d-none"></textarea>
        </form>
    </div>
@endsection

@if(!$game->soals->isEmpty())
    @push('scripts')
    <script>
        const soalList = @json($game->soals);
        const jenis = @json($game->jenis);
        const poinPerBenar = @json($game->poin_per_benar);
        const totalQ = soalList.length;

        let index = 0;
        // { soalId: { value, benar } } — jawaban terkunci setelah diperiksa
        // value: teks (kuis/bener_salah/teka_teki/isian),
        //        array urutan item (urutkan), array [kiri, kanan] (cocok_gambar)
        const jawaban = {};

        const elQ = {
            current: document.getElementById('qCurrent'),
            skor: document.getElementById('qSkor'),
            progress: document.getElementById('qProgress'),
            pertanyaan: document.getElementById('qPertanyaan'),
            answers: document.getElementById('qAnswers'),
            feedback: document.getElementById('qFeedback'),
            next: document.getElementById('btnNext'),
            prev: document.getElementById('btnPrev'),
        };

        const norm = (s) => (s ?? '').toString().trim();
        const normKecil = (s) => norm(s).toLowerCase();

        function shuffle(arr) {
            const a = [...arr];
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        // "gambar-soal/..." berarti foto hasil upload admin; selain itu emoji/teks
        const adalahGambar = (kiri) => (kiri ?? '').toString().indexOf('gambar-soal/') === 0;

        function pilihanUntuk(soal) {
            if (jenis === 'kuis') return soal.pilihan || [];
            if (jenis === 'bener_salah') return ['Benar', 'Salah'];
            // teka_teki: semua jawaban unik dari seluruh soal, diacak
            return shuffle([...new Set(soalList.map(s => s.jawaban_benar))]);
        }

        function render() {
            const soal = soalList[index];
            elQ.pertanyaan.textContent = soal.pertanyaan;
            elQ.current.textContent = index + 1;
            elQ.progress.style.width = ((index + 1) / totalQ * 100) + '%';
            elQ.prev.classList.toggle('d-none', index === 0);
            elQ.answers.innerHTML = '';
            elQ.feedback.className = 'alert mt-3 d-none animasi-muncul';
            elQ.feedback.innerHTML = '';

            if (jenis === 'isian') renderIsian(soal);
            else if (jenis === 'urutkan') renderUrutkan(soal);
            else if (jenis === 'cocok_gambar') renderCocokGambar(soal);
            else renderPilihan(soal);

            perbaruiTombol();
            perbaruiSkor();
        }

        // ===== Kuis / Benar-Salah / Teka-Teki (tombol pilihan) =====

        function renderPilihan(soal) {
            pilihanUntuk(soal).forEach((opt) => {
                elQ.answers.appendChild(makeButton(opt, soal));
            });

            // Kembalikan tampilan terkunci jika soal ini sudah dijawab (saat navigasi bolak-balik)
            const sudah = jawaban[soal.id];
            if (sudah) {
                kunciSoal(soal, sudah.value, sudah.benar, true);
            }
        }

        function makeButton(label, soal) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = label;
            btn.className = 'btn btn-jawab border text-start w-100 d-block';
            btn.addEventListener('click', () => pilih(soal, label, btn));
            return btn;
        }

        function pilih(soal, value, btn) {
            if (jawaban[soal.id]) return; // sudah terjawab — tidak boleh ganti
            const benar = norm(value) === norm(soal.jawaban_benar);
            jawaban[soal.id] = { value, benar };
            kunciSoal(soal, value, benar, false);
            perbaruiTombol();
            perbaruiSkor();
        }

        function kunciSoal(soal, value, benar, tampilkanUlang) {
            Array.from(elQ.answers.children).forEach((c) => {
                c.disabled = true;
                c.classList.add('disabled');
                if (norm(c.textContent) === norm(soal.jawaban_benar)) {
                    c.classList.add('btn-jawab-benar');
                } else if (c.textContent === value && !benar) {
                    c.classList.add('btn-jawab-salah');
                }
            });

            tampilkanFeedback(soal, benar, benar ? null : soal.jawaban_benar);
        }

        // ===== Isian Singkat =====

        function renderIsian(soal) {
            const wrap = document.createElement('div');
            wrap.className = 'd-flex gap-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control input-isian';
            input.placeholder = 'Ketik jawabanmu di sini...';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-primary fw-semibold px-4';
            btn.textContent = 'Periksa';

            const periksa = function () {
                if (jawaban[soal.id]) return;
                const nilai = input.value.trim();
                if (!nilai) {
                    elQ.feedback.className = 'alert alert-warning mt-3 animasi-muncul';
                    elQ.feedback.textContent = 'Isi jawabannya dulu ya 😊';
                    return;
                }
                const benar = isianBenar(nilai, soal);
                jawaban[soal.id] = { value: nilai, benar };
                kunciIsian(soal, input, btn);
                perbaruiTombol();
                perbaruiSkor();
            };

            btn.addEventListener('click', periksa);
            input.addEventListener('keydown', (e) => { if (e.key === 'Enter') periksa(); });

            wrap.append(input, btn);
            elQ.answers.appendChild(wrap);

            const sudah = jawaban[soal.id];
            if (sudah) {
                input.value = sudah.value;
                kunciIsian(soal, input, btn);
            }
        }

        function isianBenar(nilai, soal) {
            const normIsian = (s) => (s ?? '').toString().trim().toLowerCase().replace(/\s+/g, ' ');
            const varian = (soal.jawaban_benar || '').split('|').map(normIsian);
            return varian.includes(normIsian(nilai));
        }

        function kunciIsian(soal, input, btn) {
            input.disabled = true;
            btn.disabled = true;
            const sudah = jawaban[soal.id];
            const kunciPertama = (soal.jawaban_benar || '').split('|')[0];
            tampilkanFeedback(soal, sudah.benar, sudah.benar ? null : kunciPertama);
        }

        // ===== Mengurutkan =====

        function renderUrutkan(soal) {
            const kunci = soal.pilihan || []; // urutan benar
            const sudah = jawaban[soal.id];
            let terpasang = sudah ? [...sudah.value] : [];
            let tersedia = shuffle(kunci.filter((it) => !terpasang.includes(it)));
            let terkunci = !!sudah;

            const judulPool = document.createElement('div');
            judulPool.className = 'small fw-semibold text-secondary mb-1';
            judulPool.textContent = 'Klik kartu sesuai urutan yang benar:';
            const pool = document.createElement('div');
            pool.className = 'd-flex flex-wrap gap-2 mb-3';

            const judulUrutan = document.createElement('div');
            judulUrutan.className = 'small fw-semibold text-secondary mb-1';
            judulUrutan.textContent = 'Urutan jawabanmu:';
            const list = document.createElement('ol');
            list.className = 'list-group mb-3';

            const btnPeriksa = document.createElement('button');
            btnPeriksa.type = 'button';
            btnPeriksa.className = 'btn btn-primary fw-semibold';
            btnPeriksa.textContent = 'Periksa Jawaban';

            const gambar = function () {
                pool.innerHTML = '';
                tersedia.forEach((it) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'btn btn-jawab border chip-urutkan';
                    b.textContent = it;
                    b.disabled = terkunci;
                    b.addEventListener('click', () => {
                        if (terkunci) return;
                        tersedia = tersedia.filter((x) => x !== it);
                        terpasang.push(it);
                        gambar();
                    });
                    pool.appendChild(b);
                });

                list.innerHTML = '';
                terpasang.forEach((it, n) => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.textContent = (n + 1) + '. ' + it;
                    if (terkunci) {
                        li.classList.add(it === kunci[n] ? 'list-group-item-success' : 'list-group-item-danger');
                    } else {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'btn btn-sm btn-outline-secondary';
                        b.textContent = '↩ Kembalikan';
                        b.addEventListener('click', () => {
                            terpasang = terpasang.filter((x) => x !== it);
                            tersedia.push(it);
                            gambar();
                        });
                        li.appendChild(b);
                    }
                    list.appendChild(li);
                });

                btnPeriksa.disabled = terkunci || terpasang.length !== kunci.length;
            };

            btnPeriksa.addEventListener('click', () => {
                if (terkunci || terpasang.length !== kunci.length) return;
                const benar = terpasang.every((v, i) => normKecil(v) === normKecil(kunci[i]));
                jawaban[soal.id] = { value: [...terpasang], benar };
                terkunci = true;
                gambar();
                tampilkanFeedback(soal, benar, benar ? null : urutanTeks(kunci));
                perbaruiTombol();
                perbaruiSkor();
            });

            elQ.answers.append(judulPool, pool, judulUrutan, list, btnPeriksa);
            gambar();

            if (sudah) {
                tampilkanFeedback(soal, sudah.benar, sudah.benar ? null : urutanTeks(kunci));
            }
        }

        function urutanTeks(kunci) {
            return kunci.map((x, i) => (i + 1) + '. ' + x).join(' → ');
        }

        // ===== Menjodohkan Gambar =====

        function renderCocokGambar(soal) {
            const pasangan = (soal.pilihan || []).map((p) => ({ kiri: p.kiri, kanan: p.kanan }));
            const sudah = jawaban[soal.id];

            let terpilihKiri = null;
            let pasanganUser = sudah ? sudah.value.map((a) => ({ kiri: a[0], kanan: a[1] })) : [];
            let terkunci = !!sudah;

            const judul = document.createElement('div');
            judul.className = 'small fw-semibold text-secondary mb-1';
            judul.textContent = 'Klik gambar di kiri, lalu klik keterangannya di kanan.';

            const row = document.createElement('div');
            row.className = 'row g-3';

            const kolomKiri = document.createElement('div');
            kolomKiri.className = 'col-5';
            kolomKiri.innerHTML = '<div class="small fw-bold mb-1">🖼️ Gambar</div>';
            const poolKiri = document.createElement('div');
            poolKiri.className = 'd-grid gap-2';
            kolomKiri.appendChild(poolKiri);

            const kolomTengah = document.createElement('div');
            kolomTengah.className = 'col-2 d-flex align-items-center justify-content-center fs-3';
            kolomTengah.textContent = '➡️';

            const kolomKanan = document.createElement('div');
            kolomKanan.className = 'col-5';
            kolomKanan.innerHTML = '<div class="small fw-bold mb-1">🔤 Keterangan</div>';
            const poolKanan = document.createElement('div');
            poolKanan.className = 'd-grid gap-2';
            kolomKanan.appendChild(poolKanan);

            row.append(kolomKiri, kolomTengah, kolomKanan);

            const judulPasangan = document.createElement('div');
            judulPasangan.className = 'small fw-semibold text-secondary mt-3 mb-1';
            judulPasangan.textContent = 'Pasanganmu:';
            const listPasangan = document.createElement('div');
            listPasangan.className = 'd-flex flex-wrap gap-2 mb-3';

            const btnPeriksa = document.createElement('button');
            btnPeriksa.type = 'button';
            btnPeriksa.className = 'btn btn-primary fw-semibold';
            btnPeriksa.textContent = 'Periksa Jawaban';

            const hint = document.createElement('div');
            hint.className = 'small text-warning mt-2 d-none';

            const tampilKiri = (k, btn) => {
                if (adalahGambar(k)) {
                    const img = document.createElement('img');
                    img.src = '{{ asset('storage') }}/' + k;
                    img.alt = '';
                    img.className = 'gambar-kecil';
                    btn.appendChild(img);
                } else {
                    const s = document.createElement('span');
                    s.className = 'fs-3';
                    s.textContent = k;
                    btn.appendChild(s);
                }
            };

            const gambar = function () {
                poolKiri.innerHTML = '';
                pasangan.forEach((p) => {
                    if (pasanganUser.some((u) => u.kiri === p.kiri)) return;
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'btn btn-jawab border text-center';
                    b.disabled = terkunci;
                    tampilKiri(p.kiri, b);
                    if (terpilihKiri === p.kiri) b.classList.add('terpilih');
                    b.addEventListener('click', () => {
                        if (terkunci) return;
                        terpilihKiri = (terpilihKiri === p.kiri) ? null : p.kiri;
                        hint.classList.add('d-none');
                        gambar();
                    });
                    poolKiri.appendChild(b);
                });

                const kananAcak = shuffle(pasangan.map((p) => p.kanan));
                poolKanan.innerHTML = '';
                kananAcak.forEach((kn) => {
                    if (pasanganUser.some((u) => u.kanan === kn)) return;
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'btn btn-jawab border text-start w-100 d-block';
                    b.textContent = kn;
                    b.disabled = terkunci;
                    b.addEventListener('click', () => {
                        if (terkunci) return;
                        if (!terpilihKiri) {
                            hint.textContent = 'Pilih gambar dulu di kolom kiri ya 😊';
                            hint.classList.remove('d-none');
                            return;
                        }
                        pasanganUser.push({ kiri: terpilihKiri, kanan: kn });
                        terpilihKiri = null;
                        hint.classList.add('d-none');
                        gambar();
                    });
                    poolKanan.appendChild(b);
                });

                const mapBenar = {};
                pasangan.forEach((p) => { mapBenar[normKecil(p.kiri)] = p.kanan; });

                listPasangan.innerHTML = '';
                pasanganUser.forEach((u) => {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'badge pasangan-chip d-inline-flex align-items-center gap-1';
                    if (terkunci) {
                        chip.classList.add(mapBenar[normKecil(u.kiri)] === u.kanan ? 'pasangan-benar' : 'pasangan-salah');
                    } else {
                        chip.classList.add('text-bg-light', 'border');
                    }
                    tampilKiri(u.kiri, chip);
                    const garis = document.createElement('span');
                    garis.textContent = '—';
                    chip.appendChild(garis);
                    const teks = document.createElement('span');
                    teks.textContent = u.kanan;
                    chip.appendChild(teks);
                    if (!terkunci) {
                        const batal = document.createElement('span');
                        batal.textContent = ' ×';
                        chip.appendChild(batal);
                        chip.addEventListener('click', () => {
                            pasanganUser = pasanganUser.filter((x) => x !== u);
                            gambar();
                        });
                    } else {
                        chip.disabled = true;
                    }
                    listPasangan.appendChild(chip);
                });

                btnPeriksa.disabled = terkunci || pasanganUser.length !== pasangan.length;
            };

            btnPeriksa.addEventListener('click', () => {
                if (terkunci || pasanganUser.length !== pasangan.length) return;
                const mapBenar = {};
                pasangan.forEach((p) => { mapBenar[normKecil(p.kiri)] = p.kanan; });
                const benar = pasanganUser.every((u) => mapBenar[normKecil(u.kiri)] === u.kanan);
                jawaban[soal.id] = { value: pasanganUser.map((u) => [u.kiri, u.kanan]), benar };
                terkunci = true;
                gambar();
                const kunciTeks = pasangan
                    .map((p) => (adalahGambar(p.kiri) ? '🖼️' : p.kiri) + ' = ' + p.kanan)
                    .join(', ');
                tampilkanFeedback(soal, benar, benar ? null : kunciTeks);
                perbaruiTombol();
                perbaruiSkor();
            });

            elQ.answers.append(judul, row, judulPasangan, listPasangan, hint, btnPeriksa);
            gambar();

            if (sudah) {
                const kunciTeks = pasangan
                    .map((p) => (adalahGambar(p.kiri) ? '🖼️' : p.kiri) + ' = ' + p.kanan)
                    .join(', ');
                tampilkanFeedback(soal, sudah.benar, sudah.benar ? null : kunciTeks);
            }
        }

        // ===== Umum =====

        function tampilkanFeedback(soal, benar, jawabanBenarTeks) {
            elQ.feedback.className = 'alert mt-3 animasi-muncul ' + (benar ? 'alert-success' : 'alert-danger');
            elQ.feedback.innerHTML = '';

            const judul = document.createElement('div');
            judul.className = 'fw-bold';
            judul.textContent = benar
                ? '✅ Benar! +' + poinPerBenar + ' poin'
                : '❌ Belum tepat.' + (jawabanBenarTeks ? ' Jawaban benar: ' + jawabanBenarTeks : '');
            elQ.feedback.appendChild(judul);

            if (soal.penjelasan) {
                const penj = document.createElement('div');
                penj.className = 'small mt-1';
                penj.textContent = '💡 ' + soal.penjelasan;
                elQ.feedback.appendChild(penj);
            }
        }

        function perbaruiTombol() {
            const terakhir = index === totalQ - 1;
            const dijawab = !!jawaban[soalList[index].id];
            elQ.next.textContent = terakhir
                ? (dijawab ? 'Selesai ✓' : 'Selesai')
                : 'Lanjut';
        }

        function perbaruiSkor() {
            const totalBenar = Object.values(jawaban).filter(j => j.benar).length;
            const totalDijawab = Object.keys(jawaban).length;
            elQ.skor.textContent = totalDijawab > 0 ? '✅ ' + totalBenar + ' benar' : '';
        }

        function semuaTerjawab() {
            return soalList.every(s => jawaban[s.id] !== undefined);
        }

        function kirim() {
            const ringkas = {};
            Object.keys(jawaban).forEach(id => ringkas[id] = jawaban[id].value);
            document.getElementById('jawabanJson').value = JSON.stringify(ringkas);
            document.getElementById('submitForm').submit();
        }

        elQ.next.addEventListener('click', () => {
            const soal = soalList[index];
            if (!jawaban[soal.id]) {
                const pesan = jenis === 'isian'
                    ? 'Ketik jawabanmu lalu klik "Periksa" dulu ya 😊'
                    : (jenis === 'urutkan' || jenis === 'cocok_gambar')
                        ? 'Lengkapi jawabannya lalu klik "Periksa Jawaban" dulu ya 😊'
                        : 'Pilih jawaban dulu ya 😊';
                elQ.feedback.className = 'alert alert-warning mt-3 animasi-muncul';
                elQ.feedback.textContent = pesan;
                return;
            }
            if (index < totalQ - 1) {
                index++;
                render();
            } else {
                kirim();
            }
        });

        elQ.prev.addEventListener('click', () => {
            if (index > 0) {
                index--;
                render();
            }
        });

        render();
    </script>
    @endpush
@endif
