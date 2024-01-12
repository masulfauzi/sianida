<html>
    <head>
        <title>Download Biodata Peserta Ujian</title>
    </head>
    <body>
        <center>DATA SISWA PESERTA UJIAN & PENULISAN IJAZAH SMK NEGERI 2 SEMARANG</center>
        <center>TAHUN PELAJARAN 2022/2023</center>

        <hr>

        
        <table>
            <tr>
                <td>1.</td>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td>{{ $data->nama_siswa }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>NIS / NISN</td>
                <td>:</td>
                <td>{{ $data->nis }} / {{ $data->nisn }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Tempat & Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $data->tempat_lahir }}, {{ \App\Helpers\Format::tanggal($data->tgl_lahir) }}</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>Nama Orang Tua</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>a. Ayah</td>
                <td>:</td>
                <td>{{ $data->nama_ayah }}</td>
            </tr>
            <tr>
                <td></td>
                <td>b. Ibu</td>
                <td>:</td>
                <td>{{ $data->nama_ibu }}</td>
            </tr>
            <tr>
                <td>5.</td>
                <td>Alamat Rumah</td>
                <td>:</td>
                <td>{{ $data->alamat }}</td>
            </tr>
            
            <tr>
                <td>6.</td>
                <td>Sekolah Asal (SMP/MTs)</td>
                <td>:</td>
                <td>{{ $data->sekolah_asal }}</td>
            </tr>
            <tr>
                <td>7.</td>
                <td>Nomor Seri Ijazah</td>
                <td>:</td>
                <td>{{ $data->no_ijazah_smp }}</td>
            </tr>
            <tr>
                <td>8.</td>
                <td>Nomor Seri SKHUN</td>
                <td>:</td>
                <td>{{ $data->no_skhun }}</td>
            </tr>
            <tr>
                <td>9.</td>
                <td>Berkas yang dilampirkan</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>a. Fotocopy IJAZAH SMP/MTs 1 Lembar</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>b. Fotocopy SKHUN 1 Lembar (bagi yang memiliki)</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>c. Fotocopy KK 1 Lembar</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>d. Fotocopy Akta Kelahiran 1 Lembar</td>
                <td></td>
                <td></td>
            </tr>
        </table>


        <table>
            <tr style="vertical-align: top;">
                <td>N/b</td>
                <td>Apabila ada perubahan nama baik nama siswa maupun nama orang tua pada ijazah SMP/MTs, harap segera diperbaiki demi kemancaran penulisan IJAZAH SMK.</td>
            </tr>
        </table>

        <table style="width:100%">
            <tr>
                <td style="width: 60%"></td>
                <td style="width: 40%; text-align: center;">Yang membuat data diri</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="width: 60%"></td>
                <td style="width: 40%; text-align: center;">{{ $data->nama_siswa }}</td>
            </tr>
        </table>


        

        
    </body>
</html>