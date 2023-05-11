<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jurnal Mengajar Guru</title>
    <style>
        .border{
            border: 1px solid black;
            border-collapse: collapse;
            }
        .center{
            text-align: center;
        }
        .bold{
            font-weight: bold;
        }
    </style>
    <script>
        window.print();
    </script>
</head>
<body>
    <table class="border" width="100%">
        <tr class="border">
            <td rowspan="2" class="border" align="center"><img src="{{ env('APP_URL') }}/sianida/public/assets/images/logo/skanida.png" alt="" width="80px"></td>
            <td rowspan="2" class="border" align="center"><font style="font-weight: bold;">SMK NEGERI 2 SEMARANG</font></td>
            <td height="80%" class="border" align="right"><font style="font-weight: bold;">F-KUR.12</font></td>
        </tr>
        <tr>
            <td align="center"><font style="font-weight: bold;">JURNAL MENGAJAR</font></td>
        </tr>
    </table>
    <table>
        <tr>
            <td>Mata Pelajaran</td>
            <td>:</td>
            <td>{{ $detail_jadwal->mapel }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $detail_jadwal->kelas }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $detail_jadwal->semester }}</td>
        </tr>
        <tr>
            <td>Guru Mata Pelajaran</td>
            <td>:</td>
            <td>{{ $detail_jadwal->nama }}</td>
        </tr>
    </table>

    <table width="100%" class="border">
        <tr class="border">
            <td class="border center bold">Hari/Tanggal</td>
            <td class="border center bold">Jam ke</td>
            <td class="border center bold">Uraian Materi & Kegiatan</td>
            <td class="border center bold">Keterangan Kehadiran</td>
        </tr>
        @foreach ($jurnal as $item)
            <tr class="border">
                <td class="border">{{ \App\Helpers\Format::tanggal($item->tgl_pembelajaran) }}</td>
                <td class="border center">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                <td class="border">{!! $item->materi !!}</td>
                <td class="border">{!! $item->catatan !!}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>