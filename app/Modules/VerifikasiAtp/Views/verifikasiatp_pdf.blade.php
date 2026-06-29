<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 13px;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .title {
            font-weight: bold;
            font-size: 15px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            text-align: center;
            background-color: #d9e2f3;
        }

        td.nilai,
        td.predikat {
            text-align: center;
            width: 12%;
        }

        td.no {
            text-align: center;
            width: 5%;
        }

        .ttd-table {
            width: 100%;
            border: none;
            margin-top: 30px;
        }

        .ttd-table td {
            border: none;
            vertical-align: top;
            width: 50%;
            text-align: center;
        }

        .ttd-space {
            height: 70px;
        }
    </style>
</head>
<body>
    <div class="center title">DAFTAR NILAI {{ strtoupper($title) }}</div>
    <div class="center">Semester {{ $semester }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Guru</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Tingkat Kelayakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_guru }}</td>
                    <td>{{ $item->nama_mapel }}</td>
                    <td class="nilai">{{ $item->nilai }}</td>
                    <td class="predikat">{{ $item->predikat }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center"><i>Tidak ada data.</i></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah
                <div class="ttd-space"></div>
                <strong>Nana Mulyana, S.P., M.Si.</strong><br>
                {{-- Pembina Tk.I, IV/b<br> --}}
                NIP. 19690601 199203 1 012
            </td>
            <td>
                Semarang, {{ $tanggalCetak }}<br>
                Pengawas Sekolah
                <div class="ttd-space"></div>
                <strong>Dra. Hj. Sri Maryati, M.T.</strong><br>
                NIP. 19670319 199003 2 008
            </td>
        </tr>
    </table>
</body>
</html>
