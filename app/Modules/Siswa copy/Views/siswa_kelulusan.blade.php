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
    </style>
    <style>
        .blink {
            animation: blinker 1.5s linear infinite;
            color: red;
            font-family: sans-serif;
        }
        @keyframes blinker {
            50% {
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
                        <li class="breadcrumb-item"><a href="{{ route('siswa.kelulusan.index') }}">Kelulusan Siswa</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body" style="margin: auto;">
                <div id="pengumuman">
                    <p style="text-align: center;">Waktu Pengumuman masih kurang :</p>
                </div>
                <div class="timer" id="timer" style="margin: auto;">
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
                                <tr><td width="30%">Nama Siswa</td><td>{{ $siswa->nama_siswa }}</td></tr>
                                <tr><td>NIS</td><td>{{ $siswa->nis }}</td></tr>
                                <tr><td>NISN</td><td>{{ $siswa->nisn }}</td></tr>
                                <tr><td>Tempat & Tanggal Lahir</td><td>{{ $siswa->tempat_lahir }}, {{ \App\Helpers\Format::tanggal($siswa->tgl_lahir) }}</td></tr>
                                <tr><td>Kompetensi Keahlian</td><td>{{ $siswa->jurusan }}</td></tr>
                            </table>

                            @if ($siswa->is_lulus == 1)
                                <div style="text-align: center;" class="alert alert-success" role="alert"><strong>SELAMAT !</strong> Anda dinyatakan LULUS.</div>
                            @else
                                <div style="text-align: center;" class="alert alert-danger" role="alert"><strong>MAAF !</strong> Anda dinyatakan TIDAK LULUS.</div>
                            @endif

                            
                           
									
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="margin: auto;">
                <div>
                    
                    <h1 style="text-align: center;" class="blink">HIMBAUAN</h1>
                    <h2 style="text-align: center;">Kepada Siswa siswi Kelas XII SMK Negeri 2 Semarang.</h2>
                    <p>Berdasarkan Surat Edaran Nomor 420/06343 Kepala Dinas Pendidikan dan Kebudayaan Provinsi Jawa Tengah tentang Pelaksanaan Pengumuman Kelulusan Peserta Didik Tahun Pelajaran 2022/2023 disampaikan hal-hal sebagai berikut: </p>
                    <ol>
                        <li>Pengumuman kelulusan secara daring/OL via web sekolah dapat diakses tgl 5 Mei 2023 mulai jam 16.00 WIB</li>
                        <li>Dilarang hadir di sekolah ataupun berkumpul berkerumun di lingkungan sekitar sekolah</li>
                        <li>Dilarang mengadakan konvoi perayaan kelulusan ataupun kegiatan corat coret baju / seragam atau berkumpul di suatu tempat tertentu</li>
                        <li>Bilamana melakukan kegiatan dimaksud di atas maka dianggap melanggar tata tertib sekolah dan jika terkena razia pihak berwajib maka sekolah tidak bertanggung jawab</li>
                        <li>Hadapi kegiatan kelulusan ini secara arif dan bijak, jangan bergembira secara berlebihan, perbanyak rasa syukur dengan kegiatan² yg positif</li>
                    </ol>
                    <p>Terima kasih anak² sukses buat kalian semuanya 🙏✌️💪</p>
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
    $( "#xpengumuman" ).hide();
    
    // Update the count down every 1 second
    var x = setInterval(function() {
    
      // Get today's date and time
      var now = new Date().getTime();
    
      // Find the distance between now and the count down date
      var distance = countDownDate - now;
    
      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("day").innerHTML =days ; 
        document.getElementById("hour").innerHTML =hours; 
        document.getElementById("minute").innerHTML = minutes; 
        document.getElementById("second").innerHTML =seconds; 
    
      // Display the result in the element with id="demo"
    //   document.getElementById("demo").innerHTML = "Pengumuman dapat dilihat " + days + " Hari " + hours + " Jam "
    //   + minutes + " Menit " + seconds + " Detik lagi.";
    
      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        // document.getElementById("demo").innerHTML = "EXPIRED";
        $( "#xpengumuman" ).show();
        $( "#pengumuman" ).hide();
        $( "#timer" ).hide();
      }
    }, 1000);
    </script>
@endsection

@section('inline-js')
@endsection