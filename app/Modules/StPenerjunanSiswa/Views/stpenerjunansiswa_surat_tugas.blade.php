<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Tugas Siswa - {{ $dudi->nama_dudi }}</title>
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
            margin-top: 10px;
        }

        .memerintahkan {
            font-weight: bold;
            margin: 10px 0;
        }

        .identitas-table {
            width: 100%;
            margin-top: 4px;
        }

        .identitas-table td {
            padding: 1px 4px;
            vertical-align: top;
        }

        .label-col {
            width: 70px;
        }

        .sep-col {
            width: 12px;
        }

        .rincian-table {
            width: 100%;
        }

        .rincian-table td {
            padding: 0 4px;
            vertical-align: top;
        }

        .rincian-table td p {
            margin: 0;
        }

        .no-col {
            width: 20px;
        }

        .sublabel-col {
            width: 60px;
        }

        .subsep-col {
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
    <div class="center">NOMOR : {{ $stpenerjunansiswa->no_surat }}</div>

    <table class="identitas-table">
        <tr>
            <td class="label-col">Dasar</td>
            <td class="sep-col">:</td>
            <td>Permendikbud No. 50 Tahun 2020 tentang Praktik Kerja Lapangan (PKL)</td>
        </tr>
    </table>

    <div class="center memerintahkan">MEMERINTAHKAN:</div>

    <table class="identitas-table">
        <tr>
            <td class="label-col" valign="top">Kepada</td>
            <td class="sep-col" valign="top">:</td>
            <td>
                <table class="rincian-table">
                    @forelse ($siswas as $i => $pd)
                        <tr>
                            <td class="no-col">{{ $i + 1 }}.</td>
                            <td class="sublabel-col">Nama</td>
                            <td class="subsep-col">:</td>
                            <td>{{ $pd->siswa->nama_siswa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Kelas</td>
                            <td>:</td>
                            <td>{{ $pd->kelas->kelas ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">-</td></tr>
                    @endforelse
                </table>
            </td>
        </tr>
    </table>

    <table class="identitas-table" style="margin-top: 6px;">
        <tr>
            <td class="label-col" valign="top">Untuk</td>
            <td class="sep-col" valign="top">:</td>
            <td>
                Melaksanakan PKL pada:
                <table class="rincian-table" style="margin-top: 2px;">
                    <tr>
                        <td class="sublabel-col">Tanggal</td>
                        <td class="subsep-col">:</td>
                        <td>{{ $tglMulai }} s.d. {{ $tglSelesai }}</td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>:</td>
                        <td>Menyesuaikan Perusahaan</td>
                    </tr>
                    <tr>
                        <td valign="top">Tempat</td>
                        <td valign="top">:</td>
                        <td>
                            {{ $dudi->nama_dudi }}<br>
                            {!! $dudi->alamat !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

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
