@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Rekap Bulanan {{ $title }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presensiharian.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Rekap Bulanan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header"><h6>Filter</h6></div>
            <div class="card-body">
                <form action="{{ route('presensiharian.rekap.bulanan.index') }}" method="get" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Kelas</label>
                        <select name="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($ref_kelas as $id => $nama)
                                <option value="{{ $id }}" {{ $id_kelas == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Bulan</label>
                        <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($id_kelas && $bulan)
        <div class="card">
            <div class="card-header"><h6>Hasil Rekap</h6></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm text-center">
                    <thead>
                        <tr>
                            <th style="white-space:nowrap">No</th>
                            <th class="text-start" style="min-width:180px">Nama Siswa</th>
                            @for ($d = 1; $d <= $jumlah_hari; $d++)
                                <th>{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-start">{{ $s->nama_siswa }}</td>
                                @for ($d = 1; $d <= $jumlah_hari; $d++)
                                    <td>{{ $rekap[$s->id_siswa][$d] ?? '-' }}</td>
                                @endfor
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $jumlah_hari + 2 }}" class="text-center">
                                    <i>Tidak ada siswa di kelas ini.</i>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection
