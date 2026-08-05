<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60;url={{ route('monitoring.presensiharian') }}">
    <title>Monitoring {{ $title }} | {{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/fontawesome.css') }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background: #0d1117;
            color: #e6edf3;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .monitoring-header {
            background: #161b22;
            border-bottom: 1px solid #30363d;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
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
            padding: 24px;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .slideshow {
            position: relative;
            flex: 1;
            min-height: 0;
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 10px;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 48px;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }

        .slide.active {
            opacity: 1;
        }

        .slide .nama-kegiatan {
            font-size: 9rem;
            font-weight: 700;
            color: #f0f6fc;
            margin-bottom: 40px;
            line-height: 1.3;
        }

        .slide .tanggal {
            font-size: 4rem;
            color: #8b949e;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .slide .tanggal .badge {
            background: #1f6feb33;
            border: 1px solid #1f6feb;
            color: #58a6ff;
            padding: 16px 40px;
            border-radius: 999px;
            font-size: 3.2rem;
            font-weight: 600;
        }

        .slide-dots {
            position: absolute;
            bottom: 16px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .slide-dots .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #30363d;
            transition: background 0.3s ease;
        }

        .slide-dots .dot.active {
            background: #58a6ff;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #8b949e;
            font-size: 1.1rem;
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
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
    <div class="clock">
        <div class="time-display" id="clock">--:--:--</div>
        <div class="refresh-info">Berganti ke Presensi Harian setiap 1 menit</div>
    </div>
</div>

<div class="monitoring-body">
    <div class="slideshow">
        @forelse($agenda as $i => $item)
            <div class="slide @if($i === 0) active @endif">
                <div class="nama-kegiatan">{{ $item->nama_kegiatan }}</div>
                <div class="tanggal">
                    <span class="badge">{{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('d F Y') }}</span>
                    <span>s/d</span>
                    <span class="badge">{{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        @empty
            <div class="slide active">
                <div class="no-data">Tidak ada agenda mendatang</div>
            </div>
        @endforelse

        @if(count($agenda) > 1)
            <div class="slide-dots">
                @foreach($agenda as $i => $item)
                    <div class="dot @if($i === 0) active @endif"></div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('.slide');
        var dots = document.querySelectorAll('.slide-dots .dot');
        var current = 0;

        if (slides.length > 1) {
            setInterval(function () {
                slides[current].classList.remove('active');
                if (dots.length) dots[current].classList.remove('active');

                current = (current + 1) % slides.length;

                slides[current].classList.add('active');
                if (dots.length) dots[current].classList.add('active');
            }, 5000);
        }

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
