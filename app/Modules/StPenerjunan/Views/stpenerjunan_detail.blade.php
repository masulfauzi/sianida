@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('stpenerjunan.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('stpenerjunan.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $stpenerjunan->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $stpenerjunan->nama }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Periode</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $stpenerjunan->periode->nama_periode }}</p></div>
									<div class='col-lg-2'><p>No Surat</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $stpenerjunan->no_surat }}</p></div>
									<div class='col-lg-2'><p>Tgl Surat</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $stpenerjunan->tgl_surat }}</p></div>
									<div class='col-lg-2'><p>Tgl Penerjunan</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $stpenerjunan->tgl_penerjunan }}</p></div>
									
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Daftar Guru Pembimbing Periode: {{ $stpenerjunan->periode->nama_periode }}
            </h6>
            <div class="card-body">
                <div class="table-responsive-md col-12">
                    <table class="table" id="table2">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>Nama</td>
                                <td>Nip</td>
                                <td>Mapel</td>
                                <td>No Hp</td>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gurus as $guru)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $guru->nama }}</td>
                                    <td>{{ $guru->nip }}</td>
                                    <td>{{ $guru->mapel }}</td>
                                    <td>{{ $guru->no_hp }}</td>
                                    <td>
                                        <a href="{{ route('stpenerjunan.surat_tugas.show', [$stpenerjunan->id, $guru->id]) }}" class="btn btn-sm icon icon-left btn-outline-success" target="_blank"><i class="fa fa-download"></i> Surat Tugas</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center"><i>No data.</i></td>
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