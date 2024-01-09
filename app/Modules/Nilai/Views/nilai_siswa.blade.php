@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Manajemen Data {{ $title }}</h3>
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
                Form Transkrip Nilai
            </h6>
            <div class="card-body">
                @include('include.flash')
                <form class="form form-horizontal" action="{{ route('nilai.siswa.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-3 text-sm-start text-md-end pt-2">
                                <label>Semester</label>
                            </div>
                            <div class="col-md-9 form-group">
                                {!! Form::select('id_semester', $ref_semester, $id_semester, ["class" => "form-control select2"]) !!}
                            </div>
                        </div>
                        <div class="offset-md-3 ps-2">
                            <button class="btn btn-primary" type="submit">Simpan</button> &nbsp;
                            <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                  </div>
                </form>
            </div>
        </div>

    </section>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Tabel Transkrip Nilai
            </h6>
            <div class="card-body">
                @if ($semester != NULL)
                    <p style="text-align: center; font-size: large;">TRANSKRIP NILAI SEMESTER {{ $semester->semester }}</p>
                @endif
                
                <table class="table">
                    <tr>
                        <th>No</th>
                        <th>Mapel</th>
                        <th>Nilai</th>
                    </tr>
                    @php
                        $no = 1;
                    @endphp

                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->mapel->mapel }}</td>
                            <td>{{ $item->nilai }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center"><i>No data.</i></td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>

    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection