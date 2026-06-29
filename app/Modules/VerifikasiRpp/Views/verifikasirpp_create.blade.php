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
                <form class="form form-horizontal" action="{{ route('verifikasirpp.store') }}" method="POST">
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

                        <h6 class="mb-1">Survey Verifikasi RPP</h6>
                        <p class="text-muted small mb-3">Pilih skor <strong>0</strong> (tidak ada), <strong>1</strong> (Kurang), <strong>2</strong> (Cukup), <strong>3</strong> (Baik), <strong>4</strong> (Amat Baik).</p>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Identitas</h6>
                                <p class="text-muted small mb-3">Memuat nama satuan pendidikan, nama guru, mata pelajaran, kelas/fase.</p>
                                <div class="btn-group" role="group" aria-label="Skor Identitas">
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_0" value="0" autocomplete="off" {{ old('identitas') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_0">0</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_1" value="1" autocomplete="off" {{ old('identitas') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_1">1</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_2" value="2" autocomplete="off" {{ old('identitas') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_2">2</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_3" value="3" autocomplete="off" {{ old('identitas') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_3">3</label>
                                    <input type="radio" class="btn-check" name="identitas" id="identitas_4" value="4" autocomplete="off" {{ old('identitas') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="identitas_4">4</label>
                                </div>
                                @error('identitas')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Tujuan Pembelajaran</h6>
                                <p class="text-muted small mb-3">Memuat Tujuan Pembelajaran sesuai yang ada di ATP / Silabus.</p>
                                <div class="btn-group" role="group" aria-label="Skor Tujuan Pembelajaran">
                                    <input type="radio" class="btn-check" name="tp" id="tp_0" value="0" autocomplete="off" {{ old('tp') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_0">0</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_1" value="1" autocomplete="off" {{ old('tp') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_1">1</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_2" value="2" autocomplete="off" {{ old('tp') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_2">2</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_3" value="3" autocomplete="off" {{ old('tp') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_3">3</label>
                                    <input type="radio" class="btn-check" name="tp" id="tp_4" value="4" autocomplete="off" {{ old('tp') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="tp_4">4</label>
                                </div>
                                @error('tp')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h5 class="mb-1">Kegiatan Pembelajaran</h5>
                                <h6 class="mb-1">A. Pendahuluan</h6>
                                <p class="text-muted small mb-3">Berisi kegiatan untuk mengkondisikan siswa agar siap mengikuti pembelajaran, termasuk adanya pertanyaan pemantik (berkesadaran, bermakna, dan.atau menggembirakan).</p>
                                <div class="btn-group" role="group" aria-label="Skor Pembelajaran">
                                    <input type="radio" class="btn-check" name="pendahuluan" id="pendahuluan_0" value="0" autocomplete="off" {{ old('pendahuluan') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pendahuluan_0">0</label>
                                    <input type="radio" class="btn-check" name="pendahuluan" id="pendahuluan_1" value="1" autocomplete="off" {{ old('pendahuluan') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pendahuluan_1">1</label>
                                    <input type="radio" class="btn-check" name="pendahuluan" id="pendahuluan_2" value="2" autocomplete="off" {{ old('pendahuluan') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pendahuluan_2">2</label>
                                    <input type="radio" class="btn-check" name="pendahuluan" id="pendahuluan_3" value="3" autocomplete="off" {{ old('pendahuluan') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pendahuluan_3">3</label>
                                    <input type="radio" class="btn-check" name="pendahuluan" id="pendahuluan_4" value="4" autocomplete="off" {{ old('pendahuluan') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="pendahuluan_4">4</label>
                                </div>
                                @error('pendahuluan')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                                <div class="mt-4"></div>
                                <h6 class="mb-1">B. Kegiatan Inti</h6>
                                <p class="text-muted small mb-3">Kegiatan pembelajaran memperhatikan kesiapan, minat dan karakter belajar siswa (pembelajaran berdiferensiasai), pembelajan berpusat pada siswa dengan menggunakan metode atau model yang merangsang siswa untuk memiliki keterampilan berpikir tingkat tinggi (HOTS dan 4C), mengembangkan literasi dan numerasi, menguatkan delapan dimensi profil lulusan, pendidikan perubahan iklim, sekolah sehat, branding sekolah dilaksanakan secara menyenangkan, berkesadaran, dan bermakna siswa mempunyai pengalaman belajar memahami, mengaplikasi, dan merefleksi.</p>
                                <div class="btn-group" role="group" aria-label="Skor Inti">
                                    <input type="radio" class="btn-check" name="inti" id="inti_0" value="0" autocomplete="off" {{ old('inti') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="inti_0">0</label>
                                    <input type="radio" class="btn-check" name="inti" id="inti_1" value="1" autocomplete="off" {{ old('inti') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="inti_1">1</label>
                                    <input type="radio" class="btn-check" name="inti" id="inti_2" value="2" autocomplete="off" {{ old('inti') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="inti_2">2</label>
                                    <input type="radio" class="btn-check" name="inti" id="inti_3" value="3" autocomplete="off" {{ old('inti') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="inti_3">3</label>
                                    <input type="radio" class="btn-check" name="inti" id="inti_4" value="4" autocomplete="off" {{ old('inti') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="inti_4">4</label>
                                </div>
                                @error('inti')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                
                                <div class="mt-4"></div>
                                <h6 class="mb-1">C. Penutup</h6>
                                <p class="text-muted small mb-3">Kegiatan refleksi siswa dan guru, mengajak siswa merancang pembelajaran berikutnya, dan mengatur unsur pembelajaran mendalam (menyenangkan, berkesadaran, dan/atau bermakna)</p>
                                <div class="btn-group" role="group" aria-label="Skor Penutup">
                                    <input type="radio" class="btn-check" name="penutup" id="penutup_0" value="0" autocomplete="off" {{ old('penutup') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="penutup_0">0</label>
                                    <input type="radio" class="btn-check" name="penutup" id="penutup_1" value="1" autocomplete="off" {{ old('penutup') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="penutup_1">1</label>
                                    <input type="radio" class="btn-check" name="penutup" id="penutup_2" value="2" autocomplete="off" {{ old('penutup') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="penutup_2">2</label>
                                    <input type="radio" class="btn-check" name="penutup" id="penutup_3" value="3" autocomplete="off" {{ old('penutup') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="penutup_3">3</label>
                                    <input type="radio" class="btn-check" name="penutup" id="penutup_4" value="4" autocomplete="off" {{ old('penutup') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="penutup_4">4</label>
                                </div>
                                @error('penutup')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Asesmen</h6>
                                <p class="text-muted small mb-3">Ada kegiatan asesmen awal, asesmen formatif, asesmen sumatif.</p>
                                <p class="text-muted small mb-3">Kegiatan asesmen memuat kompetensi sikap, pengetahuan, dan keterampilan.</p>
                                <p class="text-muted small mb-3">Ada kegiatan remedial dan pengayaan.</p>
                                <div class="btn-group" role="group" aria-label="Skor Asesmen">
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_0" value="0" autocomplete="off" {{ old('assesmen') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_0">0</label>
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_1" value="1" autocomplete="off" {{ old('assesmen') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_1">1</label>
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_2" value="2" autocomplete="off" {{ old('assesmen') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_2">2</label>
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_3" value="3" autocomplete="off" {{ old('assesmen') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_3">3</label>
                                    <input type="radio" class="btn-check" name="assesmen" id="assesmen_4" value="4" autocomplete="off" {{ old('assesmen') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="assesmen_4">4</label>
                                </div>
                                @error('assesmen')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-1">Lampiran</h6>
                                <p class="text-muted small mb-3">Memuat materi pembelajaran dan contoh asesmen, remedial, dan pengayaan.</p>
                                <div class="btn-group" role="group" aria-label="Skor Lampiran">
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_0" value="0" autocomplete="off" {{ old('lampiran') == 0 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_0">0</label>
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_1" value="1" autocomplete="off" {{ old('lampiran') == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_1">1</label>
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_2" value="2" autocomplete="off" {{ old('lampiran') == 2 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_2">2</label>
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_3" value="3" autocomplete="off" {{ old('lampiran') == 3 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_3">3</label>
                                    <input type="radio" class="btn-check" name="lampiran" id="lampiran_4" value="4" autocomplete="off" {{ old('lampiran') == 4 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="lampiran_4">4</label>
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
                                {{ Form::textarea('catatan', old('catatan'), ['class' => 'form-control', 'rows' => 4, 'placeholder' => '']) }}
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
