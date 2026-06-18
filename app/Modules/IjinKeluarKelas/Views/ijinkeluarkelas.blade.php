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
                        <form action="{{ route('ijinkeluarkelas.index') }}" method="get">
                            <div class="form-group col-md-3 has-icon-left position-relative">
                                <input type="text" class="form-control" value="{{ request()->get('search') }}" name="search" placeholder="Search">
                                <div class="form-control-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-3">  
						{!! button('ijinkeluarkelas.create', $title) !!}  
                    </div>
                </div>
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <td>Siswa</td>
								<td>Guru</td>
								<td>Jenis Ijin Keluar</td>
								<td>Keperluan</td>
								<td>Jam Keluar</td>
								<td>Jam Kembali</td>
								<td>Validasi Guru</td>
								<td>Validasi BK</td>

                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $data->firstItem(); @endphp
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->nama_siswa }}</td>
									<td>{{ $item->nama_guru }}</td>
									<td>{{ $item->jenis_ijin_keluar_kelas }}</td>
									<td>{{ $item->keperluan }}</td>
									<td>{{ $item->jam_keluar_pelajaran }}</td>
									<td>{{ $item->jam_kembali_pelajaran }}</td>
									<td>
										@php
											$badgeGuru = $item->is_valid_guru == '1' ? 'bg-success' : 'bg-warning text-dark';
										@endphp
										<span class="badge {{ $badgeGuru }}">{{ $item->is_valid_guru == '1' ? 'Disetujui' : 'Belum Disetujui' }}</span>
									</td>
									<td>
										@php
											$badgeBk = $item->is_valid_bk == '1' ? 'bg-success' : 'bg-warning text-dark';
										@endphp
										<span class="badge {{ $badgeBk }}">{{ $item->is_valid_bk == '1' ? 'Disetujui' : 'Belum Disetujui' }}</span>
									</td>

                                    <td>
										{!! button('ijinkeluarkelas.show', 'Detail', $item->id) !!}
										{!! button('ijinkeluarkelas.edit', $title, $item->id) !!}
                                        {!! button('ijinkeluarkelas.destroy', $title, $item->id) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center"><i>No data.</i></td>
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