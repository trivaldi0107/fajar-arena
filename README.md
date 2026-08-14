# 🏸 Fajar Arena — Sistem Informasi Reservasi Lapangan Olahraga & Manajemen Multi-Cabang

<p align="center">
  <img src="public/images/badminton.png" width="120" alt="Fajar Arena Logo">
</p>

<p align="center">
  <strong>Platform Digital Manajemen & Reservasi Lapangan Olahraga Berbasis Web dengan Rekomendasi Algoritma Greedy, E-Tiket QR Code, dan Multi-Cabang.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📖 Tentang Aplikasi

**Fajar Arena** adalah aplikasi web modern untuk mengelola reservasi dan operasional fasilitas olahraga secara *end-to-end*. Aplikasi ini dirancang untuk memudahkan pelanggan dalam mencari dan memesan jadwal lapangan secara *real-time*, sekaligus memberikan kendali penuh kepada manajemen lapangan melalui panel admin terintegrasi.

Aplikasi ini mengimplementasikan **Algoritma Greedy** untuk mengoptimalkan pemilihan dan rekomendasi slot lapangan, meminimalisir perpindahan lapangan (*court switching*) bagi penyewa dengan durasi jam berurutan, serta mengotomatiskan validasi paket member multi-pekan.

---

## ✨ Fitur-Fitur Utama

### 1. 🏢 Manajemen Multi-Cabang (Multi-Arena)
- Mendukung berbagai cabang arena dan jenis olahraga (**Badminton, Padel, Futsal**) dalam satu aplikasi terpusat.
- Switch cabang secara dinamis dengan isolasi data jadwal, lapangan, harga, kontak, dan pengaturan beranda per cabang.

### 2. ⚡ Sistem Reservasi Cerdas & Algoritma Greedy
- **Reservasi Reguler**: Pemilihan jam fleksibel per hari dengan visualisasi matriks ketersediaan slot yang responsif dan real-time.
- **Rekomendasi Greedy Choice**: Memilih kombinasi lapangan terbaik secara otomatis untuk meminimalisir perpindahan lapangan bagi pelanggan.
- **Paket Member Multi-Pekan**: Pemesanan otomatis untuk 4 pekan berurutan pada jam dan hari yang sama dengan deteksi bentrok jadwal.

### 3. 🎟️ E-Tiket & Scanner Kamera QR Code
- **E-Tiket Digital**: Dilengkapi QR Code dinamis berbasis kode reservasi unik yang dapat diunduh langsung sebagai file gambar (PNG).
- **Scanner Kamera Admin**: Fitur pemindai barcode/QR Code via kamera smartphone/laptop pada panel admin untuk check-in dan validasi kehadiran di lokasi secara instan.

### 4. 💳 Pembayaran Fleksibel & Verifikasi Admin
- Integrasi **QRIS Statis** per cabang dengan alur unggah bukti transfer yang aman.
- Simulasi alur pembayaran online dengan *countdown timer* waktu pembayaran.
- Panel verifikasi bukti transfer untuk Admin (Setujui / Tolak transaksi) dengan status instan.

### 5. 🔐 Keamanan Akun & Autentikasi
- Registrasi akun dengan **Verifikasi Kode OTP 6-Digit via Email**.
- Proteksi bot menggunakan **Google reCAPTCHA v3**.
- Reset kata sandi mandiri dengan token aman via email.
- Pembatasan hak akses berbasis peran (*Role-Based Access Control*: Administrator & Customer).

### 6. 📱 Web Push Notification & Desain Responsif
- Integrasi notifikasi push browser (**Web Push API**) untuk pemberitahuan reservasi baru ke Admin.
- Desain antarmuka modern yang sepenuhnya responsif di smartphone, tablet, dan desktop.

### 7. 🛠️ Panel Administrasi Lengkap
- **Dashboard Analitik**: Ringkasan pendapatan, statistik reservasi, dan transaksi terbaru.
- **Kelola Jadwal**: Buka/tutup slot jam, ubah status masal (*bulk update*), atur status perbaikan, event, atau pemeliharaan.
- **Kelola Lapangan**: Tambah, ubah, dan atur lapangan beserta foto dan fasilitasnya.
- **Editor Beranda Interaktif**: Pengaturan banner hero slider, running text pengumuman, daftar berita/artikel, video YouTube, kontak WhatsApp, dan kartu fitur.

---

## 💻 Tech Stack

- **Backend Framework**: [Laravel 13.x](https://laravel.com/)
- **Bahasa Pemrograman**: [PHP 8.3+](https://www.php.net/)
- **Database**: [MySQL 8.x](https://www.mysql.com/) / MariaDB
- **Frontend Styling**: [Tailwind CSS 3.x](https://tailwindcss.com/)
- **Interaktivitas UI**: [Alpine.js](https://alpinejs.dev/), [TomSelect](https://tom-select.js.org/)
- **Scanner QR**: [HTML5-QRCode](https://github.com/mebjas/html5-qrcode)
- **Asset Bundler**: [Vite 5.x](https://vitejs.dev/)
- **Web Push**: [Minishlink Web-Push](https://github.com/web-push-libs/web-push-php)

---

## 🚀 Panduan Instalasi Lokal (Local Development)

### 1. Prasyarat Sistem
Pastikan komputer Anda telah terinstal:
- PHP >= 8.3 (dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`, `gd`, `zip`, `intl`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL Server (via Laragon / XAMPP)

### 2. Kloning Repositori
```bash
git clone https://github.com/trivaldi0107/fajar-arena.git
cd fajar-arena
```

### 3. Instalasi Dependensi PHP & Node.js
```bash
composer install
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan konfigurasi database Anda:
```dotenv
APP_NAME="Fajar Arena"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fajar_arena
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key & Symlink Storage
```bash
php artisan key:generate
php artisan storage:link
```

### 6. Migrasi Database & Seeding
```bash
php artisan migrate --seed
```

### 7. Kompilasi Aset Frontend & Jalankan Server
```bash
# Terminal 1: Kompilasi Aset (Vite)
npm run build
# atau: npm run dev

# Terminal 2: Jalankan Laravel Server
php artisan serve
```
Akses aplikasi melalui browser di: `http://localhost:8000`.

---

## 🔑 Akun Default untuk Pengujian

| Peran (Role) | Email | Password Default |
| :--- | :--- | :--- |
| **Administrator** | `fajararenabadminton@gmail.com` | `password` |
| **Pelanggan (User)** | `user@example.com` | `password` |

---

## 🧪 Menjalankan Pengujian Otomatis (Automated Tests)

Aplikasi ini dilengkapi dengan pengujian unit dan fitur komprehensif:
```bash
php artisan test
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
