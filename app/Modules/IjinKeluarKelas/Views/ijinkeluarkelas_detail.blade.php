@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('ijinkeluarkelas.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ijinkeluarkelas.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $ijinkeluarkelas->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $ijinkeluarkelas->nama }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Siswa</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->siswa->id }}</p></div>
									<div class='col-lg-2'><p>Guru</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->guru->id }}</p></div>
									<div class='col-lg-2'><p>Jenis Ijin Keluar</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->jenisIjinKeluar->id }}</p></div>
									<div class='col-lg-2'><p>Keperluan</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->keperluan }}</p></div>
									<div class='col-lg-2'><p>Jam Keluar</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->jam_keluar }}</p></div>
									<div class='col-lg-2'><p>Jam Kembali</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->jam_kembali }}</p></div>
									<div class='col-lg-2'><p>Is Valguru</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->isValguru->id }}</p></div>
									<div class='col-lg-2'><p>Is Valbk</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->isValbk->id }}</p></div>
									
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