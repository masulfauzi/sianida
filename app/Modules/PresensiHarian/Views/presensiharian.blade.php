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

    document.addEventListener('DOMContentLoaded', function () {
        renderBarChart('#chart-x',   @json($chart_x));
        renderBarChart('#chart-xi',  @json($chart_xi));
        renderBarChart('#chart-xii', @json($chart_xii));
    });
</script>
@endsection
