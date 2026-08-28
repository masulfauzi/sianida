@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('kelas.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Anggota Kelas {{ $kelas->kelas }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Anggota Kelas {{ $kelas->kelas }}
            </h6>
            <div class="card-body">
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>NIS</td>
                                <td>NISN</td>
                                <td>Nama Siswa</td>
                                <th width="15">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->siswa->nis ?? '-' }}</td>
                                    <td>{{ $item->siswa->nisn ?? '-' }}</td>
                                    <td>{{ $item->siswa->nama_siswa ?? '-' }}</td>
                                    <td>
                                        {!! button('pesertadidik.edit', '', $item->id) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center"><i>No data.</i></td>
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
