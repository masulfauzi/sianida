<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60;url={{ route('monitoring.presensiharian', request()->has('tgl') ? ['tgl' => $tgl] : []) }}">
    <title>Monitoring {{ $title }} | {{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/fontawesome.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0d1117;
            color: #e6edf3;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .monitoring-header {
            background: #161b22;
            border-bottom: 1px solid #30363d;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .monitoring-header .brand {
            font-size: 1.1rem;
            font-weight: 600;
            color: #58a6ff;
        }

        .monitoring-header .date-info {
            text-align: center;
        }

        .monitoring-header .date-info .date-label {
            font-size: 0.75rem;
            color: #8b949e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .monitoring-header .date-info .date-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f0f6fc;
        }

        .monitoring-header .clock {
            text-align: right;
        }

        .monitoring-header .clock .time-display {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3fb950;
            font-variant-numeric: tabular-nums;
        }

        .monitoring-header .clock .refresh-info {
            font-size: 0.7rem;
            color: #8b949e;
        }

        .monitoring-body {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-bar label {
            font-size: 0.85rem;
            color: #8b949e;
            white-space: nowrap;
        }

        .filter-bar input[type="date"] {
            background: #161b22;
            border: 1px solid #30363d;
            color: #e6edf3;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .filter-bar input[type="date"]:focus {
            outline: none;
            border-color: #58a6ff;
        }

        .filter-bar .btn-filter {
            background: #238636;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .filter-bar .btn-filter:hover { background: #2ea043; }

        .filter-bar .btn-reset {
            background: transparent;
            color: #8b949e;
            border: 1px solid #30363d;
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
        }

        .filter-bar .btn-reset:hover { color: #e6edf3; border-color: #8b949e; }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .charts-grid { grid-template-columns: 1fr; }
        }

        .chart-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 10px;
            overflow: hidden;
        }

        .chart-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid #30363d;
            font-size: 0.9rem;
            font-weight: 600;
            color: #58a6ff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card-header::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3fb950;
        }

        .chart-card-body {
            padding: 12px 8px 8px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #8b949e;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="monitoring-header">
    <div class="brand">
        <i class="bi bi-tornado"></i> {{ config('app.name') }}
    </div>
    <div class="date-info">
        <div class="date-label">Monitoring {{ $title }}</div>
        <div class="date-value">
            {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}
        </div>
    </div>
    <div class="clock">
        <div class="time-display" id="clock">--:--:--</div>
        <div class="refresh-info">Berganti ke Presensi Harian setiap 1 menit</div>
    </div>
</div>

<div class="monitoring-body">
    <div class="filter-bar">
        <label for="tgl">Pilih Tanggal:</label>
        <form action="{{ route('monitoring.presensisholat') }}" method="get" style="display:flex;gap:8px;align-items:center;">
            <input type="date" id="tgl" name="tgl" value="{{ $tgl }}" required>
            <button type="submit" class="btn-filter">Tampilkan</button>
            <a href="{{ route('monitoring.presensisholat') }}" class="btn-reset">Hari Ini</a>
        </form>
    </div>

    <div class="charts-grid">
        @forelse($charts as $i => $chart)
            <div class="chart-card">
                <div class="chart-card-header">Sholat Dzuhur Angkatan {{ $chart['angkatan'] }}</div>
                <div class="chart-card-body">
                    @if(count($chart['series']) > 0)
                        <div id="chart-{{ $i }}"></div>
                    @else
                        <div class="no-data">Tidak ada data presensi sholat</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="chart-card">
                <div class="chart-card-body">
                    <div class="no-data">Tidak ada data presensi sholat</div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    var statusColors = {
        'Hadir': '#3fb950',
        'Ijin':  '#f85149',
    };

    function getSeriesColors(series) {
        return series.map(function (s) {
            return statusColors[s.name] || '#58a6ff';
        });
    }

    function renderBarChart(selector, chartData) {
        var options = {
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                background: 'transparent',
                toolbar: { show: false },
                animations: { enabled: true, speed: 600 }
            },
            theme: { mode: 'dark' },
            series: chartData.series,
            colors: getSeriesColors(chartData.series),
            xaxis: {
                categories: chartData.categories,
                labels: { style: { colors: '#8b949e', fontSize: '11px' } }
            },
            yaxis: {
                labels: { style: { colors: '#8b949e', fontSize: '11px' } }
            },
            plotOptions: {
                bar: { horizontal: false, columnWidth: '60%', borderRadius: 4 }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', colors: ['#e6edf3'] },
                formatter: function(val) { return val > 0 ? val : ''; }
            },
            legend: {
                position: 'top',
                labels: { colors: '#8b949e' }
            },
            grid: {
                borderColor: '#21262d',
                strokeDashArray: 3
            },
            tooltip: { theme: 'dark' },
            noData: { text: 'Tidak ada data', style: { color: '#8b949e' } }
        };
        var chart = new ApexCharts(document.querySelector(selector), options);
        chart.render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        @foreach($charts as $i => $chart)
            @if(count($chart['series']) > 0)
                renderBarChart('#chart-{{ $i }}', @json(['categories' => $chart['categories'], 'series' => $chart['series']]));
            @endif
        @endforeach

        function updateClock() {
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = h + ':' + m + ':' + s;
        }
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>

</body>
</html>
