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
                
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                {{-- <td>Guru</td> --}}
								<td>Mapel</td>
								<td>Tingkat</td>
								<td>Perangkat</td>
								{{-- <td>Perangkat</td> --}}
								
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @inject('pas', 'App\Modules\PenilaianAkhirSemester\Models\PenilaianAkhirSemester')
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    {{-- <td>{{ $item->id_guru }}</td> --}}
									<td>{{ $item->mapel }}</td>
									<td>{{ $item->tingkat }}</td>
									{{-- <td>{{ $item->id_semester }}</td> --}}
									<td>
                                        @if ($pas->get_perangkat($item))
                                            <a href="{{ url('uploads/pas/'.$pas->get_perangkat($item)->perangkat) }}" target="_blank">
                                                <img src="{{ asset('assets/images/icon/check.png') }}" alt="">
                                            </a>
                                        @else
                                            <img src="{{ asset('assets/images/icon/cross.png') }}" alt="">
                                        @endif
                                    </td>
									
                                    <td>
										<a href="{{ route('penilaianakhirsemester.upload.index', $item->id) }}" class="btn btn-primary">Upload</a>
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
            </div>
        </div>

    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection