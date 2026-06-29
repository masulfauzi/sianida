@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('ksp.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ksp.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $ksp->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $ksp->nama }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Semester</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ksp->semester->semester }}</p></div>
									<div class='col-lg-2'><p>Nama Ksp</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ksp->nama_ksp }}</p></div>
									
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Upload File Ksp
            </h6>
            <div class="card-body">
                @include('include.flash')
                <form class="form form-horizontal" action="{{ route('fileksp.upload.store', $ksp->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 text-sm-start text-md-end pt-2">
                            <label>Nama File</label>
                        </div>
                        <div class="col-md-9 form-group">
                            <input type="text" name="nama_file" value="{{ old('nama_file') }}" class="form-control" required>
                            @error('nama_file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 text-sm-start text-md-end pt-2">
                            <label>File</label>
                        </div>
                        <div class="col-md-9 form-group">
                            <input type="file" name="file" class="form-control" required>
                            @error('file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="offset-md-3 ps-2">
                        <button class="btn btn-primary" type="submit">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Daftar File Ksp
            </h6>
            <div class="card-body">
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>Nama File</td>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse ($files as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->nama_file }}</td>
                                    <td>
                                        <a target="_blank" href="{{ url('download/ksp/file_ksp/'.$item->file) }}" class="btn btn-sm icon icon-left btn-outline-dark"><i class="fa fa-download"></i> Download</a>
                                        {!! button('fileksp.destroy', '', $item->id) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center"><i>No data.</i></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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