@extends('layouts.app')

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Soal - {{ $bankSoal->nama_bank_soal }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ujiansoal.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $bankSoal->nama_bank_soal }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Daftar Soal
                <span class="badge {{ $bankSoal->status === 'final' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $bankSoal->status === 'final' ? 'Final' : 'Draft' }}
                </span>
            </h6>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-9">
                        <a href="{{ route('ujiansoal.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali ke Bank Soal
                        </a>
                        @if ($data->isNotEmpty() && can('ujiansoal.soal.preview.show'))
                            <a href="{{ route('ujiansoal.soal.preview.show', $bankSoal->id) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i> Pratinjau Soal
                            </a>
                        @endif
                    </div>
                    <div class="col-3">
                        {!! button('ujiansoal.soal.create', 'Soal', $bankSoal->id) !!}
                    </div>
                </div>
                @include('include.flash')

                @if ($bankSoal->status === 'final')
                    <div class="alert alert-info">
                        Bank soal ini sudah <b>final</b>. Soal masih bisa diubah, tapi export perlu diulang kalau ada perubahan.
                    </div>
                @endif

                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="10">No</th>
                                <td width="15%">Jenis</td>
                                <td>Soal</td>
                                <td width="10%">Kunci</td>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $item->no_soal }}</td>
                                    <td>{{ \App\Modules\UjianSoal\Models\UjianSoal::LABEL_JENIS[$item->jenis] ?? $item->jenis }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->soal), 80) }}</td>
                                    <td>{{ $item->kunci }}</td>
                                    <td>
                                        @if (can('ujiansoal.soal.preview.show'))
                                            <a href="{{ route('ujiansoal.soal.preview.show', [$bankSoal->id, 'no' => $item->no_soal]) }}" class="btn btn-sm icon btn-outline-info" title="Pratinjau soal ini">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endif
                                        {!! button('ujiansoal.soal.edit', '', [$bankSoal->id, $item->id]) !!}
                                        {!! button('ujiansoal.soal.destroy', 'Soal', [$bankSoal->id, $item->id]) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center"><i>Belum ada soal.</i></td>
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
