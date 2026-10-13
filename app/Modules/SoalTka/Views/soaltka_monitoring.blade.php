@extends('layouts.app')

@section('page-css')
<style>
    .stat-card {
        border-radius: 8px;
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
                <p class="text-subtitle text-muted">Pantau kelengkapan unggahan Soal TKA guru berdasarkan mata pelajaran yang diajarkan pada semester aktif.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('soaltka.index') }}">Soal Tka</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row mb-3">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm border-start border-primary border-4">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon primary">
                                <i class="fa fa-book"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Total Penugasan</h6>
                            <h4 class="font-extrabold mb-0">{{ $total }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm border-start border-success border-4">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon green">
                                <i class="fa fa-check"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Sudah Upload</h6>
                            <h4 class="font-extrabold mb-0 text-success">{{ $sudah_upload }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm border-start border-danger border-4">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon red">
                                <i class="fa fa-times"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Belum Upload</h6>
                            <h4 class="font-extrabold mb-0 text-danger">{{ $belum_upload }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm border-start border-info border-4">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon blue">
                                <i class="fa fa-chart-pie"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Kelengkapan</h6>
                            <h4 class="font-extrabold mb-0 text-info">{{ $persentase }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0">Daftar Monitoring Unggahan Soal TKA Guru</h6>
                <div>
                    <a href="{{ route('soaltka.index', ['view_raw' => 1]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-list"></i> Lihat Data Mandiri Soal TKA
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Form Pencarian & Filter Status -->
                <form action="{{ route('soaltka.monitoring.index') }}" method="get" class="mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" value="{{ request()->get('search') }}" placeholder="Cari mapel / guru / tingkat...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">- Semua Status Upload -</option>
                                <option value="sudah" {{ request()->get('status') == 'sudah' ? 'selected' : '' }}>Sudah Upload (Ceklis Hijau)</option>
                                <option value="belum" {{ request()->get('status') == 'belum' ? 'selected' : '' }}>Belum Upload (Silang Merah)</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            @if(request()->has('search') || request()->has('status'))
                                <a href="{{ route('soaltka.monitoring.index') }}" class="btn btn-outline-secondary"><i class="fa fa-undo"></i> Reset</a>
                            @endif
                        </div>
                    </div>
                </form>

                @include('include.flash')

                <div class="table-responsive col-12">
                    <table class="table table-striped table-hover align-middle" id="table-monitoring">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Mapel</th>
                                <th width="12%">Tingkat</th>
                                <th>Guru</th>
                                <th width="22%" class="text-center">Status Upload</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $data->firstItem(); @endphp
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <strong>{{ $item->mapel }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Kelas {{ $item->tingkat }}</span>
                                    </td>
                                    <td>
                                        {{ $item->nama_guru }}
                                    </td>
                                    <td class="text-center">
                                        @if ($item->file_soal_tka)
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <img src="{{ asset('assets/images/icon/check.png') }}" width="24" height="24" alt="Sudah Upload" title="Sudah Upload">
                                                <a href="{{ asset('uploads/soal_tka/' . $item->file_soal_tka) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Lihat / Unduh Berkas">
                                                    <i class="fa fa-download"></i> Unduh File
                                                </a>
                                            </div>
                                        @else
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <img src="{{ asset('assets/images/icon/cross.png') }}" width="24" height="24" alt="Belum Upload" title="Belum Upload">
                                                <span class="badge bg-light-danger text-danger">Belum Upload</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i>Tidak ada data penugasan mapel yang ditemukan.</i>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->links() }}
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
