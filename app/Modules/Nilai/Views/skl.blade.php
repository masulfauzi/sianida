@extends('layouts.app')

@section('page-css')
    <style>
        .skl-modal {
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

        .skl-modal.show {
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

        .skl-modal-content {
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

        .skl-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .skl-modal-close:hover,
        .skl-modal-close:focus {
            color: black;
        }

        .skl-template {
            font-family: "Times New Roman", serif;
            margin: 20px;
            font-size: 15px;
            line-height: 1.2;
            color: #000;
        }

        .skl-template .center {
            text-align: center;
        }

        .skl-template .header {
            position: relative;
            text-align: center;
            line-height: 1.2;
            padding-left: 90px;
            padding-right: 90px;
        }

        .skl-template .header-logo-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 70px;
            height: auto;
        }

        .skl-template .header-logo {
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: auto;
        }

        .skl-template .header-line {
            border-top: 3px solid #000;
            margin: 6px -90px 6px -90px;
            width: calc(100% + 180px);
        }

        .skl-template .header-line-secondary {
            border-top: 1px solid #000;
            margin: -5px -90px 8px -90px;
            width: calc(100% + 180px);
        }

        .skl-template .title {
            font-weight: bold;
            font-size: 19px;
            margin-top: 10px;
        }

        .skl-template .subtitle {
            font-size: 17px;
            margin-bottom: 0;
            line-height: 1.1;
        }

        .skl-template .header-table {
            margin: 4px auto 6px;
            border-collapse: collapse;
            font-size: 15px;
        }

        .skl-template .header-table td {
            padding: 0 4px;
            vertical-align: top;
        }

        .skl-template .header-table .label,
        .skl-template .header-table .value {
            text-align: left;
        }

        .skl-template .content {
            margin-top: 12px;
        }

        .skl-template .table-info {
            width: 100%;
            margin-top: 10px;
        }

        .skl-template .table-info td {
            padding: 2px;
        }

        .skl-template .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .skl-template .nilai-table th,
        .skl-template .nilai-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .skl-template .nilai-table th {
            text-align: center;
        }

        .skl-template .nilai-table th:nth-child(1),
        .skl-template .nilai-table td:nth-child(1),
        .skl-template .nilai-table th:nth-child(3),
        .skl-template .nilai-table td:nth-child(3) {
            text-align: center;
        }

        .skl-template .nilai-table tfoot td {
            text-align: center;
        }

        .skl-template .footer {
            margin-top: 40px;
            width: 100%;
        }

        .skl-template .ttd {
            width: 300px;
            float: right;
            text-align: left;
        }

        .skl-template .foto {
            width: 100px;
            height: 120px;
            border: 1px solid #000;
            text-align: center;
            line-height: 160px;
            float: left;
            margin-left: 400px;
            margin-top: 50px;
        }

        .skl-template .clear {
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
            * {
                margin: 0;
                padding: 0;
                visibility: hidden;
                box-sizing: border-box;
            }

            .skl-template,
            .skl-template * {
                visibility: visible;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .skl-template {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                page-break-after: avoid;
                background: white;
                visibility: visible;
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
                    <form method="GET" action="{{ route('nilai.skl.index') }}" class="search-form">
                        <input type="text" name="search" placeholder="Cari nama siswa..." value="{{ $search }}"
                            class="form-control">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('nilai.skl.index') }}" class="btn btn-secondary">
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
                                    <th width="35%">Nama Siswa</th>
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
                                                {{ $pesertaDidik->firstItem() + $loop->index }}
                                            </span>
                                        </td>
                                        <td>{{ $pd->nama_siswa }}</td>
                                        <td>{{ $pd->kelas ?? '-' }}</td>
                                        <td>{{ $pd->jurusan ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showSklModal('{{ $pd->peserta_id }}')">
                                                <i class="fa fa-download"></i> Download SKL
                                            </button>
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
                    <div class="row mt-3">
                        <div class="col-12">
                            {{ $pesertaDidik->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- SKL Modal -->
    <div id="sklModal" class="skl-modal">
        <div class="skl-modal-content">
            <span class="skl-modal-close" onclick="closeSklModal()">&times;</span>
            <div id="sklContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat SKL...</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-js')
    <script>
        function showSklModal(pesertaDidikId) {
            const modal = document.getElementById('sklModal');
            const content = document.getElementById('sklContent');

            // Show loading
            content.innerHTML = `
                                                                                                                                                                                                                                        <div class="text-center py-5">
                                                                                                                                                                                                                                            <div class="spinner-border text-primary" role="status">
                                                                                                                                                                                                                                                <span class="visually-hidden">Loading...</span>
                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                            <p class="mt-3">Memuat SKL...</p>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                    `;

            modal.classList.add('show');

            // Fetch SKL data
            fetch(`/nilai/skl/${pesertaDidikId}/detail`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const sklHtml = generateSklHtml(data.data);
                        content.innerHTML = sklHtml;
                    } else {
                        content.innerHTML = `
                                                                                                                                                                                                                                                    <div class="alert alert-danger">
                                                                                                                                                                                                                                                        <strong>Error:</strong> ${data.error || 'Gagal memuat SKL'}
                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `
                                                                                                                                                                                                                                                <div class="alert alert-danger">
                                                                                                                                                                                                                                                    <strong>Error:</strong> Gagal memuat SKL. Silakan coba lagi.
                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                            `;
                });
        }

        function generateSklHtml(data) {
            const today = new Date();
            const bulanIndonesia = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const tanggalCetak = `${today.getDate()} ${bulanIndonesia[today.getMonth()]} ${today.getFullYear()}`;

            const nomorSurat = `SKL-${data.id.substring(0, 8).toUpperCase()}/${new Date().getFullYear()}`;
            const tempatLahir = data.tempat_lahir || '-';
            const tanggalLahir = data.tgl_lahir || '-';
            const ttl = `${tempatLahir}, ${tanggalLahir}`;
            const orangTua = data.orang_tua || '-';
            const programKeahlian = data.program_keahlian || '-';
            const konsentrasiKeahlian = data.konsentrasi_keahlian || '-';
            const tahunAjaran = data.tahun_ajaran || '-';
            const kepalaSekolah = data.kepala_sekolah || data.nama_sekolah || '-';
            const nip = data.nip || '19690601 199203 1 012';
            const nilaiList = Array.isArray(data.nilai) ? data.nilai : [];
            const formatNilai = (value) => {
                const numberValue = Number(value);
                if (Number.isNaN(numberValue)) {
                    return '-';
                }
                return Number.isInteger(numberValue) ? numberValue.toString() : numberValue.toFixed(2);
            };
            const nilaiRows = nilaiList.length
                ? nilaiList.map((item, index) => {
                    const nilaiText = formatNilai(item.rata_rata);
                    return `
                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                <td>${index + 1}</td>
                                                                                                                                                                                                                                                <td>${item.mapel || '-'}</td>
                                                                                                                                                                                                                                                <td>${nilaiText}</td>
                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                        `;
                }).join('')
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
                                                                                                                                                                                                                    <div class="skl-template">
                                                                                                                                                                                                                        <div class="header">
                                                                                                                                                                                                                            <img class="header-logo-left" src="https://humas.jatengprov.go.id/foto/1622767670852-Logo%20Provinsi%20Jawa%20Tengah%20(PNG-1080p)%20-%20FileVector69.png" alt="Logo Provinsi Jawa Tengah">
                                                                                                                                                                                                                            <img class="header-logo" src="https://upload.wikimedia.org/wikipedia/commons/b/b2/Skanida.png" alt="Logo SMK N 2 Semarang">
                                                                                                                                                                                                                            <div><strong>PEMERINTAH PROVINSI JAWA TENGAH</strong></div>
                                                                                                                                                                                                                            <div><strong>DINAS PENDIDIKAN</strong></div>
                                                                                                                                                                                                                            <div><strong>SMK NEGERI 2 SEMARANG</strong></div>
                                                                                                                                                                                                                            <div>Jalan Dr. Cipto Nomor 121-A, Semarang 50124</div>
                                                                                                                                                                                                                            <div>Telepon (024) 8455757 | smkn2semarang.sch.id</div>
                                                                                                                                                                                                                            <div class="header-line"></div>
                                                                                                                                                                                                                            <div class="header-line-secondary"></div>
                                                                                                                                                                                                                        </div>

                                                                                                                                                                                                                        <div class="center title">SURAT KETERANGAN LULUS</div>
                                                                                                                                                                                                                        <div class="center subtitle">SMK NEGERI 2 SEMARANG</div>
                                                                                                                                                                                                                        <div class="center subtitle">PROGRAM 3 TAHUN</div>
                                                                                                                                                                                                                        <div class="center subtitle">TAHUN AJARAN 2025/2026</div>
                                                                                                                                                                                                                        <table class="header-table">
                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                <td class="label">Program Keahlian</td>
                                                                                                                                                                                                                                <td>:</td>
                                                                                                                                                                                                                                <td class="value">${programKeahlian}</td>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                <td class="label">Konsentrasi Keahlian</td>
                                                                                                                                                                                                                                <td>:</td>
                                                                                                                                                                                                                                <td class="value">${konsentrasiKeahlian}</td>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                        </table>
                                                                                                                                                                                                                        <div class="center subtitle">Nomor: ${nomorSurat}</div>

                                                                                                                                                                                                                        <div class="content">
                                                                                                                                                                                                                            Yang bertanda tangan di bawah ini, Kepala Sekolah Menengah Kejuruan Negeri 2 Kota Semarang, Provinsi Jawa Tengah menerangkan bahwa:

                                                                                                                                                                                                                            <table class="table-info">
                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                    <td>Nama</td>
                                                                                                                                                                                                                                    <td>: ${data.nama || '-'}</td>
                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                    <td>Tempat, Tanggal Lahir</td>
                                                                                                                                                                                                                                    <td>: ${ttl}</td>
                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                    <td>Nama Orang Tua / Wali</td>
                                                                                                                                                                                                                                    <td>: ${orangTua}</td>
                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                    <td>NIS</td>
                                                                                                                                                                                                                                    <td>: ${data.nis || '-'}</td>
                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                    <td>NISN</td>
                                                                                                                                                                                                                                    <td>: ${data.nisn || '-'}</td>
                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                            </table>

                                                                                                                                                                                                                            <p>
                                                                                                                                                                                                                                Dinyatakan <strong>LULUS</strong> dari satuan pendidikan berdasarkan kriteria kelulusan Sekolah Menengah Kejuruan Negeri 2 Kota Semarang Tahun Ajaran 2025/2026, dengan nilai sebagai berikut:
                                                                                                                                                                                                                                dengan nilai sebagai berikut:
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

                                                                                                                                                                                                                            <p>
                                                                                                                                                                                                                                Surat Keterangan Lulus ini berlaku sementara sampai dengan diterbitkannya Ijazah Tahun Ajaran 2025/2026, untuk menjadikan maklum bagi yang berkepentingan.

                                                                                                                                                                                                                            </p>
                                                                                                                                                                                                                        </div>

                                                                                                                                                                                                                        <div class="foto">Foto 3x4</div>

                                                                                                                                                                                                                        <div class="footer">
                                                                                                                                                                                                                            <div class="ttd">
                                                                                                                                                                                                                                Semarang, ${tanggalCetak}<br>
                                                                                                                                                                                                                                Kepala Sekolah,<br><br><br><br>
                                                                                                                                                                                                                                <strong>Nana Mulyana, S.P., M.Si.</strong><br>
                                                                                                                                                                                                                                NIP. ${nip}
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                        <div class="clear"></div>

                                                                                                                                                                                                                        <div class="modal-actions">
                                                                                                                                                                                                                            <button class="btn-print" onclick="printSklModal()">
                                                                                                                                                                                                                                <i class="fa fa-print"></i> Cetak / Print
                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                            <button class="btn-close-modal" onclick="closeSklModal()">
                                                                                                                                                                                                                                <i class="fa fa-times"></i> Tutup
                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                `;
        }

        function closeSklModal() {
            const modal = document.getElementById('sklModal');
            modal.classList.remove('show');
        }

        function printSklModal() {
            window.print();
        }

        // Close modal when clicking outside of it
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('sklModal');
            if (event.target === modal) {
                closeSklModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSklModal();
            }
        });
    </script>
@endsection