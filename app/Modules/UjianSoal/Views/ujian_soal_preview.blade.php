@php
    use App\Modules\UjianSoal\Models\UjianSoal;

    $gambarUrl = fn ($path) => $path ? asset('ujian/soal/' . $path) : null;
    $total     = $data->count();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Soal - {{ $bankSoal->nama_bank_soal }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.1.11"></script>
    {{-- Token warna & font disamakan dengan aplikasi ujian (ujian-front/src/style.css) --}}
    <style type="text/tailwindcss">
        @theme {
            --color-primary: #006591;
            --color-primary-container: #0ea5e9;
            --color-secondary: #006c49;
            --color-secondary-container: #6cf8bb;
            --color-on-secondary-container: #00714d;
            --color-on-surface: #0d1c2e;
            --font-sans: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
        }
    </style>
    <style>
        body { font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif; }
        .material-symbols-outlined { font-size: inherit; line-height: 1; vertical-align: middle; }
        /* Pengganti plugin typography (class prose) untuk HTML dari CKEditor */
        .konten p { margin: 0 0 .5em; }
        .konten p:last-child { margin-bottom: 0; }
        .konten ul { list-style: disc; padding-left: 1.5em; margin: .5em 0; }
        .konten ol { list-style: decimal; padding-left: 1.5em; margin: .5em 0; }
        .konten img { max-width: 100%; height: auto; display: inline-block; }
        .konten table { border-collapse: collapse; margin: .5em 0; }
        .konten td, .konten th { border: 1px solid #cbd5e1; padding: .25em .5em; }
        .konten strong, .konten b { font-weight: 700; }
        .konten em, .konten i { font-style: italic; }
        .konten h1, .konten h2, .konten h3, .konten h4 { font-weight: 700; margin: .5em 0; }
        .konten blockquote { border-left: 3px solid #cbd5e1; padding-left: .75em; color: #475569; }
    </style>
</head>
<body class="bg-slate-50 text-on-surface min-h-screen">
<div class="max-w-7xl mx-auto px-4 py-6 md:py-8">

    {{-- Header (pengganti timer di halaman siswa) --}}
    <div class="bg-white rounded-3xl p-4 md:p-6 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-sky-50 mb-6">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wide">
                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                        Mode Pratinjau Guru
                    </span>
                    @if ($bankSoal->status === 'final')
                        <span class="px-2.5 py-1 rounded-full bg-secondary-container/30 text-on-secondary-container text-[10px] font-bold uppercase tracking-wide">Final</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wide">Draft</span>
                    @endif
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-on-surface truncate">{{ $bankSoal->nama_bank_soal }}</h2>
                <p class="text-slate-500 text-sm mt-0.5">{{ $bankSoal->mapel->mapel ?? '-' }} &middot; {{ $total }} soal</p>
            </div>
            <div class="flex items-center gap-2 md:gap-3 shrink-0 flex-wrap">
                <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-slate-200 cursor-pointer select-none text-sm font-bold text-slate-600 hover:border-slate-300">
                    <input type="checkbox" id="toggle-kunci" class="w-4 h-4 accent-[#006c49]">
                    Tampilkan kunci jawaban
                </label>
                <a href="{{ route('ujiansoal.soal.index', $bankSoal->id) }}"
                   class="px-4 py-2 md:py-3 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali ke Daftar Soal
                </a>
            </div>
        </div>
    </div>

    @if ($total === 0)
        <div class="bg-white rounded-3xl p-12 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-slate-50 text-center">
            <span class="material-symbols-outlined text-[64px] text-slate-300 block mb-4">quiz</span>
            <p class="text-slate-500 text-lg">Belum ada soal pada bank soal ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Kartu soal --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-3xl p-5 md:p-8 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-sky-50">
                    {{-- Progress --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2 gap-2 flex-wrap">
                            <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">
                                Soal <span id="label-posisi">1</span> dari {{ $total }}
                            </span>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="flex items-center gap-1 text-slate-500">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-secondary-container"></span>
                                    <span id="label-terjawab">0</span> terjawab
                                </span>
                                <span class="flex items-center gap-1 text-slate-400">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                                    <span id="label-belum">{{ $total }}</span> belum
                                </span>
                                <span class="font-bold text-primary text-sm"><span id="label-persen">0</span>%</span>
                            </div>
                        </div>
                        <div class="relative w-full bg-slate-100 rounded-full h-4 overflow-hidden shadow-inner">
                            <div id="bar-terjawab" class="absolute inset-y-0 left-0 bg-secondary-container/60 rounded-full transition-all duration-500" style="width:0%"></div>
                            <div id="bar-posisi" class="absolute inset-y-0 left-0 rounded-full transition-all duration-300 bg-slate-400" style="width:0%"></div>
                        </div>
                    </div>

                    @foreach ($data as $i => $s)
                        @php $bs = $s->jenis === UjianSoal::JENIS_BS; @endphp
                        <div class="soal-item hidden" data-index="{{ $i }}" data-jenis="{{ $s->jenis }}" data-kunci="{{ $s->kunci }}">
                            <div class="flex items-center gap-2 mb-3 text-xs text-slate-400 font-bold">
                                <span>No. {{ $s->no_soal }}</span>
                                <span>&middot;</span>
                                <span>{{ UjianSoal::LABEL_JENIS[$s->jenis] ?? $s->jenis }}</span>
                            </div>
                            <div class="mb-6">
                                <div class="konten text-lg font-semibold text-on-surface">{!! $s->soal ?: '-' !!}</div>
                                @if ($s->gambar_soal)
                                    <img src="{{ $gambarUrl($s->gambar_soal) }}" alt="Gambar soal"
                                         class="mt-4 max-h-64 w-auto rounded-xl border border-slate-100 object-contain">
                                @endif
                            </div>

                            @if ($bs)
                                <div class="space-y-3 mb-8">
                                    <p class="text-xs text-slate-500">Tentukan Benar atau Salah untuk setiap pernyataan. Jawaban tersimpan setelah ketiga pernyataan terisi.</p>
                                    @foreach (['a', 'b', 'c'] as $idx => $h)
                                        <div class="bs-row p-4 rounded-2xl border-2 border-slate-200 flex flex-col md:flex-row md:items-center gap-3" data-idx="{{ $idx }}">
                                            <div class="flex items-start gap-3 flex-1">
                                                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-1">{{ $idx + 1 }}</span>
                                                <div class="flex-1">
                                                    <div class="konten text-slate-700 text-sm">{!! $s->{"opsi_$h"} ?: '-' !!}</div>
                                                    @if ($s->{"gambar_$h"})
                                                        <img src="{{ $gambarUrl($s->{"gambar_$h"}) }}" alt="Gambar pernyataan {{ $idx + 1 }}"
                                                             class="mt-2 max-h-40 w-auto rounded-lg border border-slate-100 object-contain">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-2 flex-shrink-0 items-center">
                                                <span class="kunci-badge hidden px-2 py-1 rounded-lg bg-green-100 text-green-700 text-[10px] font-bold uppercase">
                                                    Kunci: {{ ($s->kunci[$idx] ?? '') === 'B' ? 'Benar' : 'Salah' }}
                                                </span>
                                                @foreach (['B' => 'Benar', 'S' => 'Salah'] as $val => $label)
                                                    <button type="button" class="bs-btn px-4 py-2 rounded-xl border-2 text-sm font-bold transition-all border-slate-200 text-slate-600 hover:border-slate-300"
                                                            data-idx="{{ $idx }}" data-value="{{ $val }}">{{ $label }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-3 mb-8">
                                    @if ($s->jenis === UjianSoal::JENIS_PGK)
                                        <p class="text-xs text-slate-500">Pilih satu atau lebih jawaban yang benar.</p>
                                    @endif
                                    @foreach (['A', 'B', 'C', 'D', 'E'] as $huruf)
                                        @php $h = strtolower($huruf); @endphp
                                        <div class="opsi p-4 rounded-2xl border-2 cursor-pointer transition-all border-slate-200 hover:border-slate-300" data-huruf="{{ $huruf }}">
                                            <div class="flex items-start gap-3">
                                                <div class="opsi-indikator w-6 h-6 border-2 flex items-center justify-center flex-shrink-0 mt-1 border-slate-300 {{ $s->jenis === UjianSoal::JENIS_PGK ? 'rounded-md' : 'rounded-full' }}">
                                                    <span class="opsi-check material-symbols-outlined text-[14px] hidden">check</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <div class="konten text-slate-700 text-sm">{!! $s->{"opsi_$h"} ?: '-' !!}</div>
                                                        <span class="kunci-badge hidden ml-auto px-2 py-1 rounded-lg bg-green-100 text-green-700 text-[10px] font-bold uppercase shrink-0">Kunci</span>
                                                    </div>
                                                    @if ($s->{"gambar_$h"})
                                                        <img src="{{ $gambarUrl($s->{"gambar_$h"}) }}" alt="Gambar opsi {{ $huruf }}"
                                                             class="mt-2 max-h-40 w-auto rounded-lg border border-slate-100 object-contain">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Navigasi --}}
                    <div class="flex gap-3 pt-6 border-t border-slate-200">
                        <button type="button" id="btn-prev"
                                class="px-3 md:px-6 py-3 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            <span class="hidden md:inline">Sebelumnya</span>
                            <span class="md:hidden text-xs">Back</span>
                        </button>
                        <button type="button" id="btn-next"
                                class="ml-auto px-3 md:px-6 py-3 bg-primary-container hover:bg-primary text-white font-bold rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span class="hidden md:inline">Selanjutnya</span>
                            <span class="md:hidden text-xs">Next</span>
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>

                {{-- Daftar soal versi mobile (di aplikasi ujian berupa modal) --}}
                <div class="lg:hidden bg-white rounded-3xl p-5 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-sky-50">
                    <h4 class="text-lg font-bold text-on-surface mb-4">Daftar Soal</h4>
                    <div class="grid grid-cols-6 sm:grid-cols-8 gap-2" data-grid></div>
                </div>
            </div>

            {{-- Sidebar daftar soal (desktop) --}}
            <div class="hidden lg:block lg:col-span-1">
                <div class="bg-white rounded-3xl p-6 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-sky-50 sticky top-6">
                    <h4 class="text-lg font-bold text-on-surface mb-4">Daftar Soal</h4>
                    <div class="grid grid-cols-4 lg:grid-cols-5 gap-2 mb-6" data-grid></div>
                    <div class="space-y-2 border-t border-slate-200 pt-4">
                        <div class="flex items-center gap-2 text-xs"><div class="w-3 h-3 rounded bg-slate-100"></div><span class="text-slate-600">Belum dijawab</span></div>
                        <div class="flex items-center gap-2 text-xs"><div class="w-3 h-3 rounded bg-secondary-container"></div><span class="text-slate-600">Sudah dijawab</span></div>
                        <div class="flex items-center gap-2 text-xs"><div class="w-3 h-3 rounded bg-primary"></div><span class="text-slate-600">Soal saat ini</span></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if ($total > 0)
<script>
(function () {
    var items = Array.prototype.slice.call(document.querySelectorAll('.soal-item'));
    var total = items.length;
    var current = {{ (int) $startIndex }};
    var jawaban = {}; // index -> string huruf (pg/pgk) atau array 3 nilai B/S (bs)

    var OPSI_DIPILIH   = ['border-primary', 'bg-primary/5'];
    var OPSI_BIASA     = ['border-slate-200', 'hover:border-slate-300'];
    var IND_DIPILIH    = ['border-primary', 'bg-primary', 'text-white'];
    var IND_BIASA      = ['border-slate-300'];
    var BS_BIASA       = ['border-slate-200', 'text-slate-600', 'hover:border-slate-300'];
    var BS_BENAR       = ['border-green-500', 'bg-green-50', 'text-green-700'];
    var BS_SALAH       = ['border-red-500', 'bg-red-50', 'text-red-700'];
    var GRID_AKTIF     = ['bg-primary', 'text-white', 'shadow-lg'];
    var GRID_TERJAWAB  = ['bg-secondary-container', 'text-on-secondary-container'];
    var GRID_BIASA     = ['bg-slate-100', 'text-slate-600', 'hover:bg-slate-200'];

    function setClass(el, on, off) {
        off.forEach(function (c) { el.classList.remove(c); });
        on.forEach(function (c) { el.classList.add(c); });
    }

    function sudahDijawab(i) {
        var j = jawaban[i];
        if (!j) return false;
        if (Array.isArray(j)) return j.filter(Boolean).length === 3;
        return j.length > 0;
    }

    // ---- Grid nomor soal (sidebar desktop & mobile) ----
    var grids = document.querySelectorAll('[data-grid]');
    grids.forEach(function (grid) {
        items.forEach(function (_, i) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'grid-btn aspect-square rounded-lg font-bold text-sm transition-all flex items-center justify-center';
            b.textContent = i + 1;
            b.dataset.index = i;
            b.addEventListener('click', function () { tampilkan(i); });
            grid.appendChild(b);
        });
    });

    function renderGrid() {
        document.querySelectorAll('.grid-btn').forEach(function (b) {
            var i = parseInt(b.dataset.index, 10);
            var semua = GRID_AKTIF.concat(GRID_TERJAWAB, GRID_BIASA);
            setClass(b, i === current ? GRID_AKTIF : (sudahDijawab(i) ? GRID_TERJAWAB : GRID_BIASA), semua);
        });
    }

    function renderProgress() {
        var terjawab = items.filter(function (_, i) { return sudahDijawab(i); }).length;
        var posisi = (current + 1) / total;
        document.getElementById('label-posisi').textContent = current + 1;
        document.getElementById('label-terjawab').textContent = terjawab;
        document.getElementById('label-belum').textContent = total - terjawab;
        document.getElementById('label-persen').textContent = Math.round(posisi * 100);
        document.getElementById('bar-terjawab').style.width = (terjawab / total * 100) + '%';

        var bar = document.getElementById('bar-posisi');
        bar.style.width = (posisi * 100) + '%';
        setClass(bar,
            [posisi >= 0.75 ? 'bg-green-500' : posisi >= 0.5 ? 'bg-primary' : posisi >= 0.25 ? 'bg-yellow-500' : 'bg-slate-400'],
            ['bg-green-500', 'bg-primary', 'bg-yellow-500', 'bg-slate-400']);
    }

    function renderJawaban(i) {
        var item = items[i];
        var j = jawaban[i];
        if (item.dataset.jenis === 'bs') {
            item.querySelectorAll('.bs-btn').forEach(function (btn) {
                var dipilih = Array.isArray(j) && j[btn.dataset.idx] === btn.dataset.value;
                var aktif = btn.dataset.value === 'B' ? BS_BENAR : BS_SALAH;
                setClass(btn, dipilih ? aktif : BS_BIASA, BS_BIASA.concat(BS_BENAR, BS_SALAH));
            });
            return;
        }
        item.querySelectorAll('.opsi').forEach(function (opsi) {
            var dipilih = (j || '').indexOf(opsi.dataset.huruf) !== -1;
            setClass(opsi, dipilih ? OPSI_DIPILIH : OPSI_BIASA, OPSI_DIPILIH.concat(OPSI_BIASA));
            setClass(opsi.querySelector('.opsi-indikator'), dipilih ? IND_DIPILIH : IND_BIASA, IND_DIPILIH.concat(IND_BIASA));
            opsi.querySelector('.opsi-check').classList.toggle('hidden', !dipilih);
        });
    }

    function tampilkan(i) {
        current = Math.max(0, Math.min(total - 1, i));
        items.forEach(function (item, idx) { item.classList.toggle('hidden', idx !== current); });
        document.getElementById('btn-prev').disabled = current === 0;
        document.getElementById('btn-next').disabled = current === total - 1;
        renderGrid();
        renderProgress();
    }

    // ---- Interaksi menjawab (hanya simulasi, tidak disimpan) ----
    items.forEach(function (item, i) {
        var jenis = item.dataset.jenis;
        item.querySelectorAll('.opsi').forEach(function (opsi) {
            opsi.addEventListener('click', function () {
                var huruf = opsi.dataset.huruf;
                var j = jawaban[i] || '';
                if (jenis === 'pgk') {
                    var set = j.split('').filter(Boolean);
                    var pos = set.indexOf(huruf);
                    if (pos === -1) set.push(huruf); else set.splice(pos, 1);
                    jawaban[i] = set.sort().join('');
                } else {
                    jawaban[i] = j === huruf ? '' : huruf;
                }
                renderJawaban(i); renderGrid(); renderProgress();
            });
        });
        item.querySelectorAll('.bs-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var j = Array.isArray(jawaban[i]) ? jawaban[i] : [null, null, null];
                j[btn.dataset.idx] = btn.dataset.value;
                jawaban[i] = j;
                renderJawaban(i); renderGrid(); renderProgress();
            });
        });
    });

    // ---- Tampilkan kunci jawaban ----
    var toggle = document.getElementById('toggle-kunci');
    function renderKunci() {
        var tampil = toggle.checked;
        items.forEach(function (item) {
            var kunci = item.dataset.kunci || '';
            if (item.dataset.jenis === 'bs') {
                item.querySelectorAll('.bs-row .kunci-badge').forEach(function (b) { b.classList.toggle('hidden', !tampil); });
                return;
            }
            item.querySelectorAll('.opsi').forEach(function (opsi) {
                var isKunci = kunci.indexOf(opsi.dataset.huruf) !== -1;
                opsi.querySelector('.kunci-badge').classList.toggle('hidden', !(tampil && isKunci));
                opsi.classList.toggle('ring-2', tampil && isKunci);
                opsi.classList.toggle('ring-green-400', tampil && isKunci);
            });
        });
    }
    try { toggle.checked = localStorage.getItem('pratinjau_soal_kunci') === '1'; } catch (e) {}
    toggle.addEventListener('change', function () {
        try { localStorage.setItem('pratinjau_soal_kunci', toggle.checked ? '1' : '0'); } catch (e) {}
        renderKunci();
    });

    document.getElementById('btn-prev').addEventListener('click', function () { tampilkan(current - 1); });
    document.getElementById('btn-next').addEventListener('click', function () { tampilkan(current + 1); });
    document.addEventListener('keydown', function (e) {
        if (e.target.tagName === 'INPUT') return;
        if (e.key === 'ArrowLeft') tampilkan(current - 1);
        if (e.key === 'ArrowRight') tampilkan(current + 1);
    });

    items.forEach(function (_, i) { renderJawaban(i); });
    renderKunci();
    tampilkan(current);
})();
</script>
@endif
</body>
</html>
