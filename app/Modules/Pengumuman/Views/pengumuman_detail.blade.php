@extends('layouts.app')

@section('main')
@php
    // Helper can() selalu true, jadi baca privilege langsung (sama dengan yang dicek AuthorizeRequest)
    $bisaKirim = (int) (session('privileges')['pengumuman']['update'] ?? 0) === 1;
@endphp
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('pengumuman.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @include('include.flash')
        <div class="card">
            <h6 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Detail {{ $title }}</span>
                @if ($bisaKirim)
                    <form action="{{ route('pengumuman.kirim.update', $pengumuman->id) }}" method="POST" class="m-0"
                          onsubmit="return confirm('Kirim ulang notifikasi pengumuman ini ke semua siswa?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-paper-plane"></i> Kirim ulang</button>
                    </form>
                @endif
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2"><p>Judul</p></div>
                    <div class="col-lg-10"><p class="fw-bold">{{ $pengumuman->judul }}</p></div>

                    <div class="col-lg-2"><p>Isi</p></div>
                    <div class="col-lg-10"><p style="white-space: pre-line">{{ $pengumuman->isi }}</p></div>

                    <div class="col-lg-2"><p>Notifikasi</p></div>
                    <div class="col-lg-10">
                        <p>
                            @if ($pengumuman->dikirim_at)
                                <span class="badge bg-success">Terkirim {{ $pengumuman->dikirim_at->format('d-m-Y H:i') }}</span>
                            @else
                                <span class="badge bg-danger">Belum terkirim</span>
                            @endif
                        </p>
                    </div>

                    <div class="col-lg-2"><p>Dibuat</p></div>
                    <div class="col-lg-10"><p>{{ optional($pengumuman->created_at)->format('d-m-Y H:i') }}</p></div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
