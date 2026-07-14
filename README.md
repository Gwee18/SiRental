# SiRental

SiRental adalah aplikasi web untuk pemesanan dan pengelolaan rental alat
pendakian. Aplikasi memiliki dua peran utama, yaitu customer dan admin.

## Fitur utama

### Customer

- Melihat katalog alat pendakian.
- Login menggunakan OTP email.
- Login menggunakan akun Google.
- Mengajukan rental dengan beberapa alat sekaligus.
- Mengunggah foto identitas dan foto kondisi barang.
- Menggunakan data profil sebagai isian awal form rental.
- Menyimpan snapshot data peminjam pada setiap transaksi.
- Melihat status dan detail transaksi.
- Memperbarui profil dan foto profil.

### Admin

- Login melalui halaman admin terpisah.
- Melihat ringkasan dashboard.
- Mengelola data alat dan ketersediaan stok.
- Mengaktifkan atau menonaktifkan alat.
- Menyetujui atau menolak pengajuan rental.
- Memproses pengembalian menggunakan kode transaksi.
- Menghitung keterlambatan dan denda secara otomatis.
- Mengelola data pelanggan.
- Melihat laporan bulanan.
- Mengunduh laporan PDF.

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 12
- MySQL atau MariaDB
- Blade
- Tailwind CSS
- Vite
- Laravel Socialite
- DomPDF
- Intervention Image
- PHPUnit

## Persyaratan

Pastikan perangkat sudah memiliki:

- PHP 8.2+
- Composer
- Node.js dan npm
- MySQL atau MariaDB
- Ekstensi PHP:
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `fileinfo`
  - `gd`

Pengguna XAMPP dapat menjalankan Apache dan MySQL melalui XAMPP Control
Panel.

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/Gwee18/SiRental.git
cd SiRental
```

### 2. Instal dependency PHP

```bash
composer install
```

### 3. Buat file environment

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Command Prompt:

```cmd
copy .env.example .env
```

Linux atau macOS:

```bash
cp .env.example .env
```

### 4. Buat application key

```bash
php artisan key:generate
```

### 5. Buat database

Buat database MySQL bernama:

```text
sirental
```

Contoh melalui MySQL:

```sql
CREATE DATABASE sirental
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Sesuaikan konfigurasi database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sirental
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Jalankan migration dan seeder

```bash
php artisan migrate --seed
```

### 7. Buat symbolic link storage

```bash
php artisan storage:link
```

### 8. Instal dependency frontend

```bash
npm install
```

### 9. Jalankan aplikasi

Terminal pertama:

```bash
php artisan serve
```

Terminal kedua:

```bash
npm run dev
```

Aplikasi dapat dibuka melalui:

```text
http://127.0.0.1:8000
```

Untuk build production frontend:

```bash
npm run build
```

## Konfigurasi OTP email

Secara default `.env.example` menggunakan:

```env
MAIL_MAILER=log
```

Dengan konfigurasi tersebut, kode OTP tidak dikirim ke email asli. Kode dapat
dilihat pada:

```text
storage/logs/laravel.log
```

Untuk mengirim OTP melalui SMTP, ubah konfigurasi berikut sesuai penyedia
email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Setelah mengubah `.env`, jalankan:

```bash
php artisan config:clear
```

## Konfigurasi Google Login

Buat kredensial OAuth pada Google Cloud Console dan tambahkan callback:

```text
http://127.0.0.1:8000/auth/google/callback
```

Isi variabel berikut pada `.env`:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Kemudian jalankan:

```bash
php artisan config:clear
```

## Konfigurasi akun admin

Kredensial admin dibaca dari file `.env`:

```env
ADMIN_NAME="Admin SiRental"
ADMIN_EMAIL=admin@sirental.com
ADMIN_PASSWORD=admin123
```

Buat atau perbarui akun admin melalui:

```bash
php artisan db:seed --class=AdminSeeder
```

Seeder dapat dijalankan berulang kali tanpa membuat akun duplikat. Akun dengan
email yang sama akan diperbarui.

Halaman login admin:

```text
http://127.0.0.1:8000/admin/login
```

Password `admin123` hanya untuk pengembangan lokal. Seeder akan menolak
password tersebut ketika aplikasi memakai environment `production`. Gunakan
password unik minimal delapan karakter sebelum deployment.

## Menjalankan test

Jalankan seluruh test:

```bash
php artisan test
```

Pemeriksaan berhenti saat kegagalan pertama:

```bash
php artisan test --stop-on-failure
```

Pemeriksaan format kode:

```bash
./vendor/bin/pint --test
```

Windows PowerShell:

```powershell
.\vendor\bin\pint --test
```

Memperbaiki format secara otomatis:

```bash
./vendor/bin/pint
```

## Pemeriksaan keamanan dependency

```bash
composer audit
npm audit
```

## Command setelah perubahan konfigurasi atau tampilan

```bash
php artisan optimize:clear
php artisan view:clear
```

## Struktur utama

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Auth/
│   │   └── Customer/
│   └── Middleware/
├── Models/
database/
├── migrations/
└── seeders/
resources/
└── views/
    ├── admin/
    ├── auth/
    ├── customer/
    └── layouts/
routes/
└── web.php
tests/
├── Feature/
└── Unit/
```

## Alur rental

```text
Customer login
    ↓
Memilih alat dan lama sewa
    ↓
Mengisi data diri dan mengunggah dokumen
    ↓
Pengajuan berstatus menunggu
    ↓
Admin menyetujui atau menolak
    ↓
Pembayaran sewa dikonfirmasi
    ↓
Rental berstatus aktif
    ↓
Admin memproses pengembalian
    ↓
Denda dihitung jika terlambat
    ↓
Transaksi selesai
```

## Catatan keamanan

- Jangan commit file `.env`.
- Jangan menyimpan password SMTP atau Google OAuth di repository.
- Gunakan `APP_DEBUG=false` pada production.
- Ganti akun admin bawaan sebelum deployment.
- Jalankan `composer audit`, `npm audit`, dan test sebelum deployment.

## Lisensi

Proyek ini dibuat untuk kebutuhan pembelajaran dan pengembangan sistem
rental alat pendakian.