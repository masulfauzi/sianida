<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | {{ $title }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/main/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/fontawesome.css') }}">
</head>

<body>
    <div class="container py-5">
        <section class="section">
            <div class="card">
                <h6 class="card-header">
                    Detail Data {{ $title }}: {{ $ksp->nama_ksp }}
                </h6>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="row">
                                <div class='col-lg-2'><p>Semester</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ksp->semester->semester }}</p></div>
                                <div class='col-lg-2'><p>Nama Ksp</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ksp->nama_ksp }}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="card">
                <h6 class="card-header">
                    Daftar File Ksp
                </h6>
                <div class="card-body">
                    <div class="table-responsive-md col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="15">No</th>
                                    <td>Nama File</td>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse ($files as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->nama_file }}</td>
                                        <td>
                                            <a target="_blank" href="{{ url('download/ksp/file_ksp/'.$item->file) }}" class="btn btn-sm icon icon-left btn-outline-dark"><i class="fa fa-download"></i> Download</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center"><i>No data.</i></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
