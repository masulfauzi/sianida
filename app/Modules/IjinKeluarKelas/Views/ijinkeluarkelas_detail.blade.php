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
                        <li class="breadcrumb-item active" aria-current="page">{{ $ijinkeluarkelas->siswa->nama_siswa ?? '-' }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $ijinkeluarkelas->siswa->nama_siswa ?? '-' }}
            </h6>
            <div class="card-body">
                @include('include.flash')
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Siswa</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->siswa->nama_siswa ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Guru</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->guru->nama ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Jenis Ijin Keluar</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->jenisIjinKeluar->jenis_ijin_keluar_kelas ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Keperluan</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->keperluan }}</p></div>
									<div class='col-lg-2'><p>Tanggal</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->tanggal }}</p></div>
									<div class='col-lg-2'><p>Jam Keluar</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->jamKeluar->jam_pelajaran ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Jam Kembali</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijinkeluarkelas->jamKembali->jam_pelajaran ?? '-' }}</p></div>
									<div class='col-lg-2'><p>Validasi Guru</p></div><div class='col-lg-10'>
										<span class="badge {{ $ijinkeluarkelas->is_valid_guru == '1' ? 'bg-success' : 'bg-danger' }}">{{ $ijinkeluarkelas->is_valid_guru == '1' ? 'Disetujui' : 'Belum Disetujui' }}</span>
									</div>
									<div class='col-lg-2'><p>Validasi BK</p></div><div class='col-lg-10'>
										<span class="badge {{ $ijinkeluarkelas->is_valid_bk == '1' ? 'bg-success' : 'bg-danger' }}">{{ $ijinkeluarkelas->is_valid_bk == '1' ? 'Disetujui' : 'Belum Disetujui' }}</span>
										@if($ijinkeluarkelas->is_valid_bk != '1')
											<form id="formValidasiBk" action="{{ route('ijinkeluarkelas.validasi_bk', $ijinkeluarkelas->id) }}" method="POST" class="d-inline">
												@csrf
												@method('PATCH')
											</form>
											<button type="button" onclick="validasiConfirm('formValidasiBk', 'Ijin keluar kelas ini akan divalidasi oleh BK.')" class="btn btn-sm btn-success ms-2"><i class="fa fa-check"></i> Validasi BK</button>
										@endif
									</div>

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