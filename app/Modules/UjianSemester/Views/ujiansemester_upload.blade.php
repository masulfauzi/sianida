@extends('layouts.app')

@section('page-css')
@endsection

@section('main')
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-2">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Pengumpulan Soal Ujian Semester</h3>
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
                    Tabel Data {{ $title }}
                </h6>
                <div class="card-body">
                    @include('include.flash')
                    <div class="table-responsive-md col-12">
                        <table class="table" id="table1">
                            <thead>
                                <tr align="center">
                                    <th width="15">No</th>
                                    <th>Kelengkapan</th>
                                    <th>File Sekarang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td>Kisi-Kisi</td>
                                    <td align="center">
                                        @if ($data->kisi_kisi)
                                            <a
                                                href="JavaScript:newPopup('{{ url('/gurumapel/' . $data->kisi_kisi . '/lihat/kisikisi') }}');">
                                                <img src="{{ asset('assets/images/icon/check.png') }}" alt="">
                                            </a>
                                        @else
                                            <img src="{{ asset('assets/images/icon/cross.png') }}" alt="">
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('ujiansemester.aksi_upload.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $data->id }}">
                                            <input type="hidden" name="jenis" value="kisikisi">
                                            <input type="file" name="file" class="form-control">
                                            <button type="submit" class="btn btn-primary mt-1">Upload</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Norma Penilaian</td>
                                    <td align="center">
                                        @if ($data->norma_penilaian)
                                            <a
                                                href="JavaScript:newPopup('{{ url('/gurumapel/' . $data->norma_penilaian . '/lihat/norma') }}');">
                                                <img src="{{ asset('assets/images/icon/check.png') }}" alt="">
                                            </a>
                                        @else
                                            <img src="{{ asset('assets/images/icon/cross.png') }}" alt="">
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->kisi_kisi)
                                            <form action="{{ route('ujiansemester.aksi_upload.store') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                <input type="hidden" name="jenis" value="norma">
                                                <input type="file" name="file" class="form-control">
                                                <button type="submit" class="btn btn-primary mt-1">Upload</button>
                                            </form>
                                        @else
                                            Kisi-Kisi belum di upload.
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Soal</td>
                                    <td align="center">
                                        @if ($soal->count() > 0)
                                            <a href="javascript:void(0);"
                                                onclick="window.open('{{ route('soalsemester.lihat_soal.index', [$data->id, 'c365b003-7203-4e5d-b215-1f934238db2f']) }}', '_blank', 'width=auto,height=auto');">
                                                {{-- <a
                                                    href="{{ route('soal.lihat_soal.index', [$data->id, 'c365b003-7203-4e5d-b215-1f934238db2f']) }}">
                                                    --}}
                                                    <img src="{{ asset('assets/images/icon/check.png') }}" alt="">
                                                </a>
                                        @else
                                                <img src="{{ asset('assets/images/icon/cross.png') }}" alt="">
                                            @endif
                                    </td>
                                    <td>
                                        @if ($data->norma_penilaian)
                                            <a href="{{ route('soalsemester.input.create', array('id_ujiansemester' => $data->id, 'no_soal' => '1')) }}"
                                                class="btn btn-primary">Input Soal</a>
                                            <button type="button" class="btn btn-success" id="btn-import-soal"
                                                data-id="{{ $data->id }}">Import Soal</button>
                                        @else
                                            Kunci & Norma belum di upload.
                                        @endif

                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>4</td>
                                    <td>Soal Susulan</td>
                                    <td align="center">
                                        @if ($soal_susulan->count() > 0)
                                        <a href="javascript:void(0);"
                                            onclick="window.open('{{ route('soal.lihat_soal.index', [$data->id, '068aa935-e996-4f86-9689-3da4a9aee8f5']) }}', '_blank', 'width=auto,height=auto');">
                                            <img src="{{ asset('assets/images/icon/check.png') }}" alt="">
                                        </a>
                                        @else
                                        <img src="{{ asset('assets/images/icon/cross.png') }}" alt="">
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->norma_penilaian)
                                        <a href="{{ route('soal.input_soal.create', array('id_ujian' => $data->id, 'id_jenissoal' => '068aa935-e996-4f86-9689-3da4a9aee8f5', 'no_soal' => '1')) }}"
                                            class="btn btn-primary">Input Soal</a>
                                        <hr>
                                        <form action="{{ route('ujiansekolah.guru.upload_excel.index') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $data->id }}">
                                            <input type="hidden" name="id_jenissoal"
                                                value="068aa935-e996-4f86-9689-3da4a9aee8f5">
                                            <input type="file" name="file" class="form-control">
                                            <button type="submit" class="btn btn-primary mt-1">Upload</button>
                                        </form>
                                        @else
                                        Kunci & Norma belum di upload.
                                        @endif

                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </section>
    </div>
    <!-- Modal Import Soal -->
    <div class="modal fade" id="modalImportSoal" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportLabel">Import Soal dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="import-message" class="alert" style="display:none;"></div>
                    <p class="text-muted small mb-2">
                        Format Excel harus sesuai dengan template berikut:<br>
                        A: No Soal | B: Soal | C-G: Opsi A-E | H: Kunci (A/B/C/D/E) | J: Gambar | L-P: Gambar A-E
                    </p>
                    <p class="mb-3">
                        <a href="{{ asset('templates/Template_Soal_Semester.xlsx') }}" class="btn btn-sm btn-info" download>
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </p>
                    <form id="formImportSoal">
                        @csrf
                        <input type="hidden" id="id_ujiansemester" name="id_ujiansemester">
                        <div class="form-group">
                            <label for="file-import">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file-import" name="file" accept=".xlsx,.xls"
                                required>
                            <small class="text-muted">Format: .xlsx atau .xls</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-submit-import">
                        <span id="btn-text">Submit</span>
                        <span id="btn-spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-js')
    <script type="text/javascript">
        // Popup window code
        function newPopup(url) {
            popupWindow = window.open(
                url, 'popUpWindow', 'height=300,width=400,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes')
        }
    </script>
@endsection

@section('inline-js')
    <script>
        $(document).ready(function () {
            // Open modal when Import Soal button is clicked
            $(document).on('click', '#btn-import-soal', function () {
                const id = $(this).data('id');
                $('#id_ujiansemester').val(id);
                $('#modalImportSoal').modal('show');
                $('#import-message').hide();
                $('#formImportSoal')[0].reset();
            });

            // Submit form
            $(document).on('click', '#btn-submit-import', function () {
                const fileInput = $('#file-import')[0];
                const idUjian = $('#id_ujiansemester').val();

                if (!fileInput.files || fileInput.files.length === 0) {
                    showMessage('error', 'Pilih file Excel terlebih dahulu!');
                    return;
                }

                const formData = new FormData();
                formData.append('id_ujiansemester', idUjian);
                formData.append('file', fileInput.files[0]);
                formData.append('_token', $('input[name="_token"]').val());

                // Disable button and show spinner
                $('#btn-submit-import').prop('disabled', true);
                $('#btn-text').hide();
                $('#btn-spinner').show();

                fetch('/ujiansemester/import', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('success', data.message);
                            setTimeout(() => {
                                $('#modalImportSoal').modal('hide');
                                location.reload();
                            }, 1500);
                        } else {
                            showMessage('error', data.message);
                        }
                    })
                    .catch(error => {
                        showMessage('error', 'Terjadi kesalahan: ' + error.message);
                    })
                    .finally(() => {
                        $('#btn-submit-import').prop('disabled', false);
                        $('#btn-text').show();
                        $('#btn-spinner').hide();
                    });
            });

            function showMessage(type, message) {
                const messageDiv = $('#import-message');
                messageDiv.removeClass('alert-success alert-danger alert-warning');
                messageDiv.addClass('alert-' + (type === 'success' ? 'success' : 'danger'));
                messageDiv.html(message);
                messageDiv.show();
            }
        });
    </script>
@endsection