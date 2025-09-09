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
                            <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}">{{ $title }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Form Tambah {{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="row">
                    <div class="col-9">
                        <h6 class="card-header">
                            Form Import Data {{ $title }}
                        </h6>
                    </div>
                    <div class="col-3">
                        <a href="{{ url('download/template/import_siswa.xlsx') }}" class="btn btn-success">Download Template</a>
                    </div>
                </div>
                <div class="card-body">
                    @include('include.flash')
                    <form class="form form-horizontal" action="{{ route('siswa.import.store') }}" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>Pilih File</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <input type="file" name="file" class="form-control">
                                    @error('file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="offset-md-3 ps-2">
                                <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </section>
    </div>
@endsection
