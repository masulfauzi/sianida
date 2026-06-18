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
										@if($item->is_valid_bk == '1')
											<button type="button" class="btn btn-sm icon icon-left btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalCetak{{ $item->id }}"><i class="fa fa-print"></i> Cetak</button>
										@endif
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

	@foreach ($data as $item)
		@if($item->is_valid_bk == '1')
			<div class="modal fade" id="modalCetak{{ $item->id }}" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Surat Ijin Meninggalkan Pelajaran</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div id="cetakArea{{ $item->id }}">
								<table width="100%">
									<tr>
										<td width="15%"><img src="{{ asset('assets/images/logo/logo_jateng.png') }}" alt="" width="80px"></td>
										<td class="center">
											<font style="font-weight: bold; font-size: 12pt;">PEMERINTAH PROVINSI JAWA TENGAH</font><br>
											<font style="font-weight: bold; font-size: 12pt;">DINAS PENDIDIKAN DAN KEBUDAYAAN</font><br>
											<font style="font-weight: bold; font-size: 14pt;">SEKOLAH MENENGAH KEJURUAN NEGERI 2 SEMARANG</font><br>
											<font style="font-size: 10pt;">Jalan Dr. Cipto Nomor 121 A, Kota Semarang Kode Pos 50124 Telepon 024-8455757</font><br>
											<font style="font-size: 10pt;">Faksimile 024-8455757 Surat Elektronik smeansa_smg@yahoo.co.id</font>
										</td>
									</tr>
								</table>
								<hr class="atas">
								<hr class="bawah">

								<p class="center bold underline" style="font-size: 13pt;">SURAT IJIN MENINGGALKAN PELAJARAN</p>

								<p>Diberikan Kepada</p>
								<table>
									<tr>
										<td width="20%">Nama</td>
										<td width="2%">:</td>
										<td>{{ $item->nama_siswa }}</td>
									</tr>
									<tr>
										<td width="20%">Nomor Induk Siswa</td>
										<td width="2%">:</td>
										<td>{{ $item->nis }}</td>
									</tr>
									<tr>
										<td width="20%">Ijin Jam Ke</td>
										<td width="2%">:</td>
										<td>{{ $item->jam_keluar_pelajaran }} s.d. {{ $item->jam_kembali_pelajaran }}</td>
									</tr>
									<tr>
										<td width="20%">Alasan ijin</td>
										<td width="2%">:</td>
										<td>{{ $item->jenis_ijin_keluar_kelas }} - {{ $item->keperluan }}</td>
									</tr>
									<tr>
										<td width="20%">Alamat Rumah</td>
										<td width="2%">:</td>
										<td>{{ $item->alamat_siswa }}</td>
									</tr>
								</table>

								<br>

								<table>
									<tr>
										<td width="50%">Mengetahui :</td>
										<td>Semarang, {{ \App\Helpers\Format::tanggal($item->tanggal) }}</td>
									</tr>
									<tr>
										<td>Orang Tua/Wali/Lembaga</td>
										<td>a.n. Kepala SMK 2 Semarang<br>Guru BK / Guru Piket</td>
									</tr>
									<tr>
										<td><br><br><br></td>
										<td><br><br><br></td>
									</tr>
									<tr>
										<td>( .......................................... )</td>
										<td>( .......................................... )</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
							<button type="button" class="btn btn-primary" onclick="printArea('cetakArea{{ $item->id }}')"><i class="fa fa-print"></i> Cetak</button>
						</div>
					</div>
				</div>
			</div>
		@endif
	@endforeach
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@endsection