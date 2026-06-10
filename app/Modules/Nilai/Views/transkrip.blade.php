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
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            align-items: flex-start;
        }

        .transkrip-template .ttd {
            width: 300px;
            text-align: left;
        }

        .transkrip-template .foto {
            width: 70px;
            height: 90px;
            border: 1px solid #000;
            text-align: center;
            line-height: 90px;
            flex-shrink: 0;
            font-size: 11px;
            color: #888;
            margin-top: 30px;
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
                box-sizing: border-box;
            }

            /* Hapus chrome layout (sidebar/header/footer) agar tidak menggeser konten.
               display:none mengkolaps layout, berbeda dengan visibility:hidden. */
            #sidebar,
            #main > header,
            #main > footer,
            footer {
                display: none !important;
            }

            #app {
                display: block !important;
            }

            #main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            #main-content {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Sembunyikan konten halaman (search/table) selain modal transkrip */
            #main-content > .page-heading {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .modal-actions,
            .transkrip-modal-close {
                display: none !important;
            }

            /* Sembunyikan semua modal; hanya yang terbuka (.show) yang dicetak.
               Kedua modal memakai class .transkrip-modal, jadi harus discope. */
            .transkrip-modal {
                display: none !important;
            }

            /* Tampilkan modal terbuka sebagai aliran normal di atas halaman */
            .transkrip-modal.show {
                position: static !important;
                display: block !important;
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                background: none !important;
                animation: none !important;
            }

            .transkrip-modal.show .transkrip-modal-content {
                position: static !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
                max-height: none !important;
                height: auto !important;
                overflow: visible !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: white !important;
                animation: none !important;
            }

            .transkrip-template {
                position: static;
                width: 100%;
                margin: 0;
                padding: 10px;
                page-break-after: avoid;
                background: white;
                font-size: 11px;
                line-height: 1;
            }

            .transkrip-template .header {
                padding-left: 10px;
                padding-right: 10px;
                font-size: 10px;
            }

            .transkrip-template .header-logo-left {
                width: 50px;
            }

            .transkrip-template .header-logo {
                width: 50px;
            }

            .transkrip-template .header-line {
                margin: 2px -10px 2px -10px;
                width: calc(100% + 20px);
            }

            .transkrip-template .header-line-secondary {
                margin: -2px -10px 3px -10px;
                width: calc(100% + 20px);
            }

            .transkrip-template .subtitle {
                font-size: 13px;
                margin: 0px 0 1px 0;
            }

            .transkrip-template .table-info {
                margin-top: 3px;
                font-size: 11px;
            }

            .transkrip-template .table-info td {
                padding: 1px 2px;
            }

            .transkrip-template .nilai-table {
                margin-top: 5px;
                font-size: 11px;
            }

            .transkrip-template .nilai-table th,
            .transkrip-template .nilai-table td {
                padding: 1px 1px;
                border: 0.5px solid #000;
            }

            .transkrip-template .footer {
                margin-top: 5px;
            }

            .transkrip-template .ttd {
                width: 200px;
                font-size: 10px;
                line-height: 1.2;
            }

            .transkrip-template .content {
                margin-top: 3px;
            }

            .transkrip-template .content p {
                margin: 1px 0;
                font-size: 11px;
            }

            .transkrip-template .header > div {
                margin: 0px !important;
                line-height: 0.9 !important;
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
                                    <th width="20%">Aksi</th>
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
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="bulkPrintKelas('{{ $pd->id }}', '{{ $pd->kelas }}')">
                                                    <i class="fa fa-print"></i> Cetak Semua
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
                        <div style="font-size:12px;margin:0;line-height:0.9;">PEMERINTAH PROVINSI JAWA TENGAH</div>
                        <div style="font-size:14px;margin:0;line-height:0.9;font-weight:bold;">DINAS PENDIDIKAN</div>
                        <div style="font-size:14px;margin:0;line-height:0.9;"><strong>SEKOLAH MENENGAH KEJURUAN NEGERI 2<br>SEMARANG</strong></div>
                        <div style="font-size:8px;margin:0;line-height:0.8;">Jalan Dr. Cipto Nomor 121 A, Semarang Timur, Kota Semarang, Jawa Tengah, Kode Pos 50124</div>
                        <div style="font-size:8px;margin:0;line-height:0.8;">Telepon 024-8455757, Faksimile 024-8455757, Laman https://smkn2semarang.sch.id</div>
                        <div style="font-size:8px;margin:0;line-height:0.8;">Pos-el smkn2kotasemarang@gmail.com, smeansa_smg@yahoo.co.id</div>
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

                        <p style="margin-top: 3px;">
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

                        <p style="margin-top: 3px; margin-bottom: 5px;">
                            Demikian transkrip ini dibuat dengan sebenarnya untuk keperluan yang diinginkan oleh siswa yang bersangkutan.
                        </p>
                    </div>

                    <div class="footer">
                        <div class="foto">Foto</div>
                        <div class="ttd">
                            Semarang, 29 Mei 2026<br>
                            Kepala Sekolah,<br><br><br><br>
                            <strong>Nana Mulyana, S.P., M.Si.</strong><br>
                            Pembina Tk.I, IV/b<br>
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

        function getPrintCss() {
            return `
                @page { size: A4; margin: 10mm 10mm 12px 10mm; }
                * { box-sizing: border-box; }
                html, body { margin: 0; padding: 0; }
                body {
                    font-family: "Times New Roman", serif;
                    color: #000;
                }
                .transkrip-template { width: 100%; font-size: 12px; line-height: 1; }
                .transkrip-template .center { text-align: center; }
                .transkrip-template .header {
                    position: relative;
                    text-align: center;
                    padding: 0;
                }
                /* Teks di header diberi padding agar tidak tertimpa logo */
                .transkrip-template .header > div:not(.header-line):not(.header-line-secondary) {
                    padding: 0 65px;
                    margin: 0 !important;
                    line-height: 0.95 !important;
                }
                .transkrip-template .header-logo-left {
                    position: absolute; top: 0; left: 0; width: 65px; height: auto;
                }
                .transkrip-template .header-logo {
                    position: absolute; top: 0; right: 0; width: 65px; height: auto;
                }
                /* Garis tidak perlu negative margin — header sudah tanpa padding */
                .transkrip-template .header-line {
                    border-top: 3px solid #000; margin: 4px 0; width: 100%;
                }
                .transkrip-template .header-line-secondary {
                    border-top: 1px solid #000; margin: -2px 0 6px; width: 100%;
                }
                .transkrip-template .subtitle { font-size: 14px; margin: 1px 0; }
                .transkrip-template .content { margin-top: 6px; }
                .transkrip-template .content p { margin: 3px 0; }
                .transkrip-template .table-info { width: 100%; margin-top: 6px; }
                .transkrip-template .table-info td { padding: 0px 2px; }
                .transkrip-template .table-info td:nth-child(2) { text-transform: capitalize; }
                .transkrip-template .nilai-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                .transkrip-template .nilai-table th,
                .transkrip-template .nilai-table td { border: 1px solid #000; padding: 1px 3px; }
                .transkrip-template .nilai-table th { text-align: center; }
                .transkrip-template .nilai-table .group-header td {
                    background-color: #f0f0f0; font-weight: bold; text-align: left !important;
                }
                .transkrip-template .nilai-table th:nth-child(1),
                .transkrip-template .nilai-table td:nth-child(1),
                .transkrip-template .nilai-table th:nth-child(3),
                .transkrip-template .nilai-table td:nth-child(3) { text-align: center; }
                .transkrip-template .nilai-table tfoot td { text-align: center; }
                .transkrip-template .footer { margin-top: 18px; width: 100%; display: flex; justify-content: flex-end; gap: 12px; align-items: flex-start; }
                .transkrip-template .foto { width: 60px; height: 75px; border: 1px solid #000; text-align: center; line-height: 75px; font-size: 9px; color: #888; flex-shrink: 0; margin-top: 10mm; }
                .transkrip-template .ttd { width: 280px; text-align: left; line-height: 1.3; }
                .transkrip-template .clear { clear: both; }
                .modal-actions { display: none !important; }
                .transkrip-template + .transkrip-template {
                    page-break-before: always;
                    break-before: page;
                }
            `;
        }

        function printViaIframe(htmlContent, title) {
            const printCss = getPrintCss();
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(
                '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + title + '</title>' +
                '<style>' + printCss + '</style></head><body>' +
                htmlContent +
                '</body></html>'
            );
            doc.close();

            const cleanup = () => {
                if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
            };

            const doPrint = () => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                setTimeout(cleanup, 1000);
            };

            // Tunggu semua gambar selesai dimuat
            const imgs = doc.images;
            if (!imgs || imgs.length === 0) {
                setTimeout(doPrint, 250);
                return;
            }

            let loaded = 0;
            let printed = false;
            const tryPrint = () => { if (!printed) { printed = true; doPrint(); } };
            const onImg = () => { if (++loaded >= imgs.length) tryPrint(); };
            for (let i = 0; i < imgs.length; i++) {
                if (imgs[i].complete) { onImg(); }
                else {
                    imgs[i].addEventListener('load', onImg);
                    imgs[i].addEventListener('error', onImg);
                }
            }
            setTimeout(tryPrint, 2500);
        }

        function printTranskripModal() {
            const template = document.querySelector('#transkripModal .transkrip-template');
            if (!template) { window.print(); return; }
            printViaIframe(template.outerHTML, 'Transkrip Nilai');
        }

        async function bulkPrintKelas(idKelas, namaKelas) {
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memuat...';

            try {
                // 1. Ambil daftar siswa di kelas
                const res = await fetch(`/nilai/transkrip/kelas/${idKelas}/siswa`);
                const json = await res.json();
                if (!json.success || !json.data.length) {
                    alert('Tidak ada siswa di kelas ini.');
                    return;
                }

                // 2. Fetch detail transkrip semua siswa secara paralel
                const results = await Promise.all(
                    json.data.map(s =>
                        fetch(`/nilai/transkrip/${s.peserta_id}/detail`).then(r => r.json())
                    )
                );

                // 3. Generate HTML tiap transkrip — CSS adjacent sibling (+) menangani page break
                const pages = results
                    .filter(r => r.success)
                    .map(r => generateTranskripHtml(r.data));

                if (!pages.length) {
                    alert('Tidak ada data transkrip yang tersedia.');
                    return;
                }

                printViaIframe(pages.join(''), 'Transkrip Nilai - ' + namaKelas);

            } catch (e) {
                console.error(e);
                alert('Gagal memuat data. Silakan coba lagi.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
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
