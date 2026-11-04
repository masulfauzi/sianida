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

        <div class="row">
            @foreach (['X', 'XI', 'XII'] as $tingkat)
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6>Kelas {{ $tingkat }} &mdash; {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') }}</h6></div>
                        <div class="card-body">
                            <div id="chart-{{ strtolower($tingkat) }}"></div>
                            <hr>
                            @include('PresensiHarian::presensiharian_belum_presensi', [
                                'tingkat'   => $tingkat,
                                'list'      => $belum_presensi[$tingkat],
                                'kelasOff'  => $kelas_off[$tingkat] ?? [],
                                'liburInfo' => $libur_info,
                            ])
                        </div>
                    </div>
                </div>
            @endforeach
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

    function filterBelumPresensi(wrapper) {
        var tabel = wrapper.querySelector('.tabel-belum-presensi');
        if (!tabel) return;
        var kelas = wrapper.querySelector('.filter-belum-kelas').value;
        var nama = wrapper.querySelector('.filter-belum-nama').value.trim().toLowerCase();
        var rows = tabel.querySelectorAll('tbody tr');
        var no = 0;
        rows.forEach(function (row) {
            var cocok = (!kelas || row.dataset.kelas === kelas) && (!nama || row.dataset.nama.indexOf(nama) !== -1);
            row.style.display = cocok ? '' : 'none';
            if (cocok) row.querySelector('.no-urut').textContent = ++no;
        });
        wrapper.querySelector('.filter-belum-info').textContent = 'Menampilkan ' + no + ' dari ' + rows.length + ' siswa';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.belum-presensi-wrapper').forEach(function (wrapper) {
            if (!wrapper.querySelector('.tabel-belum-presensi')) return;
            var jalankan = function () { filterBelumPresensi(wrapper); };
            wrapper.querySelector('.filter-belum-kelas').addEventListener('change', jalankan);
            wrapper.querySelector('.filter-belum-nama').addEventListener('input', jalankan);
            jalankan();
        });

        renderBarChart('#chart-x',   @json($chart_x));
        renderBarChart('#chart-xi',  @json($chart_xi));
        renderBarChart('#chart-xii', @json($chart_xii));
    });
</script>
@endsection
