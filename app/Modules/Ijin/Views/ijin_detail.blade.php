@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('ijin.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ijin.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $ijin->siswa->nama_siswa }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $ijin->siswa->nama_siswa }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Nama Siswa</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->siswa->nama_siswa }}</p></div>
                            <div class='col-lg-2'><p>Jenis Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->jenisIjin->jenis_ijin }}</p></div>
                            <div class='col-lg-2'><p>Lama Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->lama_ijin }} hari</p></div>
                            <div class='col-lg-2'><p>Tgl Mulai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_mulai }}</p></div>
                            <div class='col-lg-2'><p>Tgl Selesai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_selesai }}</p></div>
                            <div class='col-lg-2'><p>Status</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->statusIjin->status_ijin }}</p></div>
                        </div>
                    </div>
                </div>

                @if ($ijin->statusIjin->status_ijin === 'Menunggu')
                    <div class="row mt-4">
                        <div class="col-lg-10 offset-lg-2">
                            <form action="{{ route('ijin.approve', $ijin->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i> Terima
                                </button>
                            </form>
                            <form action="{{ route('ijin.reject', $ijin->id) }}" method="post" class="d-inline"
                                  onsubmit="return confirm('Yakin tolak ajuan ijin ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="row mt-4">
                        <div class="col-lg-10 offset-lg-2">
                            <p class="text-muted">Ajuan ini sudah diproses (status: {{ $ijin->statusIjin->status_ijin }}).</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection