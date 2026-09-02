@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('stpenerjunansiswa.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('stpenerjunansiswa.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $stpenerjunansiswa->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $stpenerjunansiswa->nama }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Periode</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $stpenerjunansiswa->periode->nama_periode }}</p></div>
									<div class='col-lg-2'><p>Tgl Surat</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $stpenerjunansiswa->tgl_surat }}</p></div>
									
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Daftar Dudi Periode: {{ $stpenerjunansiswa->periode->nama_periode }}
            </h6>
            <div class="card-body">
                <div class="table-responsive-md col-12">
                    <table class="table" id="table2">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>Nama Dudi</td>
                                <td>Alamat</td>
                                <td>Pimpinan</td>
                                <td>Nama Siswa</td>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dudis as $dudi)
                                @php $rowspan = max($dudi->siswas->count(), 1); @endphp
                                <tr>
                                    <td rowspan="{{ $rowspan }}">{{ $loop->iteration }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $dudi->nama_dudi }}</td>
                                    <td rowspan="{{ $rowspan }}">{!! $dudi->alamat !!}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $dudi->pimpinan }}</td>
                                    <td>{{ $dudi->siswas->first() ?? '-' }}</td>
                                    <td rowspan="{{ $rowspan }}">
                                        <a href="{{ route('stpenerjunansiswa.surat_tugas.show', [$stpenerjunansiswa->id, $dudi->id]) }}" class="btn btn-sm icon icon-left btn-outline-success" target="_blank"><i class="fa fa-download"></i> Surat Tugas</a>
                                    </td>
                                </tr>
                                @foreach ($dudi->siswas->slice(1) as $siswa)
                                    <tr>
                                        <td>{{ $siswa }}</td>
                                    </tr>
                                @endforeach
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