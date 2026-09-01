@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Rekap Bulanan {{ $title }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presensiharian.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Rekap Bulanan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header"><h6>Filter</h6></div>
            <div class="card-body">
                <form action="{{ route('presensiharian.rekap.bulanan.index') }}" method="get" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Kelas</label>
                        <select name="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($ref_kelas as $id => $nama)
                                <option value="{{ $id }}" {{ $id_kelas == $id ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
                                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ $tahun ?: date('Y') }}" min="2000" max="2099" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($id_kelas && $bulan)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Hasil Rekap</h6>
                <a href="{{ route('presensiharian.rekap.bulanan.export.show', ['id_kelas' => $id_kelas, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Download Excel
                </a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" style="white-space:nowrap">No</th>
                            <th rowspan="2" class="text-start" style="min-width:180px">Nama Siswa</th>
                            <th colspan="{{ $jumlah_hari }}">Tanggal</th>
                            <th rowspan="2" class="table-success">Hadir</th>
                            <th rowspan="2" class="table-warning">Sakit</th>
                            <th rowspan="2" class="table-info">Ijin</th>
                            <th rowspan="2" class="table-danger">Alfa</th>
                        </tr>
                        <tr>
                            @for ($d = 1; $d <= $jumlah_hari; $d++)
                                @php
                                    $isWeekend = in_array(date('N', mktime(0,0,0,$bulan,$d,$tahun)), [6,7]);
                                    $isLibur = in_array($d, $hari_libur);
                                @endphp
                                <th @class(['table-secondary' => $isWeekend || $isLibur])>{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-start">
                                    <a href="#" class="link-primary" data-bs-toggle="modal" data-bs-target="#modalDetailSiswa{{ $s->id_siswa }}">{{ $s->nama_siswa }}</a>
                                </td>
                                @for ($d = 1; $d <= $jumlah_hari; $d++)
                                    @php
                                        $isWeekend = in_array(date('N', mktime(0,0,0,$bulan,$d,$tahun)), [6,7]);
                                        $isLibur = in_array($d, $hari_libur);
                                    @endphp
                                    @if ($isWeekend || $isLibur)
                                        <td class="table-secondary text-muted">OFF</td>
                                    @else
                                        <td>{{ $rekap[$s->id_siswa][$d] ?? 'A' }}</td>
                                    @endif
                                @endfor
                                <td class="table-success fw-bold">{{ $summary[$s->id_siswa]['hadir'] ?? 0 }}</td>
                                <td class="table-warning fw-bold">{{ $summary[$s->id_siswa]['sakit'] ?? 0 }}</td>
                                <td class="table-info fw-bold">{{ $summary[$s->id_siswa]['ijin'] ?? 0 }}</td>
                                @php
                                    $alfa = $hari_efektif
                                        - ($summary[$s->id_siswa]['hadir'] ?? 0)
                                        - ($summary[$s->id_siswa]['sakit'] ?? 0)
                                        - ($summary[$s->id_siswa]['ijin'] ?? 0);
                                @endphp
                                <td class="table-danger fw-bold">{{ max(0, $alfa) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $jumlah_hari + 6 }}" class="text-center">
                                    <i>Tidak ada siswa di kelas ini.</i>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $namaBulanList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        @endphp
        @foreach ($siswa as $s)
            <div class="modal fade" id="modalDetailSiswa{{ $s->id_siswa }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Presensi - {{ $s->nama_siswa }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Bulan: <strong>{{ $namaBulanList[(int) $bulan] ?? $bulan }} {{ $tahun }}</strong></p>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th style="white-space:nowrap">Tanggal</th>
                                        <th>Hari</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($d = 1; $d <= $jumlah_hari; $d++)
                                        @php
                                            $isWeekend = in_array(date('N', mktime(0,0,0,$bulan,$d,$tahun)), [6,7]);
                                            $isLibur = in_array($d, $hari_libur);
                                            $statusLengkap = $rekap_lengkap[$s->id_siswa][$d] ?? 'Alfa';
                                            $statusClass = match (strtolower($statusLengkap)) {
                                                'hadir', 'terlambat' => 'table-success',
                                                'sakit' => 'table-warning',
                                                'ijin', 'izin' => 'table-info',
                                                default => 'table-danger',
                                            };
                                            $idPresensi = $rekap_id[$s->id_siswa][$d] ?? null;
                                            $tglPresensi = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                        @endphp
                                        <tr @class(['table-secondary text-muted' => $isWeekend || $isLibur, $statusClass => !($isWeekend || $isLibur)])>
                                            <td>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}</td>
                                            <td>{{ \Carbon\Carbon::create($tahun, $bulan, $d)->translatedFormat('l') }}</td>
                                            <td class="fw-bold">
                                                @if ($isWeekend || $isLibur)
                                                    {{ $isLibur ? 'Hari Libur' : 'Akhir Pekan' }}
                                                @else
                                                    {{ $statusLengkap }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($isWeekend || $isLibur)
                                                    -
                                                @elseif ($idPresensi)
                                                    <a href="{{ route('presensiharian.edit', ['presensiharian' => $idPresensi, 'id_kelas' => $id_kelas, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm icon icon-left btn-outline-primary"><i class="fa fa-pencil-alt"></i> Edit</a>
                                                @else
                                                    <a href="{{ route('presensiharian.create', ['id_siswa' => $s->id_siswa, 'tgl' => $tglPresensi, 'id_kelas' => $id_kelas, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm icon icon-left btn-outline-success"><i class="fa fa-plus"></i> Isi</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @endif
    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
@if ($open_siswa ?? null)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('modalDetailSiswa{{ $open_siswa }}');
        if (modalEl && window.bootstrap) {
            new bootstrap.Modal(modalEl).show();
        }
        if (window.history.replaceState) {
            var url = new URL(window.location.href);
            url.searchParams.delete('open_siswa');
            window.history.replaceState({}, document.title, url.toString());
        }
    });
</script>
@endif
@endsection
