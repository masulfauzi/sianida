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

        td.nilai {
            text-align: center;
            width: 15%;
        }

        td.no {
            text-align: center;
            width: 5%;
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
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_guru }}</td>
                    <td class="nilai">{{ $item->nilai }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center"><i>Tidak ada data.</i></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
