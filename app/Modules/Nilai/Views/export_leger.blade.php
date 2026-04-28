@extends('layouts.app')

@section('main')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ $title }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('nilai.index') }}">Nilai</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('nilai.leger.index') }}">Leger</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Export</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <h6 class="card-header">
                    Data Leger Nilai
                </h6>
                <div class="card-body">
                    @include('include.flash')

                    <div class="alert alert-info" role="alert">
                        <strong>Filter Terpilih:</strong><br>
                        Semester: <strong>{{ $semester->semester ?? 'N/A' }}</strong><br>
                        Kelas: <strong>{{ $kelas->kelas ?? 'N/A' }}</strong><br>
                        Jumlah Siswa: <strong>{{ count($siswa) }}</strong>
                    </div>

                    <div class="mb-3">
                        <form action="{{ route('nilai.leger.export.show') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id_semester" value="{{ $semester->id ?? '' }}">
                            <input type="hidden" name="id_kelas" value="{{ $kelas->id ?? '' }}">
                            <button type="submit" class="btn btn-success">Export Excel</button>
                        </form>
                        <a href="{{ route('nilai.leger.index') }}" class="btn btn-secondary">Kembali ke Form</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    @php
                                        $mapel_list = $nilai->groupBy('id_mapel')->keys();
                                    @endphp
                                    @foreach ($nilai->groupBy('id_mapel') as $mapel_group)
                                        <th>{{ $mapel_group->first()->mapel }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswa as $key => $item_siswa)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item_siswa->nama_siswa }}</td>
                                        <td>{{ $item_siswa->nisn }}</td>
                                        @foreach ($nilai->groupBy('id_mapel') as $mapel_group)
                                            @php
                                                $cek_nilai = $nilai->where('id_siswa', $item_siswa->id_siswa)
                                                    ->where('id_mapel', $mapel_group->first()->id_mapel)
                                                    ->first();
                                                $tampil_nilai = $cek_nilai ? $cek_nilai->nilai : '-';
                                            @endphp
                                            <td>{{ $tampil_nilai }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">Tidak ada data siswa untuk filter yang
                                            dipilih</td>
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

@section('inline-js')
@endsection