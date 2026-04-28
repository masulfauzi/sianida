@extends('layouts.app')

@section('main')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ $title }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('nilai.index') }}">Nilai</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <h6 class="card-header">
                    Form Filter Leger
                </h6>
                <div class="card-body">
                    @include('include.flash')
                    <form class="form form-horizontal" action="{{ route('nilai.leger.show') }}" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>Semester</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    {!! Form::select('id_semester', $semester, null, [
        'class' => 'form-control select2',
        'required',
    ]) !!}
                                    @error('id_semester')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-sm-start text-md-end pt-2">
                                    <label>Kelas</label>
                                </div>
                                <div class="col-md-9 form-group">
                                    {!! Form::select('id_kelas', $kelas, null, [
        'class' => 'form-control select2',
        'required',
    ]) !!}
                                    @error('id_kelas')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="offset-md-3 ps-2">
                                <button class="btn btn-primary" type="submit">Export</button> &nbsp;
                                <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </section>

    </div>
@endsection

@section('inline-js')
@endsection