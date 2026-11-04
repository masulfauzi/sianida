@extends('layouts.app')

@section('page-css')
<style>
    .surat-rotate-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 520px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    #surat-img { max-height: 500px; max-width: 100%; transition: transform .2s; }
</style>
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
                            <div class='col-lg-2'><p>Kelas</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $kelas }}</p></div>
                            <div class='col-lg-2'><p>No. HP</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->siswa->no_hp }}</p></div>
                            <div class='col-lg-2'><p>Jenis Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->jenisIjin->jenis_ijin }}</p></div>
                            <div class='col-lg-2'><p>Lama Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->lama_ijin }} hari</p></div>
                            <div class='col-lg-2'><p>Tgl Mulai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_mulai }}</p></div>
                            <div class='col-lg-2'><p>Tgl Selesai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_selesai }}</p></div>
                            <div class='col-lg-2'><p>Status</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->statusIjin->status_ijin }}</p></div>
                            @if ($ijin->alasan_tolak)
                                <div class='col-lg-2'><p>Alasan Ditolak</p></div><div class='col-lg-10'><p class='fw-bold text-danger'>{!! nl2br(e($ijin->alasan_tolak)) !!}</p></div>
                            @endif
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
                                <div class="surat-rotate-wrapper">
                                    <img src="{{ asset('surat_ijin/' . $ijin->surat) }}"
                                         alt="Surat Keterangan"
                                         id="surat-img">
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="putarSurat(-90)">
                                        <i class="fa fa-undo"></i> Putar kiri
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="putarSurat(90)">
                                        <i class="fa fa-redo"></i> Putar kanan
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="resetSurat()">
                                        Reset
                                    </button>
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
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolakIjin">
                                <i class="fa fa-times"></i> Tolak
                            </button>
                        </div>
                    </div>

                    <div class="modal fade" id="modalTolakIjin" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="form-reject" action="{{ route('ijin.reject.update', $ijin->id) }}" method="post" class="modal-content">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title">Tolak ajuan ijin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Ajuan ijin <strong>{{ $ijin->siswa->nama_siswa }}</strong> akan ditolak.</p>
                                    <label for="alasan_tolak" class="form-label">Alasan penolakan</label>
                                    <textarea name="alasan_tolak" id="alasan_tolak" rows="4" maxlength="1000" required
                                        class="form-control @error('alasan_tolak') is-invalid @enderror"
                                        placeholder="Tuliskan alasan ajuan ini ditolak">{{ old('alasan_tolak') }}</textarea>
                                    @error('alasan_tolak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> Tolak Ijin</button>
                                </div>
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

    @if ($errors->has('alasan_tolak'))
        document.addEventListener('DOMContentLoaded', function () {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTolakIjin')).show();
        });
    @endif

    var rotasiSurat = 0;

    function terapkanRotasiSurat() {
        var img = document.getElementById('surat-img');
        if (!img) return;
        var wrapper = img.parentElement;
        var miring = Math.abs(rotasiSurat % 180) === 90;

        img.style.transform = 'rotate(' + rotasiSurat + 'deg)';
        // Saat diputar 90°/270°, lebar dan tinggi gambar tertukar, jadi batas ukurannya ikut ditukar
        img.style.maxWidth = miring ? '500px' : '';
        img.style.maxHeight = miring ? wrapper.clientWidth + 'px' : '';
    }

    function putarSurat(delta) {
        rotasiSurat = (rotasiSurat + delta) % 360;
        terapkanRotasiSurat();
    }

    function resetSurat() {
        rotasiSurat = 0;
        terapkanRotasiSurat();
    }
</script>
@endsection
