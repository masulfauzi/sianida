@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row mb-2">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <a href="{{ route('ijin.index') }}" class="btn btn-sm icon icon-left btn-outline-secondary"><i class="fa fa-arrow-left"></i> Kembali </a>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ijin.index') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $ijin->siswa->nama_siswa }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <h6 class="card-header">
                Detail Data {{ $title }}: {{ $ijin->siswa->nama_siswa }}
            </h6>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10 offset-lg-2">
                        <div class="row">
                            <div class='col-lg-2'><p>Nama Siswa</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->siswa->nama_siswa }}</p></div>
                            <div class='col-lg-2'><p>Jenis Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->jenisIjin->jenis_ijin }}</p></div>
                            <div class='col-lg-2'><p>Lama Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->lama_ijin }} hari</p></div>
                            <div class='col-lg-2'><p>Tgl Mulai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_mulai }}</p></div>
                            <div class='col-lg-2'><p>Tgl Selesai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_selesai }}</p></div>
                            <div class='col-lg-2'><p>Status</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->statusIjin->status_ijin }}</p></div>
                        </div>
                    </div>
                </div>

                {{-- Tampilan surat --}}
                <div class="row mt-4">
                    <div class="col-lg-10 offset-lg-2">
                        <p class="fw-semibold mb-2">Surat Keterangan</p>
                        @if ($ijin->surat)
                            @php
                                $ext = strtolower(pathinfo($ijin->surat, PATHINFO_EXTENSION));
                            @endphp
                            @if ($ext === 'pdf')
                                <iframe src="{{ asset('surat_ijin/' . $ijin->surat) }}"
                                        width="100%" height="500px"
                                        style="border: 1px solid #dee2e6; border-radius: 4px;">
                                </iframe>
                                <div class="mt-2">
                                    <a href="{{ asset('surat_ijin/' . $ijin->surat) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-external-link-alt"></i> Buka di tab baru
                                    </a>
                                </div>
                            @else
                                <img src="{{ asset('surat_ijin/' . $ijin->surat) }}"
                                     alt="Surat Keterangan"
                                     class="img-fluid"
                                     style="max-height: 500px; border: 1px solid #dee2e6; border-radius: 4px;">
                                <div class="mt-2">
                                    <a href="{{ asset('surat_ijin/' . $ijin->surat) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-external-link-alt"></i> Buka di tab baru
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-muted fst-italic">Tidak ada surat yang dilampirkan.</p>
                        @endif
                    </div>
                </div>

                {{-- Tombol aksi --}}
                @if ($ijin->statusIjin->status_ijin === 'Menunggu')
                    <div class="row mt-4">
                        <div class="col-lg-10 offset-lg-2">
                            <form id="form-approve" action="{{ route('ijin.approve.update', $ijin->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="btn btn-success" onclick="confirmApprove()">
                                    <i class="fa fa-check"></i> Terima
                                </button>
                            </form>
                            <form id="form-reject" action="{{ route('ijin.reject.update', $ijin->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="btn btn-danger" onclick="confirmReject()">
                                    <i class="fa fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="row mt-4">
                        <div class="col-lg-10 offset-lg-2">
                            <p class="text-muted">Ajuan ini sudah diproses (status: {{ $ijin->statusIjin->status_ijin }}).</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </section>
</div>
@endsection

@section('page-js')
@endsection

@section('inline-js')
<script>
    function confirmApprove() {
        swal({
            title: "Terima ajuan ijin?",
            text: "Ajuan ijin {{ $ijin->siswa->nama_siswa }} akan diterima.",
            icon: "info",
            buttons: { cancel: "Batal", confirm: { text: "Ya, Terima", value: true } },
        }).then(function (confirmed) {
            if (confirmed) {
                document.getElementById('form-approve').submit();
            }
        });
    }

    function confirmReject() {
        swal({
            title: "Tolak ajuan ijin?",
            text: "Ajuan ijin {{ $ijin->siswa->nama_siswa }} akan ditolak.",
            icon: "warning",
            buttons: { cancel: "Batal", confirm: { text: "Ya, Tolak", value: true } },
            dangerMode: true,
        }).then(function (confirmed) {
            if (confirmed) {
                document.getElementById('form-reject').submit();
            }
        });
    }
</script>
@endsection
