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
                        <form action="{{ route('skmengajar.index') }}" method="get">
                            <div class="form-group col-md-3 has-icon-left position-relative">
                                <input type="text" class="form-control" value="{{ request()->get('search') }}" name="search" placeholder="Search">
                                <div class="form-control-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-3">  

                        @if (in_array(session('active_role')['id'], $allow))
                            {!! button('skmengajar.create', $title) !!}  
                        @endif
						
                    </div>
                </div>
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
								<td>Nama</td>
                                <td>Semester</td>
								<td>Download</td>

								@if (in_array(session('active_role')['id'], $allow))
                                    <th width="20%">Aksi</th>
                                @endif
                                
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $data->firstItem(); @endphp
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
									<td>{{ $item->nama }}</td>
                                    <td>{{ $item->semester->semester }}</td>
									<td><a target="_blank" class="btn btn-primary" href="{{ url('uploads/sk/'.$item->file) }}">Download</a></td>
									
                                    @if (in_array(session('active_role')['id'], $allow))
                                        <td>
                                            {!! button('skmengajar.show','', $item->id) !!}
                                            {!! button('skmengajar.edit', $title, $item->id) !!}
                                            {!! button('skmengajar.destroy', $title, $item->id) !!}
                                        </td>
                                    @endif
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center"><i>No data.</i></td>
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