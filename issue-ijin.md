# Issue: Tampilkan Daftar Ajuan Ijin + Tombol Detail & Aksi Terima/Tolak

## Tujuan

Mengubah halaman **manajemen Ijin** agar:

1. Pada `function index()`, menampilkan data tabel **ijin** yang sudah di-**join** dengan tabel **status_ijin** dan **siswa** (bukan menampilkan ID mentah seperti sekarang).
2. Pada **kolom paling kanan** setiap baris, ada tombol **Detail**.
3. Di **halaman Detail**, ada tombol untuk **Terima** atau **Tolak** ajuan ijin. Saat ditekan, status ijin berubah menjadi *Diterima* / *Ditolak*.

---

## Konteks Teknis (WAJIB DIBACA DULU)

Proyek ini Laravel dengan struktur modular di `app/Modules/`.

> **PENTING — ada DUA file `IjinController.php`. Jangan tertukar.**
> - ❌ [app/Http/Controllers/IjinController.php](app/Http/Controllers/IjinController.php) → ini versi **API JSON** untuk aplikasi siswa. **JANGAN diubah.**
> - ✅ [app/Modules/Ijin/Controllers/IjinController.php](app/Modules/Ijin/Controllers/IjinController.php) → ini versi **web/admin** yang punya tampilan blade. **File inilah yang dikerjakan.**

File-file yang relevan:

| Hal | Lokasi |
|-----|--------|
| Controller yang diubah | [IjinController.php](app/Modules/Ijin/Controllers/IjinController.php) |
| View tabel (index) | [ijin.blade.php](app/Modules/Ijin/Views/ijin.blade.php) |
| View detail | [ijin_detail.blade.php](app/Modules/Ijin/Views/ijin_detail.blade.php) |
| Routes | [routes.php](app/Modules/Ijin/routes.php) |
| Model Ijin | [Ijin.php](app/Modules/Ijin/Models/Ijin.php) |

### Skema Tabel & Relasi

```
ijin
  - id
  - id_jenis_ijin   -> jenis_ijin.id
  - id_siswa        -> siswa.id
  - id_status_ijin  -> status_ijin.id
  - lama_ijin
  - tgl_mulai
  - tgl_selesai
  - surat           (nama file surat, opsional)
  - created_at

siswa
  - id
  - nama_siswa
  - nisn

status_ijin
  - id
  - status_ijin     (contoh nilai: "Menunggu", "Diterima", "Ditolak")

jenis_ijin
  - id
  - jenis_ijin      (contoh: "Sakit", "Ijin")
```

> Relasi Eloquent **sudah ada** di model [Ijin.php](app/Modules/Ijin/Models/Ijin.php): `siswa()`, `statusIjin()`, `jenisIjin()`. Boleh dipakai bila perlu.

### Nilai status_ijin (PENTING — verifikasi dulu)

Nilai status disimpan sebagai teks di kolom `status_ijin.status_ijin`. Yang pasti ada: **"Menunggu"** (dipakai saat siswa mengajukan).

**Sebelum coding, cek dulu** nilai apa saja yang ada di tabel, dengan menjalankan query di database (phpMyAdmin):

```sql
SELECT id, status_ijin FROM status_ijin;
```

- Jika **belum ada** baris untuk "Diterima" dan "Ditolak", tambahkan dulu lewat menu **Status Ijin** di aplikasi (route `statusijin.create`), atau via SQL `INSERT`. Tulis persis: `Diterima` dan `Ditolak`.
- Gunakan **teks persis** yang ada di tabel di langkah-langkah berikut (case-sensitive). Contoh di bawah memakai `"Diterima"` dan `"Ditolak"` — sesuaikan jika berbeda.

---

## Tahapan Implementasi

### Langkah 1 — Ubah `function index()` di Controller

Ganti isi `function index()` pada [IjinController.php](app/Modules/Ijin/Controllers/IjinController.php#L26-L37) agar mengambil data ijin yang sudah di-join dengan `siswa` dan `status_ijin` (dan `jenis_ijin` supaya jenis ijin tampil), lalu tetap memakai pagination.

```php
public function index(Request $request)
{
    $query = Ijin::query()
        ->join('siswa', 'ijin.id_siswa', '=', 'siswa.id')
        ->join('status_ijin', 'ijin.id_status_ijin', '=', 'status_ijin.id')
        ->join('jenis_ijin', 'ijin.id_jenis_ijin', '=', 'jenis_ijin.id')
        ->select(
            'ijin.id',
            'siswa.nama_siswa',
            'siswa.nisn',
            'jenis_ijin.jenis_ijin',
            'status_ijin.status_ijin',
            'ijin.lama_ijin',
            'ijin.tgl_mulai',
            'ijin.tgl_selesai'
        );

    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where('siswa.nama_siswa', 'like', "%$search%");
    }

    $data['data'] = $query->orderBy('ijin.created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

    $this->log($request, 'melihat halaman manajemen data ' . $this->title);
    return view('Ijin::ijin', array_merge($data, ['title' => $this->title]));
}
```

> Catatan: karena memakai `join` + `select`, setiap baris hasil sudah punya properti `nama_siswa`, `status_ijin`, `jenis_ijin`, dll. yang dipakai langsung di view (Langkah 2). Kolom `ijin.id` tetap di-select karena dipakai untuk tombol Detail.

### Langkah 2 — Ubah View tabel `ijin.blade.php`

Pada [ijin.blade.php](app/Modules/Ijin/Views/ijin.blade.php), ganti header tabel dan isi baris agar menampilkan data join (bukan ID mentah), serta sederhanakan kolom **Aksi** menjadi hanya tombol **Detail** di kolom paling kanan.

Ganti bagian `<thead>` (baris 46-57) menjadi:

```blade
<thead>
    <tr>
        <th width="15">No</th>
        <td>Nama Siswa</td>
        <td>NISN</td>
        <td>Jenis Ijin</td>
        <td>Tgl Mulai</td>
        <td>Tgl Selesai</td>
        <td>Lama Ijin</td>
        <td>Status</td>
        <th width="10%">Aksi</th>
    </tr>
</thead>
```

Ganti bagian `<tbody>` (baris 59-81) menjadi:

```blade
<tbody>
    @php $no = $data->firstItem(); @endphp
    @forelse ($data as $item)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $item->nama_siswa }}</td>
            <td>{{ $item->nisn }}</td>
            <td>{{ $item->jenis_ijin }}</td>
            <td>{{ $item->tgl_mulai }}</td>
            <td>{{ $item->tgl_selesai }}</td>
            <td>{{ $item->lama_ijin }} hari</td>
            <td>{{ $item->status_ijin }}</td>
            <td>
                {!! button('ijin.show', 'Detail', $item->id) !!}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center"><i>No data.</i></td>
        </tr>
    @endforelse
</tbody>
```

> - Helper `button('ijin.show', 'Detail', $item->id)` sudah otomatis membuat link ke halaman detail. Lihat helper di [Functions.php](app/Helpers/Functions.php#L54).
> - `colspan` diubah ke **9** mengikuti jumlah kolom baru.
> - Tombol Tambah/Edit/Hapus dihapus dari tabel sesuai permintaan (kolom kanan hanya Detail). Jika ingin tetap ada, bodoh tidak masalah, tapi minimal Detail wajib ada.

### Langkah 3 — Tambahkan Route untuk Terima & Tolak

Pada [routes.php](app/Modules/Ijin/routes.php), tambahkan 2 route baru **di dalam** group yang sudah ada (setelah baris route `show`):

```php
Route::patch('/ijin/{ijin}/approve', 'approve')->name('approve');
Route::patch('/ijin/{ijin}/reject', 'reject')->name('reject');
```

> Letakkan route `approve`/`reject` **sebelum** atau di antara route lain selama tidak bentrok. Pola `{ijin}` memakai route-model-binding (Laravel otomatis mencari record Ijin berdasarkan id).

### Langkah 4 — Tambahkan method `approve()` & `reject()` di Controller

Tambahkan dua method baru di [IjinController.php](app/Modules/Ijin/Controllers/IjinController.php) (boleh diletakkan setelah method `show()`).

```php
public function approve(Request $request, Ijin $ijin)
{
    $status = StatusIjin::where('status_ijin', 'Diterima')->first();
    if (! $status) {
        return back()->with('message_error', 'Status "Diterima" belum ada di tabel status_ijin.');
    }

    $ijin->id_status_ijin = $status->id;
    $ijin->updated_by = Auth::id();
    $ijin->save();

    $this->log($request, 'menerima ajuan ' . $this->title, ['ijin.id' => $ijin->id]);
    return redirect()->route('ijin.index')->with('message_success', 'Ajuan ijin diterima!');
}

public function reject(Request $request, Ijin $ijin)
{
    $status = StatusIjin::where('status_ijin', 'Ditolak')->first();
    if (! $status) {
        return back()->with('message_error', 'Status "Ditolak" belum ada di tabel status_ijin.');
    }

    $ijin->id_status_ijin = $status->id;
    $ijin->updated_by = Auth::id();
    $ijin->save();

    $this->log($request, 'menolak ajuan ' . $this->title, ['ijin.id' => $ijin->id]);
    return redirect()->route('ijin.index')->with('message_success', 'Ajuan ijin ditolak!');
}
```

> Import `StatusIjin` dan `Auth` **sudah ada** di bagian atas controller. Tidak perlu menambah `use` baru.

### Langkah 5 — Ubah View Detail `ijin_detail.blade.php` + tombol Terima/Tolak

Pada [ijin_detail.blade.php](app/Modules/Ijin/Views/ijin_detail.blade.php):

**(a)** Perbaiki tampilan data. Saat ini view menampilkan `->id` (salah). Ganti bagian isi detail (baris 34-39) menjadi nilai yang benar lewat relasi:

```blade
<div class='col-lg-2'><p>Nama Siswa</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->siswa->nama_siswa }}</p></div>
<div class='col-lg-2'><p>Jenis Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->jenisIjin->jenis_ijin }}</p></div>
<div class='col-lg-2'><p>Lama Ijin</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->lama_ijin }} hari</p></div>
<div class='col-lg-2'><p>Tgl Mulai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_mulai }}</p></div>
<div class='col-lg-2'><p>Tgl Selesai</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->tgl_selesai }}</p></div>
<div class='col-lg-2'><p>Status</p></div><div class='col-lg-10'><p class='fw-bold'>{{ $ijin->statusIjin->status_ijin }}</p></div>
```

> Juga ganti `{{ $ijin->nama }}` di baris 18 dan 28 (header/breadcrumb) menjadi `{{ $ijin->siswa->nama_siswa }}` karena kolom `nama` tidak ada di tabel ijin.

**(b)** Tambahkan tombol **Terima** dan **Tolak** di bawah data (sebelum penutup `</div>` card-body, sekitar baris 42). Tombol hanya muncul bila status masih **"Menunggu"** (supaya tidak bisa di-approve dua kali):

```blade
@if ($ijin->statusIjin->status_ijin === 'Menunggu')
    <div class="row mt-4">
        <div class="col-lg-10 offset-lg-2">
            <form action="{{ route('ijin.approve', $ijin->id) }}" method="post" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-check"></i> Terima
                </button>
            </form>
            <form action="{{ route('ijin.reject', $ijin->id) }}" method="post" class="d-inline"
                  onsubmit="return confirm('Yakin tolak ajuan ijin ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger">
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
```

> `@csrf` dan `@method('PATCH')` **wajib** ada — Laravel menolak form tanpa CSRF token, dan route memakai method PATCH.

### Langkah 6 — Uji Coba Manual

1. Pastikan tabel `status_ijin` punya baris `Menunggu`, `Diterima`, `Ditolak` (lihat bagian "Nilai status_ijin" di atas).
2. Login ke aplikasi, buka menu **Ijin** (route `ijin.index`).
3. Pastikan tabel menampilkan **Nama Siswa, NISN, Jenis Ijin, tanggal, lama, dan Status** (bukan ID).
4. Klik tombol **Detail** pada salah satu baris → halaman detail terbuka dengan data lengkap.
5. Pada ajuan berstatus "Menunggu", tekan **Terima** → kembali ke daftar, status berubah jadi "Diterima".
6. Ulangi dengan ajuan lain, tekan **Tolak** → status jadi "Ditolak".
7. Buka detail ajuan yang sudah diproses → tombol Terima/Tolak **tidak muncul lagi**.
8. Pastikan tidak ada error PHP (cek `storage/logs/laravel.log`).

---

## Kriteria Selesai (Acceptance Criteria)

- [ ] `function index()` di **module** controller menampilkan data hasil join `ijin` + `siswa` + `status_ijin` (+ `jenis_ijin`).
- [ ] Tabel menampilkan nama siswa & status dalam bentuk teks, bukan ID/UUID.
- [ ] Kolom paling kanan punya tombol **Detail** yang mengarah ke halaman detail.
- [ ] Halaman detail menampilkan data ajuan dengan benar (bukan ID).
- [ ] Ada tombol **Terima** & **Tolak** di halaman detail, dan hanya muncul saat status "Menunggu".
- [ ] Menekan Terima/Tolak mengubah `id_status_ijin` di database dan menampilkan pesan sukses.
- [ ] Tidak ada error di `laravel.log`.
- [ ] File [app/Http/Controllers/IjinController.php](app/Http/Controllers/IjinController.php) (versi API) **tidak diubah**.

---

## Catatan & Potensi Jebakan

1. **Salah file controller**: kerjakan yang di `app/Modules/Ijin/`, BUKAN yang di `app/Http/Controllers/`.
2. **Nama tabel/kolom persis**: `status_ijin`, `jenis_ijin` pakai underscore; kolom nama siswa adalah `nama_siswa` (bukan `nama`).
3. **Teks status case-sensitive**: query `where('status_ijin', 'Diterima')` harus sama persis dengan isi di tabel. Cek dulu lewat SQL.
4. **Wajib `@csrf` + `@method('PATCH')`** di form Terima/Tolak, kalau tidak akan error 419 / 405.
5. **Soft delete**: model Ijin pakai SoftDeletes. Eloquent (`Ijin::query()`) otomatis memfilter `deleted_at`, jadi aman. (Beda dengan query DB facade mentah.)
6. **Jangan ubah** method lain (`create`, `store`, `edit`, `update`, `destroy`) kecuali memang diperlukan untuk fitur ini.
