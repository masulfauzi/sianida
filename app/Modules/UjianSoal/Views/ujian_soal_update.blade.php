@extends('layouts.app')

@php
    $kunciPgLama  = $soal->jenis === 'pg' ? $soal->kunci : null;
    $kunciPgkLama = $soal->jenis === 'pgk' ? str_split($soal->kunci) : [];
    $kunciBsLama  = $soal->jenis === 'bs' ? str_split($soal->kunci) : [];
@endphp

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Soal</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ujiansoal.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ujiansoal.soal.index', $bankSoal->id) }}">{{ $bankSoal->nama_bank_soal }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Soal No. {{ $soal->no_soal }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">Form Edit Soal</h6>
            <div class="card-body">
                @include('include.flash')
                @error('kunci')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <form action="{{ route('ujiansoal.soal.update', [$bankSoal->id, $soal->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-md-end">No Soal</label>
                        <div class="col-md-9">
                            <input type="number" name="no_soal" min="1" class="form-control" value="{{ old('no_soal', $soal->no_soal) }}" required>
                            @error('no_soal')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-md-end">Jenis Soal</label>
                        <div class="col-md-9">
                            <select name="jenis" id="jenis_soal" class="form-control">
                                @foreach (\App\Modules\UjianSoal\Models\UjianSoal::LABEL_JENIS as $kode => $label)
                                    <option value="{{ $kode }}" {{ old('jenis', $soal->jenis) == $kode ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-md-end">Soal</label>
                        <div class="col-md-9">
                            <textarea name="soal" class="form-control rich-editor" rows="4">{{ old('soal', $soal->soal) }}</textarea>
                            <small class="text-muted">Kalau menyisipkan gambar di dalam teks soal, pastikan gambar sudah punya URL publik. Untuk gambar soal yang diunggah dari komputer, pakai field "Gambar Soal" di bawah.</small>
                            @error('soal')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-md-end">Gambar Soal</label>
                        <div class="col-md-9">
                            @if ($soal->gambar_soal)
                                <div>
                                    <img src="{{ asset('ujian/soal/' . $soal->gambar_soal) }}" alt="Gambar Soal" width="250">
                                    <br>
                                    <a href="{{ route('ujiansoal.soal.hapus_gambar.destroy', [$bankSoal->id, $soal->id, 'gambar_soal']) }}" style="color: red" onclick="return confirm('Hapus gambar soal ini?')">
                                        <i class="fa fa-times-circle"></i> Hapus Gambar
                                    </a>
                                </div>
                            @else
                                <input type="file" name="gambar_soal" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                            @endif
                            @error('gambar_soal')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr>

                    @foreach (['a', 'b', 'c', 'd', 'e'] as $i => $h)
                        <div class="opsi-block" data-huruf="{{ strtoupper($h) }}">
                            <div class="row mb-2">
                                <label class="col-md-3 col-form-label text-md-end opsi-label" data-huruf="{{ strtoupper($h) }}">
                                    Opsi {{ strtoupper($h) }}
                                </label>
                                <div class="col-md-9">
                                    <textarea name="opsi_{{ $h }}" class="form-control" rows="2">{{ old("opsi_$h", $soal->{"opsi_$h"}) }}</textarea>
                                    @error("opsi_$h")<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-md-3 col-form-label text-md-end">Gambar Opsi {{ strtoupper($h) }}</label>
                                <div class="col-md-9">
                                    @if ($soal->{"gambar_$h"})
                                        <div>
                                            <img src="{{ asset('ujian/soal/' . $soal->{"gambar_$h"}) }}" alt="Gambar Opsi {{ strtoupper($h) }}" width="200">
                                            <br>
                                            <a href="{{ route('ujiansoal.soal.hapus_gambar.destroy', [$bankSoal->id, $soal->id, 'gambar_' . $h]) }}" style="color: red" onclick="return confirm('Hapus gambar opsi {{ strtoupper($h) }} ini?')">
                                                <i class="fa fa-times-circle"></i> Hapus Gambar
                                            </a>
                                        </div>
                                    @else
                                        <input type="file" name="gambar_{{ $h }}" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                    @endif
                                    @error("gambar_$h")<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row mb-2 kunci-selector-row">
                                <label class="col-md-3 col-form-label text-md-end">Kunci</label>
                                <div class="col-md-9">
                                    <span class="kunci-pg-radio">
                                        <label class="me-3">
                                            <input type="radio" name="kunci_pg" value="{{ strtoupper($h) }}" {{ old('kunci_pg', $kunciPgLama) == strtoupper($h) ? 'checked' : '' }}>
                                            Kunci {{ strtoupper($h) }}
                                        </label>
                                    </span>
                                    <span class="kunci-pgk-checkbox">
                                        <label class="me-3">
                                            <input type="checkbox" name="kunci_pgk[]" value="{{ strtoupper($h) }}" {{ in_array(strtoupper($h), (array) old('kunci_pgk', $kunciPgkLama)) ? 'checked' : '' }}>
                                            Kunci {{ strtoupper($h) }}
                                        </label>
                                    </span>
                                </div>
                            </div>

                            @if ($i < 3)
                                <div class="row mb-3 kunci-bs">
                                    <label class="col-md-3 col-form-label text-md-end">Kunci Pernyataan {{ $i + 1 }}</label>
                                    <div class="col-md-9">
                                        <label class="me-3">
                                            <input type="radio" name="kunci_bs[{{ $i }}]" value="B" {{ old("kunci_bs.$i", $kunciBsLama[$i] ?? null) == 'B' ? 'checked' : '' }}>
                                            Benar
                                        </label>
                                        <label>
                                            <input type="radio" name="kunci_bs[{{ $i }}]" value="S" {{ old("kunci_bs.$i", $kunciBsLama[$i] ?? null) == 'S' ? 'checked' : '' }}>
                                            Salah
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <hr>
                        </div>
                    @endforeach

                    <div class="offset-md-3 ps-2">
                        <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                        <a href="{{ route('ujiansoal.soal.index', $bankSoal->id) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@section('inline-js')
<script>
$(function () {
    function terapkanJenisSoal() {
        var jenis = $('#jenis_soal').val();

        $('.opsi-block').each(function () {
            var huruf = $(this).data('huruf');
            if (jenis === 'bs' && (huruf === 'D' || huruf === 'E')) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

        $('.opsi-label').each(function () {
            var huruf = $(this).data('huruf');
            var nomorPernyataan = {A: 1, B: 2, C: 3}[huruf];
            if (jenis === 'bs' && nomorPernyataan) {
                $(this).text('Pernyataan ' + nomorPernyataan);
            } else {
                $(this).text('Opsi ' + huruf);
            }
        });

        $('.kunci-selector-row').toggle(jenis === 'pg' || jenis === 'pgk');
        $('.kunci-pg-radio').toggle(jenis === 'pg');
        $('.kunci-pgk-checkbox').toggle(jenis === 'pgk');
        $('.kunci-bs').toggle(jenis === 'bs');
    }

    $('#jenis_soal').on('change', terapkanJenisSoal);
    terapkanJenisSoal();
});
</script>
@endsection
