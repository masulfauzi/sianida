<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support & Bantuan - SKANIDA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            padding-top: 30px;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .support-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .support-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .support-card svg {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
            color: #667eea;
        }

        .support-card h2 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #333;
        }

        .support-card p {
            color: #666;
            margin-bottom: 15px;
        }

        .support-card a {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
            font-weight: 500;
        }

        .support-card a:hover {
            background: #764ba2;
        }

        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .faq-section h2 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #333;
        }

        .faq-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .faq-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .faq-question {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .faq-answer {
            color: #666;
            line-height: 1.8;
        }

        .contact-info {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .contact-info h2 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #333;
        }

        .contact-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .contact-method {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .contact-method h3 {
            color: #667eea;
            margin-bottom: 8px;
        }

        .contact-method p {
            color: #666;
        }

        .app-info {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-top: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .app-info h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }

        .app-info ul {
            list-style: none;
            padding-left: 0;
        }

        .app-info li {
            padding: 8px 0;
            color: #666;
        }

        .app-info li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 8px;
        }

        footer {
            text-align: center;
            color: white;
            padding: 30px 20px;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8rem;
            }

            .support-grid {
                grid-template-columns: 1fr;
            }

            .faq-section,
            .contact-info,
            .app-info {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>🆘 SKANIDA Support & Bantuan</h1>
            <p>Kami siap membantu Anda menggunakan aplikasi SKANIDA</p>
        </header>

        <!-- Support Options Grid -->
        <div class="support-grid">
            <div class="support-card">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2>FAQ</h2>
                <p>Tanya jawab seputar fitur dan penggunaan aplikasi</p>
                <a href="#faq-section">Lihat FAQ →</a>
            </div>

            <div class="support-card">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h2>Hubungi Kami</h2>
                <p>Kirim pertanyaan langsung melalui email atau formulir</p>
                <a href="#contact-section">Hubungi Kami →</a>
            </div>

            <div class="support-card">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2>Dokumentasi</h2>
                <p>Panduan lengkap penggunaan setiap fitur aplikasi</p>
                <a href="#documentation-section">Baca Dokumentasi →</a>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section" id="faq-section">
            <h2>❓ Pertanyaan yang Sering Diajukan (FAQ)</h2>

            <div class="faq-item">
                <div class="faq-question">1. Bagaimana cara login ke aplikasi SKANIDA?</div>
                <div class="faq-answer">
                    Gunakan NIP atau NISN Anda sebagai username dan kata sandi yang telah diberikan oleh sekolah.
                    Jika lupa kata sandi, gunakan fitur "Lupa Kata Sandi" di halaman login.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">2. Apa itu SKANIDA?</div>
                <div class="faq-answer">
                    SKANIDA adalah aplikasi mobile resmi dari Sistem Informasi Akademik SIANIDA yang dirancang untuk
                    memudahkan siswa dan guru mengakses data akademis di mana saja dan kapan saja.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">3. Fitur apa saja yang tersedia di SKANIDA?</div>
                <div class="faq-answer">
                    SKANIDA menyediakan berbagai fitur seperti:
                    <ul style="list-style: disc; margin-left: 20px; margin-top: 10px;">
                        <li>Melihat jadwal pelajaran</li>
                        <li>Mengecek nilai dan prestasi</li>
                        <li>Melihat informasi kelas dan guru</li>
                        <li>Absensi dan presensi sholat</li>
                        <li>Pengumuman dari sekolah</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">4. Bagaimana jika data di aplikasi tidak sama dengan web?</div>
                <div class="faq-answer">
                    Data di aplikasi SKANIDA disinkronisasi secara berkala dari sistem utama SIANIDA.
                    Jika ada perbedaan, coba perbarui aplikasi atau logout dan login kembali.
                    Jika masalah persisten, hubungi support tim kami.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">5. Apakah aplikasi SKANIDA gratis?</div>
                <div class="faq-answer">
                    Ya, aplikasi SKANIDA sepenuhnya gratis dan disediakan oleh pihak sekolah untuk semua siswa dan guru.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">6. Berapa besar ukuran aplikasi SKANIDA?</div>
                <div class="faq-answer">
                    Ukuran aplikasi SKANIDA relatif kecil (kurang lebih 50-100 MB tergantung platform),
                    sehingga mudah diinstal di perangkat yang memiliki storage terbatas.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">7. Apa yang harus dilakukan jika aplikasi tidak bisa login?</div>
                <div class="faq-answer">
                    Berikut langkah-langkah yang bisa dicoba:
                    <ul style="list-style: decimal; margin-left: 20px; margin-top: 10px;">
                        <li>Pastikan koneksi internet stabil</li>
                        <li>Periksa kembali username dan password</li>
                        <li>Perbarui aplikasi ke versi terbaru</li>
                        <li>Hapus cache aplikasi dan coba login ulang</li>
                        <li>Jika masih tidak bisa, hubungi support</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">8. Bagaimana keamanan data saya di aplikasi?</div>
                <div class="faq-answer">
                    Keamanan data adalah prioritas utama kami. Aplikasi SKANIDA menggunakan enkripsi standar industri
                    dan semua data dikomunikasikan melalui koneksi HTTPS yang aman. Lihat Kebijakan Privasi kami untuk
                    informasi lebih lengkap.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">9. Bisakah saya logout dari aplikasi?</div>
                <div class="faq-answer">
                    Ya, Anda dapat logout melalui menu pengaturan di aplikasi. Anda dapat login kembali kapan saja
                    dengan kredensial Anda.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">10. Bagaimana jika menemukan bug atau masalah teknis lainnya?</div>
                <div class="faq-answer">
                    Silakan laporkan kepada tim support kami melalui email atau formulir kontak.
                    Sertakan deskripsi masalah dan screenshot jika memungkinkan untuk membantu kami mereproduksi
                    masalah.
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-info" id="contact-section">
            <h2>📞 Hubungi Tim Support Kami</h2>

            <div class="contact-methods">
                <div class="contact-method">
                    <h3>📧 Email</h3>
                    <p><a href="mailto:support@smkn2semarang.sch.id" style="color: #667eea; text-decoration: none;">
                            support@smkn2semarang.sch.id
                        </a></p>
                    <p style="font-size: 0.9rem; margin-top: 5px;">Respons: 1-2 jam kerja</p>
                </div>

                <div class="contact-method">
                    <h3>🏢 Alamat Sekolah</h3>
                    <p>SMK Negeri 2 Semarang<br>
                        Jl. Majapahit No. 56, Semarang<br>
                        Jawa Tengah, Indonesia</p>
                </div>

                <div class="contact-method">
                    <h3>🌐 Website Utama</h3>
                    <p><a href="https://smkn2semarang.sch.id" target="_blank"
                            style="color: #667eea; text-decoration: none;">
                            https://smkn2semarang.sch.id
                        </a></p>
                </div>

                <div class="contact-method">
                    <h3>💬 WhatsApp</h3>
                    <p><a href="https://wa.me/6283133276442" target="_blank"
                            style="color: #667eea; text-decoration: none;">
                            +62 831-3327-6442
                        </a></p>
                    <p style="font-size: 0.9rem; margin-top: 5px;">Chat langsung via WhatsApp</p>
                </div>
            </div>
        </div>

        <!-- App Info -->
        <div class="app-info" id="documentation-section">
            <h2>ℹ️ Informasi Aplikasi SKANIDA</h2>

            <h3 style="margin-top: 20px; margin-bottom: 10px; color: #667eea;">Fitur Utama:</h3>
            <ul>
                <li>Dashboard berisi ringkasan informasi akademik</li>
                <li>Jadwal pelajaran dengan detail guru dan kelas</li>
                <li>Laporan nilai dan prestasi akademis</li>
                <li>Data kehadiran dan presensi sholat</li>
                <li>Pengumuman dan informasi dari sekolah</li>
                <li>Profil pengguna dan pengaturan akun</li>
                <li>Fitur pencarian dan filter data</li>
            </ul>

            <h3 style="margin-top: 20px; margin-bottom: 10px; color: #667eea;">Persyaratan Sistem:</h3>
            <ul>
                <li>iOS 12.0 atau lebih baru</li>
                <li>Koneksi internet yang stabil</li>
                <li>Akun aktif di sistem SIANIDA sekolah</li>
            </ul>

            <h3 style="margin-top: 20px; margin-bottom: 10px; color: #667eea;">Versi Saat Ini:</h3>
            <p style="color: #666;">1.0.0</p>

            <h3 style="margin-top: 20px; margin-bottom: 10px; color: #667eea;">Dokumen Penting:</h3>
            <ul>
                <li><a href="{{ route('kebijakan-privasi') }}" style="color: #667eea; text-decoration: none;">Kebijakan
                        Privasi</a></li>
                <li><a href="#" style="color: #667eea; text-decoration: none;">Syarat dan Ketentuan Penggunaan</a></li>
            </ul>
        </div>

        <footer>
            <p>&copy; 2024 SKANIDA - Aplikasi Mobile SIANIDA | SMK Negeri 2 Semarang</p>
            <p style="margin-top: 10px; opacity: 0.8;">Untuk bantuan lebih lanjut, hubungi support@smkn2semarang.sch.id
            </p>
        </footer>
    </div>
</body>

</html>