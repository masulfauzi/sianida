@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Penilaian{{ $title }} - {{ $ekskul->nama }}</h3>
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
                Tabel Data {{ $title }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <form action="{{ route('anggotaekskul.index',['id_ekskul' => $ekskul->id]) }}" method="get">
                            <div class="form-group col-md-3 has-icon-left position-relative">
                                <input type="text" class="form-control" value="{{ request()->get('search') }}" name="search" placeholder="Search">
                                <div class="form-control-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-3">
						{!! button('anggotaekskul.create', $title, ['id_ekskul' => $ekskul->id]) !!}
                    </div>
                </div>
                @include('include.flash')
                <div class="table-responsive-md col-12">
                <form action="{{ route('anggotaekskul.nilai.update') }}" method="POST">
                    @method('patch')
                    @csrf

                    <input type="hidden" name="id_ekskul" value="{{ $ekskul->id }}">

                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <th>Anggota</th>
                                <th>Kelas</th>
                                <th>Nilai</th>
                                {{-- <th>Semester</th> --}}
                            </tr>
                        </thead>

                        <tbody>
                            @php $no = $data->firstItem(); @endphp

                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>

                                    <td>
                                        {{ $item->pd->siswa->nama_siswa ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $item->pd->kelas->kelas ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <select name="nilai[{{ $item->id }}]" class="form-select">
                                            <option value="">-- Pilih Nilai --</option>
                                            <option value="A" {{ $item->nilai == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $item->nilai == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $item->nilai == 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ $item->nilai == 'D' ? 'selected' : '' }}>D</option>
                                        </select>
                                    </td>

                                    {{-- <td>{{ $item->id_semester }}</td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <i>No data.</i>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">
                        Simpan Nilai
                    </button>
                </form>
                </div>
				{{ $data->links() }}
            </div>
        </div>

    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection
