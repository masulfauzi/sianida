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
                Tabel Data {{ $title }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <form action="{{ route('verifikasirpp.index') }}" method="get">
                            <div class="form-group col-md-3 has-icon-left position-relative">
                                <input type="text" class="form-control" value="{{ request()->get('search') }}" name="search" placeholder="Search">
                                <div class="form-control-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-3 text-end">
						<a href="{{ route('verifikasirpp.export.pdf.show') }}" class="btn btn-sm icon icon-left btn-outline-dark"><i class="fa fa-file-pdf"></i> Export PDF</a>
						{!! button('verifikasirpp.create', $title) !!}
                    </div>
                </div>
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>Guru</td>
								<td>Nilai</td>
								<td>Predikat</td>
								<td>File Penilaian</td>
								<td>URL File</td>

                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $data->firstItem(); @endphp
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->nama_guru }}</td>
									<td>{{ $item->nilai }}</td>
									<td>{{ $item->predikat }}</td>
									<td>
										@if ($item->file_penilaian)
											<a href="{{ asset('download/ksp/verifikasi_rpp/'.$item->file_penilaian) }}" class="btn btn-sm btn-outline-dark" target="_blank">
												<i class="fa fa-file-pdf"></i> Download
											</a>
										@else
											<span class="text-muted"><i>Belum ada</i></span>
										@endif
									</td>
									<td>
										@if ($item->file_penilaian)
											{{ asset('download/ksp/verifikasi_rpp/'.$item->file_penilaian) }}
										@else
											<span class="text-muted">-</span>
										@endif
									</td>

                                    <td>
										{!! button('verifikasirpp.show','', $item->id) !!}
										{!! button('verifikasirpp.edit', $title, $item->id) !!}
                                        {!! button('verifikasirpp.destroy', $title, $item->id) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center"><i>No data.</i></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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