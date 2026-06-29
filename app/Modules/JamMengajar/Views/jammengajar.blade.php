@extends('layouts.app')

@section('page-css')
    <style>
        .atp-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            overflow-y: auto;
        }

        .atp-modal.show {
            display: block;
        }

        .atp-modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 950px;
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .atp-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .atp-modal-close:hover,
        .atp-modal-close:focus {
            color: black;
        }

        .atp-template {
            font-family: "Times New Roman", serif;
            margin: 10px;
            font-size: 15px;
            line-height: 1.3;
            color: #000;
        }

        .atp-template .center {
            text-align: center;
        }

        .atp-template .title {
            font-weight: bold;
            font-size: 16px;
            margin: 0;
        }

        .atp-template .identitas-table {
            margin-top: 16px;
            font-size: 14px;
        }

        .atp-template .identitas-table td {
            padding: 1px 4px;
            vertical-align: top;
        }

        .atp-template .skor-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 13px;
        }

        .atp-template .skor-table th,
        .atp-template .skor-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .atp-template .skor-table th {
            text-align: center;
        }

        .atp-template .skor-table .group-header td {
            background-color: #d9e2f3;
            font-weight: bold;
        }

        .atp-template .skor-table td:nth-child(1),
        .atp-template .skor-table th:nth-child(1) {
            text-align: center;
            width: 3%;
        }

        .atp-template .skor-table td.skor-col,
        .atp-template .skor-table th.skor-col {
            text-align: center;
            width: 9%;
        }

        .atp-template .skor-table td.catatan-col {
            width: 15%;
        }

        .atp-template .keterangan {
            margin-top: 10px;
            font-size: 13px;
        }

        .atp-template .review-box {
            margin-top: 16px;
            border: 1px solid #000;
            min-height: 60px;
            padding: 6px;
        }

        .atp-template .ttd {
            width: 280px;
            float: right;
            text-align: left;
            margin-top: 16px;
            font-size: 14px;
        }

        .atp-template .clear {
            clear: both;
        }

        .atp-modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: center;
        }

        .atp-modal-actions button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-atp-print {
            background-color: #28a745;
            color: white;
        }

        .btn-atp-close {
            background-color: #6c757d;
            color: white;
        }

        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }

            #sidebar,
            #main > header,
            #main-content > .page-heading,
            #main-content > footer {
                display: none !important;
            }

            #main {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .atp-modal {
                position: static !important;
                height: auto !important;
                background: none !important;
                overflow: visible !important;
            }

            .atp-modal-content {
                position: static !important;
                width: 100% !important;
                max-width: none !important;
                max-height: none !important;
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            .atp-modal-close,
            .atp-modal-actions {
                display: none !important;
            }

            .atp-template {
                margin: 0 !important;
                font-size: 16px !important;
                line-height: 1.15 !important;
            }

            .atp-template .title {
                font-size: 17px !important;
            }

            .atp-template .identitas-table {
                margin-top: 6px !important;
                font-size: 15px !important;
            }

            .atp-template .identitas-table td {
                padding: 0 4px !important;
            }

            .atp-template .skor-table {
                margin-top: 6px !important;
                font-size: 14px !important;
            }

            .atp-template .skor-table th,
            .atp-template .skor-table td {
                padding: 2px !important;
            }

            .atp-template .keterangan {
                margin-top: 4px !important;
                font-size: 14px !important;
            }

            .atp-template .review-box {
                margin-top: 6px !important;
                min-height: 30px !important;
                padding: 3px !important;
            }

            .atp-template .ttd {
                margin-top: 6px !important;
                font-size: 15px !important;
            }

            .rpp-template {
                font-size: 16px !important;
                line-height: 1.05 !important;
            }

            .rpp-template .title {
                font-size: 18px !important;
            }

            .rpp-template .identitas-table {
                margin-top: 4px !important;
                font-size: 15px !important;
            }

            .rpp-template .skor-table {
                margin-top: 4px !important;
                font-size: 14px !important;
            }

            .rpp-template .skor-table th,
            .rpp-template .skor-table td {
                padding: 1px 2px !important;
                line-height: 1.05 !important;
            }

            .rpp-template .keterangan {
                margin-top: 2px !important;
                font-size: 14px !important;
            }

            .rpp-template .review-box {
                margin-top: 4px !important;
                min-height: 20px !important;
                padding: 2px !important;
            }

            .rpp-template .ttd {
                margin-top: 4px !important;
                font-size: 15px !important;
            }
        }
    </style>
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

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Pembagian Jam Mengajar</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home"
                                        role="tab" aria-controls="home" aria-selected="true">Perguru</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                                        aria-controls="profile" aria-selected="false">Perkelas</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab"
                                        aria-controls="contact" aria-selected="false">Contact</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">
                                    <div class="card">

                                        <div class="card-body">

                                            @include('include.flash')
                                            <div class="table-responsive-md col-12">
                                                <table class="table" id="table1">
                                                    <thead>
                                                        <tr>
                                                            <th width="15">No</th>
                                                            <td>Guru</td>
                                                            <td>Jml Jam</td>
                                                            <td>Verifikasi ATP</td>
                                                            <td>Verifikasi RPP</td>

                                                            <th width="30%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $no = 1; @endphp
                                                        @forelse ($data as $item)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>{{ $item->nama }}</td>
                                                                <td>{{ $item->jml_jam }}</td>
                                                                <td>
                                                                    @if (isset($verifikasi_atp_by_guru[$item->id_guru]))
                                                                        <span class="badge bg-success" role="button"
                                                                            style="cursor: pointer;"
                                                                            onclick="showAtpModal('{{ $verifikasi_atp_by_guru[$item->id_guru] }}')">Sudah
                                                                            Diverifikasi</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">Belum Diverifikasi</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (isset($verifikasi_rpp_by_guru[$item->id_guru]))
                                                                        <span class="badge bg-success" role="button"
                                                                            style="cursor: pointer;"
                                                                            onclick="showRppModal('{{ $verifikasi_rpp_by_guru[$item->id_guru] }}')">Sudah
                                                                            Diverifikasi</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">Belum Diverifikasi</span>
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <a href="{{ route('jammengajar.guru.index', $item->id_guru) }}"
                                                                        class="btn btn-success">Detail</a>
                                                                    @if (isset($verifikasi_atp_by_guru[$item->id_guru]))
                                                                        <a href="{{ route('verifikasiatp.edit', $verifikasi_atp_by_guru[$item->id_guru]) }}"
                                                                            class="btn btn-primary">Verifikasi ATP</a>
                                                                    @else
                                                                        <a href="{{ route('verifikasiatp.create', ['id_guru' => $item->id_guru]) }}"
                                                                            class="btn btn-primary">Verifikasi ATP</a>
                                                                    @endif
                                                                    @if (isset($verifikasi_rpp_by_guru[$item->id_guru]))
                                                                        <a href="{{ route('verifikasirpp.edit', $verifikasi_rpp_by_guru[$item->id_guru]) }}"
                                                                            class="btn btn-info">Verifikasi RPP</a>
                                                                    @else
                                                                        <a href="{{ route('verifikasirpp.create', ['id_guru' => $item->id_guru]) }}"
                                                                            class="btn btn-info">Verifikasi RPP</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center"><i>No data.</i></td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="card">

                                        <div class="card-body">

                                            @include('include.flash')
                                            <div class="table-responsive-md col-12">
                                                <table class="table" id="table1">
                                                    <thead>
                                                        <tr>
                                                            <th width="15">No</th>
                                                            <td>Kelas</td>
                                                            <td>Jml Jam</td>

                                                            <th width="30%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $no = 1; @endphp
                                                        @forelse ($kelas as $item_kelas)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>{{ $item_kelas->kelas }}</td>
                                                                <td>{{ $item_kelas->jml_jam }}</td>

                                                                <td>
                                                                    <a href="{{ route('jammengajar.kelas.index', $item_kelas->id_kelas) }}"
                                                                        class="btn btn-success">Detail</a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center"><i>No data.</i></td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                    <a href="{{ route('jammengajar.cetak.index') }}" class="btn btn-secondary">Cetak</a>
                                    <div id="printableArea">
                                        @foreach ($data as $guru_cetak)
                                            <h2>{{ $guru_cetak->nama }}</h2>
                                            @php
                                                $data_jam_mengajar = App\Modules\JamMengajar\Models\JamMengajar::whereIdGuru(
                                                    $guru_cetak->id_guru,
                                                )
                                                    ->whereIdSemester($id_semester)
                                                    ->OrderBy('id_kelas')
                                                    ->get();
                                            @endphp

                                            <table class="table">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kelas</th>
                                                    <th>Mapel</th>
                                                    <th>Jml Jam</th>
                                                </tr>
                                                @php
                                                    $no = 1;
                                                    $tmp_jam = 0;
                                                @endphp
                                                @foreach ($data_jam_mengajar as $item_jam)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $item_jam->kelas->kelas }}</td>
                                                        <td>{{ $item_jam->mapel->mapel }}</td>
                                                        <td>{{ $item_jam->jml_jam }}</td>
                                                    </tr>
                                                    @php
                                                        $tmp_jam += $item_jam->jml_jam;
                                                    @endphp
                                                @endforeach


                                                <tr>
                                                    <th colspan="3">Total Jam Mengajar</th>
                                                    <th>{{ $tmp_jam }}</th>
                                                </tr>
                                            </table>

                                            <div class="page_break"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- Modal Verifikasi ATP -->
    <div id="atpModal" class="atp-modal">
        <div class="atp-modal-content">
            <span class="atp-modal-close" onclick="closeAtpModal()">&times;</span>
            <div id="atpContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data verifikasi ATP...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi RPP -->
    <div id="rppModal" class="atp-modal">
        <div class="atp-modal-content">
            <span class="atp-modal-close" onclick="closeRppModal()">&times;</span>
            <div id="rppContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data verifikasi RPP...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
    <script>
        let lastAtpData = null;
        let lastRppData = null;

        function showAtpModal(verifikasiAtpId) {
            const modal = document.getElementById('atpModal');
            const content = document.getElementById('atpContent');

            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data verifikasi ATP...</p>
                </div>
            `;

            modal.classList.add('show');

            fetch(`/verifikasiatp/${verifikasiAtpId}/detail`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        lastAtpData = data.data;
                        content.innerHTML = generateAtpHtml(data.data);
                    } else {
                        content.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> ${data.error || 'Gagal memuat data verifikasi ATP'}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> Gagal memuat data verifikasi ATP. Silakan coba lagi.</div>`;
                });
        }

        function escapeHtml(text) {
            if (!text) {
                return '';
            }
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function generateAtpHtml(data) {
            const bulanIndonesia = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const dibuatPada = data.created_at ? new Date(data.created_at) : new Date();
            const tanggalCetak = `${dibuatPada.getDate()} ${bulanIndonesia[dibuatPada.getMonth()]} ${dibuatPada.getFullYear()}`;

            const c = data.components;
            const mark = (value, target) => (value == target ? 'v' : '');

            const rows = [
                { no: 1, desc: 'ATP/Silabus memuat nama sekolah, nama mata pelajaran, kelas/fase.', value: c.identitas },
            ];
            const rowsB1 = { no: 2, label: 'Capaian Pembelajaran', desc: 'Memuat Capaian Pembelajaran secara lengkap sesuai dengan Keputusan Kepala BSKAP Nomor 0446/H/KR/2025.', value: c.cp };
            const rowsB2 = { no: 3, label: 'Tujuan Pembelajaran', desc: 'Tujuan pembelajaran memuat koempetensi sikap, pengetahuan, dan keterampilan dan memuat konten sesuai dengan capaian pembelajaran.', value: c.tp };
            const rowsB3 = { no: 4, label: 'Alokasi Waktu', desc: 'Memuat alokasi waktu dengan jumlah sama dengan jumlah jam intrakurikuler pertahun.', value: c.alokasi_waktu };
            const rowsB4 = { no: 5, label: 'Materi Pembelajaran', desc: 'Memuat materi pembelajaran yang esensial sesuai dengan capaian pembelajaran.', value: c.materi };
            const rowsB5 = { no: 6, label: 'Lain-lain', desc: 'Memuat metode/model pembelajaran penilaian/asesmen yang dapat menilai sikap, pengetahuan, dan keterampilan.', value: c.metode };

            const renderRow = (row) => `
                <tr>
                    <td>${row.no}</td>
                    <td>${row.desc ? `${row.desc}` : ''}</td>
                    <td class="skor-col">${mark(row.value, 0)}</td>
                    <td class="skor-col">${mark(row.value, 1)}</td>
                    <td class="skor-col">${mark(row.value, 2)}</td>
                    <td class="skor-col">${mark(row.value, 3)}</td>
                    <td class="skor-col">${mark(row.value, 4)}</td>
                    <td class="catatan-col"></td>
                </tr>
            `;

            const tahunPelajaran = (data.semester || '').replace(/ganjil|genap/gi, '').trim();

            const allValues = Object.values(c);
            const count0 = allValues.filter(v => v == 0).length;
            const count1 = allValues.filter(v => v == 1).length;
            const count2 = allValues.filter(v => v == 2).length;
            const count3 = allValues.filter(v => v == 3).length;
            const count4 = allValues.filter(v => v == 4).length;

            return `
                <div class="atp-template">
                    <div class="center title">INSTRUMEN VERIFIKASI/VALIDASI</div>
                    <div class="center title">ALUR TUJUAN PEMBELAJARAN (ATP)</div>
                    <div class="center title">TAHUN AJARAN ${tahunPelajaran}</div>

                    <table class="identitas-table">
                        <tr><td>Nama Sekolah</td><td>:</td><td><strong>SMK Negeri 2 Semarang</strong></td></tr>
                        <tr><td>Nama Guru</td><td>:</td><td><strong>${data.nama_guru}</strong></td></tr>
                        <tr><td>Mata Pelajaran</td><td>:</td><td><strong>${data.mapel}</strong></td></tr>
                        <tr><td>Kelas/Program Keahlian</td><td>:</td><td><strong>${data.tingkat} / ${data.jurusan}</strong></td></tr>
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
                            <tr class="group-header"><td>I</td><td colspan="7">Identitas</td></tr>
                            ${rows.map(renderRow).join('')}
                            <tr class="group-header"><td>II</td><td colspan="7">Capaian Pembelajaran</td></tr>
                            ${renderRow(rowsB1)}
                            <tr class="group-header"><td>III</td><td colspan="7">Tujuan Pembelajaran</td></tr>
                            ${renderRow(rowsB2)}
                            <tr class="group-header"><td>IV</td><td colspan="7">Alokasi Waktu</td></tr>
                            ${renderRow(rowsB3)}
                            <tr class="group-header"><td>V</td><td colspan="7">Materi Pembelajaran</td></tr>
                            ${renderRow(rowsB4)}
                            <tr class="group-header"><td>VI</td><td colspan="7">Lain-lain</td></tr>
                            ${renderRow(rowsB5)}
                            <tr>
                                <td colspan="2"><strong>JUMLAH SKOR</strong></td>
                                <td class="skor-col">${count0 * 0}</td>
                                <td class="skor-col">${count1 * 1}</td>
                                <td class="skor-col">${count2 * 2}</td>
                                <td class="skor-col">${count3 * 3}</td>
                                <td class="skor-col">${count4 * 4}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>NILAI</strong></td>
                                <td colspan="3" class="center"><strong>${data.nilai}</strong></td>
                                <td colspan="3"><strong>PREDIKAT : ${data.predikat.toUpperCase()}</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                        <strong>Rekomendasi</strong>
                        <div class="review-box">${escapeHtml(data.catatan).replace(/\n/g, '<br>')}</div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div class="keterangan" style="margin-top: 16px; border: 1px solid #000; padding: 6px;">
                            Keterangan:<br>
                            1. Nilai = (Skor perolehan : skor maksimal) x 100<br>
                            2. Predikat:<br>
                            &nbsp;&nbsp;Nilai 91 - 100 = Amat baik<br>
                            &nbsp;&nbsp;Nilai 81 - 90 = Baik<br>
                            &nbsp;&nbsp;Nilai 71 - 80 = Cukup<br>
                            &nbsp;&nbsp;Nilai &le; 70 = Kurang
                        </div>

                        <div class="ttd" style="float: none; margin-top: 16px;">
                            Semarang, ${tanggalCetak}<br>
                            Kepala Sekolah,<br>
                            <img src="{{ asset('assets/images/ttd/ttd_stempel.png') }}" alt="Tanda Tangan dan Stempel" style="height: 140px; display: block; margin-left: -80px; margin-top: -20px; position: relative; top: 20px;">
                            Nana Mulyana, S.P., M.Si.<br>
                            Pembina Tk.I, IV/b<br>
                            NIP. 19690601 199203 1 012
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="atp-modal-actions">
                        <button class="btn-atp-print" onclick="printAtpModal()">
                            <i class="fa fa-print"></i> Cetak / Print
                        </button>
                        <button class="btn-atp-close" onclick="closeAtpModal()">
                            <i class="fa fa-times"></i> Tutup
                        </button>
                    </div>
                </div>
            `;
        }

        function closeAtpModal() {
            document.getElementById('atpModal').classList.remove('show');
        }

        function printAtpModal() {
            const originalTitle = document.title;
            if (lastAtpData) {
                document.title = `Verifikasi ATP - ${lastAtpData.nama_guru} - ${lastAtpData.mapel}`;
            }
            window.onafterprint = () => {
                document.title = originalTitle;
                window.onafterprint = null;
            };
            window.print();
        }

        function showRppModal(verifikasiRppId) {
            const modal = document.getElementById('rppModal');
            const content = document.getElementById('rppContent');

            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data verifikasi RPP...</p>
                </div>
            `;

            modal.classList.add('show');

            fetch(`/verifikasirpp/${verifikasiRppId}/detail`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        lastRppData = data.data;
                        content.innerHTML = generateRppHtml(data.data);
                    } else {
                        content.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> ${data.error || 'Gagal memuat data verifikasi RPP'}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> Gagal memuat data verifikasi RPP. Silakan coba lagi.</div>`;
                });
        }

        function generateRppHtml(data) {
            const bulanIndonesia = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const dibuatPada = data.created_at ? new Date(data.created_at) : new Date();
            const tanggalCetak = `${dibuatPada.getDate()} ${bulanIndonesia[dibuatPada.getMonth()]} ${dibuatPada.getFullYear()}`;

            const c = data.components;
            const mark = (value, target) => (value == target ? 'v' : '');

            const rowsA = [
                { no: 1, desc: 'Memuat nama sekolah, nama mata pelajaran, kelas/fase.', value: c.identitas },
            ];
            const rowsB = [
                { no: 2, desc: 'Memuat Tujuan pembelajaran sesuai yang ada di ATP / Silabus.', value: c.tp },
            ];
            const KP = [
                { no: 3, desc: 'Berisi kegiatan untuk mengkondisikan siswa agar siap mengikuti pembelajaran, termasuk adanya pertanyaan pemantik (berkesadaran, bermakna, dan.atau menggembirakan).', value: c.pendahuluan },
                { no: 4, desc: 'Kegiatan pembelajaran memperhatikan kesiapan, minat dan karakter belajar siswa (pembelajaran berdiferensiasai), pembelajan berpusat pada siswa dengan menggunakan metode atau model yang merangsang siswa untuk memiliki keterampilan berpikir tingkat tinggi (HOTS dan 4C), mengembangkan literasi dan numerasi, menguatkan delapan dimensi profil lulusan, pendidikan perubahan iklim, sekolah sehat, branding sekolah dilaksanakan secara menyenangkan, berkesadaran, dan bermakna siswa mempunyai pengalaman belajar memahami, mengaplikasi, dan merefleksi.', value: c.inti },
                { no: 5, desc: 'Kegiatan refleksi siswa dan guru, mengajak siswa merancang pembelajaran berikutnya, dan mengatur unsur pembelajaran mendalam (menyenangkan, berkesadaran, dan/atau bermakna)', value: c.penutup },
            ];
            const asesmen = [
                { desc: 'Ada kegiatan asesmen awal, asesmen formatif, asesmen sumatif. Kegiatan asesmen memuat kompetensi sikap, pengetahuan, dan keterampilan. Ada kegiatan remedial dan pengayaan.', value: c.assesmen },
            ];
            const rowsC = [
                { desc: 'Memuat materi pembelajaran dan contoh asesmen, remedial, dan pengayaan.', value: c.lampiran },
            ];

            const renderRow = (row) => `
                <tr>
                    <td>${row.no || ''}</td>
                    <td>${row.desc ? `${row.desc}` : ''}</td>
                    <td class="skor-col">${mark(row.value, 0)}</td>
                    <td class="skor-col">${mark(row.value, 1)}</td>
                    <td class="skor-col">${mark(row.value, 2)}</td>
                    <td class="skor-col">${mark(row.value, 3)}</td>
                    <td class="skor-col">${mark(row.value, 4)}</td>
                    <td class="catatan-col"></td>
                </tr>
            `;

            const tahunPelajaran = (data.semester || '').replace(/ganjil|genap/gi, '').trim();

            const allValues = Object.values(c);
            const count0 = allValues.filter(v => v == 0).length;
            const count1 = allValues.filter(v => v == 1).length;
            const count2 = allValues.filter(v => v == 2).length;
            const count3 = allValues.filter(v => v == 3).length;
            const count4 = allValues.filter(v => v == 4).length;

            return `
                <div class="atp-template rpp-template">
                    <div class="center title">INSTRUMEN VERIFIKASI DAN VALIDASI</div>
                    <div class="center title">RENCANA PELAKSANAAN PEMBELAJARAN (RPP)</div>
                    <div class="center title">TAHUN AJARAN ${tahunPelajaran}</div>

                    <table class="identitas-table">
                        <tr><td>Nama Sekolah</td><td>:</td><td><strong>SMK Negeri 2 Semarang</strong></td></tr>
                        <tr><td>Nama Guru</td><td>:</td><td><strong>${data.nama_guru}</strong></td></tr>
                        <tr><td>Mata Pelajaran</td><td>:</td><td><strong>${data.mapel}</strong></td></tr>
                        <tr><td>Kelas/ Program Keahlian/ Konsentrasi Keahlian</td><td>:</td><td><strong>${data.tingkat} / ${data.jurusan}</strong></td></tr>
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
                            <tr class="group-header"><td>I</td><td colspan="7">Identitas</td></tr>
                            ${rowsA.map(renderRow).join('')}
                            <tr class="group-header"><td>II</td><td colspan="7">Tujuan Pembelajaran</td></tr>
                            ${rowsB.map(renderRow).join('')}
                            <tr class="group-header"><td>III</td><td colspan="7">Kegiatan Pembelajaran</td></tr>
                            ${KP.map(renderRow).join('')}
                            <tr class="group-header"><td>IV</td><td colspan="7">Asesmen</td></tr>
                            ${asesmen.map(renderRow).join('')}
                            <tr class="group-header" style="page-break-before: always; break-before: page;"><td>V</td><td colspan="7">Lampiran</td></tr>
                            ${rowsC.map(renderRow).join('')}
                            <tr>
                                <td colspan="2"><strong>JUMLAH SKOR</strong></td>
                                <td class="skor-col">${count0 * 0}</td>
                                <td class="skor-col">${count1 * 1}</td>
                                <td class="skor-col">${count2 * 2}</td>
                                <td class="skor-col">${count3 * 3}</td>
                                <td class="skor-col">${count4 * 4}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>NILAI</strong></td>
                                <td colspan="3" class="center"><strong>${data.nilai}</strong></td>
                                <td colspan="3"><strong>PREDIKAT : ${data.predikat.toUpperCase()}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div>
                        <strong>Review Kepala Sekolah</strong>
                        <div class="review-box">${escapeHtml(data.catatan).replace(/\n/g, '<br>')}</div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div class="keterangan" style="margin-top: 16px; border: 1px solid #000; padding: 6px;">
                            Keterangan:<br>
                            1. Nilai = (Skor perolehan : skor maksimal) x 100<br>
                            2. Predikat:<br>
                            &nbsp;&nbsp;Nilai 91 - 100 = Amat baik<br>
                            &nbsp;&nbsp;Nilai 81 - 90 = Baik<br>
                            &nbsp;&nbsp;Nilai 71 - 80 = Cukup<br>
                            &nbsp;&nbsp;Nilai &le; 70 = Kurang
                        </div>

                        <div class="ttd" style="float: none; margin-top: 16px;">
                            Semarang, ${tanggalCetak}<br>
                            Kepala Sekolah,<br>
                            <img src="{{ asset('assets/images/ttd/ttd_stempel.png') }}" alt="Tanda Tangan dan Stempel" style="height: 140px; display: block; margin-left: -80px; margin-top: -20px; position: relative; top: 20px;">
                            Nana Mulyana, S.P., M.Si.<br>
                            Pembina Tk.I, IV/b<br>
                            NIP. 19690601 199203 1 012
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="atp-modal-actions">
                        <button class="btn-atp-print" onclick="printRppModal()">
                            <i class="fa fa-print"></i> Cetak / Print
                        </button>
                        <button class="btn-atp-close" onclick="closeRppModal()">
                            <i class="fa fa-times"></i> Tutup
                        </button>
                    </div>
                </div>
            `;
        }

        function closeRppModal() {
            document.getElementById('rppModal').classList.remove('show');
        }

        function printRppModal() {
            const originalTitle = document.title;
            if (lastRppData) {
                document.title = `Verifikasi RPP - ${lastRppData.nama_guru} - ${lastRppData.mapel}`;
            }
            window.onafterprint = () => {
                document.title = originalTitle;
                window.onafterprint = null;
            };
            window.print();
        }

        window.addEventListener('click', function (event) {
            const atpModal = document.getElementById('atpModal');
            const rppModal = document.getElementById('rppModal');
            if (event.target === atpModal) {
                closeAtpModal();
            }
            if (event.target === rppModal) {
                closeRppModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAtpModal();
                closeRppModal();
            }
        });
    </script>
@endsection
