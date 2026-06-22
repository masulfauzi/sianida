@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('verifikasiatp.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('verifikasiatp.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $verifikasiatp->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $verifikasiatp->nama }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Alokasi Waktu</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->alokasi_waktu }}</p></div>
									<div class='col-lg-2'><p>Atp</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->atp }}</p></div>
									<div class='col-lg-2'><p>Cp Elemen</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->cp_elemen }}</p></div>
									<div class='col-lg-2'><p>Elemen</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->elemen }}</p></div>
									<div class='col-lg-2'><p>Guru</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->guru->id }}</p></div>
									<div class='col-lg-2'><p>Jurusan</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->jurusan->id }}</p></div>
									<div class='col-lg-2'><p>Mapel</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->mapel->id }}</p></div>
									<div class='col-lg-2'><p>Semester</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->semester->id }}</p></div>
									<div class='col-lg-2'><p>Tingkat</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->tingkat->id }}</p></div>
									<div class='col-lg-2'><p>Identitas</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->identitas }}</p></div>
									<div class='col-lg-2'><p>Infografis</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->infografis }}</p></div>
									<div class='col-lg-2'><p>Tp</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $verifikasiatp->tp }}</p></div>
									
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