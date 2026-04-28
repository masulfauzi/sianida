@extends('layouts.app')

@section('page-css')
    <style>
        .timer {
            margin: 0 0 45px;
            font-family: sans-serif;
            color: #fff;
            display: inline-block;
            font-weight: 100;
            text-align: center;
            font-size: 30px;
        }

        .timer div {
            padding: 10px;
            border-radius: 3px;
            background: #6b02ff;
            display: inline-block;
            font-family: Oswald;
            font-size: 26px;
            font-weight: 400;
            width: 80px;
        }

        .timer .smalltext {
            color: #ffffff;
            font-size: 12px;
            font-family: Poppins;
            font-weight: 500;
            display: block;
            padding: 0;
            width: auto;
        }

        .timer #time-up {
            margin: 8px 0 0;
            text-align: left;
            font-size: 14px;
            font-style: normal;
            color: #000000;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .graduation-anim {
            position: relative;
            margin: 18px 0 8px;
            padding: 18px 16px 26px;
            border-radius: 12px;
            background: #f8fafc;
            overflow: hidden;
        }

        .graduation-title {
            text-align: center;
            font-family: Oswald, sans-serif;
            font-size: 22px;
            letter-spacing: 0.5px;
            margin: 0 0 14px;
            color: #1f2937;
        }

        .status-initial {
            position: relative;
            display: inline-block;
            font-family: Oswald, sans-serif;
            font-size: 28px;
            color: #b91c1c;
            letter-spacing: 1px;
            margin: 0 auto;
        }

        .status-initial::after {
            content: "";
            position: absolute;
            left: -6px;
            right: -6px;
            top: 50%;
            height: 3px;
            background: #b91c1c;
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 5s ease;
        }

        .status-initial.strike::after {
            transform: scaleX(1);
        }

        .status-final {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.7s ease, transform 0.7s ease;
            font-family: Oswald, sans-serif;
            font-size: 30px;
            color: #15803d;
            letter-spacing: 0.6px;
            text-align: center;
            margin-top: 12px;
        }

        .status-final.show {
            opacity: 1;
            transform: translateY(0);
        }

        #confetti-screen {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 30;
        }

        #confetti-screen.show {
            opacity: 1;
        }

        #confetti-screen.run .confetti-piece {
            animation-play-state: running;
        }

        .confetti-piece {
            position: absolute;
            width: 7px;
            height: 11px;
            background: #f59e0b;
            top: -10px;
            animation: confetti-fall 2.8s linear infinite;
            animation-play-state: paused;
        }

        .confetti-piece.sm {
            width: 5px;
            height: 8px;
        }

        .confetti-piece.lg {
            width: 10px;
            height: 16px;
        }

        .confetti-piece.c1 {
            background: #ef4444;
        }

        .confetti-piece.c2 {
            background: #22c55e;
        }

        .confetti-piece.c3 {
            background: #3b82f6;
        }

        .confetti-piece.c4 {
            background: #a855f7;
        }

        .confetti-piece.c5 {
            background: #f59e0b;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 1;
            }

            50% {
                transform: translateY(50vh) translateX(12px) rotate(160deg);
                opacity: 0.9;
            }

            100% {
                transform: translateY(100vh) translateX(-10px) rotate(320deg);
                opacity: 0;
            }
        }
    </style>
@endsection

@section('main')
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-2">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kelulusan Peserta Didik</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('siswa.kelulusan.index') }}">Kelulusan Siswa</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if ($siswa->is_lulus == 1)
            <div id="confetti-screen">
                <span class="confetti-piece c1 sm" style="left: 3%; animation-delay: 0ms; animation-duration: 2.1s;"></span>
                <span class="confetti-piece c2" style="left: 7%; animation-delay: 120ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c3 sm" style="left: 11%; animation-delay: 220ms; animation-duration: 2.2s;"></span>
                <span class="confetti-piece c4 lg" style="left: 15%; animation-delay: 320ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c5 sm" style="left: 19%; animation-delay: 420ms; animation-duration: 2.3s;"></span>
                <span class="confetti-piece c1" style="left: 23%; animation-delay: 520ms; animation-duration: 2.5s;"></span>
                <span class="confetti-piece c2 lg" style="left: 27%; animation-delay: 620ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c3" style="left: 31%; animation-delay: 720ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c4 sm" style="left: 35%; animation-delay: 820ms; animation-duration: 2.2s;"></span>
                <span class="confetti-piece c5" style="left: 39%; animation-delay: 920ms; animation-duration: 2.5s;"></span>
                <span class="confetti-piece c1 lg" style="left: 43%; animation-delay: 1020ms; animation-duration: 2.8s;"></span>
                <span class="confetti-piece c2 sm" style="left: 47%; animation-delay: 1120ms; animation-duration: 2.2s;"></span>
                <span class="confetti-piece c3" style="left: 51%; animation-delay: 1220ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c4 lg" style="left: 55%; animation-delay: 1320ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c5 sm" style="left: 59%; animation-delay: 1420ms; animation-duration: 2.3s;"></span>
                <span class="confetti-piece c1" style="left: 63%; animation-delay: 1520ms; animation-duration: 2.5s;"></span>
                <span class="confetti-piece c2 lg" style="left: 67%; animation-delay: 1620ms; animation-duration: 2.8s;"></span>
                <span class="confetti-piece c3 sm" style="left: 71%; animation-delay: 1720ms; animation-duration: 2.2s;"></span>
                <span class="confetti-piece c4" style="left: 75%; animation-delay: 1820ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c5 lg" style="left: 79%; animation-delay: 1920ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c1 sm" style="left: 83%; animation-delay: 2020ms; animation-duration: 2.3s;"></span>
                <span class="confetti-piece c2" style="left: 87%; animation-delay: 2120ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c3 lg" style="left: 91%; animation-delay: 2220ms; animation-duration: 2.8s;"></span>
                <span class="confetti-piece c4 sm" style="left: 95%; animation-delay: 2320ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c5" style="left: 2%; animation-delay: 250ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c1 lg" style="left: 5%; animation-delay: 620ms; animation-duration: 3s;"></span>
                <span class="confetti-piece c2 sm" style="left: 9%; animation-delay: 980ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c3" style="left: 13%; animation-delay: 1320ms; animation-duration: 2.9s;"></span>
                <span class="confetti-piece c4 sm" style="left: 17%; animation-delay: 1680ms; animation-duration: 2.3s;"></span>
                <span class="confetti-piece c5 lg" style="left: 21%; animation-delay: 2040ms; animation-duration: 3s;"></span>
                <span class="confetti-piece c1" style="left: 25%; animation-delay: 2400ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c2 sm" style="left: 29%; animation-delay: 2760ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c3 lg" style="left: 33%; animation-delay: 3120ms; animation-duration: 3.1s;"></span>
                <span class="confetti-piece c4" style="left: 37%; animation-delay: 3480ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c5 sm" style="left: 41%; animation-delay: 3840ms; animation-duration: 2.5s;"></span>
                <span class="confetti-piece c1 lg" style="left: 45%; animation-delay: 4200ms; animation-duration: 3s;"></span>
                <span class="confetti-piece c2" style="left: 49%; animation-delay: 4560ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c3 sm" style="left: 53%; animation-delay: 4920ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c4 lg" style="left: 57%; animation-delay: 5280ms; animation-duration: 3s;"></span>
                <span class="confetti-piece c5" style="left: 61%; animation-delay: 5640ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c1 sm" style="left: 65%; animation-delay: 6000ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c2 lg" style="left: 69%; animation-delay: 6360ms; animation-duration: 3.1s;"></span>
                <span class="confetti-piece c3" style="left: 73%; animation-delay: 6720ms; animation-duration: 2.6s;"></span>
                <span class="confetti-piece c4 sm" style="left: 77%; animation-delay: 7080ms; animation-duration: 2.5s;"></span>
                <span class="confetti-piece c5 lg" style="left: 81%; animation-delay: 7440ms; animation-duration: 3s;"></span>
                <span class="confetti-piece c1" style="left: 85%; animation-delay: 7800ms; animation-duration: 2.7s;"></span>
                <span class="confetti-piece c2 sm" style="left: 89%; animation-delay: 8160ms; animation-duration: 2.4s;"></span>
                <span class="confetti-piece c3 lg" style="left: 93%; animation-delay: 8520ms; animation-duration: 3.1s;"></span>
                <span class="confetti-piece c4" style="left: 97%; animation-delay: 8880ms; animation-duration: 2.6s;"></span>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-body" style="margin: auto;">
                    <div id="pengumuman">
                        <p style="text-align: center;">Waktu Pengumuman masih kurang :</p>
                    </div>
                    <div class="timer" id="timer">
                        <div>
                            <span class="days" id="day"></span>
                            <div class="smalltext">Hari</div>
                        </div>
                        <div>
                            <span class="hours" id="hour"></span>
                            <div class="smalltext">Jam</div>
                        </div>
                        <div>
                            <span class="minutes" id="minute"></span>
                            <div class="smalltext">Menit</div>
                        </div>
                        <div>
                            <span class="seconds" id="second"></span>
                            <div class="smalltext">Detik</div>
                        </div>
                        <p id="time-up"></p>
                    </div>

                    <div class="row" id="xpengumuman" style="display: none;">
                        <div class="col-lg-12">
                            <div class="row">
                                <table class="table table-bordered">
                                    <tr>
                                        <td width="30%">Nama Siswa</td>
                                        <td>{{ $siswa->nama_siswa }}</td>
                                    </tr>
                                    <tr>
                                        <td>NISN</td>
                                        <td>{{ $siswa->nisn }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tempat & Tanggal Lahir</td>
                                        <td>{{ $siswa->tempat_lahir }},
                                            {{ \App\Helpers\Format::tanggal($siswa->tgl_lahir) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kompetensi Keahlian</td>
                                        <td>{{ $siswa->jurusan }}</td>
                                    </tr>
                                </table>
                                {{-- <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Bahasa Indonesia</th>
                                            <th>Bahasa Inggris</th>
                                            <th>Matematika</th>
                                            <th>Kejuruan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td>45345</td>
                                        <td>3453</td>
                                        <td>345</td>
                                        <td>456</td>
                                    </tbody>
                                </table> --}}

                                @if ($siswa->is_lulus == 1)
                                    <div class="graduation-anim" id="graduation-anim">
                                        <h4 class="graduation-title">Pengumuman Kelulusan</h4>
                                        <div style="text-align: center;">
                                            <div class="status-initial" id="status-initial">Tidak Lulus</div>
                                        </div>
                                        <div class="status-final" id="status-final">Selamat Anda Lulus</div>
                                    </div>
                                @else
                                    <div style="text-align: center;" class="alert alert-danger" role="alert"><strong>MAAF
                                            !</strong> Anda dinyatakan TIDAK LULUS.</div>
                                @endif




                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection

@section('page-js')
    <script>
        // Set the date we're counting down to
        var countDownDate = new Date("{{ $semester->wkt_kelulusan }}").getTime();
        $("#xpengumuman").hide();
        var isLulus = {{ $siswa->is_lulus == 1 ? 'true' : 'false' }};
        var animStarted = false;

        function startGraduationAnimation() {
            if (animStarted || !isLulus) {
                return;
            }
            animStarted = true;

            var statusInitial = document.getElementById("status-initial");
            var statusFinal = document.getElementById("status-final");
            var confetti = document.getElementById("confetti-screen");

            if (!statusInitial || !statusFinal || !confetti) {
                return;
            }

            var showInitialMs = 3000;
            var strikeMs = 8000;
            var showFinalMs = 700;

            setTimeout(function () {
                statusInitial.classList.add("strike");
            }, showInitialMs);

            setTimeout(function () {
                statusFinal.classList.add("show");
            }, showInitialMs + strikeMs);

            setTimeout(function () {
                confetti.classList.add("show");
                confetti.classList.add("run");
            }, showInitialMs + strikeMs + showFinalMs);

            setTimeout(function () {
                confetti.classList.remove("run");
                confetti.classList.remove("show");
            }, showInitialMs + strikeMs + showFinalMs + 30000);
        }

        // Update the count down every 1 second
        var x = setInterval(function () {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("day").innerHTML = days;
            document.getElementById("hour").innerHTML = hours;
            document.getElementById("minute").innerHTML = minutes;
            document.getElementById("second").innerHTML = seconds;

            // Display the result in the element with id="demo"
            //   document.getElementById("demo").innerHTML = "Pengumuman dapat dilihat " + days + " Hari " + hours + " Jam "
            //   + minutes + " Menit " + seconds + " Detik lagi.";

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval(x);
                // document.getElementById("demo").innerHTML = "EXPIRED";
                $("#xpengumuman").show();
                $("#pengumuman").hide();
                $("#timer").hide();
                startGraduationAnimation();
            }
        }, 1000);
    </script>
@endsection

@section('inline-js')
@endsection