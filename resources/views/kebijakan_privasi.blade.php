<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            background-color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 0.95em;
        }

        .content {
            line-height: 1.8;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            font-size: 1.5em;
            color: #007bff;
            margin-bottom: 15px;
            margin-top: 20px;
        }

        .section h3 {
            font-size: 1.2em;
            color: #0056b3;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        .section p {
            margin-bottom: 12px;
            text-align: justify;
        }

        ul,
        ol {
            margin-left: 20px;
            margin-bottom: 12px;
        }

        ul li,
        ol li {
            margin-bottom: 8px;
        }

        .important-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }

        .last-updated {
            text-align: right;
            color: #999;
            font-size: 0.9em;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Kebijakan Privasi</h1>
            <p>Pedoman Privasi dan Perlindungan Data Pengguna</p>
        </div>

        <div class="last-updated">
            <p>Tanggal Berlaku: Februari 2026 | Versi: 1.0 | Status: Aktif</p>
        </div>

        <div class="content">
            <div class="section">
                <h2>1. Pendahuluan</h2>
                <p>
                    Kebijakan Privasi ini menjelaskan bagaimana aplikasi Skanida Student ("Aplikasi") mengumpulkan,
                    menggunakan, dan melindungi data pribadi pengguna. Kami berkomitmen untuk menjaga privasi dan
                    keamanan data Anda.
                </p>
            </div>

            <div class="section">
                <h2>2. Informasi yang Kami Kumpulkan</h2>
                <p>Aplikasi Skanida Student mengumpulkan informasi berikut:</p>

                <h3>2.1 Informasi Akun</h3>
                <ul>
                    <li>Nama pengguna (NISN/Username)</li>
                    <li>Kata sandi (disimpan terenkripsi)</li>
                    <li>Nama lengkap</li>
                    <li>User ID dari sistem sekolah</li>
                    <li>Token autentikasi</li>
                </ul>

                <h3>2.2 Data Akademik</h3>
                <ul>
                    <li>Riwayat presensi siswa</li>
                    <li>Data izin meninggalkan kelas</li>
                    <li>Riwayat izin (dengan status persetujuan)</li>
                    <li>Data presensi sholat</li>
                    <li>Riwayat izin sholat</li>
                </ul>

                <h3>2.3 Data Lokasi</h3>
                <ul>
                    <li>Lokasi geografis (hanya saat pengguna mengaktifkan fitur presensi lokasi)</li>
                    <li>Koordinat GPS untuk keperluan presensi</li>
                </ul>

                <h3>2.4 Data Perangkat</h3>
                <ul>
                    <li>Informasi perangkat (model, versi OS)</li>
                    <li>Identifier unik perangkat</li>
                    <li>Log aktivitas aplikasi</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. Bagaimana Kami Menggunakan Data</h2>
                <p>Data yang dikumpulkan digunakan untuk:</p>
                <ul>
                    <li><strong>Autentikasi dan Akses:</strong> Memverifikasi identitas pengguna dan memberikan akses ke
                        fitur aplikasi</li>
                    <li><strong>Manajemen Presensi:</strong> Mencatat dan melacak presensi siswa</li>
                    <li><strong>Manajemen Izin:</strong> Memproses permintaan izin dan menampilkan riwayat izin</li>
                    <li><strong>Pelaporan:</strong> Membuat laporan akademik dan administratif</li>
                    <li><strong>Komunikasi:</strong> Mengirimkan notifikasi penting terkait presensi dan izin</li>
                    <li><strong>Perbaikan Layanan:</strong> Menganalisis penggunaan aplikasi untuk perbaikan
                        berkelanjutan</li>
                    <li><strong>Keamanan:</strong> Mencegah penggunaan yang tidak sah dan melindungi dari ancaman
                        keamanan</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Penyimpanan Data</h2>

                <h3>4.1 Penyimpanan Lokal</h3>
                <p>Data berikut disimpan secara lokal di perangkat Anda menggunakan SharedPreferences:</p>
                <ul>
                    <li>Status login</li>
                    <li>Token autentikasi</li>
                    <li>Nama pengguna</li>
                    <li>ID pengguna</li>
                    <li>Informasi siswa</li>
                </ul>

                <h3>4.2 Penyimpanan Server</h3>
                <p>Data akademik dan riwayat disimpan di server aplikasi dengan enkripsi:</p>
                <ul>
                    <li>Semua komunikasi dengan server menggunakan protokol HTTPS</li>
                    <li>Data sensitif terenkripsi</li>
                </ul>

                <h3>4.3 Retensi Data</h3>
                <ul>
                    <li>Data sesi disimpan selama pengguna terdaftar</li>
                    <li>Data akademik disimpan sesuai kebijakan sekolah</li>
                    <li>Pengguna dapat menghapus data lokal dengan logout</li>
                </ul>
            </div>

            <div class="section">
                <h2>5. Keamanan Data</h2>
                <p>Kami mengimplementasikan langkah-langkah keamanan berikut:</p>
                <ul>
                    <li><strong>Enkripsi Transportasi:</strong> Semua data ditransmisikan melalui HTTPS</li>
                    <li><strong>Autentikasi Bearer Token:</strong> Setiap permintaan ke API dilindungi dengan token
                        autentikasi</li>
                    <li><strong>Penyimpanan Aman:</strong> Data sensitif disimpan dengan aman di perangkat</li>
                    <li><strong>Akses Terbatas:</strong> Hanya personel yang berwenang yang dapat mengakses data</li>
                </ul>
            </div>

            <div class="section">
                <h2>6. Pembagian Data dengan Pihak Ketiga</h2>
                <p>Kami <strong>tidak membagikan</strong> data pribadi Anda dengan pihak ketiga kecuali:</p>
                <ul>
                    <li>Dengan sekolah/institusi pendidikan untuk keperluan administrasi akademik</li>
                    <li>Ketika diperlukan oleh hukum atau otoritas pemerintah</li>
                    <li>Untuk melindungi hak, privasi, keamanan Anda atau pihak lain</li>
                </ul>
            </div>

            <div class="section">
                <h2>7. Layanan Pihak Ketiga</h2>
                <p>Aplikasi menggunakan layanan pihak ketiga berikut:</p>
                <ul>
                    <li><strong>Google Play Services:</strong> Untuk distribusi aplikasi</li>
                    <li><strong>HTTP Client Library:</strong> Untuk komunikasi dengan server</li>
                    <li><strong>SharedPreferences:</strong> Untuk penyimpanan data lokal</li>
                    <li><strong>Flutter Framework:</strong> Untuk menjalankan aplikasi</li>
                </ul>
                <p>Kami tidak membagikan data pribadi Anda dengan layanan-layanan ini di luar kebutuhan fungsional.</p>
            </div>

            <div class="section">
                <h2>8. Hak Pengguna</h2>
                <p>Sebagai pengguna, Anda memiliki hak untuk:</p>
                <ul>
                    <li>Mengakses data pribadi Anda</li>
                    <li>Memperbaiki data yang tidak akurat</li>
                    <li>Menghapus akun Anda dan data terkait</li>
                    <li>Menarik persetujuan kapan saja</li>
                    <li>Mengajukan keluhan tentang penggunaan data</li>
                </ul>
                <p>Untuk melaksanakan hak-hak ini, hubungi administrator sekolah atau kontak di bagian 11.</p>
            </div>

            <div class="section">
                <h2>9. Privasi Anak-Anak</h2>
                <p>
                    Aplikasi ini dirancang untuk siswa berusia 12 tahun ke atas. Kami tidak secara sengaja mengumpulkan
                    informasi dari anak-anak di bawah 12 tahun. Jika kami menyadari bahwa kami telah mengumpulkan data
                    dari
                    anak di bawah 12 tahun, kami akan menghapusnya segera.
                </p>
            </div>

            <div class="section">
                <h2>10. Perubahan Kebijakan Privasi</h2>
                <p>Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan material akan
                    dikomunikasikan melalui:</p>
                <ul>
                    <li>Pembaruan di aplikasi</li>
                    <li>Email ke alamat yang terdaftar</li>
                    <li>Pemberitahuan dalam aplikasi</li>
                </ul>
                <p>Penggunaan berkelanjutan aplikasi setelah perubahan berarti Anda menerima kebijakan yang diperbarui.
                </p>
            </div>

            <div class="section">
                <h2>11. Hubungi Kami</h2>
                <p>Jika Anda memiliki pertanyaan, kekhawatiran, atau permintaan terkait privasi, silakan hubungi:</p>
                <div class="important-box">
                    <p><strong>Sekolah/Institusi Pendidikan:</strong></p>
                    <ul style="margin-top: 10px;">
                        <li>Hubungi administrator atau guru pembimbing Anda</li>
                        <li>Kunjungi kantor sekolah untuk informasi lebih lanjut</li>
                    </ul>
                </div>
                <div class="important-box">
                    <p><strong>Pengembang Aplikasi:</strong></p>
                    <ul style="margin-top: 10px;">
                        <li>Hubungi melalui email yang disediakan oleh sekolah</li>
                        <li>Ajukan pertanyaan melalui portal sekolah</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>12. Kepatuhan Hukum</h2>
                <p>Kebijakan Privasi ini mematuhi:</p>
                <ul>
                    <li>Undang-Undang Nomor 27 Tahun 2022 tentang Perlindungan Data Pribadi (Indonesia)</li>
                    <li>Peraturan perundang-undangan terkait privasi data di Indonesia</li>
                </ul>
            </div>

            <div class="section">
                <h2>13. Penerimaan Kebijakan</h2>
                <p>
                    Dengan menggunakan aplikasi Skanida Student, Anda menerima kebijakan privasi ini. Jika Anda tidak
                    setuju dengan kebijakan ini, mohon jangan gunakan aplikasi ini.
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Versi:</strong> 1.0 | <strong>Tanggal Pembaruan Terakhir:</strong> Februari 2026 |
                <strong>Status:</strong> Aktif</p>
            <p style="margin-top: 10px;">&copy; 2026 Skanida Student. Semua hak dilindungi.</p>
        </div>
    </div>
</body>

</html>