@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Statistik Data Peserta Didik</h3>
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

    <div class="row">
        <div class="col-6">
            <section class="section">
                <div class="card">
                    <h6 class="card-header">
                        Jumlah Siswa Perjurusan
                    </h6>
                    <div class="card-body">
                        <div id="perjurusan"></div>
                    </div>
                </div>
        
            </section>
        </div>
    </div>

    

    
</div>
@endsection

@section('page-js')
<script src="https://code.highcharts.com/highcharts.js"></script>
@endsection
 
@section('inline-js')
<script>
    // Data retrieved from https://netmarketshare.com
Highcharts.chart('perjurusan', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: '',
        align: 'left'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.y:.0f}</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            }
        }
    },
    series: [{
        name: 'Jumlah',
        colorByPoint: true,
        data: [
            @foreach($perjurusan as $item)
                {
                    name: "{{ $item->jurusan }}",
                    y: {{ $item->jml }}
                },
            @endforeach
        ]
    }]
});

</script>

@endsection