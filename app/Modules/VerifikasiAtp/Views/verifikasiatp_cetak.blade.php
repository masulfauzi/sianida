<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi ATP - {{ $verifikasiatp->guru->nama ?? '-' }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 6px;
            font-size: 15px;
            line-height: 1.05;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .title {
            font-weight: bold;
            font-size: 16px;
            margin: 0;
        }

        .identitas-table {
            margin-top: 6px;
            font-size: 14px;
        }

        .identitas-table td {
            padding: 0 4px;
            vertical-align: top;
        }

        .skor-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 13px;
        }

        .skor-table th,
        .skor-table td {
            border: 1px solid #000;
            padding: 2px;
            line-height: 1.05;
        }

        .skor-table th {
            text-align: center;
        }

        .skor-table .group-header td {
            background-color: #d9e2f3;
            font-weight: bold;
        }

        .skor-table td:nth-child(1),
        .skor-table th:nth-child(1) {
            text-align: center;
            width: 3%;
        }

        .skor-table td.skor-col,
        .skor-table th.skor-col {
            text-align: center;
            width: 9%;
        }

        .skor-table td.catatan-col {
            width: 15%;
        }

        .keterangan {
            margin-top: 4px;
            font-size: 13px;
            border: 1px solid #000;
            padding: 4px;
        }

        .review-box {
            margin-top: 4px;
            border: 1px solid #000;
            min-height: 30px;
            padding: 4px;
        }

        .ttd {
            width: 280px;
            float: right;
            text-align: left;
            margin-top: 4px;
            font-size: 14px;
        }

        .clear {
            clear: both;
        }

        .footer-row {
            width: 100%;
            margin-top: 4px;
        }

        .footer-row td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="center title">INSTRUMEN VERIFIKASI/VALIDASI</div>
    <div class="center title">ALUR TUJUAN PEMBELAJARAN (ATP)</div>
    <div class="center title">TAHUN AJARAN {{ $tahunPelajaran }}</div>

    <table class="identitas-table">
        <tr><td>Nama Sekolah</td><td>:</td><td><strong>SMK Negeri 2 Semarang</strong></td></tr>
        <tr><td>Nama Guru</td><td>:</td><td><strong>{{ $verifikasiatp->guru->nama ?? '-' }}</strong></td></tr>
        <tr><td>Mata Pelajaran</td><td>:</td><td><strong>{{ $verifikasiatp->mapel->mapel ?? '-' }}</strong></td></tr>
        <tr><td>Kelas/Program Keahlian</td><td>:</td><td><strong>{{ $verifikasiatp->tingkat->tingkat ?? '-' }} / {{ $verifikasiatp->jurusan->jurusan ?? '-' }}</strong></td></tr>
    </table>

    <table class="skor-table">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Komponen/Indikator</th>
                <th colspan="5">Hasil Telaah/ Skor</th>
                <th rowspan="2">Catatan</th>
            </tr>
            <tr>
                <th class="skor-col">Tidak ada<br>0</th>
                <th class="skor-col">Kurang<br>1</th>
                <th class="skor-col">Cukup<br>2</th>
                <th class="skor-col">Baik<br>3</th>
                <th class="skor-col">Amat Baik<br>4</th>
            </tr>
        </thead>
        <tbody>
            @php
                $mark = fn($value, $target) => $value == $target ? 'v' : '';
                $renderRow = function ($row) use ($mark) {
                    echo '<tr>';
                    echo '<td>'.$row['no'].'</td>';
                    echo '<td>'.($row['desc'] ?? '').'</td>';
                    foreach ([0, 1, 2, 3, 4] as $target) {
                        echo '<td class="skor-col">'.$mark($row['value'], $target).'</td>';
                    }
                    echo '<td class="catatan-col"></td>';
                    echo '</tr>';
                };
            @endphp

            <tr class="group-header"><td>I</td><td colspan="7">Identitas</td></tr>
            @php $renderRow(['no' => 1, 'desc' => 'ATP/Silabus memuat nama sekolah, nama mata pelajaran, kelas/fase.', 'value' => $components['identitas']]); @endphp

            <tr class="group-header"><td>II</td><td colspan="7">Capaian Pembelajaran</td></tr>
            @php $renderRow(['no' => 2, 'desc' => 'Memuat Capaian Pembelajaran secara lengkap sesuai dengan Keputusan Kepala BSKAP Nomor 0446/H/KR/2025.', 'value' => $components['cp']]); @endphp

            <tr class="group-header"><td>III</td><td colspan="7">Tujuan Pembelajaran</td></tr>
            @php $renderRow(['no' => 3, 'desc' => 'Tujuan pembelajaran memuat koempetensi sikap, pengetahuan, dan keterampilan dan memuat konten sesuai dengan capaian pembelajaran.', 'value' => $components['tp']]); @endphp

            <tr class="group-header"><td>IV</td><td colspan="7">Alokasi Waktu</td></tr>
            @php $renderRow(['no' => 4, 'desc' => 'Memuat alokasi waktu dengan jumlah sama dengan jumlah jam intrakurikuler pertahun.', 'value' => $components['alokasi_waktu']]); @endphp

            <tr class="group-header"><td>V</td><td colspan="7">Materi Pembelajaran</td></tr>
            @php $renderRow(['no' => 5, 'desc' => 'Memuat materi pembelajaran yang esensial sesuai dengan capaian pembelajaran.', 'value' => $components['materi']]); @endphp

            <tr class="group-header"><td>VI</td><td colspan="7">Lain-lain</td></tr>
            @php $renderRow(['no' => 6, 'desc' => 'Memuat metode/model pembelajaran penilaian/asesmen yang dapat menilai sikap, pengetahuan, dan keterampilan.', 'value' => $components['metode']]); @endphp

            <tr>
                <td colspan="2"><strong>JUMLAH SKOR</strong></td>
                @foreach ([0, 1, 2, 3, 4] as $target)
                    <td class="skor-col">{{ $counts[$target] * $target }}</td>
                @endforeach
                <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong>NILAI</strong></td>
                <td colspan="3" class="center"><strong>{{ $nilai }}</strong></td>
                <td colspan="3"><strong>PREDIKAT : {{ strtoupper($predikat) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div>
        <strong>Rekomendasi</strong>
        <div class="review-box">{!! nl2br(e($verifikasiatp->catatan)) !!}</div>
    </div>

    <table class="footer-row">
        <tr>
            <td style="width: 40%;">
                <div class="keterangan">
                    Keterangan:<br>
                    1. Nilai = (Skor perolehan : skor maksimal) x 100<br>
                    2. Predikat:<br>
                    &nbsp;&nbsp;Nilai 91 - 100 = Amat baik<br>
                    &nbsp;&nbsp;Nilai 81 - 90 = Baik<br>
                    &nbsp;&nbsp;Nilai 71 - 80 = Cukup<br>
                    &nbsp;&nbsp;Nilai <= 70 = Kurang
                </div>
            </td>
            <td style="width: 30%;">

            </td>
            <td style="width: 30%;">
                <div class="ttd" style="float: none; margin-top: 16px;">
                    Semarang, {{ $tanggalCetak }}<br>
                    Kepala Sekolah,<br>
                    <img src="{{ public_path('assets/images/ttd/ttd_stempel.png') }}" alt="Tanda Tangan dan Stempel" style="height: 110px; display: block; margin-left: -50px; margin-top: -12px; position: relative; top: 12px;"><br>
                    Nana Mulyana, S.P., M.Si.<br>
                    Pembina Tk.I, IV/b<br>
                    NIP. 19690601 199203 1 012
                </div>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
</body>
</html>
