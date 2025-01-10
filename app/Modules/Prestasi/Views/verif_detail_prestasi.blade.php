<div class="row">
    <div class="col-6">
        <table>
            <tr>
                <th>Nama Prestasi</th>
                <td>{{ $prestasi->prestasi }}</td>
            </tr>
            <tr>
                <th>Juara</th>
                <td>{{ $prestasi->juara->juara }}</td>
            </tr>
            <tr>
                <th>Tanggal Perolehan</th>
                <td>{{ \App\Helpers\Format::tanggal($prestasi->tgl_perolehan) }}</td>
            </tr>
            <tr>
                <th>Sertifikat</th>
                <td>
                    <img src="{{ url('uploads/sertifikat_prestasi/' . $prestasi->sertifikat) }}" width="400px;"
                        alt="Bukti Prestasi">
                </td>
            </tr>
        </table>
    </div>
    <div class="col-6">
        <table>
            <tr>
                <th>Nama Prestasi</th>
                <td>{{ $prestasi->prestasi }}</td>
            </tr>
            <tr>
                <th>Juara</th>
                <td>{{ $prestasi->juara->juara }}</td>
            </tr>
            <tr>
                <th>Tanggal Perolehan</th>
                <td>{{ \App\Helpers\Format::tanggal($prestasi->tgl_perolehan) }}</td>
            </tr>
            <tr>
                <th>Sertifikat</th>
                <td>
                    <img src="{{ url('uploads/sertifikat_prestasi/' . $prestasi->sertifikat) }}" width="400px;"
                        alt="Bukti Prestasi">
                </td>
            </tr>
        </table>
    </div>
</div>
