@extends('layouts.app')

@section('page-css')
    <style>
        .transkrip-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            overflow-y: auto;
            animation: fadeIn 0.3s ease;
        }

        .transkrip-modal.show {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .transkrip-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .transkrip-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .transkrip-modal-close:hover,
        .transkrip-modal-close:focus {
            color: black;
        }

        .transkrip-template {
            font-family: "Times New Roman", serif;
            margin: 20px;
            font-size: 17px;
            line-height: 1.2;
            color: #000;
        }

        .transkrip-template .center {
            text-align: center;
        }

        .transkrip-template .header {
            position: relative;
            text-align: center;
            line-height: 1.2;
            padding-left: 90px;
            padding-right: 90px;
        }

        .transkrip-template .header-logo-left {
            position: absolute;
            top: 15px;
            left: 0;
            width: 95px;
            height: auto;
        }

        .transkrip-template .header-logo {
            position: absolute;
            top: 15px;
            right: 0;
            width: 90px;
            height: auto;
        }

        .transkrip-template .header-line {
            border-top: 3px solid #000;
            margin: 6px -90px 6px -90px;
            width: calc(100% + 180px);
        }

        .transkrip-template .header-line-secondary {
            border-top: 1px solid #000;
            margin: -5px -90px 8px -90px;
            width: calc(100% + 180px);
        }

        .transkrip-template .title {
            font-weight: bold;
            font-size: 19px;
            margin-top: 10px;
        }

        .transkrip-template .subtitle {
            font-size: 17px;
            margin-bottom: 0;
            line-height: 1.1;
        }

        .transkrip-template .header-table {
            margin: 4px auto 6px;
            border-collapse: collapse;
            font-size: 15px;
        }

        .transkrip-template .header-table td {
            padding: 0 4px;
            vertical-align: top;
        }

        .transkrip-template .header-table .label,
        .transkrip-template .header-table .value {
            text-align: left;
        }

        .transkrip-template .content {
            margin-top: 12px;
        }

        .transkrip-template .table-info {
            width: 100%;
            margin-top: 10px;
        }

        .transkrip-template .table-info td {
            padding: 2px;
        }

        .transkrip-template .table-info td:nth-child(2) {
            text-transform: capitalize;
        }

        .transkrip-template .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transkrip-template .nilai-table th,
        .transkrip-template .nilai-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .transkrip-template .nilai-table th {
            text-align: center;
        }

        .transkrip-template .nilai-table .group-header td {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left !important;
            padding: 4px 4px;
            border: 1px solid #000;
        }

        .transkrip-template .nilai-table th:nth-child(1),
        .transkrip-template .nilai-table td:nth-child(1),
        .transkrip-template .nilai-table th:nth-child(3),
        .transkrip-template .nilai-table td:nth-child(3) {
            text-align: center;
        }

        .transkrip-template .nilai-table tfoot td {
            text-align: center;
        }

        .transkrip-template .footer {
            margin-top: 40px;
            width: 100%;
        }

        .transkrip-template .ttd {
            width: 300px;
            float: right;
            text-align: left;
        }

        .transkrip-template .foto {
            width: 100px;
            height: 120px;
            border: 1px solid #000;
            text-align: center;
            line-height: 160px;
            float: left;
            margin-left: 400px;
            margin-top: 50px;
        }

        .transkrip-template .clear {
            clear: both;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: center;
        }

        .modal-actions button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-print {
            background-color: #28a745;
            color: white;
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .btn-close-modal {
            background-color: #6c757d;
            color: white;
        }

        .btn-close-modal:hover {
            background-color: #5a6268;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            flex: 1;
            max-width: 400px;
        }

        .search-form button {
            padding: 10px 25px;
        }

        .badge-no {
            background-color: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            * {
                margin: 0;
                padding: 0;
                visibility: hidden;
                box-sizing: border-box;
            }

            .transkrip-template,
            .transkrip-template * {
                visibility: visible;
            }

            .modal-actions {
                display: none !important;
                visibility: hidden !important;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .transkrip-template {
                position: relative;
                width: 100%;
                margin: 0;
                padding: 15px;
                page-break-after: avoid;
                background: white;
                visibility: visible;
                font-size: 13px;
                line-height: 1.1;
            }

            .transkrip-template .header {
                padding-left: 15px;
                padding-right: 15px;
            }

            .transkrip-template .header-logo-left {
                width: 70px;
            }

            .transkrip-template .header-logo {
                width: 70px;
            }

            .transkrip-template .header-line {
                margin: 3px -15px 3px -15px;
                width: calc(100% + 30px);
            }

            .transkrip-template .header-line-secondary {
                margin: -3px -15px 5px -15px;
                width: calc(100% + 30px);
            }

            .transkrip-template .subtitle {
                font-size: 16px;
                margin: 2px 0;
            }

            .transkrip-template .table-info {
                margin-top: 8px;
                font-size: 16px;
            }

            .transkrip-template .table-info td {
                padding: 0px 2px;
            }

            .transkrip-template .nilai-table {
                margin-top: 10px;
                font-size: 16px;
            }

            .transkrip-template .nilai-table th,
            .transkrip-template .nilai-table td {
                padding: 1px 2px;
                border: 0.5px solid #000;
            }

            .transkrip-template .footer {
                margin-top: 15px;
            }

            .transkrip-template .ttd {
                width: 250px;
                font-size: 16px;
                line-height: 1;
            }

            .transkrip-template .content {
                margin-top: 8px;
            }

            .transkrip-template .content p {
                margin: 2px 0;
                font-size: 16px;
            }
        }
    </style>
@endsection

@section('main')
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-2">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ $title }}</h3>
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
            <div class="card">
                <h6 class="card-header">
                    {{ $title }}
                </h6>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('nilai.transkrip.index') }}" class="search-form">
                        <input type="text" name="search" placeholder="Cari nama siswa..." value="{{ $search }}"
                            class="form-control">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('nilai.transkrip.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Reset
                            </a>
                        @endif
                    </form>

                    @include('include.flash')

                    <!-- Results Table -->
                    <div class="table-responsive-md col-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Kelas</th>
                                    <th width="20%">Jurusan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pesertaDidik as $pd)
                                    <tr>
                                        <td>
                                            <span class="badge-no">
                                                {{ method_exists($pesertaDidik, 'firstItem') ? $pesertaDidik->firstItem() + $loop->index : $loop->iteration }}
                                            </span>
                                        </td>
                                        <td>{{ $pd->kelas ?? '-' }}</td>
                                        <td>{{ $pd->jurusan->jurusan ?? '-' }}</td>
                                        <td>
                                            @if ($search)
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="showTranskripModal('{{ $pd->peserta_id }}')">
                                                    <i class="fa fa-file-text"></i> Lihat Transkrip
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    onclick="showSiswaModal('{{ $pd->id }}', '{{ $pd->kelas }}')">
                                                    <i class="fa fa-users"></i> Lihat Siswa
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fa fa-inbox fa-2x mb-3"></i>
                                            <p>Tidak ada data siswa yang ditemukan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if (method_exists($pesertaDidik, 'links'))
                        <div class="row mt-3">
                            <div class="col-12">
                                {{ $pesertaDidik->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <!-- Transkrip Modal -->
    <div id="transkripModal" class="transkrip-modal">
        <div class="transkrip-modal-content">
            <span class="transkrip-modal-close" onclick="closeTranskripModal()">&times;</span>
            <div id="transkripContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat Transkrip...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Siswa Modal -->
    <div id="siswaModal" class="transkrip-modal">
        <div class="transkrip-modal-content">
            <span class="transkrip-modal-close" onclick="closeSiswaModal()">&times;</span>
            <div id="siswaContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data siswa...</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-js')
    <script>
        function showTranskripModal(pesertaDidikId) {
            // Tutup modal siswa jika terbuka
            const siswaModal = document.getElementById('siswaModal');
            if (siswaModal && siswaModal.classList.contains('show')) {
                siswaModal.classList.remove('show');
            }

            const modal = document.getElementById('transkripModal');
            const content = document.getElementById('transkripContent');

            // Show loading
            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat Transkrip...</p>
                </div>
            `;

            modal.classList.add('show');

            // Fetch Transkrip data
            fetch(`/nilai/transkrip/${pesertaDidikId}/detail`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const transkripHtml = generateTranskripHtml(data.data);
                        content.innerHTML = transkripHtml;
                    } else {
                        content.innerHTML = `
                            <div class="alert alert-danger">
                                <strong>Error:</strong> ${data.error || 'Gagal memuat Transkrip'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> Gagal memuat Transkrip. Silakan coba lagi.
                        </div>
                    `;
                });
        }

        function generateTranskripHtml(data) {
            const today = new Date();
            const bulanIndonesia = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const tanggalCetak = `${today.getDate()} ${bulanIndonesia[today.getMonth()]} ${today.getFullYear()}`;

            const satuan_pendidikan = data.satuan_pendidikan || 'SMK Negeri 2 Semarang';
            const npsn = data.npsn || '20328970';
            const noTranskrip = data.no_transkrip || '-';
            const capitalizeWords = (str) => {
                if (!str || str === '-') return str;
                return str.toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
            };
            const namaLengkap = capitalizeWords(data.nama_lengkap || data.nama || '-');
            const tempatLahir = capitalizeWords(data.tempat_lahir || '-');
            const tanggalLahir = data.tgl_lahir || '-';
            const ttl = `${tempatLahir}, ${tanggalLahir}`;
            const nisn = data.nisn || '-';
            const nis = data.nis || '-';
            const no_ijazah = data.no_ijazah || '-';
            const kurikulum = data.kurikulum || 'Kurikulum Merdeka';
            const orangTua = capitalizeWords(data.orang_tua || '-');
            const programKeahlian = capitalizeWords(data.program_keahlian || '-');
            const konsentrasiKeahlian = capitalizeWords(data.konsentrasi_keahlian || '-');
            const kepalaSekolah = data.kepala_sekolah || data.nama_sekolah || '-';
            const nip = data.nip || '19690601 199203 1 012';
            const nilaiList = Array.isArray(data.nilai) ? data.nilai : [];
            const formatNilai = (value) => {
                const numberValue = Number(value);
                if (Number.isNaN(numberValue)) {
                    return '-';
                }
                return numberValue.toFixed(2).replace('.', ',');
            };
            const nilaiRows = nilaiList.length
                ? (() => {
                    const nilaiUmum = nilaiList.filter(item => (!item.is_kejuruan || item.is_kejuruan === 0 || item.is_kejuruan === '0') && (!item.is_mulok || item.is_mulok === 0 || item.is_mulok === '0') && (!item.is_pilihan || item.is_pilihan === 0 || item.is_pilihan === '0'));
                    const nilaiKejuruan = nilaiList.filter(item => item.is_kejuruan === 1 || item.is_kejuruan === '1');
                    const nilaiMulok = nilaiList.filter(item => item.is_mulok === 1 || item.is_mulok === '1');
                    const nilaiPilihan = nilaiList.filter(item => item.is_pilihan === 1 || item.is_pilihan === '1');
                    let html = '';
                    let noCounter = 1;

                    if (nilaiUmum.length > 0) {
                        html += `<tr class="group-header"><td colspan="3">Mata Pelajaran Umum</td></tr>`;
                        nilaiUmum.forEach((item) => {
                            const nilaiText = formatNilai(item.rata_rata);
                            html += `<tr><td>${noCounter}</td><td>${item.mapel || '-'}</td><td>${nilaiText}</td></tr>`;
                            noCounter++;
                        });
                    }

                    if (nilaiKejuruan.length > 0) {
                        html += `<tr class="group-header"><td colspan="3">Mata Pelajaran Kejuruan</td></tr>`;
                        nilaiKejuruan.forEach((item) => {
                            const nilaiText = formatNilai(item.rata_rata);
                            html += `<tr><td>${noCounter}</td><td>${item.mapel || '-'}</td><td>${nilaiText}</td></tr>`;
                            noCounter++;
                        });
                    }

                    if (nilaiPilihan.length > 0) {
                        html += `<tr class="group-header"><td colspan="3">Mata Pelajaran Pilihan</td></tr>`;
                        nilaiPilihan.forEach((item) => {
                            const nilaiText = formatNilai(item.rata_rata);
                            html += `<tr><td>${noCounter}</td><td>${item.mapel || '-'}</td><td>${nilaiText}</td></tr>`;
                            noCounter++;
                        });
                    }

                    if (nilaiMulok.length > 0) {
                        html += `<tr class="group-header"><td colspan="3">Muatan Lokal</td></tr>`;
                        nilaiMulok.forEach((item) => {
                            const nilaiText = formatNilai(item.rata_rata);
                            html += `<tr><td>${noCounter}</td><td>${item.mapel || '-'}</td><td>${nilaiText}</td></tr>`;
                            noCounter++;
                        });
                    }

                    return html;
                })()
                : `
                    <tr>
                        <td>1</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                `;
            const totalNilai = nilaiList.reduce((sum, item) => {
                const numberValue = Number(item.rata_rata);
                return sum + (Number.isNaN(numberValue) ? 0 : numberValue);
            }, 0);
            const rataRataAll = nilaiList.length ? totalNilai / nilaiList.length : null;
            const rataRataText = rataRataAll !== null ? formatNilai(rataRataAll) : '-';

            return `
                <div class="transkrip-template">
                    <div class="header">
                        <img class="header-logo-left" src="https://humas.jatengprov.go.id/foto/1622767670852-Logo%20Provinsi%20Jawa%20Tengah%20(PNG-1080p)%20-%20FileVector69.png" alt="Logo Provinsi Jawa Tengah">
                        <img class="header-logo" src="https://upload.wikimedia.org/wikipedia/commons/b/b2/Skanida.png" alt="Logo SMK N 2 Semarang">
                        <div style="font-size: 20px; margin: 2px 0px; line-height: 0.9;">PEMERINTAH PROVINSI JAWA TENGAH</div>
                        <div style="font-size: 25px; margin: 2px 0px; line-height: 0.9; font-weight: bold;">DINAS PENDIDIKAN</div>
                        <div style="font-size: 25px; margin: 2px 0px; line-height: 0.9;"><strong>SEKOLAH MENENGAH KEJURUAN NEGERI 2 SEMARANG</strong></div>
                        <div style="font-size: 14px; margin: 2px 0px; line-height: 0.8;">Jalan Dr. Cipto Nomor 121 A, Semarang Timur, Kota Semarang, Jawa Tengah, Kode Pos 50124</div>
                        <div style="font-size: 14px; margin: 2px 0px; line-height: 0.8;">Telepon 024-8455757, Fakssimile 024-8455757, Laman https://smkn2semarang.sch.id</div>
                        <div style="font-size: 14px; margin: 2px 0px; line-height: 0.8;">Pos-el smkn2kotasemarang@gmail.com, smeansa_smg@yahoo.co.id</div>
                        <div class="header-line"></div>
                        <div class="header-line-secondary"></div>
                    </div>

                    <div class="center subtitle">TRANSKRIP NILAI</div>
                    <div class="center subtitle">Nomor: ${noTranskrip}</div>
                    <table class="header-table">
                    </table>

                    <div class="content">
                        <table class="table-info">
                            <tr>
                                <td>Satuan Pendidikan</td>
                                <td>: ${satuan_pendidikan}</td>
                            </tr>
                            <tr>
                                <td>Nomor Pokok Satuan Pendidikan</td>
                                <td>: ${npsn}</td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>: ${namaLengkap}</td>
                            </tr>
                            <tr>
                                <td>Tempat, Tanggal Lahir</td>
                                <td>: ${ttl}</td>
                            </tr>
                            <tr>
                                <td>Nomor Induk Siswa Nasional</td>
                                <td>: ${nisn}</td>
                            </tr>
                            <tr>
                                <td>Nomor Ijazah</td>
                                <td>: ${no_ijazah}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Kelulusan</td>
                                <td>: 4 Mei 2026</td>
                            </tr>
                            <tr>
                                <td>Kurikulum</td>
                                <td>: ${kurikulum}</td>
                            </tr>
                            <tr>
                                <td>Program Keahlian</td>
                                <td>: ${programKeahlian}</td>
                            </tr>
                            <tr>
                                <td>Konsentrasi Keahlian</td>
                                <td>: ${konsentrasiKeahlian}</td>
                            </tr>
                        </table>

                        <p style="margin-top: 15px;">
                            Berikut ini adalah daftar nilai mata pelajaran siswa:
                        </p>

                        <table class="nilai-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${nilaiRows}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><strong>Rata-rata</strong></td>
                                    <td><strong>${rataRataText}</strong></td>
                                </tr>
                            </tfoot>
                        </table>

                        <p style="margin-top: 20px;">
                            Demikian transkrip ini dibuat dengan sebenarnya untuk keperluan yang diinginkan oleh siswa yang bersangkutan.
                        </p>
                    </div>

                    <div class="footer">
                        <div class="ttd">
                            Semarang, ${tanggalCetak}<br>
                            Kepala Sekolah,<br><br><br><br><br><br>
                            <strong>Nana Mulyana, S.P., M.Si.</strong><br>
                            NIP. ${nip}
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="modal-actions">
                        <button class="btn-print" onclick="printTranskripModal()">
                            <i class="fa fa-print"></i> Cetak / Print
                        </button>
                        <button class="btn-close-modal" onclick="closeTranskripModal()">
                            <i class="fa fa-times"></i> Tutup
                        </button>
                    </div>
                </div>
            `;
        }

        function closeTranskripModal() {
            const modal = document.getElementById('transkripModal');
            modal.classList.remove('show');
        }

        function printTranskripModal() {
            window.print();
        }

        window.addEventListener('click', function (event) {
            const modal = document.getElementById('transkripModal');
            if (event.target === modal) {
                closeTranskripModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeTranskripModal();
            }
        });

        function showSiswaModal(idKelas, namaKelas) {
            const modal = document.getElementById('siswaModal');
            const content = document.getElementById('siswaContent');

            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data siswa...</p>
                </div>
            `;

            modal.classList.add('show');

            fetch(`/nilai/transkrip/kelas/${idKelas}/siswa`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const siswaHtml = generateSiswaHtml(data.data, namaKelas);
                        content.innerHTML = siswaHtml;
                    } else {
                        content.innerHTML = `
                            <div class="alert alert-danger">
                                <strong>Error:</strong> ${data.error || 'Gagal memuat data siswa'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> Gagal memuat data siswa. Silakan coba lagi.
                        </div>
                    `;
                });
        }

        function generateSiswaHtml(siswaList, namaKelas) {
            let html = `
                <div style="padding: 20px;">
                    <h5 class="mb-4">Daftar Siswa Kelas ${namaKelas}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="40%">Nama Siswa</th>
                                    <th width="20%">NISN</th>
                                    <th width="20%">NIS</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            if (siswaList && siswaList.length > 0) {
                siswaList.forEach((siswa, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${siswa.nama_siswa || '-'}</td>
                            <td>${siswa.nisn || '-'}</td>
                            <td>${siswa.nis || '-'}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info"
                                    onclick="showTranskripModal('${siswa.peserta_id}')">
                                    <i class="fa fa-file-text"></i> Transkrip
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-3"></i>
                            <p>Tidak ada data siswa di kelas ini</p>
                        </td>
                    </tr>
                `;
            }

            html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-actions">
                        <button class="btn-close-modal" onclick="closeSiswaModal()">
                            <i class="fa fa-times"></i> Tutup
                        </button>
                    </div>
                </div>
            `;

            return html;
        }

        function closeSiswaModal() {
            const modal = document.getElementById('siswaModal');
            modal.classList.remove('show');
        }

        window.addEventListener('click', function (event) {
            const siswaModal = document.getElementById('siswaModal');
            if (event.target === siswaModal) {
                closeSiswaModal();
            }
        });

        // Escape key untuk siswa modal
        const originalEscapeListener = document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const siswaModal = document.getElementById('siswaModal');
                if (siswaModal && siswaModal.classList.contains('show')) {
                    closeSiswaModal();
                }
            }
        });
    </script>
@endsection
