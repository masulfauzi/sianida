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
                        <li class="breadcrumb-item active" aria-current="page">Form Tambah {{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Form Tambah Data {{ $title }}
            </h6>
            <div class="card-body">
                @include('include.flash')
                <form class="form form-horizontal" action="{{ route('verifikasiatp.store') }}" method="POST">
                    <div class="form-body">
                        @csrf

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
                                {{ Form::select('id_mapel', $ref_mapel, old('id_mapel'), ['class' => 'form-control select2']) }}
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
                                {{ Form::select('id_tingkat', $ref_tingkat, old('id_tingkat'), ['class' => 'form-control select2']) }}
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
                                {{ Form::select('id_jurusan', $ref_jurusan, old('id_jurusan'), ['class' => 'form-control select2']) }}
                                @error('id_jurusan')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{ Form::hidden('id_semester', $selected_id_semester) }}

                        <hr>

                        <h6 class="mb-1">Survey Verifikasi ATP</h6>
                        <p class="text-muted small mb-3">Pilih skor <strong>0</strong> (tidak ada), <strong>1</strong> (Kurang Lengkap), atau <strong>2</strong> (Sudah Lengkap).</p>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Identitas</h6>
                                <p class="text-muted small mb-3">Memuat: nama satuan pendidikan, nama guru, nama mata pelajaran, kelas/program keahlian, alokasi waktu.</p>
                                <div class="btn-group" role="group" aria-label="Skor Identitas">
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_0" value="0" autocomplete="off" {{ old('identitas') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_0">0</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_1" value="1" autocomplete="off" {{ old('identitas') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_1">1</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_2" value="2" autocomplete="off" {{ old('identitas') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_2">2</label>
                                </div>
                                @error('identitas')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Alur Tujuan Pembelajaran bisa berbentuk infografis</h6>
                                <div class="btn-group mt-2" role="group" aria-label="Skor Infografis">
                                    <input type="radio" class="btn-check" name="infografis" id="infografis_0" value="0" autocomplete="off" {{ old('infografis') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="infografis_0">0</label>
                                    <input type="radio" class="btn-check" name="infografis" id="infografis_1" value="1" autocomplete="off" {{ old('infografis') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="infografis_1">1</label>
                                    <input type="radio" class="btn-check" name="infografis" id="infografis_2" value="2" autocomplete="off" {{ old('infografis') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="infografis_2">2</label>
                                </div>
                                @error('infografis')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Elemen</h6>
                                <p class="text-muted small mb-3">Memuat elemen yang terdapat pada Capaian Pembelajaran.</p>
                                <div class="btn-group" role="group" aria-label="Skor Elemen">
                                    <input type="radio" class="btn-check" name="elemen" id="elemen_0" value="0" autocomplete="off" {{ old('elemen') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="elemen_0">0</label>
                                    <input type="radio" class="btn-check" name="elemen" id="elemen_1" value="1" autocomplete="off" {{ old('elemen') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="elemen_1">1</label>
                                    <input type="radio" class="btn-check" name="elemen" id="elemen_2" value="2" autocomplete="off" {{ old('elemen') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="elemen_2">2</label>
                                </div>
                                @error('elemen')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">CP Elemen</h6>
                                <p class="text-muted small mb-3">Memuat Capaian Pembelajaran per-elemen sesuai pada elemen yang ada pada kolom sebelumnya.</p>
                                <div class="btn-group" role="group" aria-label="Skor CP Elemen">
                                    <input type="radio" class="btn-check" name="cp_elemen" id="cp_elemen_0" value="0" autocomplete="off" {{ old('cp_elemen') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_elemen_0">0</label>
                                    <input type="radio" class="btn-check" name="cp_elemen" id="cp_elemen_1" value="1" autocomplete="off" {{ old('cp_elemen') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_elemen_1">1</label>
                                    <input type="radio" class="btn-check" name="cp_elemen" id="cp_elemen_2" value="2" autocomplete="off" {{ old('cp_elemen') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="cp_elemen_2">2</label>
                                </div>
                                @error('cp_elemen')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Tujuan Pembelajaran</h6>
                                <p class="text-muted small mb-3">Merupakan tujuan yang lebih umum bukan tujuan pembelajaran harian (goal bukan objectives) disertai indikator ketercapaian tujuan pembelajaran.</p>
                                <div class="btn-group" role="group" aria-label="Skor Tujuan Pembelajaran">
                                    <input type="radio" class="btn-check" name="tp" id="tp_0" value="0" autocomplete="off" {{ old('tp') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_0">0</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_1" value="1" autocomplete="off" {{ old('tp') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_1">1</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_2" value="2" autocomplete="off" {{ old('tp') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_2">2</label>
                                </div>
                                @error('tp')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Alur Tujuan Pembelajaran</h6>
                                <p class="text-muted small mb-3">Menggambarkan urutan pengembangan kompetensi yang harus dikuasai murid yang tersusun secara berkesinambungan dan urut secara berjenjang.</p>
                                <div class="btn-group" role="group" aria-label="Skor Alur Tujuan Pembelajaran">
                                    <input type="radio" class="btn-check" name="atp" id="atp_0" value="0" autocomplete="off" {{ old('atp') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="atp_0">0</label>
                                    <input type="radio" class="btn-check" name="atp" id="atp_1" value="1" autocomplete="off" {{ old('atp') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="atp_1">1</label>
                                    <input type="radio" class="btn-check" name="atp" id="atp_2" value="2" autocomplete="off" {{ old('atp') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="atp_2">2</label>
                                </div>
                                @error('atp')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Alokasi Waktu (JP)</h6>
                                <div class="btn-group mt-2" role="group" aria-label="Skor Alokasi Waktu">
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_0" value="0" autocomplete="off" {{ old('alokasi_waktu') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_0">0</label>
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_1" value="1" autocomplete="off" {{ old('alokasi_waktu') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_1">1</label>
                                    <input type="radio" class="btn-check" name="alokasi_waktu" id="alokasi_waktu_2" value="2" autocomplete="off" {{ old('alokasi_waktu') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="alokasi_waktu_2">2</label>
                                </div>
                                @error('alokasi_waktu')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Catatan</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {{ Form::textarea('catatan', old('catatan'), ['class' => 'form-control', 'rows' => 4, 'placeholder' => '']) }}
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
