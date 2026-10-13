@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('soaltka.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('soaltka.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $soaltka->file }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $soaltka->file }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>File</p></div><div class='col-lg-10'><p class='fw-bold'>@if ($soaltka->file) <a href="{{ asset('uploads/soal_tka/' . $soaltka->file) }}" target="_blank">{{ $soaltka->file }}</a> @else - @endif</p></div>
									<div class='col-lg-2'><p>Guru</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $soaltka->guru->nama ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Mapel</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $soaltka->mapel->mapel ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Semester</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $soaltka->semester->semester ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Tingkat</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $soaltka->tingkat->tingkat ?? '-' }}</p></div>
									
                        </div>
                    </div>
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