@php
    $kelasPerTingkat = $ref_kelas->groupBy(fn ($k) => $k->tingkat->tingkat ?? '-');
    $daftarJurusan   = $ref_kelas->pluck('jurusan.jurusan', 'id_jurusan')->filter()->unique()->sort();
    $kelas_terpilih  = collect($kelas_terpilih)->map(fn ($id) => (string) $id)->all();
@endphp
<div class="row">
    <div class="col-md-3 text-sm-start text-md-end pt-2">
        <label>Berlaku untuk</label>
    </div>
    <div class="col-md-9 form-group">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="berlaku" id="berlaku_semua" value="semua" {{ $berlaku === 'semua' ? 'checked' : '' }}>
            <label class="form-check-label" for="berlaku_semua">Semua kelas</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="berlaku" id="berlaku_off" value="off" {{ $berlaku === 'off' ? 'checked' : '' }}>
            <label class="form-check-label" for="berlaku_off">Hanya kelas yang dipilih (pilih kelas yang <b>OFF</b>)</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="berlaku" id="berlaku_masuk" value="masuk" {{ $berlaku === 'masuk' ? 'checked' : '' }}>
            <label class="form-check-label" for="berlaku_masuk">Semua kelas kecuali yang dipilih (pilih kelas yang tetap <b>masuk</b>)</label>
        </div>
        @error('berlaku')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row" id="pilih-kelas" style="{{ $berlaku === 'semua' ? 'display:none' : '' }}">
    <div class="col-md-3 text-sm-start text-md-end pt-2">
        <label>Kelas</label>
    </div>
    <div class="col-md-9 form-group">
        <div class="mb-2">
            <small class="text-muted d-block mb-1" id="pilih-kelas-hint"></small>
            <button type="button" class="btn btn-sm btn-outline-primary mb-1" data-pilih="semua">Pilih semua</button>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-pilih="kosong">Kosongkan</button>
            @foreach ($kelasPerTingkat as $tingkat => $list)
                <button type="button" class="btn btn-sm btn-outline-info mb-1" data-pilih="tingkat" data-nilai="{{ $tingkat }}">Kelas {{ $tingkat }}</button>
            @endforeach
            @foreach ($daftarJurusan as $idJurusan => $jurusan)
                <button type="button" class="btn btn-sm btn-outline-info mb-1" data-pilih="jurusan" data-nilai="{{ $idJurusan }}">{{ $jurusan }}</button>
            @endforeach
        </div>

        @foreach ($kelasPerTingkat as $tingkat => $list)
            <div class="border rounded p-2 mb-2">
                <div class="fw-bold mb-1">Kelas {{ $tingkat }}</div>
                <div class="row">
                    @foreach ($list as $k)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="form-check">
                                <input class="form-check-input kelas-check" type="checkbox" name="kelas[]" value="{{ $k->id }}" id="kelas_{{ $k->id }}"
                                    data-tingkat="{{ $tingkat }}" data-jurusan="{{ $k->id_jurusan }}"
                                    {{ in_array((string) $k->id, $kelas_terpilih, true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="kelas_{{ $k->id }}">{{ $k->kelas }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @error('kelas')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        @error('kelas.*')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<script>
    (function () {
        var wrapper = document.getElementById('pilih-kelas');
        var hint = document.getElementById('pilih-kelas-hint');
        var radios = document.querySelectorAll('input[name="berlaku"]');
        var checks = document.querySelectorAll('.kelas-check');

        function updateMode() {
            var mode = document.querySelector('input[name="berlaku"]:checked');
            mode = mode ? mode.value : 'semua';
            wrapper.style.display = mode === 'semua' ? 'none' : '';
            hint.textContent = mode === 'masuk'
                ? 'Centang kelas yang tetap MASUK. Kelas lain akan OFF.'
                : 'Centang kelas yang OFF.';
        }

        radios.forEach(function (r) { r.addEventListener('change', updateMode); });
        updateMode();

        wrapper.querySelectorAll('[data-pilih]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var jenis = btn.getAttribute('data-pilih');
                var nilai = btn.getAttribute('data-nilai');
                checks.forEach(function (c) {
                    if (jenis === 'semua') c.checked = true;
                    else if (jenis === 'kosong') c.checked = false;
                    else if (jenis === 'tingkat' && c.getAttribute('data-tingkat') === nilai) c.checked = true;
                    else if (jenis === 'jurusan' && c.getAttribute('data-jurusan') === nilai) c.checked = true;
                });
            });
        });
    })();
</script>
