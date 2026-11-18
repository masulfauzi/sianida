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
                        <li class="breadcrumb-item active" aria-current="page">Form Tambah {{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">Form Tambah Data {{ $title }}</h6>
            <div class="card-body">
                @include('include.flash')
                <div class="alert alert-light-warning color-warning small">
                    <i class="fa fa-bell"></i> Setelah disimpan, pengumuman <strong>langsung dikirim</strong> sebagai notifikasi ke semua siswa.
                </div>
                <form class="form form-horizontal" action="{{ route('pengumuman.store') }}" method="POST"
                      onsubmit="return confirm('Simpan dan kirim pengumuman ini ke semua siswa?')">
                    <div class="form-body">
                        @csrf
                        @include('Pengumuman::pengumuman_form')
                        <div class="offset-md-3 ps-2">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i> Simpan &amp; Kirim</button> &nbsp;
                            <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
