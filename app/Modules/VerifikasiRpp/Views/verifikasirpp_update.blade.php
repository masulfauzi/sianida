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
                        <li class="breadcrumb-item"><a href="{{ route('verifikasirpp.index') }}">{{ $title }}</a></li>
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
                <form class="form form-horizontal" action="{{ route('verifikasirpp.update', $verifikasirpp->id) }}" method="POST">
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
                                {{ Form::select('id_mapel', $ref_mapel, old('id_mapel', $verifikasirpp->id_mapel), ['class' => 'form-control select2']) }}
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
                                {{ Form::select('id_tingkat', $ref_tingkat, old('id_tingkat', $verifikasirpp->id_tingkat), ['class' => 'form-control select2']) }}
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
                                {{ Form::select('id_jurusan', $ref_jurusan, old('id_jurusan', $verifikasirpp->id_jurusan), ['class' => 'form-control select2']) }}
                                @error('id_jurusan')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{ Form::hidden('id_semester', $selected_id_semester) }}

                        <hr>

                        <h6 class="mb-1">Survey Verifikasi RPP</h6>
                        <p class="text-muted small mb-3">Pilih skor <strong>0</strong> (tidak ada), <strong>1</strong> (Kurang Lengkap), atau <strong>2</strong> (Sudah Lengkap).</p>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Identitas</h6>
                                <p class="text-muted small mb-3">Memuat: nama satuan pendidikan, nama guru, mata pelajaran, kelas/semester, dan alokasi waktu.</p>
                                <div class="btn-group" role="group" aria-label="Skor Identitas">
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_0" value="0" autocomplete="off" {{ old('identitas', $verifikasirpp->identitas) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_0">0</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_1" value="1" autocomplete="off" {{ old('identitas', $verifikasirpp->identitas) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_1">1</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_2" value="2" autocomplete="off" {{ old('identitas', $verifikasirpp->identitas) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_2">2</label>
                                </div>
                                @error('identitas')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Tujuan Pembelajaran</h6>
                                <p class="text-muted small mb-3">Memuat Tujuan Pembelajaran sesuai dengan Alur Tujuan Pembelajaran (ATP)</p>
                                <div class="btn-group" role="group" aria-label="Skor Tujuan Pembelajaran">
                                    <input type="radio" class="btn-check" name="tp" id="tp_0" value="0" autocomplete="off" {{ old('tp', $verifikasirpp->tp) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_0">0</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_1" value="1" autocomplete="off" {{ old('tp', $verifikasirpp->tp) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_1">1</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_2" value="2" autocomplete="off" {{ old('tp', $verifikasirpp->tp) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_2">2</label>
                                </div>
                                @error('tp')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Kegiatan Pembelajaran</h6>
                                <p class="text-muted small mb-3">(1) Kegiatan awal, meliputi assesmen awal, dan pemberian apersepsi, <br>(2) Kegiatan inti, yang memuat Pendekatan Pembelajaran Mendalam untuk merefleksikan 8 Dimensi Profil Lulusan, rancangan assesmen proses.<br>(3) Kegiatan akhir/ penutup, yang meliputi kegiatan refleksi, umpan balik, dan rancangan asesmen akhir</p>
                                <div class="btn-group" role="group" aria-label="Skor Pembelajaran">
                                    <input type="radio" class="btn-check" name="pembelajaran" id="pembelajaran_0" value="0" autocomplete="off" {{ old('pembelajaran', $verifikasirpp->pembelajaran) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pembelajaran_0">0</label>
                                    <input type="radio" class="btn-check" name="pembelajaran" id="pembelajaran_1" value="1" autocomplete="off" {{ old('pembelajaran', $verifikasirpp->pembelajaran) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pembelajaran_1">1</label>
                                    <input type="radio" class="btn-check" name="pembelajaran" id="pembelajaran_2" value="2" autocomplete="off" {{ old('pembelajaran', $verifikasirpp->pembelajaran) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pembelajaran_2">2</label>
                                </div>
                                @error('pembelajaran')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Asesmen</h6>
                                <p class="text-muted small mb-3">Memuat jenis asesmen yang dilaksanakan, yaitu asesmen formatif, dan asesmen sumatif. Menerapkan prinsip asesmen, jenis dan teknik penilaian yang sesuai dan relevan dengan tujuan pemebelajaran yang ditetapkan.<br>Menggunakan perangkat asesmen yang sesuai dangan jenis dan teknik asesmen yang dilakukan dilengkapi dengan rubrik.</p>
                                <div class="btn-group" role="group" aria-label="Skor Asesmen">
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_0" value="0" autocomplete="off" {{ old('assesmen', $verifikasirpp->assesmen) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_0">0</label>
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_1" value="1" autocomplete="off" {{ old('assesmen', $verifikasirpp->assesmen) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_1">1</label>
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_2" value="2" autocomplete="off" {{ old('assesmen', $verifikasirpp->assesmen) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_2">2</label>
                                </div>
                                @error('assesmen')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Lampiran</h6>
                                <p class="text-muted small mb-3">Meliputi Lembar Kerja Murid, ringkasan materi, lembar penilaian/ jobsheet, Glosarium dan daftar pustaka</p>
                                <div class="btn-group" role="group" aria-label="Skor Lampiran">
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_0" value="0" autocomplete="off" {{ old('lampiran', $verifikasirpp->lampiran) == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_0">0</label>
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_1" value="1" autocomplete="off" {{ old('lampiran', $verifikasirpp->lampiran) == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_1">1</label>
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_2" value="2" autocomplete="off" {{ old('lampiran', $verifikasirpp->lampiran) == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_2">2</label>
                                </div>
                                @error('lampiran')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Catatan</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::textarea('catatan', old('catatan', $verifikasirpp->catatan), ['class' => 'form-control', 'rows' => 4, 'placeholder' => '']) }}
                                @error('catatan')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="ps-2">
                            <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                            <a href="{{ route('verifikasirpp.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                  </div>
                </form>
            </div>
        </div>

    </section>
</div>
@endsection
