@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('periodemagang.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('periodemagang.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $periodemagang->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $periodemagang->nama }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Semester</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $periodemagang->semester->semester }}</p></div>
									<div class='col-lg-2'><p>Tgl Mulai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $periodemagang->tgl_mulai }}</p></div>
									<div class='col-lg-2'><p>Tgl Selesai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $periodemagang->tgl_selesai }}</p></div>
									
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Data Magang
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-end">
                        {!! button('magang.create', 'Magang', ['id_periode_magang' => $periodemagang->id]) !!}
                    </div>
                </div>
                <div class="table-responsive-md col-12">
                    <table class="table" id="table2">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>Dudi</td>
                                <td>Peserta Didik</td>
                                <td>Guru Pembimbing</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; $grouped = $magang->groupBy('id_dudi'); @endphp
                            @forelse ($grouped as $id_dudi => $items)
                                @foreach ($items as $item)
                                    <tr>
                                        @if ($loop->first)
                                            <td rowspan="{{ $items->count() }}">{{ $no++ }}</td>
                                            <td rowspan="{{ $items->count() }}">{{ $item->dudi->nama_dudi ?? '-' }}</td>
                                        @endif
                                        <td>{{ $item->pesertadidik->siswa->nama_siswa ?? '-' }}</td>
                                        <td>{{ $item->pembimbing->nama ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center"><i>No data.</i></td>
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