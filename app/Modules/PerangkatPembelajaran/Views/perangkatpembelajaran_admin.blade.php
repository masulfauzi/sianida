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
                Tabel Data {{ $title }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <form action="{{ route('perangkatpembelajaran.index') }}" method="get">
                            <div class="form-group col-md-3 has-icon-left position-relative">
                                <input type="text" class="form-control" value="{{ request()->get('search') }}" name="search" placeholder="Search">
                                <div class="form-control-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-3">  
						{!! button('perangkatpembelajaran.create', 'Data') !!}  
                    </div>
                </div>
                @include('include.flash')
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
								<td>Nama Guru</td>
								<td>Tingkat</td>

								<td>Mapel</td>
								
								<td>Jenis Perangkat</td>
								<td>File</td>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $no = $data->firstItem(); 
                            @endphp
                            @forelse ($data as $item)
                                <input type="hidden" id="id-{{ $item->id }}" value="{{ url('/uploads/perangkat/'.$item->file) }}">
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->guru['nama'] }}</td>
                                    <td colspan="1">{{ $item->tingkat['tingkat'] }}</td>

									<td colspan="1">{{ $item->mapel['mapel'] }}</td>
									<td>{{ $item->jenisPerangkat['jenis_perangkat'] }}</td>
                                    <td><a href="{{ url('/uploads/perangkat/'.$item->file) }}">{{ $item->file }}</a></td>

									
                                    <td>
                                        <button class="btn btn-secondary" onclick="copyValue('id-{{ $item->id }}')">Copy URL</button>
										{{-- {!! button('perangkatpembelajaran.show','', $item->id) !!}
										{!! button('perangkatpembelajaran.edit', $title, $item->id) !!} --}}
                                        {!! button('perangkatpembelajaran.destroy', $title, $item->id) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center"><i>No data.</i></td>
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
<script>
    function copyValue(id) {

        // console.log(id);

        // // Get the text field
        var copyText = document.getElementById(id);

        // console.log(copyText);

        // // Select the text field
        // var data = copyText.select();
        // console.log(copyText.value);

        // copyText.setSelectionRange(0, 99999); // For mobile devices

        // // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // // Alert the copied text
        alert("Copied the text: " + copyText.value);
    } 
</script>
@endsection