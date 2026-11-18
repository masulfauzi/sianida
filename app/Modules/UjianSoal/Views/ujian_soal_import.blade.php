@extends('layouts.app')

@section('main')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ujiansoal.monitoring.index') }}">Monitoring Bank Soal Ujian</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @php $hasil = session('import_hasil'); @endphp

        @if (session('message_success'))
            <div class="alert alert-success">{{ session('message_success') }}</div>
        @endif

        @if ($hasil)
            @if (! empty($hasil['duplikat']))
                <div class="alert alert-light-warning color-warning">
                    <i class="fa fa-info-circle"></i> Nomor soal duplikat di file, yang dipakai baris terakhir:
                    @foreach ($hasil['duplikat'] as $no => $jumlah)
                        <strong>No {{ $no }}</strong> ({{ $jumlah }}&times;){{ $loop->last ? '.' : ',' }}
                    @endforeach
                </div>
            @endif
            @if (! empty($hasil['nomor_bergambar']))
                <div class="alert alert-light-warning color-warning">
                    <i class="fa fa-image"></i> Gambar tidak ikut diimport. Soal berikut punya gambar di file Excel,
                    unggah gambarnya manual lewat edit soal:
                    <strong>No {{ implode(', ', $hasil['nomor_bergambar']) }}</strong>.
                    <a href="{{ route('ujiansoal.soal.index', $hasil['id_bank_soal']) }}">Buka daftar soal</a>
                </div>
            @endif
        @endif

        @if ($errors->any())
            <div class="alert alert-light-danger color-danger">
                <strong>Import dibatalkan, tidak ada soal yang disimpan.</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <h6 class="card-header">Upload File Excel Soal</h6>
            <div class="card-body">
                <form class="form form-horizontal" action="{{ route('ujiansoalmonitoring.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 text-sm-start text-md-end pt-2"><label>Bank Soal Tujuan</label></div>
                        <div class="col-md-9 form-group">
                            {{ Form::select('id_bank_soal', $refBank, $selected, ['class' => 'form-control select2', 'placeholder' => '-- Pilih bank soal --', 'required']) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 text-sm-start text-md-end pt-2"><label>File Excel</label></div>
                        <div class="col-md-9 form-group">
                            <input type="file" name="file" class="form-control" accept=".xls,.xlsx" required>
                            <small class="text-muted d-block mt-1">Format .xls / .xlsx, maksimal 10 MB.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9 offset-md-3 form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="konfirmasi" value="1" id="konfirmasi" required>
                                <label class="form-check-label" for="konfirmasi">
                                    Saya paham soal di bank ini dengan nomor yang sama akan <strong>ditimpa</strong> oleh isi Excel.
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="offset-md-3 ps-2">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-upload"></i> Import</button> &nbsp;
                        <a href="{{ route('ujiansoal.monitoring.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <h6 class="card-header">Format File</h6>
            <div class="card-body small">
                <p class="mb-2">Baris pertama adalah header. Kolom yang dibaca (nama kolom tidak peka huruf besar/kecil):</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-2">
                        <thead class="table-light">
                            <tr><th>No Soal</th><th>Soal</th><th>PilA</th><th>PilB</th><th>PilC</th><th>PilD</th><th>PilE</th><th>Jawaban</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>&lt;p&gt;Teks soal (boleh HTML)&lt;/p&gt;</td><td>Opsi A</td><td>Opsi B</td><td>Opsi C</td><td>Opsi D</td><td>Opsi E</td><td>B</td></tr>
                        </tbody>
                    </table>
                </div>
                <ul class="mb-0">
                    <li>Semua soal diimport sebagai <strong>pilihan ganda</strong> dengan satu jawaban (A–E).</li>
                    <li><strong>No Soal wajib diisi.</strong> Kolom Soal boleh kosong (mis. soal bergambar), soal tetap diimport.</li>
                    <li>Jika bank soal sudah punya soal dengan nomor yang sama, isinya diperbarui. Soal lain di bank tidak berubah.</li>
                    <li>Jika satu nomor muncul lebih dari sekali di file, yang dipakai baris terakhir.</li>
                    <li>Kolom Jenis dan file1/file2/fileA–fileE diabaikan. Gambar diunggah manual lewat edit soal.</li>
                    <li>Jika ada baris yang salah, seluruh import dibatalkan.</li>
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
