# 📖 Panduan Integrasi Fitur Materi Kuliah & Setup Aplikasi FoodFlow

> **Dokumen Resmi Integrasi Kurikulum & Panduan Setup**  
> **Aplikasi:** FoodFlow — Sistem Informasi Manajemen Persediaan & Evaluasi Gizi MBG/Catering  
> **Versi Framework:** Laravel 11/12 (PHP 8.4)  
> **Database:** PostgreSQL (Supabase Cloud) / SQLite (Testing)  
> **Frontend:** Blade Templates, Tailwind CSS, Alpine.js  

---

## 🎯 1. Ringkasan & Filosofi Desain

Dokumen ini disusun untuk menjelaskan **keterkaitan seluruh fitur aplikasi FoodFlow dengan materi perkuliahan** (Rekayasa Perangkat Lunak, Pemrograman Web Lanjut, dan Penjaminan Mutu Perangkat Lunak / PMPL), mencakup:
1. **Daftar teknologi/library yang digunakan di web ini**.
2. **Lokasi file (di mana letaknya)**.
3. **Fungsi teknis & manfaat bisnisnya di aplikasi**.
4. **Panduan instalasi & setup step-by-step (termasuk Laravel Breeze, Socialite, dsb)**.

---

## 🗺️ 2. Pemetaan Fitur Materi ke Codebase FoodFlow

Berikut adalah tabel pemetaan lengkap antara konsep materi perkuliahan dengan implementasi nyata pada sistem FoodFlow:

| Kategori Materi | Teknologi / Fitur | Lokasi File di Codebase | Fungsi & Peran Bisnis di FoodFlow |
| :--- | :--- | :--- | :--- |
| **Authentication Starter Kit** | **Laravel Breeze (Blade Stack)** | `routes/auth.php`<br>`app/Http/Controllers/Auth/`<br>`resources/views/auth/`<br>`resources/views/layouts/guest.blade.php` | Menyediakan pondasi autentikasi modern berbasis session cookie yang aman, CSRF protection, dan layout Blade minimalis. |
| **Third-Party API / OAuth 2.0** | **Laravel Socialite (Google SSO)** | `app/Http/Controllers/Auth/SocialiteController.php`<br>`config/services.php`<br>`routes/auth.php` | Integrasi API OAuth Google. Berfungsi sebagai **Single Sign-On (SSO)** dan **solusi instan lupa password** untuk akun pegawai yang sudah didaftarkan Admin. |
| **Multi-Role Authorization (RBAC)** | **Role-Based Access Control** | `app/Enums/UserRole.php`<br>`app/Http/Middleware/EnsureUserHasRole.php`<br>`bootstrap/app.php` | Memisahkan hak akses 4 departemen: **Admin**, **Gudang**, **Produksi**, dan **Owner**. Memastikan staf dapur tidak bisa approve barang gudang, dan staf gudang tidak bisa merubah resep gizi. |
| **Anti-Brute Force Protection** | **Rate Limiting & Throttling** | `app/Providers/AppServiceProvider.php`<br>`app/Http/Requests/Auth/LoginRequest.php`<br>`routes/auth.php` | Melindungi sistem dari serangan kamus / brute-force password. Dibatasi maksimal 5 percobaan per menit per email + IP. |
| **Password Hashing & Security** | **Bcrypt & Password Validation Rules** | `app/Providers/AppServiceProvider.php`<br>`app/Models/User.php` (`casts: hashed`)<br>`app/Http/Requests/Admin/StoreUserRequest.php` | Menjamin kata sandi tidak pernah disimpan dalam plain text. Menegakkan standar password kuat minimal 8 karakter kombinasi huruf dan angka. |
| **Password Recovery Workflow** | **Admin Approval Request Flow** | `app/Http/Controllers/Admin/PasswordResetRequestController.php`<br>`resources/views/admin/password-resets/index.blade.php`<br>`resources/views/auth/forgot-password.blade.php` | Alur internal katering: pegawai yang lupa password mengajukan permohonan ke Admin. Admin memvalidasi fisik staf sebelum mereset sandi. |
| **Live Presentation Tool** | **1-Click Demo Role Switcher** | `app/Http/Controllers/DemoRoleSwitcherController.php`<br>`resources/views/components/demo-role-switcher.blade.php`<br>`routes/web.php` | Memungkinkan presenter berpindah peran seketika (Admin $\leftrightarrow$ Gudang $\leftrightarrow$ Produksi $\leftrightarrow$ Owner) dalam 1 detik tanpa perlu login-logout berulang saat sidang demo. |
| **Advanced Eloquent ORM** | **Relasi 1-to-N, N-to-N, HasManyThrough** | `app/Models/ProductionPlan.php`<br>`app/Models/PurchaseOrder.php`<br>`app/Models/Product.php` | Mengaitkan data transaksi katering, resep bahan baku, batch rencana masak dapur, hingga realisasi porsi matang secara otomatis. |
| **Domain Service Layer** | **Kalkulator & Business Logic Terisolasi** | `app/Services/ProductionIngredientCalculator.php`<br>`app/Services/MaterialReceiptService.php`<br>`app/Services/UnitConverter.php` | Menghitung kebutuhan bahan baku masak secara otomatis berdasarkan porsi pesanan dan formula resep gizi (*Skinny Controller, Rich Service*). |
| **Data Seeding & Pagination** | **Database Seeders (6 Seeder Komprehensif)** | `database/seeders/DatabaseSeeder.php`<br>`database/seeders/IngredientSeeder.php`<br>`database/seeders/ProductSeeder.php`<br>`database/seeders/ProductionPlanSeeder.php`<br>`database/seeders/IngredientStockTransactionSeeder.php` | Mengisi puluhan data realistis (28 bahan baku, 22 menu hidangan, 15 supplier, 15 pelanggan, 16 batch rencana produksi) agar pengujian **Pagination (10 item/page)** langsung aktif. |
| **Executive Reporting** | **Print-Ready CSS Portrait Mode** | `resources/views/owner/reports/inventory-print.blade.php`<br>`resources/views/owner/reports/production-print.blade.php` | Mengatur format dokumen cetak resmi ke ukuran **A4 Portrait** (`@page { size: A4 portrait; }`) dengan header tabel berulang otomatis dan anti-terpotong pada tanda tangan. |
| **Automated Testing (PMPL)** | **PHPUnit Feature & Unit Testing** | `tests/Feature/Auth/`<br>`tests/Feature/DemoRoleSwitcherTest.php`<br>`tests/Feature/OwnerReportPrintTest.php`<br>`tests/Feature/IngredientCrudTest.php` | Membuktikan mutu perangkat lunak dengan puluhan skenario pengujian otomatis (Green Test) untuk memastikan tidak ada celah keamanan atau regresi fungsi. |
| **Code Style & Quality** | **Laravel Pint Formatter** | `vendor/bin/pint`<br>`pint.json` | Menjamin seluruh baris kode PHP mematuhi standar PSR-12 dan konvensi resmi Laravel secara otomatis. |

---

## 🛠️ 3. Panduan Setup & Instalasi Lingkungan Kerja

### A. Prasyarat Sistem
* **PHP:** Versi 8.4 (ekstensi: `pdo_pgsql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `curl`).
* **Composer:** Versi 2.8+
* **Node.js & NPM:** Node.js v20+ & NPM v10+
* **Database:** PostgreSQL (Supabase Cloud atau PostgreSQL lokal)

---

### B. Langkah 1: Kloning & Pengaturan File Lingkungan (`.env`)
1. Klon repositori dan masuk ke direktori:
   ```bash
   cd c:\laragon\www\Foodflow
   ```
2. Salin template environment:
   ```bash
   cp .env.example .env
   ```
3. Konfigurasikan koneksi Database PostgreSQL (contoh Supabase):
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-ap-south-1.pooler.supabase.com
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres.YOUR_USER
   DB_PASSWORD=YOUR_PASSWORD
   DB_SSLMODE=require
   ```
4. Generate Application Encryption Key:
   ```bash
   php artisan key:generate
   ```

---

### C. Langkah 2: Setup Laravel Breeze (Authentication Foundation)
Pada proyek FoodFlow, Laravel Breeze diintegrasikan sebagai pondasi otentikasi berbasis Blade.

> [!NOTE]
> Jika kamu ingin mengetahui bagaimana Breeze dipasang pertama kali:
> ```bash
> composer require laravel/breeze --dev
> php artisan breeze:install blade --no-interaction
> ```

**Penyesuaian Khusus FoodFlow pada Breeze:**
1. **Registrasi Mandiri Ditutup**: Rute `/register` publik dialihkan ke `/login` karena di aplikasi internal katering, pembuatan akun **hanya boleh dilakukan oleh Admin** (`app/Http/Controllers/Admin/UserController.php`).
2. **Multi-Role Redirection**: Setelah login, pengguna tidak diarahkan ke dashboard umum, melainkan ke dashboard departemennya masing-masing via `$user->getDashboardRoute()`:
   * Admin $\rightarrow$ `/admin/dashboard`
   * Gudang $\rightarrow$ `/gudang/dashboard`
   * Produksi $\rightarrow$ `/produksi/dashboard`
   * Owner $\rightarrow$ `/owner/dashboard`

---

### D. Langkah 3: Setup Laravel Socialite (Google OAuth SSO)

1. **Instalasi Package**:
   ```bash
   composer require laravel/socialite
   ```
2. **Mendapatkan Kredensial Google Cloud Console**:
   * Buka [Google Cloud Console](https://console.cloud.google.com/).
   * Buat Project baru: `FoodFlow Catering`.
   * Buka menu **APIs & Services** $\rightarrow$ **Credentials** $\rightarrow$ **Create Credentials** $\rightarrow$ **OAuth Client ID**.
   * Pilih Application Type: **Web application**.
   * Tambahkan **Authorized redirect URIs**:
     * Untuk lokal: `http://localhost:8000/auth/google/callback` atau `http://foodflow.test/auth/google/callback`
     * Untuk server AWS EC2 / Production: `http://DOMAIN_ANDA/auth/google/callback`
3. **Konfigurasi pada `.env`**:
   ```env
   GOOGLE_CLIENT_ID=isi_client_id_dari_google_console
   GOOGLE_CLIENT_SECRET=isi_client_secret_dari_google_console
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```
4. **Konfigurasi pada `config/services.php`**:
   ```php
   'google' => [
       'client_id' => env('GOOGLE_CLIENT_ID'),
       'client_secret' => env('GOOGLE_CLIENT_SECRET'),
       'redirect' => env('GOOGLE_REDIRECT_URI'),
   ],
   ```
5. **Jalankan Migrasi Database**:
   ```bash
   php artisan migrate
   ```
   *(Menambahkan kolom `google_id` dan `avatar` pada tabel `users`)*.

---

### E. Langkah 4: Setup Master Data & Seeder (Untuk Testing Pagination)
Jalankan perintah seeder untuk mengisi seluruh data demo, bahan baku, produk, dan transaksi:
```bash
php artisan db:seed
```
Seeder ini otomatis mengisikan:
* **4 Akun Demo**:
  * Admin: `admin@foodflow.test` (password: `password`)
  * Gudang: `gudang@foodflow.test` (password: `password`)
  * Produksi: `produksi@foodflow.test` (password: `password`)
  * Owner: `owner@foodflow.test` (password: `password`)
* **28 Bahan Baku Lengkap** (membuat pagination 3 halaman di menu Bahan Baku).
* **22 Menu Makanan Sehat MBG** (membuat pagination di menu Produk & Evaluasi Gizi).
* **16 Batch Jadwal Rencana Produksi Dapur** (membuat pagination di menu Monitoring Produksi).
* **Puluhan Catatan Mutasi Barang** (menghasilkan cetak laporan portrait multi-halaman).

---

### F. Langkah 5: Kompilasi Aset Frontend (Tailwind CSS & Vite)
1. Install dependensi Node.js:
   ```bash
   npm install
   ```
2. Jalankan build produksi:
   ```bash
   npm run build
   ```
   *(Atau `npm run dev` jika sedang dalam proses pengembangan lokal).*

---

## 🚀 4. Panduan Menjalankan Pengujian & Sidang Demo

### A. Menjalankan Pengujian Otomatis (PHPUnit Test Suite)
Untuk memverifikasi seluruh fungsionalitas sistem berjalan tanpa error:
```bash
# Jalankan seluruh test suite
php artisan test --compact

# Jalankan pengujian autentikasi
php artisan test --filter=AuthenticationTest

# Jalankan pengujian role switcher demo
php artisan test --filter=DemoRoleSwitcherTest
```

### B. Menjalankan Formatter Kode (Laravel Pint)
Untuk memastikan seluruh file kode PHP rapi dan sesuai standar industri:
```bash
vendor/bin/pint
```

### C. Panduan Menjalankan Sidang Demo Menggunakan 1-Click Role Switcher
1. Buka browser dan login menggunakan akun apa saja (misal `admin@foodflow.test`).
2. Perhatikan dua komponen navigasi cepat yang tersedia:
   * **Dropdown Header**: Terletak di kanan atas samping tombol notifikasi.
   * **Floating Action Bar**: Melayang di pojok kanan bawah layar.
3. Saat dosen meminta melihat perspektif peran lain:
   * Klik tombol **[Gudang]** $\rightarrow$ Sistem langsung beralih ke Staf Gudang (< 1 detik).
   * Klik tombol **[Produksi]** $\rightarrow$ Sistem langsung membuka dapur masak.
   * Klik tombol **[Owner]** $\rightarrow$ Sistem langsung membuka pusat monitoring dan cetak laporan resmi eksekutif.
4. **Cetak Dokumen Laporan**:
   * Masuk ke menu **Pusat Cetak Laporan Eksekutif** (`/owner/reports`).
   * Klik salah satu laporan $\rightarrow$ Klik tombol **Cetak Dokumen / Simpan PDF**.
   * Dialog print browser akan langsung menampilkan format rapi **A4 Portrait**, header tabel berulang di tiap lembar, dan widget demo otomatis tersembunyi.

---
*Dokumen ini dirancang sebagai panduan komprehensif bagi pengembang, penguji (QA), serta bahan presentasi di hadapan dosen pembimbing dan penguji.*
