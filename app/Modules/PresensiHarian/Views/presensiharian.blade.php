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
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6>Filter Tanggal</h6></div>
                    <div class="card-body">
                        <form action="{{ route('presensiharian.index') }}" method="get" class="d-flex gap-2 align-items-end">
                            <div class="form-group">
                                <label for="tgl" class="form-label">Pilih Tanggal</label>
                                <input type="date" id="tgl" name="tgl" class="form-control" value="{{ $tgl }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('presensiharian.index') }}" class="btn btn-secondary">Reset</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Siswa Belum Presensi &mdash; {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') }}</h6>
                        @unless ($libur_info)
                            <span class="badge bg-danger">{{ $belum_presensi->count() }} siswa</span>
                        @endunless
                    </div>
                    <div class="card-body">
                        @if ($libur_info)
                            <div class="alert alert-light-info mb-0">{{ $libur_info }} &mdash; tidak ada presensi yang perlu dicek.</div>
                        @else
                            @if (count($kelas_off))
                                <p class="text-muted small mb-2">Kelas OFF (tidak ditampilkan): {{ implode(', ', $kelas_off) }}</p>
                            @endif

                            @if ($belum_presensi->isEmpty())
                                <p class="mb-0"><i>Semua siswa sudah presensi.</i></p>
                            @else
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <select id="filter-belum-kelas" class="form-select form-select-sm">
                                            <option value="">Semua kelas</option>
                                            @foreach ($belum_presensi->pluck('kelas', 'id_kelas')->unique() as $idKelas => $namaKelas)
                                                <option value="{{ $idKelas }}">{{ $namaKelas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="filter-belum-nama" class="form-control form-control-sm" placeholder="Cari nama siswa">
                                    </div>
                                    <div class="col-md-4 text-md-end small text-muted pt-1" id="filter-belum-info"></div>
                                </div>
                                <div style="max-height:400px; overflow-y:auto">
                                    <table class="table table-sm table-bordered mb-0" id="tabel-belum-presensi">
                                        <thead style="position:sticky; top:0; z-index:1" class="table-light">
                                            <tr>
                                                <th style="width:60px">No</th>
                                                <th>Nama Siswa</th>
                                                <th>NIS</th>
                                                <th>Kelas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($belum_presensi as $i => $s)
                                                <tr data-kelas="{{ $s->id_kelas }}" data-nama="{{ strtolower($s->nama_siswa) }}">
                                                    <td class="no-urut">{{ $i + 1 }}</td>
                                                    <td>{{ $s->nama_siswa }}</td>
                                                    <td>{{ $s->nis }}</td>
                                                    <td>{{ $s->kelas }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6>Grafik Kehadiran Kelas X</h6></div>
                    <div class="card-body"><div id="chart-x"></div></div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6>Grafik Kehadiran Kelas XI</h6></div>
                    <div class="card-body"><div id="chart-xi"></div></div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6>Grafik Kehadiran Kelas XII</h6></div>
                    <div class="card-body"><div id="chart-xii"></div></div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
<script>
    var statusColors = {
        'Hadir':        '#28a745',
        'Terlambat':    '#fd7e14',
        'Tidak Hadir':  '#dc3545',
    };

    function getSeriesColors(series) {
        return series.map(function (s) {
            return statusColors[s.name] || '#6c757d';
        });
    }

    function renderBarChart(selector, chartData) {
        var options = {
            chart: { type: 'bar', height: 350, stacked: false },
            series: chartData.series,
            colors: getSeriesColors(chartData.series),
            xaxis: { categories: chartData.categories },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', distributed: false } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
            noData: { text: 'Tidak ada data kehadiran' }
        };
        var chart = new ApexCharts(document.querySelector(selector), options);
        chart.render();
    }

    function filterBelumPresensi() {
        var tabel = document.getElementById('tabel-belum-presensi');
        if (!tabel) return;
        var kelas = document.getElementById('filter-belum-kelas').value;
        var nama = document.getElementById('filter-belum-nama').value.trim().toLowerCase();
        var rows = tabel.querySelectorAll('tbody tr');
        var no = 0;
        rows.forEach(function (row) {
            var cocok = (!kelas || row.dataset.kelas === kelas) && (!nama || row.dataset.nama.indexOf(nama) !== -1);
            row.style.display = cocok ? '' : 'none';
            if (cocok) row.querySelector('.no-urut').textContent = ++no;
        });
        document.getElementById('filter-belum-info').textContent = 'Menampilkan ' + no + ' dari ' + rows.length + ' siswa';
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('tabel-belum-presensi')) {
            document.getElementById('filter-belum-kelas').addEventListener('change', filterBelumPresensi);
            document.getElementById('filter-belum-nama').addEventListener('input', filterBelumPresensi);
            filterBelumPresensi();
        }

        renderBarChart('#chart-x',   @json($chart_x));
        renderBarChart('#chart-xi',  @json($chart_xi));
        renderBarChart('#chart-xii', @json($chart_xii));
    });
</script>
@endsection
