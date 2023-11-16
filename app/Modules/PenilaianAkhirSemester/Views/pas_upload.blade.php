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
        <div class="row mb-3">
            <div class="col-10"></div>
            <div class="col-2">
                <a class="btn btn-primary" href="{{ route('penilaianakhirsemester.index') }}">Kembali</a>
            </div>
        </div>

        <div class="card">
            <h6 class="card-header">
                Upload Perangkat Pembelajaran
            </h6>
            <div class="card-body">
                
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <form class="form form-horizontal" enctype="multipart/form-data" action="{{ route('penilaianakhirsemester.store') }}" method="POST" enctype="multipart/form-data">
                        <div class="form-body">
                            @csrf 
                            <input type="hidden" name="id_mapel" value="{{ $data->id_mapel }}">
                            <input type="hidden" name="id_tingkat" value="{{ $data->kelas['id_tingkat'] }}">
                            <input type="hidden" name="id_semester" value="{{ $data->id_semester }}">
                            <input type="hidden" name="id_guru" value="{{ $data->id_guru }}">
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>Mapel</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <input disabled type="text" class="form-control" value="{{ $data->mapel['mapel'] }}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>Tingkat</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <input disabled type="text" class="form-control" value="{{ $data->kelas->tingkat->tingkat }}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>File Sekarang</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    @if ($perangkat)
                                        <img src="{{ asset('assets/images/icon/check.png') }}" alt="">
                                    @else
                                        <input type="text" class="form-control" disabled value="Belum Ada File">
                                    @endif
                                </div>
                            </div>
                            
                            
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>Pilih File</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    {!! Form::file('perangkat', ['class' => "form-control"]) !!}
                                    @error('perangkat')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="offset-md-3 ps-2">
                                <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                                <a href="{{ route('perangkatpembelajaran.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                      </div>
                    </form>
                </div>
            </div>
        </div>

        

    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection