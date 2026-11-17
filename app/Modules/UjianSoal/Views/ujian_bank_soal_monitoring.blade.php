@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
    {{-- Helper can() selalu true, jadi baca privilege langsung (sama dengan yang dicek AuthorizeRequest) --}}
    @php
        $bisaPratinjau = (int) (session('privileges')['ujiansoalmonitoring']['show'] ?? 0) === 1;
        $bisaImport    = (int) (session('privileges')['ujiansoalmonitoring']['create'] ?? 0) === 1;
    @endphp
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-2">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ $title }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <h6 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>Status Upload Soal Ujian &mdash; Semester {{ $semester ?: '-' }}</span>
                    @if ($bisaImport)
                        <a href="{{ route('ujiansoalmonitoring.import.create') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-file-excel"></i> Import Soal Excel
                        </a>
                    @endif
                </h6>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <form action="{{ route('ujiansoal.monitoring.index') }}" method="get">
                                <div class="form-group has-icon-left position-relative mb-0">
                                    <input type="text" class="form-control" value="{{ $search }}" name="search" placeholder="Cari mapel">
                                    <div class="form-control-icon"><i class="fa fa-search"></i></div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8 text-md-end mt-2 mt-md-0">
                            @foreach ($ringkasan as $namaTingkat => $r)
                                <span class="badge {{ $r['total'] && $r['sudah'] === $r['total'] ? 'bg-success' : 'bg-light-secondary text-dark' }} me-1">
                                    Kelas {{ $namaTingkat }}: {{ $r['sudah'] }}/{{ $r['total'] }} sudah
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="text-center">No</th>
                                    <th>Mapel</th>
                                    @foreach ($tingkat as $t)
                                        <th width="16%" class="text-center">Kelas {{ $t }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $i => $row)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $row['mapel'] }}</td>
                                        @foreach ($tingkat as $t)
                                            @php $sel = $row['sel'][$t]; @endphp
                                            <td class="text-center">
                                                @if ($sel['status'] === 'sudah')
                                                    <i class="fa fa-check-circle text-success fs-4"
                                                       title="{{ $sel['detail']->jumlah_soal }} soal oleh {{ $sel['detail']->nama_guru ?: '-' }}"></i>
                                                    <div class="small text-muted">
                                                        {{ $sel['detail']->jumlah_soal }} soal
                                                    </div>
                                                    <div class="small text-start mt-1">
                                                        @foreach ($sel['detail']->bank as $bank)
                                                            @php
                                                                $teks = ($bank->jurusan ?: 'Jurusan belum diisi') . ' · ' . ($bank->nama_guru ?: '-') . ' · ' . $bank->jumlah_soal . ' soal';
                                                            @endphp
                                                            <div class="mb-1">
                                                                @if ($bisaPratinjau)
                                                                    <a href="{{ route('ujiansoalmonitoring.preview.show', $bank->id) }}" target="_blank"
                                                                       title="Pratinjau: {{ $bank->nama_bank_soal }}">
                                                                        <i class="fa fa-eye"></i> {{ $teks }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted" title="{{ $bank->nama_bank_soal }}">{{ $teks }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif ($sel['status'] === 'belum')
                                                    <i class="fa fa-times-circle text-danger fs-4" title="Belum ada soal"></i>
                                                @else
                                                    <span class="text-muted" title="Mapel tidak diajarkan di kelas {{ $t }}">&ndash;</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + count($tingkat) }}" class="text-center"><i>Tidak ada data mapel.</i></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="small text-muted mt-2">
                        <i class="fa fa-check-circle text-success"></i> Sudah ada bank soal berisi minimal 1 soal
                        (daftar di bawahnya: jurusan · guru · jumlah soal@if ($bisaPratinjau), klik <i class="fa fa-eye"></i> untuk pratinjau@endif) &nbsp;
                        <i class="fa fa-times-circle text-danger"></i> Belum ada soal &nbsp;
                        <span>&ndash;</span> Mapel tidak diajarkan di tingkat tersebut
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
