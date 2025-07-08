# ForumXploit - Aplikasi Forum Laravel yang Rentan

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Security](https://img.shields.io/badge/Security-Vulnerable-red?style=for-the-badge&logo=security&logoColor=white)

## ⚠️ PERINGATAN

**Aplikasi ini sengaja dibuat rentan dan dirancang khusus untuk tujuan edukasi. JANGAN gunakan di lingkungan produksi.**

## 📋 Gambaran Umum

ForumXploit adalah aplikasi forum Laravel yang sengaja dibuat rentan untuk keperluan pengujian keamanan, pelatihan penetration testing, dan tujuan edukasi. Aplikasi ini mendemonstrasikan kerentanan umum aplikasi web berdasarkan OWASP Top 10.

## 🚨 Kerentanan yang Diimplementasikan

### A01: Broken Access Control (Kontrol Akses Rusak)

-   **Tidak Ada Otorisasi**: Pengguna dapat mengakses fungsi admin tanpa pengecekan yang tepat
-   **Insecure Direct Object References**: Akses langsung ke postingan/komentar tanpa validasi kepemilikan
-   **Privilege Escalation**: Pengguna biasa dapat melakukan aksi admin

### A02: Cryptographic Failures (Kegagalan Kriptografi)

-   **Penyimpanan Password Lemah**: Hashing dasar tanpa salt yang tepat
-   **Transmisi Data Tidak Aman**: Data sensitif terekspos dalam teks biasa

### A03: Injection (Injeksi)

-   **SQL Injection**: Query SQL langsung tanpa parameterisasi
    ```php
    $post = DB::select("SELECT * FROM posts WHERE id = $id")[0] ?? null;
    ```
-   **XSS (Cross-Site Scripting)**: Konten HTML mentah tanpa sanitasi
-   **Command Injection**: Eksekusi perintah sistem yang tidak aman

### A04: Insecure Design (Desain Tidak Aman)

-   **Tidak Ada Validasi Input**: Form menerima input apa pun tanpa validasi
-   **Autentikasi Lemah**: Login dasar tanpa pembatasan percobaan
-   **Masalah Manajemen Sesi**: Penanganan sesi yang tidak aman

### A05: Security Misconfiguration (Konfigurasi Keamanan yang Salah)

-   **Mode Debug Aktif**: Mode debug Laravel terekspos di produksi
-   **Konfigurasi Default**: Menggunakan pengaturan keamanan default
-   **Fitur Tidak Perlu**: Fitur yang diaktifkan meningkatkan permukaan serangan

### A06: Vulnerable and Outdated Components (Komponen Rentan dan Usang)

-   **Dependensi Usang**: Menggunakan versi paket yang rentan
-   **Library Tidak Dipatch**: Patch keamanan tidak diterapkan

### A07: Identification and Authentication Failures (Kegagalan Identifikasi dan Autentikasi)

-   **Kebijakan Password Lemah**: Tidak ada persyaratan kompleksitas password
-   **Tidak Ada Penguncian Akun**: Percobaan login tanpa batas
-   **Session Fixation**: Manajemen sesi yang rentan

### A08: Software and Data Integrity Failures (Kegagalan Integritas Software dan Data)

-   **Kerentanan Upload File**: Tidak ada validasi tipe file
-   **Upload File Executable**: File PHP/script dapat diupload dan dieksekusi
-   **Tidak Ada Pengecekan Integritas File**: File dapat dimodifikasi setelah upload

### A09: Security Logging and Monitoring Failures (Kegagalan Logging dan Monitoring Keamanan)

-   **Tidak Ada Logging**: Event keamanan tidak dicatat
-   **Tidak Ada Monitoring**: Tidak ada monitoring keamanan real-time
-   **Tidak Ada Alerting**: Tidak ada alert insiden keamanan

### A10: Server-Side Request Forgery (SSRF)

-   **URL Fetching**: Pengambilan URL yang tidak aman tanpa validasi
-   **Akses Jaringan Internal**: Dapat mengakses layanan internal
-   **Akses Metadata Cloud**: Potensi akses ke metadata cloud

## 🛠️ Instalasi

### Persyaratan

-   PHP 8.1+
-   Composer
-   Node.js & NPM
-   Database MySQL/PostgreSQL

### Langkah-langkah Setup

1. **Clone repository**

    ```bash
    git clone https://github.com/yourusername/forumXploit.git
    cd forumXploit
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Setup environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Konfigurasi database**

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=forumxploit
    DB_USERNAME=username_anda
    DB_PASSWORD=password_anda
    ```

5. **Jalankan migrasi dan seeder**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

6. **Buat symlink storage**

    ```bash
    php artisan storage:link
    ```

7. **Build assets**

    ```bash
    npm run build
    ```

8. **Jalankan server development**
    ```bash
    php artisan serve
    ```

## 🎯 Fitur Pengujian

### 1. Pengujian Kerentanan Upload File

-   Buka halaman utama forum
-   Gunakan panel "Unggah File"
-   Coba upload berbagai jenis file:
    -   File PHP (`.php`)
    -   File executable (`.exe`, `.bat`)
    -   File script (`.js`, `.html`)
    -   File gambar (`.jpg`, `.png`)

### 2. Pengujian SQL Injection

-   Buka halaman detail postingan
-   Ubah parameter URL: `/post/1` → `/post/1 OR 1=1`
-   Coba berbagai payload SQL injection

### 3. Pengujian XSS

-   Buat postingan baru dengan konten HTML/JavaScript
-   Contoh: `<script>alert('XSS')</script>`
-   Tes stored XSS di komentar

### 4. Pengujian SSRF

-   Gunakan panel "Uji SSRF"
-   Coba akses URL internal:
    -   `http://localhost:8000/admin`
    -   `http://169.254.169.254/` (metadata AWS)
    -   `file:///etc/passwd`

### 5. Pengujian Eksekusi File

-   Upload file PHP menggunakan form upload
-   Gunakan panel "Uji Eksekusi File"
-   Eksekusi file PHP yang telah diupload

## 🔐 Kredensial Default

### Akun Admin

-   **Username**: admin@example.com
-   **Password**: password

### Pengguna Biasa

-   **Username**: user@example.com
-   **Password**: password

## 📁 Struktur Proyek

```
forumXploit/
├── app/
│   ├── Http/Controllers/
│   │   └── ForumController.php    # Controller utama dengan kerentanan
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   └── Comment.php
│   └── ...
├── resources/views/
│   ├── forum/
│   │   ├── index.blade.php        # Halaman utama forum
│   │   ├── show.blade.php         # Halaman detail postingan
│   │   └── search.blade.php       # Hasil pencarian
│   ├── admin/
│   │   └── dashboard.blade.php    # Dashboard admin
│   └── layouts/
│       └── app.blade.php          # Layout utama
├── routes/
│   └── web.php                    # Routes aplikasi
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── storage/                   # Storage yang di-symlink
└── storage/
    └── app/public/uploads/        # Direktori upload file
```

## 🧪 Checklist Pengujian Keamanan

-   [ ] Tes SQL injection di URL postingan
-   [ ] Tes XSS di konten postingan dan komentar
-   [ ] Tes kerentanan upload file
-   [ ] Tes SSRF melalui pengambilan URL
-   [ ] Tes bypass autentikasi
-   [ ] Tes bypass otorisasi
-   [ ] Tes session fixation
-   [ ] Tes serangan CSRF
-   [ ] Tes directory traversal
-   [ ] Tes remote code execution

## 📚 Sumber Belajar

### OWASP Top 10 2021

-   [OWASP Top 10](https://owasp.org/www-project-top-ten/)
-   [OWASP Web Security Testing Guide](https://owasp.org/www-project-web-security-testing-guide/)

### Tools Keamanan

-   **Burp Suite**: Testing keamanan aplikasi web
-   **OWASP ZAP**: Scanner keamanan open-source
-   **SQLMap**: Testing SQL injection
-   **Nikto**: Scanner web server

## 🤝 Kontribusi

Proyek ini untuk tujuan edukasi. Jika Anda menemukan kerentanan tambahan atau ingin menambah fitur:

1. Fork repository
2. Buat branch fitur
3. Tambahkan kerentanan/fitur Anda
4. Submit pull request

## 📄 Lisensi

Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## ⚠️ Pemberitahuan Hukum

**PENTING**: Aplikasi ini mengandung kerentanan keamanan yang disengaja dan hanya boleh digunakan di lingkungan terkontrol untuk tujuan edukasi. Developer tidak bertanggung jawab atas penyalahgunaan aplikasi ini.

-   Gunakan hanya di lingkungan testing yang terisolasi
-   Jangan deploy ke server produksi
-   Pastikan isolasi jaringan yang tepat
-   Gunakan hanya untuk testing keamanan yang diotorisasi

## 🔍 Laporan Bug

Jika Anda menemukan bug yang bukan merupakan kerentanan yang disengaja, silakan laporkan melalui GitHub issues.

## 📞 Dukungan

Untuk pertanyaan tentang tool edukasi ini:

-   Buat issue di GitHub
-   Hubungi tim pengembangan

---

**Ingat**: Ini adalah aplikasi yang rentan by design. Selalu praktikkan responsible disclosure dan prinsip ethical hacking.
