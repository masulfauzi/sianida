@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Manajemen Data {{ $title }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6>Filter Tanggal</h6></div>
                    <div class="card-body">
                        <form action="{{ route('presensiharian.index') }}" method="get" class="d-flex gap-2 align-items-end">
                            <div class="form-group">
                                <label for="tgl" class="form-label">Pilih Tanggal</label>
                                <input type="date" id="tgl" name="tgl" class="form-control" value="{{ $tgl }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('presensiharian.index') }}" class="btn btn-secondary">Reset</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach (['X', 'XI', 'XII'] as $tingkat)
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6>Kelas {{ $tingkat }} &mdash; {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') }}</h6></div>
                        <div class="card-body">
                            <div id="chart-{{ strtolower($tingkat) }}"></div>
                            <hr>
                            @include('PresensiHarian::presensiharian_belum_presensi', [
                                'tingkat'   => $tingkat,
                                'list'      => $belum_presensi[$tingkat],
                                'kelasOff'  => $kelas_off[$tingkat] ?? [],
                                'liburInfo' => $libur_info,
                            ])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="modal fade" id="modalDetailSiswa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-4" data-detail-state="loading">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2 text-muted">Memuat data...</div>
                    </div>
                    <div class="alert alert-light-danger mb-0" data-detail-state="error" style="display:none">Gagal memuat data siswa.</div>
                    <div class="row" data-detail-state="content" style="display:none">
                        <div class="col-md-3 mb-3 text-center" data-detail-foto-wrapper>
                            <img src="" alt="Foto siswa" class="img-fluid rounded" data-detail-foto>
                        </div>
                        <div class="col" data-detail-biodata>
                            <table class="table table-sm mb-0">
                                @foreach (['nama_siswa' => 'Nama', 'nis' => 'NIS', 'nisn' => 'NISN', 'nik' => 'NIK', 'kelas' => 'Kelas', 'jenis_kelamin' => 'Jenis Kelamin', 'agama' => 'Agama', 'ttl' => 'Tempat, Tanggal Lahir', 'no_hp' => 'No HP', 'alamat' => 'Alamat', 'nama_ayah' => 'Nama Ayah', 'nama_ibu' => 'Nama Ibu', 'presensi_terakhir' => 'Presensi Terakhir'] as $field => $label)
                                    <tr>
                                        <td width="180">{{ $label }}</td>
                                        <td width="10">:</td>
                                        <td data-detail-field="{{ $field }}"></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" data-detail-profil style="display:none">Lihat profil lengkap</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
<script>
    var statusColors = {
        'Hadir':        '#28a745',
        'Terlambat':    '#fd7e14',
        'Tidak Hadir':  '#dc3545',
    };

    function getSeriesColors(series) {
        return series.map(function (s) {
            return statusColors[s.name] || '#6c757d';
        });
    }

    function renderBarChart(selector, chartData) {
        var options = {
            chart: { type: 'bar', height: 350, stacked: false },
            series: chartData.series,
            colors: getSeriesColors(chartData.series),
            xaxis: { categories: chartData.categories },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', distributed: false } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
            noData: { text: 'Tidak ada data kehadiran' }
        };
        var chart = new ApexCharts(document.querySelector(selector), options);
        chart.render();
    }

    function filterBelumPresensi(wrapper) {
        var tabel = wrapper.querySelector('.tabel-belum-presensi');
        if (!tabel) return;
        var kelas = wrapper.querySelector('.filter-belum-kelas').value;
        var nama = wrapper.querySelector('.filter-belum-nama').value.trim().toLowerCase();
        var rows = tabel.querySelectorAll('tbody tr');
        var no = 0;
        rows.forEach(function (row) {
            var cocok = (!kelas || row.dataset.kelas === kelas) && (!nama || row.dataset.nama.indexOf(nama) !== -1);
            row.style.display = cocok ? '' : 'none';
            if (cocok) row.querySelector('.no-urut').textContent = ++no;
        });
        wrapper.querySelector('.filter-belum-info').textContent = 'Menampilkan ' + no + ' dari ' + rows.length + ' siswa';
    }

    function tampilkanDetailSiswa(url) {
        var modalEl = document.getElementById('modalDetailSiswa');
        var setState = function (state) {
            modalEl.querySelectorAll('[data-detail-state]').forEach(function (el) {
                el.style.display = el.dataset.detailState === state ? '' : 'none';
            });
        };
        var profil = modalEl.querySelector('[data-detail-profil]');

        setState('loading');
        profil.style.display = 'none';
        modalEl.querySelectorAll('[data-detail-field]').forEach(function (el) { el.textContent = ''; });
        bootstrap.Modal.getOrCreateInstance(modalEl).show();

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error(res.status);
                return res.json();
            })
            .then(function (d) {
                d.ttl = [d.tempat_lahir, d.tgl_lahir].filter(Boolean).join(', ');
                modalEl.querySelectorAll('[data-detail-field]').forEach(function (el) {
                    el.textContent = d[el.dataset.detailField] || '-';
                });

                var foto = modalEl.querySelector('[data-detail-foto]');
                var fotoWrapper = modalEl.querySelector('[data-detail-foto-wrapper]');
                if (d.foto_url) {
                    foto.src = d.foto_url;
                    foto.alt = 'Foto ' + d.nama_siswa;
                    fotoWrapper.style.display = '';
                } else {
                    foto.removeAttribute('src');
                    fotoWrapper.style.display = 'none';
                }

                profil.href = d.url_profil;
                profil.style.display = '';
                setState('content');
            })
            .catch(function () { setState('error'); });
    }

    document.addEventListener('click', function (e) {
        var link = e.target.closest('.link-detail-siswa');
        if (!link) return;
        e.preventDefault();
        tampilkanDetailSiswa(link.dataset.url);
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.belum-presensi-wrapper').forEach(function (wrapper) {
            if (!wrapper.querySelector('.tabel-belum-presensi')) return;
            var jalankan = function () { filterBelumPresensi(wrapper); };
            wrapper.querySelector('.filter-belum-kelas').addEventListener('change', jalankan);
            wrapper.querySelector('.filter-belum-nama').addEventListener('input', jalankan);
            jalankan();
        });

        renderBarChart('#chart-x',   @json($chart_x));
        renderBarChart('#chart-xi',  @json($chart_xi));
        renderBarChart('#chart-xii', @json($chart_xii));
    });
</script>
@endsection
