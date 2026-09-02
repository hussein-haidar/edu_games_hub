@extends('layouts.app')

@section('title', $game->nama)

@section('content')
    <a href="{{ route('mapel.show', $game->mapel) }}" class="small text-primary text-decoration-none">&larr; Kembali ke {{ $game->mapel->nama }}</a>

    <div class="d-flex align-items-center gap-2 mt-3">
        <h1 class="h3 fw-bold mb-0">{{ $game->nama }}</h1>
        <span class="badge text-bg-primary">
            @switch($game->jenis)
                @case('kuis') Pilihan Ganda @break
                @case('bener_salah') Benar / Salah @break
                @case('teka_teki') Teka-Teki Jodohkan @break
                @default {{ $game->jenis }}
            @endswitch
        </span>
    </div>
    <p class="text-secondary small mt-2 mb-0">{{ $game->deskripsi }}</p>
    <p class="text-muted small">{{ $game->soals->count() }} soal · {{ $game->poin_per_benar }} poin per jawaban benar</p>

    @if($game->soals->isEmpty())
        <div class="text-center text-secondary py-5 border rounded bg-white mt-3">Belum ada soal di game ini.</div>
    @else
        <div class="card border mt-3 p-4 mx-auto" style="max-width: 44rem;">
            <div class="mb-4">
                <div class="d-flex justify-content-between small text-secondary mb-1">
                    <span>Soal <span id="qCurrent">1</span>/{{ $game->soals->count() }}</span>
                    <span id="qStatus"></span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div id="qProgress" class="progress-bar bg-primary" role="progressbar" style="width:{{ 100 / $game->soals->count() }}%"></div>
                </div>
            </div>

            <div id="qPertanyaan" class="h5 fw-semibold mb-4"></div>

            <div id="qAnswers" class="d-grid gap-2"></div>

            <div id="qFeedback" class="alert mt-3 d-none" role="alert"></div>

            <div class="d-flex justify-content-between mt-4">
                <button id="btnPrev" class="btn btn-light d-none">Sebelumnya</button>
                <button id="btnNext" class="btn btn-primary px-4">Lanjut</button>
            </div>
        </div>

        <form id="submitForm" method="POST" action="{{ route('games.submit', $game) }}" class="d-none">
            @csrf
            <textarea name="jawaban" id="jawabanJson" class="d-none"></textarea>
        </form>
    @endif
@endsection

@if(!$game->soals->isEmpty())
    @push('scripts')
    <script>
        const soalList = @json($game->soals);
        const jenis = @json($game->jenis);
        const totalQ = soalList.length;

        let index = 0;
        const jawaban = {};

        const elQ = {
            current: document.getElementById('qCurrent'),
            progress: document.getElementById('qProgress'),
            pertanyaan: document.getElementById('qPertanyaan'),
            answers: document.getElementById('qAnswers'),
            feedback: document.getElementById('qFeedback'),
            next: document.getElementById('btnNext'),
            prev: document.getElementById('btnPrev'),
            status: document.getElementById('qStatus'),
        };

        function shuffle(arr) {
            const a = [...arr];
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        function render() {
            const soal = soalList[index];
            elQ.pertanyaan.textContent = soal.pertanyaan;
            elQ.current.textContent = index + 1;
            elQ.progress.style.width = ((index + 1) / totalQ * 100) + '%';
            elQ.feedback.classList.add('d-none');
            elQ.prev.classList.toggle('d-none', index === 0);

            if (index === totalQ - 1) {
                elQ.next.textContent = jawabanSemuaTerisi() ? 'Selesai ✓' : 'Selesai';
            } else {
                elQ.next.textContent = 'Lanjut';
            }

            elQ.answers.innerHTML = '';
            elQ.status.textContent = '';

            const selected = jawaban[soal.id];

            if (jenis === 'kuis') {
                const pilihan = soal.pilihan || [];
                pilihan.forEach((opt) => {
                    elQ.answers.appendChild(makeButton(opt, opt, selected, soal.id));
                });
            } else if (jenis === 'bener_salah') {
                ['Benar', 'Salah'].forEach((opt) => {
                    elQ.answers.appendChild(makeButton(opt, opt, selected, soal.id));
                });
            } else if (jenis === 'teka_teki') {
                const semuaJawaban = [...new Set(soalList.map(s => s.jawaban_benar))];
                const acak = shuffle(semuaJawaban);
                acak.forEach((opt) => {
                    elQ.answers.appendChild(makeButton(opt, opt, selected, soal.id));
                });
            }
        }

        function makeButton(label, value, selected, soalId) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = label;
            btn.className = 'btn border text-start w-100 d-block';
            if (selected === value) {
                btn.classList.add('btn-primary', 'border-primary');
            } else {
                btn.classList.add('btn-light', 'border-secondary-subtle');
            }
            btn.addEventListener('click', () => selectAnswer(soalId, value, btn));
            return btn;
        }

        function selectAnswer(soalId, value, btn) {
            jawaban[soalId] = value;
            Array.from(elQ.answers.children).forEach(c => {
                c.classList.remove('btn-primary', 'border-primary', 'btn-light');
                if (c.textContent === value) {
                    c.classList.add('btn-primary', 'border-primary');
                } else {
                    c.classList.add('btn-light');
                }
            });
            if (index === totalQ - 1) elQ.next.textContent = 'Selesai ✓';
        }

        function jawabanSemuaTerisi() {
            return soalList.every(s => jawaban[s.id] !== undefined);
        }

        elQ.next.addEventListener('click', () => {
            if (index < totalQ - 1) {
                index++;
                render();
            } else {
                if (!jawabanSemuaTerisi()) {
                    alert('Selesaikan semua soal dulu ya!');
                    return;
                }
                document.getElementById('jawabanJson').value = JSON.stringify(jawaban);
                document.getElementById('submitForm').submit();
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
