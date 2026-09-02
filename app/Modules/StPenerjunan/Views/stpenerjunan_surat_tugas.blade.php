<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Tugas - {{ $guru->nama }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 10px;
            font-size: 13px;
            color: #000;
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

        .title {
            font-weight: bold;
            font-size: 15px;
            text-decoration: underline;
            margin-top: 10px;
        }

        .identitas-table {
            margin-top: 4px;
        }

        .identitas-table td {
            padding: 1px 4px;
            vertical-align: top;
        }

        .identitas-table td p {
            margin: 0;
        }

        .tempat-table {
            width: 100%;
        }

        .tempat-table td {
            padding: 0;
        }

        .tempat-no-col {
            width: 16px;
        }

        .label-col {
            width: 110px;
        }

        .sep-col {
            width: 12px;
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

    <div class="center title">SURAT PERINTAH TUGAS</div>
    <div class="center">Nomor : {{ $stpenerjunan->no_surat }}</div>

    <p>Yang bertanda tangan di bawah ini :</p>
    <table class="identitas-table">
        <tr><td class="label-col">Nama</td><td class="sep-col">:</td><td>Nana Mulyana, S.P., M.Si.</td></tr>
        <tr><td>NIP</td><td>:</td><td>196906011992031012</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>Kepala Sekolah</td></tr>
        <tr><td>Unit Kerja</td><td>:</td><td>SMK Negeri 2 Semarang</td></tr>
    </table>

    <p>dengan ini menugaskan kepada :</p>
    <table class="identitas-table">
        <tr><td class="label-col">Nama</td><td class="sep-col">:</td><td>{{ $guru->nama }}</td></tr>
        <tr><td>NIP</td><td>:</td><td>{{ $guru->nip }}</td></tr>
        <tr><td>Keperluan</td><td>:</td><td>Mengantar siswa PKL periode {{ $stpenerjunan->periode->nama_periode }}</td></tr>
        <tr><td>Tanggal</td><td>:</td><td>{{ $tglPenerjunan }}</td></tr>
        <tr><td>Waktu</td><td>:</td><td>07.00 - selesai</td></tr>
        <tr>
            <td valign="top">Tempat</td>
            <td valign="top">:</td>
            <td>
                @forelse ($tempats as $i => $tempat)
                    <table class="tempat-table">
                        <tr>
                            <td class="tempat-no-col" valign="top">{{ $i + 1 }}.</td>
                            <td valign="top">
                                {{ $tempat->nama_dudi }}<br>
                                {!! $tempat->alamat !!}
                            </td>
                        </tr>
                    </table>
                @empty
                    -
                @endforelse
            </td>
        </tr>
    </table>

    <p>Demikian surat tugas ini dibuat untuk dipergunakan sebagaimana mestinya dan dengan penuh tanggung jawab.</p>

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
