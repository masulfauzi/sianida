@extends('layouts.app')

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Manajemen Data {{ $title }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('verifikasiatp.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Edit {{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Form Edit Data {{ $title }}
            </h6>
            <div class="card-body">
                @include('include.flash')
                <form class="form form-horizontal" action="{{ route('verifikasiatp.update', $verifikasiatp->id) }}" method="POST">
                    <div class="form-body">
                        @csrf @method('patch')

                        <h6 class="mb-3">Data Guru &amp; Mapel</h6>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Guru</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::select('id_guru_display', $ref_guru, $selected_id_guru, ['class' => 'form-control select2', 'disabled' => 'disabled']) }}
                                {{ Form::hidden('id_guru', $selected_id_guru) }}
                                @error('id_guru')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Mapel</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::select('id_mapel', $ref_mapel, old('id_mapel', $verifikasiatp->id_mapel), ['class' => 'form-control select2']) }}
                                @error('id_mapel')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Tingkat</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::select('id_tingkat', $ref_tingkat, old('id_tingkat', $verifikasiatp->id_tingkat), ['class' => 'form-control select2']) }}
                                @error('id_tingkat')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Jurusan</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::select('id_jurusan', $ref_jurusan, old('id_jurusan', $verifikasiatp->id_jurusan), ['class' => 'form-control select2']) }}
                                @error('id_jurusan')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{ Form::hidden('id_semester', $selected_id_semester) }}

                        <hr>

                        <h6 class="mb-1">Survey Verifikasi ATP</h6>
                        <p class="text-muted small mb-3">Pilih skor <strong>0</strong> (tidak ada), <strong>1</strong> (Kurang), <strong>2</strong> (Cukup), <strong>3</strong> (Baik), <strong>4</strong> (Amat Baik).</p>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Identitas</h6>
                                <p class="text-muted small mb-3">ATP/Silabus memuat nama sekolah, nama mata pelajaran, kelas/fase.</p>
                                <div class="btn-group" role="group" aria-label="Skor Identitas">
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_0" value="0" autocomplete="off" {{ old('identitas', $verifikasiatp->identitas) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_0">0</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_1" value="1" autocomplete="off" {{ old('identitas', $verifikasiatp->identitas) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_1">1</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_2" value="2" autocomplete="off" {{ old('identitas', $verifikasiatp->identitas) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_2">2</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_3" value="3" autocomplete="off" {{ old('identitas', $verifikasiatp->identitas) == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_3">3</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_4" value="4" autocomplete="off" {{ old('identitas', $verifikasiatp->identitas) == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_4">4</label>
                                </div>
                                @error('identitas')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Capaian Pembelajaran</h6>
                                <p class="text-muted small mb-3">Memuat Capaian Pembelajaran secara lengkap sesuai dengan Keputusan Kepala BSKAP Nomor 0446/H/KR/2025.</p>
                                <div class="btn-group" role="group" aria-label="Skor CP Elemen">
                                    <input type="radio" class="btn-check" name="cp" id="cp_0" value="0" autocomplete="off" {{ old('cp', $verifikasiatp->cp) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_0">0</label>
                                    <input type="radio" class="btn-check" name="cp" id="cp_1" value="1" autocomplete="off" {{ old('cp', $verifikasiatp->cp) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_1">1</label>
                                    <input type="radio" class="btn-check" name="cp" id="cp_2" value="2" autocomplete="off" {{ old('cp', $verifikasiatp->cp) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_2">2</label>
                                    <input type="radio" class="btn-check" name="cp" id="cp_3" value="3" autocomplete="off" {{ old('cp', $verifikasiatp->cp) == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_3">3</label>
                                    <input type="radio" class="btn-check" name="cp" id="cp_4" value="4" autocomplete="off" {{ old('cp', $verifikasiatp->cp) == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_4">4</label>
                                </div>
                                @error('cp')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Tujuan Pembelajaran</h6>
                                <p class="text-muted small mb-3">Tujuan pembelajaran memuat koempetensi sikap, pengetahuan, dan keterampilan dan memuat konten sesuai dengan capaian pembelajaran.</p>
                                <div class="btn-group" role="group" aria-label="Skor Tujuan Pembelajaran">
                                    <input type="radio" class="btn-check" name="tp" id="tp_0" value="0" autocomplete="off" {{ old('tp', $verifikasiatp->tp) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_0">0</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_1" value="1" autocomplete="off" {{ old('tp', $verifikasiatp->tp) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_1">1</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_2" value="2" autocomplete="off" {{ old('tp', $verifikasiatp->tp) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_2">2</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_3" value="3" autocomplete="off" {{ old('tp', $verifikasiatp->tp) == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_3">3</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_4" value="4" autocomplete="off" {{ old('tp', $verifikasiatp->tp) == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_4">4</label>
                                </div>
                                @error('tp')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Alokasi Waktu</h6>
                                <p class="text-muted small mb-3">Memuat alokasi waktu dengan jumlah sama dengan jumlah jam intrakurikuler pertahun.</p>
                                <div class="btn-group mt-2" role="group" aria-label="Skor Alokasi Waktu">
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_0" value="0" autocomplete="off" {{ old('alokasi_waktu', $verifikasiatp->alokasi_waktu) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_0">0</label>
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_1" value="1" autocomplete="off" {{ old('alokasi_waktu', $verifikasiatp->alokasi_waktu) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_1">1</label>
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_2" value="2" autocomplete="off" {{ old('alokasi_waktu', $verifikasiatp->alokasi_waktu) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_2">2</label>
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_3" value="3" autocomplete="off" {{ old('alokasi_waktu', $verifikasiatp->alokasi_waktu) == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_3">3</label>
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_4" value="4" autocomplete="off" {{ old('alokasi_waktu', $verifikasiatp->alokasi_waktu) == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_4">4</label>
                                </div>
                                @error('alokasi_waktu')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Materi Pembelajaran</h6>
                                <p class="text-muted small mb-3">Memuat materi pembelajaran yang esensial sesuai dengan capaian pembelajaran.</p>
                                <div class="btn-group" role="group" aria-label="Skor Materi">
                                    <input type="radio" class="btn-check" name="materi" id="materi_0" value="0" autocomplete="off" {{ old('materi', $verifikasiatp->materi) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="materi_0">0</label>
                                    <input type="radio" class="btn-check" name="materi" id="materi_1" value="1" autocomplete="off" {{ old('materi', $verifikasiatp->materi) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="materi_1">1</label>
                                    <input type="radio" class="btn-check" name="materi" id="materi_2" value="2" autocomplete="off" {{ old('materi', $verifikasiatp->materi) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="materi_2">2</label>
                                    <input type="radio" class="btn-check" name="materi" id="materi_3" value="3" autocomplete="off" {{ old('materi', $verifikasiatp->materi) == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="materi_3">3</label>
                                    <input type="radio" class="btn-check" name="materi" id="materi_4" value="4" autocomplete="off" {{ old('materi', $verifikasiatp->materi) == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="materi_4">4</label>
                                </div>
                                @error('materi')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Lain-lain</h6>
                                <p class="text-muted small mb-3">Memuat metode/model pembelajaran penilaian/asesmen yang dapat menilai sikap, pengetahuan, dan keterampilan.</p>
                                <div class="btn-group" role="group" aria-label="Skor Lain-Lain">
                                    <input type="radio" class="btn-check" name="metode" id="metode_0" value="0" autocomplete="off" {{ old('metode', $verifikasiatp->metode) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="metode_0">0</label>
                                    <input type="radio" class="btn-check" name="metode" id="metode_1" value="1" autocomplete="off" {{ old('metode', $verifikasiatp->metode) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="metode_1">1</label>
                                    <input type="radio" class="btn-check" name="metode" id="metode_2" value="2" autocomplete="off" {{ old('metode', $verifikasiatp->metode) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="metode_2">2</label>
                                    <input type="radio" class="btn-check" name="metode" id="metode_3" value="3" autocomplete="off" {{ old('metode', $verifikasiatp->metode) == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="metode_3">3</label>
                                    <input type="radio" class="btn-check" name="metode" id="metode_4" value="4" autocomplete="off" {{ old('metode', $verifikasiatp->metode) == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="metode_4">4</label>
                                </div>
                                @error('metode')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Catatan</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::textarea('catatan', old('catatan', $verifikasiatp->catatan), ['class' => 'form-control', 'rows' => 4, 'placeholder' => '']) }}
                                @error('catatan')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="ps-2">
                            <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                            <a href="{{ route('verifikasiatp.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                  </div>
                </form>
            </div>
        </div>

    </section>
</div>
@endsection
