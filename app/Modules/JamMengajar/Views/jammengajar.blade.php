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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Pembagian Jam Mengajar</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Perguru</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Perkelas</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab"
                                    aria-controls="contact" aria-selected="false">Contact</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="card">
                                    
                                    <div class="card-body">
                                        
                                        @include('include.flash')
                                        <div class="table-responsive-md col-12">
                                            <table class="table" id="table1">
                                                <thead>
                                                    <tr>
                                                        <th width="15">No</th>
                                                        <td>Guru</td>
                                                        <td>Jml Jam</td>
                                                        
                                                        <th width="30%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @forelse ($data as $item)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $item->nama }}</td>
                                                            <td>{{ $item->jml_jam }}</td>
                                                            
                                                            <td>
                                                                <a href="{{ route('jammengajar.guru.index', $item->id_guru) }}" class="btn btn-success">Detail</a>
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
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="card">
                                    
                                    <div class="card-body">
                                        
                                        @include('include.flash')
                                        <div class="table-responsive-md col-12">
                                            <table class="table" id="table1">
                                                <thead>
                                                    <tr>
                                                        <th width="15">No</th>
                                                        <td>Kelas</td>
                                                        <td>Jml Jam</td>
                                                        
                                                        <th width="30%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @forelse ($kelas as $item_kelas)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $item_kelas->kelas }}</td>
                                                            <td>{{ $item_kelas->jml_jam }}</td>
                                                            
                                                            <td>
                                                                <a href="{{ route('jammengajar.guru.index', $item_kelas->id_kelas) }}" class="btn btn-success">Detail</a>
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
                            </div>
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <p class="mt-2">Duis ultrices purus non eros fermentum hendrerit. Aenean ornare interdum
                                    viverra. Sed ut odio velit. Aenean eu diam
                                    dictum nibh rhoncus mattis quis ac risus. Vivamus eu congue ipsum. Maecenas id
                                    sollicitudin ex. Cras in ex vestibulum,
                                    posuere orci at, sollicitudin purus. Morbi mollis elementum enim, in cursus sem
                                    placerat ut.</p>
                            </div>
                        </div>
                    </div>
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