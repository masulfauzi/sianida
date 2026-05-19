# Panduan Fitur Import Soal dari Excel

## Deskripsi Fitur
Fitur Import Soal memungkinkan guru untuk secara cepat menambahkan banyak soal ujian semester sekaligus melalui file Excel, tanpa perlu menginput satu per satu melalui form.

---

## ⚠️ Penting Diketahui

**Fitur import akan menghapus SEMUA soal yang sudah ada pada ujian tersebut dan menggantinya dengan soal dari file import!**

Pastikan Anda yakin sebelum melakukan import, karena soal lama tidak bisa dikembalikan (kecuali ada backup database).

## Cara Menggunakan

### 1. Persiapan
- Buka halaman "Data Pengumpulan Soal Ujian Semester" (Ujian Semester → Upload)
- Pastikan Kisi-Kisi dan Norma Penilaian sudah diupload terlebih dahulu
- Tombol "Import Soal" akan muncul pada baris "Soal"
- **PERHATIAN:** Soal lama akan dihapus, pastikan sudah backup jika perlu

### 2. Download Template
- Klik tombol "Import Soal"
- Modal akan terbuka menampilkan instruksi format Excel
- Template Excel tersedia di: `/public/templates/Template_Soal_Semester.xlsx`

### 3. Isi File Excel
Gunakan template yang sudah disediakan dengan struktur kolom sebagai berikut:

| Kolom | Field | Keterangan |
|-------|-------|-----------|
| A | No Soal | Nomor urut soal (wajib, harus unik) |
| B | Soal | Pertanyaan soal (wajib) |
| C | Opsi A | Pilihan jawaban A (wajib) |
| D | Opsi B | Pilihan jawaban B (wajib) |
| E | Opsi C | Pilihan jawaban C (wajib) |
| F | Opsi D | Pilihan jawaban D (wajib) |
| G | Opsi E | Pilihan jawaban E (wajib) |
| H | Kunci | Jawaban benar (A/B/C/D/E, wajib) |
| I | Jenis | Tidak digunakan (opsional) |
| J | Gambar Soal | Nama file gambar soal (opsional) |
| K | File2 | Tidak digunakan (opsional) |
| L | Gambar Opsi A | Nama file gambar untuk opsi A (opsional) |
| M | Gambar Opsi B | Nama file gambar untuk opsi B (opsional) |
| N | Gambar Opsi C | Nama file gambar untuk opsi C (opsional) |
| O | Gambar Opsi D | Nama file gambar untuk opsi D (opsional) |
| P | Gambar Opsi E | Nama file gambar untuk opsi E (opsional) |

### 4. Upload File
1. Klik tombol "Import Soal" di halaman upload ujian
2. Modal akan terbuka
3. Klik "Pilih File" dan pilih file Excel yang sudah disiapkan
4. Klik tombol "Submit"
5. Tunggu proses import selesai

### 5. Verifikasi Hasil
- Jika berhasil: Halaman akan otomatis di-refresh dan menampilkan pesan sukses
- Jika ada error: Pesan error akan ditampilkan di modal, perbaiki dan coba lagi

---

## Contoh Format Excel

### Contoh 1: Soal Pilihan Ganda Sederhana
```
No Soal | Soal                      | Opsi A  | Opsi B  | Opsi C  | Opsi D  | Opsi E  | Kunci
1       | Ibu kota Indonesia adalah | Jakarta | Bandung | Medan   | Makassar | Surabaya| A
2       | Laut terdalam dunia adalah| Merah   | Hitam   | Mati    | Banda   | Pasifik | E
```

### Contoh 2: Soal dengan Gambar
```
No Soal | Soal          | Opsi A | ... | Kunci | ... | Gambar Soal
1       | Gambar apa ini| Cat    | ... | B     | ... | kucing.jpg
2       | Warna apa ini | Merah  | ... | A     | ... | merah.png
```

---

## Validasi Data

Sistem akan melakukan validasi terhadap data yang diimport:

### ✓ Data Valid
- Nomor soal tidak kosong
- Pertanyaan soal tidak kosong
- Kunci jawaban harus salah satu dari: A, B, C, D, E

### ✗ Data Invalid (akan ditolak)
- Nomor soal kosong
- Pertanyaan soal kosong
- Kunci jawaban tidak sesuai format (misalnya: F, Z, kosong)
- File bukan Excel (.xlsx atau .xls)

---

## Troubleshooting

### Error: "File harus berformat Excel (.xlsx atau .xls)"
**Solusi:** Pastikan file benar-benar Excel format. Jika file dibuat dengan Google Sheets, export dalam format Excel terlebih dahulu.

### Error: "Error pada baris X: No soal tidak boleh kosong"
**Solusi:** Cek baris X di file Excel, pastikan kolom No Soal tidak kosong. Baris header tidak dihitung.

### Error: "Error pada baris X: Kunci harus A, B, C, D, atau E"
**Solusi:** Cek nilai kunci jawaban pada baris X, harus berupa huruf A-E. Jangan menggunakan angka atau huruf lain.

### Error: "Tidak ada data soal yang valid untuk diimport"
**Solusi:** Semua baris data memiliki error validasi. Pastikan minimal 1 baris data valid.

---

## Fitur Keamanan

1. **Autentikasi:** Hanya user yang login yang bisa mengakses fitur import
2. **Autorisasi:** Guru hanya bisa mengimport soal ujian miliknya sendiri (kecuali admin)
3. **Validasi Input:** Semua data divalidasi sebelum disimpan ke database
4. **Database Transaction:** Jika ada error, semua perubahan akan dibatalkan (tidak sebagian)

---

## Tips & Trik

### 1. Bulk Import Besar
Jika memiliki 1000+ soal, bagi menjadi beberapa file kecil dan import satu per satu untuk performa lebih baik.

### 2. Format Soal yang Baik
- Nomor soal: gunakan angka berurutan (1, 2, 3, ...)
- Soal: tulis dengan jelas dan singkat
- Opsi: hindari opsi yang terlalu panjang

### 3. Kolom Gambar
- Opsional, cukup isikan nama file jika ada gambar
- Pastikan nama file sesuai dengan file gambar yang akan diunggah

---

## Spesifikasi Teknis

### Endpoint
- **URL:** `/ujiansemester/import`
- **Method:** POST
- **Middleware:** auth (memerlukan login)

### Request Format
```
Content-Type: multipart/form-data

Parameters:
- id_ujiansemester: UUID (required)
- file: File Excel (required, max 10MB)
```

### Response Format
```json
Success:
{
  "success": true,
  "message": "Import berhasil! 150 soal telah ditambahkan.",
  "count": 150
}

Error:
{
  "success": false,
  "message": "Error pada baris 5: Kunci harus A, B, C, D, atau E",
  "errors": [...]
}
```

---

## Kontakt & Support

Untuk pertanyaan atau masalah teknis, hubungi admin sistem.

**Terakhir diperbarui:** 19 Mei 2026
