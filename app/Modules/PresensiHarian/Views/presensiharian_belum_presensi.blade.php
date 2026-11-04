<div class="belum-presensi-wrapper" data-tingkat="{{ $tingkat }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">Siswa Belum Presensi Kelas {{ $tingkat }}</h6>
        @unless ($liburInfo)
            <span class="badge bg-danger">{{ $list->count() }} siswa</span>
        @endunless
    </div>

    @if ($liburInfo)
        <div class="alert alert-light-info mb-0">{{ $liburInfo }} &mdash; tidak ada presensi yang perlu dicek.</div>
    @else
        @if (count($kelasOff))
            <p class="text-muted small mb-2">Kelas OFF (tidak ditampilkan): {{ implode(', ', $kelasOff) }}</p>
        @endif

        @if ($list->isEmpty())
            <p class="mb-0"><i>Semua siswa sudah presensi.</i></p>
        @else
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <select class="form-select form-select-sm filter-belum-kelas">
                        <option value="">Semua kelas</option>
                        @foreach ($list->pluck('kelas', 'id_kelas')->unique() as $idKelas => $namaKelas)
                            <option value="{{ $idKelas }}">{{ $namaKelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm filter-belum-nama" placeholder="Cari nama siswa">
                </div>
                <div class="col-md-4 text-md-end small text-muted pt-1 filter-belum-info"></div>
            </div>
            <div style="max-height:400px; overflow-y:auto">
                <table class="table table-sm table-bordered mb-0 tabel-belum-presensi">
                    <thead style="position:sticky; top:0; z-index:1" class="table-light">
                        <tr>
                            <th style="width:60px">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $i => $s)
                            <tr data-kelas="{{ $s->id_kelas }}" data-nama="{{ strtolower($s->nama_siswa) }}">
                                <td class="no-urut">{{ $i + 1 }}</td>
                                <td>
                                    <a href="#" class="link-primary link-detail-siswa" data-url="{{ route('presensiharian.siswa.detail.show', $s->id_siswa) }}">{{ $s->nama_siswa }}</a>
                                </td>
                                <td>{{ $s->nis }}</td>
                                <td>{{ $s->kelas }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
