@extends('layouts.app')

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
            <h6 class="card-header">Tabel Data {{ $title }}</h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <form action="{{ route('pengumuman.index') }}" method="get">
                            <div class="form-group col-md-4 has-icon-left position-relative">
                                <input type="text" class="form-control" value="{{ request()->get('search') }}" name="search" placeholder="Cari judul / isi">
                                <div class="form-control-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </form>
                    </div>
                    <div class="col-3">
                        {!! button('pengumuman.create', $title) !!}
                    </div>
                </div>
                @include('include.flash')
                <div class="alert alert-light-info color-info small">
                    <i class="fa fa-bell"></i> Pengumuman baru langsung dikirim sebagai notifikasi ke <strong>semua siswa</strong> yang login di aplikasi Skanida Student. Mengedit pengumuman tidak mengirim ulang notifikasi.
                </div>
                <div class="table-responsive-md col-12">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th width="15">No</th>
                                <th>Judul</th>
                                <th>Ringkasan</th>
                                <th width="170">Notifikasi</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $data->firstItem(); @endphp
                            @forelse ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->judul }}</td>
                                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 80) }}</td>
                                    <td>
                                        @if ($item->dikirim_at)
                                            <span class="badge bg-success" title="Terkirim">{{ $item->dikirim_at->format('d-m-Y H:i') }}</span>
                                        @else
                                            <span class="badge bg-danger">Belum terkirim</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! button('pengumuman.show', '', $item->id) !!}
                                        {!! button('pengumuman.edit', $title, $item->id) !!}
                                        {!! button('pengumuman.destroy', $title, $item->id) !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center"><i>Belum ada pengumuman.</i></td>
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
