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
                        <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Edit {{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">Form Edit Data {{ $title }}</h6>
            <div class="card-body">
                @include('include.flash')
                <div class="alert alert-light-info color-info small">
                    <i class="fa fa-info-circle"></i> Perubahan hanya disimpan, notifikasi <strong>tidak</strong> dikirim ulang.
                    Untuk mengirim ulang, gunakan tombol Kirim ulang di halaman detail.
                </div>
                <form class="form form-horizontal" action="{{ route('pengumuman.update', $pengumuman->id) }}" method="POST">
                    <div class="form-body">
                        @csrf
                        @method('patch')
                        @include('Pengumuman::pengumuman_form')
                        <div class="offset-md-3 ps-2">
                            <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                            <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
