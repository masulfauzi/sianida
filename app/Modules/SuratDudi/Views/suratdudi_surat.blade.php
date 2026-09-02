<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Penerjunan - {{ $dudi->nama_dudi }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 10px;
            font-size: 13px;
            color: #000;
            text-align: justify;
        }

        .center {
            text-align: center;
        }

        .kop-table {
            width: 100%;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-nama-instansi {
            font-weight: bold;
            font-size: 15px;
            line-height: 1.2;
        }

        .kop-alamat {
            font-size: 11px;
            line-height: 1.2;
        }

        .kop-line-tebal {
            border-top: 3px solid #000;
            margin: 4px 0 1px 0;
        }

        .kop-line-tipis {
            border-top: 1px solid #000;
            margin: 0 0 8px 0;
        }

        .surat-info-table td {
            padding: 1px 4px;
            vertical-align: top;
        }

        .surat-info-table td p {
            margin: 0;
        }

        .label-col {
            width: 60px;
        }

        .sep-col {
            width: 12px;
        }

        .tujuan {
            margin-top: 10px;
        }

        .siswa-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .siswa-table th,
        .siswa-table td {
            border: 1px solid #000;
            padding: 3px 6px;
        }

        .siswa-table th {
            text-align: center;
        }

        .siswa-table td:first-child {
            text-align: center;
            width: 5%;
        }

        .ttd {
            width: 260px;
            float: right;
            text-align: left;
            margin-top: 10px;
        }

        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td width="15%" class="center">
                <img src="{{ public_path('assets/images/logo/logo_jateng.png') }}" width="75">
            </td>
            <td width="70%" class="center">
                <div class="kop-nama-instansi">PEMERINTAH PROVINSI JAWA TENGAH</div>
                <div class="kop-nama-instansi">DINAS PENDIDIKAN</div>
                <div class="kop-nama-instansi" style="font-size: 18px;">SEKOLAH MENENGAH KEJURUAN NEGERI 2 SEMARANG</div>
                <div class="kop-alamat">Jl. Dr. Cipto Nomor 121 A, Semarang Timur, Kota Semarang, Jawa Tengah, Kode Pos 50124</div>
                <div class="kop-alamat">Telepon 024-8455757, Faksimile 024-8455757, Laman https://smkn2semarang.sch.id</div>
                <div class="kop-alamat">Pos-el smkn2kotasemarang@gmail.com, smeansa_smg@yahoo.co.id</div>
            </td>
            <td width="15%" class="center">
                <img src="{{ public_path('assets/images/logo/skanida.png') }}" width="75">
            </td>
        </tr>
    </table>
    <div class="kop-line-tebal"></div>
    <div class="kop-line-tipis"></div>

    <table class="surat-info-table">
        <tr><td class="label-col">Nomor</td><td class="sep-col">:</td><td>{{ $suratdudi->no_surat }}</td></tr>
        <tr><td>Lamp.</td><td>:</td><td>-</td></tr>
        <tr><td>Hal</td><td>:</td><td>Penerjunan Siswa PKL</td></tr>
    </table>

    <table class="surat-info-table" style="margin-top: 10px;">
        <tr>
            <td class="label-col" valign="top">Yth.</td>
            {{-- <td class="sep-col" valign="top"></td> --}}
            <td>
                Pimpinan {{ $dudi->nama_dudi }}<br>
                {!! $dudi->alamat !!}
            </td>
        </tr>
    </table>

    <p class="tujuan">
        Sehubungan dengan adanya surat penarikan bagi siswa SMK Negeri 2 Semarang yang sudah kami sampaikan,
        maka pada hari ini kami kirimkan siswa PKL dengan periode {{ $tglMulai }} s.d. {{ $tglSelesai }}.
    </p>

    <p>Adapun daftar nama siswa sebagai berikut :</p>

    <table class="siswa-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Kelas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $i => $pd)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pd->siswa->nama_siswa ?? '-' }}</td>
                    <td>{{ $pd->kelas->kelas ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="center">-</td></tr>
            @endforelse
        </tbody>
    </table>

    <p>Demikian surat penerjunan kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.</p>

    <div class="ttd">
        Semarang, {{ $tglSurat }}<br>
        Kepala Sekolah<br>
        <br><br><br><br>
        <strong>Nana Mulyana, S.P., M.Si.</strong><br>
        Pembina Tingkat I<br>
        NIP 196906011992031012
    </div>
    <div class="clear"></div>
</body>
</html>
