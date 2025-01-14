@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-2">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manajemen Data {{ $title }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <h6 class="card-header">
                    Info Data Eligible
                </h6>
                <div class="card-body">

                    @include('include.flash')

                    @if ($data->is_eligible == 1)
                        <div class="table-responsive-md col-12">
                            <p class="text-center text-xl"><strong>SELAMAT!</strong> Anda termasuk dalam siswa
                                <strong>ELIGIBLE</strong>.
                            </p>
                            <p class="text-center text-xl">Anda peringkat {{ $data->peringkat }} dari
                                {{ count($pesertadidik) }}.</p>
                        </div>
                    @else
                        <div class="table-responsive-md col-12">
                            <p class="text-center text-xl"><strong>MAAF!</strong> Anda termasuk dalam siswa <strong>TIDAK
                                    ELIGIBLE</strong>.</p>
                            <p class="text-center text-xl">Anda peringkat {{ $data->peringkat }} dari
                                {{ count($pesertadidik) }}.</p>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-4"></div>
                        <div class="col-4">
                            <table class="table">
                                <tr>
                                    <td>Nilai Rata-Rata Rapor</td>
                                    <td>{{ $data->rata_rata }}</td>
                                </tr>
                                <tr>
                                    <td>Nilai Tambahan</td>
                                    <td>{{ $data->nilai_tambah }}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{ $data->total }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4"></div>
                    </div>

                </div>
            </div>

        </section>

        {{-- <section class="section">
        <div class="card">
            <h6 class="card-header">
                Info Data Eligible FINAL
            </h6>
            <div class="card-body">
                
                @include('include.flash')
                
                @if ($data->is_eligible_final == 1)
                    <div class="table-responsive-md col-12">
                        <p class="text-center text-xl"><strong>SELAMAT!</strong> Anda termasuk dalam siswa <strong>ELIGIBLE</strong>.</p>
                        <p class="text-center text-xl">Anda peringkat {{ $data->peringkat_final }} dari {{ count($pesertadidik) }}.</p>
                    </div>
                @else
                    <div class="table-responsive-md col-12">
                        <p class="text-center text-xl"><strong>Maaf!</strong> Anda termasuk dalam siswa <strong>TIDAK ELIGIBLE</strong>.</p>
                        <p class="text-center text-xl">Anda peringkat {{ $data->peringkat_final }} dari {{ count($pesertadidik) }}.</p>
                    </div>
                @endif
                
            </div>
        </div>

    </section> --}}

        <section class="section">
            <div class="card">
                <h6 class="card-header">
                    Kesediaan Mengikuti Proses SNBP
                </h6>
                <div class="card-body">

                    @if ($data->is_eligible == 1)
                        <div class="table-responsive-md col-12">
                            <p>Saya masuk dalam kategori siswa <strong>ELIGIBLE.</strong></p>

                            @if ($data->is_berminat === 1)
                                @php
                                    $disable = 'disabled';
                                    $button = '';
                                @endphp
                            @else
                                @if ($data->is_berminat === 0)
                                    @php
                                        $disable = 'disabled';
                                        $button = '';
                                    @endphp
                                @else
                                    @php
                                        $disable = '';
                                        $button = '<button type="submit" class="btn btn-secondary">Simpan</button>';
                                    @endphp
                                @endif
                            @endif

                            <form method="POST" action="{{ route('snbp.berminat.update', $data->id) }}">
                                @csrf
                                <input {{ $disable }} type="radio"
                                    @if ($data->is_berminat === 1) @checked(true) @endif name="berminat"
                                    id="" value="1"> Saya bersedia mengikuti proses SNBP hingga selesai.
                                <br>
                                <input {{ $disable }} type="radio"
                                    @if ($data->is_berminat === 0) @checked(true) @endif name="berminat"
                                    id="" value="0"> Saya tidak bersedia mengikuti proses SNBP.
                                <br>
                                <br>
                                {!! $button !!}
                            </form>

                        </div>
                    @else
                        <div class="table-responsive-md col-12">
                            <p>Saya masuk dalam kategori siswa <strong>TIDAK ELIGIBLE.</strong></p>
                            @if ($data->is_berminat === 1)
                                @php
                                    $disable = 'disabled';
                                    $button = '';
                                @endphp
                            @else
                                @if ($data->is_berminat === 0)
                                    @php
                                        $disable = 'disabled';
                                        $button = '';
                                    @endphp
                                @else
                                    @php
                                        $disable = '';
                                        $button = '<button type="submit" class="btn btn-secondary">Simpan</button>';
                                    @endphp
                                @endif
                            @endif
                            <form method="POST" action="{{ route('snbp.berminat.update', $data->id) }}">
                                @csrf
                                <input {{ $disable }} type="radio"
                                    @if ($data->is_berminat === 1) @checked(true) @endif name="berminat"
                                    id="" value="1"> Saya berminat mengikuti proses SNBP jika ada siswa
                                ELIGIBLE yang mengundurkan diri.
                                <br>
                                <input {{ $disable }} type="radio"
                                    @if ($data->is_berminat === 0) @checked(true) @endif name="berminat"
                                    id="" value="0"> Saya tidak berminat mengikuti proses SNBP.
                                <br>
                                <br>
                                {!! $button !!}
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </section>

        @if ($data->is_berminat === 1 or $data->is_berminat === 0)

            @if ($data->is_eligible === 0 and $data->is_berminat === 0)
            @else
                @if ($data->is_eligible === 1 and $data->is_berminat === 1)
                @else
                    <section class="section">
                        <div class="card">
                            <h6 class="card-header">
                                Upload Surat Pernyataan
                            </h6>
                            <div class="card-body">



                                <form class="form form-horizontal" action="{{ route('snbp.super.store', $data->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-body">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                                <label>Surat Keterangan Sekarang</label>
                                            </div>
                                            <div class="col-md-9 form-group">
                                                @if ($data->super)
                                                    <a href="{{ url('uploads/super/' . $data->super) }}" target="_blank">
                                                        <img src="{{ asset('assets/images/icon/check.png') }}"
                                                            alt="">
                                                    </a>
                                                @else
                                                    <div class="form-control">Belum ada data</div>
                                                @endif

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                                <label>Upload Suket</label>
                                            </div>
                                            <div class="col-md-9 form-group">
                                                <input type="file" class="form-control" name="file">
                                            </div>
                                        </div>
                                        <div class="offset-md-3 ps-2">
                                            <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                                        </div>
                                    </div>
                                </form>
                                @if ($data->is_eligible === 1)
                                    @if ($data->is_berminat === 1)
                                        {{-- <div><a href="{{ url('download/form/super_eligible_berminat.docx') }}">Download Surat
                                            Pernyataan Eligible dan Berminat.</a></div> --}}
                                    @else
                                        <div><a href="{{ url('download/form/super_eligible_tidak_berminat.docx') }}">Download
                                                Surat Pernyataan Eligible Tidak Berminat.</a></div>
                                    @endif
                                @else
                                    @if ($data->is_berminat === 1)
                                        <div><a href="{{ url('download/form/super_tidak_eligible_berminat.docx') }}">Download
                                                Surat Pernyataan Tidak Eligible dan Berminat.</a></div>
                                    @else
                                    @endif
                                @endif

                            </div>
                        </div>

                    </section>
                @endif

            @endif



        @endif



    </div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection
