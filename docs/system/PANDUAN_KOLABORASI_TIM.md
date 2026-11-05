# PANDUAN KOLABORASI TIM & STANDAR FORMAT PROMPT ANTIGRAVITY (FOODFLOW)

Dokumen ini adalah pedoman operasional bagi seluruh 4 anggota tim pengembang proyek **FoodFlow** agar kode yang dihasilkan seragam, rapi, bebas dari tabrakan git (*merge conflict*), dan asisten AI (Antigravity) masing-masing anggota bekerja dengan standar yang sama persis.

---

## 📌 ATURAN DASAR KERJA TIM (GIT & CODING WORKFLOW)

### 1. Alur Kerja Git (Git Workflow)
Setiap kali ingin memulai fitur baru:
1. Pastikan branch `main` lokal Anda adalah yang paling mutakhir:
   ```bash
   git checkout main
   git pull origin main
   ```
2. Buat branch baru dari `main` sesuai nama resmi di `docs/Branch.md`:
   ```bash
   git checkout -b <nama-branch-resmi>
   # Contoh: git checkout -b feat/ingredients-migration-model
   ```
3. Kerjakan fitur bersama Antigravity.
4. **Format Commit Message WAJIB Bahasa Indonesia** menggunakan konvensi:
   - `feat: <penjelasan fitur baru>` (Contoh: `feat: buat migrasi dan model bahan baku`)
   - `fix: <penjelasan perbaikan bug>` (Contoh: `fix: perbaiki validasi stok minimum bahan`)
   - `test: <penjelasan penambahan test>` (Contoh: `test: tambahkan feature test crud supplier`)
   - `refactor: <penjelasan perapihan kode>`
5. Sebelum push, wajib jalankan verifikasi:
   ```bash
   vendor/bin/pint --format agent
   php artisan test --compact
   ```
6. Push branch ke GitHub dan buat Pull Request (PR) ke `main`:
   ```bash
   git push -u origin <nama-branch-resmi>
   ```

---

### 2. Standar Arsitektur & UI
- **Tech Stack**: Laravel 12/13, PHP 8.4, Tailwind CSS, Alpine.js, PostgreSQL (Supabase).
- **Layout Utama**: Selalu gunakan `<x-app-layout>` atau `@extends('layouts.app')` yang sudah ada agar sidebar, navigasi, dan flash alert seragam.
- **Validasi**: Gunakan Form Request khusus (`php artisan make:request ...`), jangan validasi manual di dalam Controller yang membengkak.
- **Status & Tipe Data**: Gunakan PHP Typed Enum di namespace `App\Enums` (seperti `UserRole` dan `UserStatus` yang sudah ada di Modul 1).
- **Testing**: Setiap branch fitur baru wajib memiliki minimal 1 Feature Test di folder `tests/Feature/`.

---

## 🤖 FORMAT PROMPT ANTIGRAVITY UNTUK 4 PENGEMBANG

Agar Antigravity dari masing-masing anggota tim menghasilkan kualitas kode, struktur file, dan gaya commit yang konsisten dengan proyek kita, berikan **Template Prompt Awal (Starter Prompt)** berikut kepada masing-masing anak saat memulai percakapan di Antigravity:

---

### 📋 TEMPLATE PROMPT UMUM (BISA DI-COPY PASTE SETIAP ANAK)

```markdown
Halo Antigravity! Kita sedang mengerjakan proyek aplikasi "FoodFlow" (Sistem Informasi Manajemen Rantai Pasok Catering Makanan Sehat).

Berikut adalah aturan baku proyek yang WAJIB kamu patuhi tanpa pengecualian:
1. **Bahasa Commit**: SEMUA git commit message WAJIB menggunakan Bahasa Indonesia dengan format Conventional Commits (contoh: `feat: tambahkan crud bahan baku`, `fix: perbaiki validasi satuan unit`).
2. **Nama Branch**: Selalu gunakan nama branch resmi yang sudah disepakati di file `docs/Branch.md`. Jangan buat nama branch sendiri!
3. **Tech Stack & Arsitektur**:
   - Backend: Laravel 12/13 (PHP 8.4), PostgreSQL, Monolith MVC.
   - Frontend: Blade Template + Tailwind CSS + Alpine.js (Interaktivitas dinamis pakai Alpine.js, BUKAN Vue/React SPA).
   - UI Layout: Wajib menginduk ke layout yang sudah ada (`layouts.app`). Gunakan komponen Blade yang sudah ada.
   - Validasi: Gunakan Form Request (`App\Http\Requests\...`).
   - Enums: Gunakan PHP Typed Enum di `App\Enums\...` untuk status/tipe.
4. **Pengujian & Formatting**:
   - Setiap fitur yang kamu buat WAJIB dibuatkan Feature Test (`tests/Feature/...`) dan dipastikan PASS saat dijalankan dengan `php artisan test --compact`.
   - Sebelum selesai, WAJIB rapikan kode dengan `vendor/bin/pint --format agent`.
5. **Referensi Spesifikasi**:
   - Baca dan ikuti spesifikasi use case di `docs/UseCaseSPEC.md` dan kebutuhan fungsional di `docs/PRD.md`.

---
TUGAS SAYA SEKARANG:
- Saya adalah: [Isi: Mahasiswa 1 / Mahasiswa 2 / Mahasiswa 3 / Mahasiswa 4]
- Branch yang sedang saya kerjakan: [Isi: contoh feat/ingredients-migration-model]
- Deskripsi fitur yang ingin dikerjakan: [Jelaskan apa yang ingin dikerjakan sesuai docs/Branch.md]

Tolong periksa branch git saya saat ini, pastikan branch sesuai, buat rancangan kodenya, implementasikan secara lengkap, buat Feature Test-nya, dan pastikan formatting Pint rapi!
```

---

### 🎯 CONTOH PROMPT KHUSUS PER NAMA ANGGOTA TIM

#### 1. Untuk Yola (Divisi Gudang & Persediaan)
> **Branch Selanjutnya**: `feat/ingredients-min-stock` (atau `feat/material-receipts-form`)
```markdown
Halo Antigravity! Saya adalah Yola di tim FoodFlow (Divisi Gudang & Persediaan).
Tolong patuhi aturan baku proyek tanpa pengecualian:
1. Semua git commit message WAJIB menggunakan Bahasa Indonesia (contoh: `feat: implementasi batas minimum stok dan alert bahan`).
2. Branch resmi yang saya kerjakan sekarang: `feat/ingredients-min-stock`.
3. Referensi use case: Modul 2 & 9 di `docs/Branch.md`, `docs/PRD.md` (FR-08, BR-06), dan `docs/UseCaseSPEC.md`.
4. Tech stack: Laravel Blade + Tailwind CSS + Alpine.js + PHPUnit Feature Test.
5. Sebelum selesai, selalu jalankan `vendor/bin/pint --format agent` dan `php artisan test --compact`.

TUGAS SAYA SEKARANG:
Tolong implementasikan fungsionalitas monitoring dan badge status batas minimum stok bahan baku (`ingredients.minimum_stock`):
- Berikan penanda visual/badge peringatan di tabel stok jika persediaan bahan saat ini <= batas minimum stok.
- Buatkan filter atau tab daftar bahan yang menipis/kritis.
- Buatkan Feature Test lengkapnya dan pastikan semua test PASS.
```

---

#### 2. Untuk Izam (Divisi Admin Penjualan & Pengadaan)
> **Branch Selanjutnya**: `feat/orders-migration-model`
```markdown
Halo Antigravity! Saya adalah Izam di tim FoodFlow (Divisi Admin Penjualan & Pengadaan Bahan).
Tolong patuhi aturan baku proyek tanpa pengecualian:
1. Semua git commit message WAJIB menggunakan Bahasa Indonesia (contoh: `feat: migrasi dan model pesanan pelanggan catering`).
2. Branch resmi yang saya kerjakan sekarang: `feat/orders-migration-model`.
3. Referensi use case: Modul 5 di `docs/Branch.md`, `docs/PRD.md` (FR-14 s/d FR-17), dan `docs/UseCaseSPEC.md` (UC-09 s/d UC-11).
4. Tech stack: Laravel Blade + Tailwind CSS + Alpine.js + PHPUnit Feature Test.
5. Sebelum selesai, selalu jalankan `vendor/bin/pint --format agent` dan `php artisan test --compact`.

TUGAS SAYA SEKARANG:
Tolong siapkan skema database dan Model untuk pesanan catering (`catering_orders` atau `orders`) serta detail item pesanannya (`catering_order_details`):
- Relasikan pesanan ke `Customer` (`customer_id`), status pesanan (Pending, Dikonfirmasi, Diproses, Selesai, Dibatalkan), tanggal pengiriman/acara, dan total harga.
- Buatkan Eloquent Model, Factory, Seeder, dan relasinya.
- Buatkan Feature Test untuk model & relasinya, lalu jalankan `php artisan test --compact`.
```

---

#### 3. Untuk Muti (Divisi Operasional Masak & Dapur Produksi) — 4 Task Inti
> **Alur 4 Pengerjaan Muti Secara Berurutan**:
> 1. `feat/production-plan-list` ➔ Lihat jadwal & menu yang harus dimasak
> 2. `feat/material-requests-create` ➔ Minta bahan baku ke gudang
> 3. `feat/production-realization-record` ➔ Catat hasil masak aktual (porsi jadi)
> 4. `feat/production-material-usage` ➔ Catat pemakaian riil bahan baku di dapur
>
> *(Catatan: Task cetak laporan & komparasi ditunda sementara ke tahap berikutnya).*

```markdown
Halo Antigravity! Saya adalah Muti di tim FoodFlow (Divisi Operasional Masak & Dapur Produksi).
Tolong patuhi aturan baku proyek tanpa pengecualian:
1. Semua git commit message WAJIB menggunakan Bahasa Indonesia (contoh: `feat: halaman daftar rencana produksi dapur`).
2. Branch resmi yang saya kerjakan sekarang: `feat/production-plan-list` (Step 1 dari 4 task inti saya).
3. Referensi use case: Modul 6 di `docs/Branch.md`, `docs/PRD.md` (FR-18 s/d FR-21), dan `docs/UseCaseSPEC.md` (UC-12).
4. Tech stack: Laravel Blade + Tailwind CSS + Alpine.js + PHPUnit Feature Test.
5. Sebelum selesai, selalu jalankan `vendor/bin/pint --format agent` dan `php artisan test --compact`.

TUGAS SAYA SEKARANG (Step 1):
Tolong buatkan antarmuka untuk staf dapur/produksi dalam memantau daftar rencana produksi (`production_plans`):
- Filter berdasarkan tanggal masak/shift dan status rencana (Draft, Dijadwalkan, Sedang Dimasak, Selesai).
- Tampilkan ringkasan menu yang harus dimasak dan target jumlah porsi.
- Batasi hak akses agar staf produksi dan admin dapat melihatnya (otorisasi role).
- Buatkan Feature Test lengkap dan rapikan dengan Pint.
```

---

#### 4. Untuk Syafrida (Divisi Monitoring Eksekutif & Pelaporan Owner) — 4 Task Inti
> **Alur 4 Pengerjaan Syafrida Secara Berurutan**:
> 1. `feat/owner-production-monitoring` ➔ Dashboard pantau progres masak dapur hari ini
> 2. `feat/owner-stock-monitoring` ➔ Pantau persediaan stok gudang & alert bahan kritis
> 3. `feat/owner-nutrition-monitoring` ➔ Pantau kepatuhan standar nutrisi & gizi menu
> 4. `feat/report-stock-print` ➔ Cetak / download PDF rekap persediaan stok bahan baku
>
> *(Catatan: Task laporan pengadaan ditunda sementara ke tahap berikutnya).*

```markdown
Halo Antigravity! Saya adalah Syafrida di tim FoodFlow (Divisi Monitoring Eksekutif & Pelaporan Owner).
Tolong patuhi aturan baku proyek tanpa pengecualian:
1. Semua git commit message WAJIB menggunakan Bahasa Indonesia (contoh: `feat: dashboard monitoring produksi untuk owner`).
2. Branch resmi yang saya kerjakan sekarang: `feat/owner-production-monitoring` (Step 1 dari 4 task inti saya).
3. Referensi use case: Modul 12 di `docs/Branch.md`, `docs/PRD.md` (FR-33), dan `docs/UseCaseSPEC.md` (UC-20).
4. Tech stack: Laravel Blade + Tailwind CSS + Alpine.js + PHPUnit Feature Test.
5. Sebelum selesai, selalu jalankan `vendor/bin/pint --format agent` dan `php artisan test --compact`.

TUGAS SAYA SEKARANG (Step 1):
Tolong buatkan halaman Dashboard Monitoring Produksi khusus untuk Owner / Manajer:
- Menampilkan metrik real-time: jumlah batch produksi hari ini, persentase capaian target porsi, dan grafik/tabel status dapur.
- Otorisasi ketat: hanya user dengan role `Owner` dan `Admin` yang bisa mengakses halaman ini. Role lain harus mendapat 403 Forbidden.
- Buatkan Feature Test otorisasi & tampilan datanya, lalu jalankan `php artisan test --compact`.
```

---

#### 5. Untuk Maharaja Zaki (Lead Developer)
> **Branch Selanjutnya**: `feat/production-ingredient-calculator`
> *(Catatan: Modul Auth, User Management, Bahan Baku, Supplier, Pelanggan, Menu Makanan, Resep Bahan, dan Rencana Produksi telah selesai & merged ✅)*

```markdown
Halo Antigravity! Saya adalah Maharaja Zaki (Lead Developer FoodFlow).
Tolong patuhi aturan baku proyek tanpa pengecualian:
1. Semua git commit message WAJIB menggunakan Bahasa Indonesia (contoh: `feat: kalkulator otomatis kebutuhan bahan baku produksi katering`).
2. Branch resmi yang saya kerjakan sekarang: `feat/production-ingredient-calculator`.
3. Referensi use case: Modul 7 di `docs/Branch.md`, `docs/PRD.md` (FR-19 s/d FR-21), dan `docs/UseCaseSPEC.md` (UC-16).
4. Catatan penting bisnis yang sudah disepakati:
   - Kategori menu ditiadakan (menu langsung berbasis nama, harga jual, dan porsi).
   - Kandungan gizi menu dikelola eksklusif oleh Staff Produksi / Ahli Gizi. Admin hanya dapat melihat nilai gizi (read-only).
   - Kebutuhan bahan baku dihitung otomatis dengan mengalikan target porsi rencana produksi dengan takaran bahan pada resep menu (`product_ingredients`).
5. Tech stack: Laravel Blade + Tailwind CSS + Alpine.js + PHPUnit Feature Test.
6. Sebelum selesai, selalu jalankan `vendor/bin/pint --format agent` dan `php artisan test --compact`.

TUGAS SAYA SEKARANG:
Tolong implementasikan modul Kalkulator Kebutuhan Bahan Produksi (UC-16 & FR-19):
- Hitung total kebutuhan masing-masing bahan baku mentah berdasarkan menu dan target porsi dari rencana produksi yang dipilih (`production_plans`).
- Agregasikan kebutuhan bahan baku yang sama dari beberapa menu dalam satu rencana produksi.
- Bandingkan total kebutuhan bahan dengan stok yang ada di gudang, tampilkan badge apakah stok bahan mencukupi atau kurang.
- Buatkan antarmuka ringkasan kalkulasi kebutuhan bahan dan Feature Test lengkapnya.
```

---

## 💡 TIPS MENGATASI MERGE CONFLICT ANTAR ANGGOTA
1. **Migrations**: Setiap anggota yang membuat migrasi baru, pastikan nama tabel dan kolom tidak tumpang tindih. Timestamp migrasi Laravel otomatis mengurutkan eksekusi secara berurutan.
2. **Routes (`routes/web.php`)**: Kelompokkan route berdasarkan prefix atau role (misal: `Route::prefix('admin')`, `Route::prefix('warehouse')`, `Route::prefix('production')`). Hal ini mencegah konflik saat merge file `routes/web.php`.
3. **Sidebar / Navigasi (`resources/views/layouts/navigation.blade.php`)**: Sidebar sudah memiliki blok kondisi berdasarkan role (`auth()->user()->role`). Setiap anggota hanya menambahkan menu di dalam blok role masing-masing!
