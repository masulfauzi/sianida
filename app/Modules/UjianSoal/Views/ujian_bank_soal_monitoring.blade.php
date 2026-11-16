@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
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
                <h6 class="card-header">
                    Status Upload Soal Ujian &mdash; Semester {{ $semester ?: '-' }}
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
                                                        @if (count($sel['detail']->jurusan))
                                                            <div class="my-1">
                                                                @foreach ($sel['detail']->jurusan as $namaJurusan)
                                                                    <span class="badge bg-light-primary text-wrap mb-1">{{ $namaJurusan }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if ($sel['detail']->nama_guru)
                                                            <br>{{ $sel['detail']->nama_guru }}
                                                        @endif
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
                        (<span class="badge bg-light-primary">label</span> = jurusan yang sudah upload) &nbsp;
                        <i class="fa fa-times-circle text-danger"></i> Belum ada soal &nbsp;
                        <span>&ndash;</span> Mapel tidak diajarkan di tingkat tersebut
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
