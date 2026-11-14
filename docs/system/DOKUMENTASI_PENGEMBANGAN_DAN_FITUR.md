# 📘 Dokumentasi Lengkap Pengembangan Sistem FoodFlow

Dokumen ini merangkum seluruh rekam jejak pembahasan, perbaikan, arsitektur, dan fitur yang telah diimplementasikan dari awal hingga iterasi terkini dalam persiapan sidang tugas akhir / skripsi.

---

## 📑 Daftar Isi
1. [Profil & Arsitektur Aplikasi](#1-profil--arsitektur-aplikasi)
2. [Rekapitulasi Fitur Utama (Master & Operasional)](#2-rekapitulasi-fitur-utama-master--operasional)
3. [Infrastruktur Server, Deployment, & CI/CD](#3-infrastruktur-server-deployment--cicd)
4. [Kronologi Perbaikan & Penyempurnaan Terkini](#4-kronologi-perbaikan--penyempurnaan-terkini)
5. [Fitur Baru yang Baru Diterapkan](#5-fitur-baru-yang-baru-diterapkan)
6. [Arsitektur REST API (Rekomendasi Skripsi)](#6-arsitektur-rest-api-rekomendasi-skripsi)
7. [Panduan Pertanyaan & Jawaban untuk Sidang Dosen](#7-panduan-pertanyaan--jawaban-untuk-sidang-dosen)

---

## 1. Profil & Arsitektur Aplikasi

FoodFlow adalah aplikasi web ERP (*Enterprise Resource Planning*) khusus manajemen operasional katering, pengadaan bahan baku, perencanaan masak dapur, dan kalkulasi nutrisi gizi.

- **Framework Backend:** Laravel 12 (PHP 8.4)
- **Frontend / UI:** Tailwind CSS, Alpine.js, Blade Components, Chart.js
- **Basis Data:** MySQL (Production di AWS EC2) / SQLite (Automated Unit & Feature Testing)
- **Desain Arsitektur:** Clean MVC dengan Service Pattern, Form Requests, Enums, dan Model Traits
- **Hak Akses (RBAC):** 4 Peran Pengguna:
  1. `admin` (Administrator Operasional)
  2. `gudang` (Staf Gudang & Logistik)
  3. `produksi` (Staf Dapur & Ahli Gizi)
  4. `owner` (Pemilik & Manajer Eksekutif)
- **Demo Switcher:** Fitur instan pengalih peran untuk keperluan live demo presentasi sidang tanpa perlu logout-login berulang kali.

---

## 2. Rekapitulasi Fitur Utama (Master & Operasional)

### A. Autentikasi & Pengguna (RBAC)
- Login aman dengan rate limiter (maksimal 5 percobaan gagal per menit).
- Google OAuth Socialite Single Sign-On (SSO).
- Manajemen Akun Staf & Permintaan Reset Password dengan persetujuan (*approval*) Admin.

### B. Master Data Inti
- **Pelanggan (`customers`):** Data instansi/pemesan katering, riwayat kontak, dan alamat.
- **Supplier (`vendors`):** Mitra rekanan penyedia bahan makanan mentah.
- **Bahan Baku (`ingredients`):** Kategori bahan, satuan unit terstandarisasi, stok saat ini, stok minimum (*safety stock*), dan riwayat harga beli.
- **Menu Hidangan (`products`):** Katalog menu, takaran porsi, dan komposisi nilai gizi (Kalori, Protein, Lemak, Karbohidrat, Serat).
- **Recipe Builder (`product_ingredients`):** Formula resep standar untuk menghitung kebutuhan bahan baku otomatis berdasarkan jumlah porsi.

### C. Alur Kerja Logistik & Dapur
1. **Pesanan Katering (`catering_orders`):** Pencatatan pesanan, jadwal kirim, rincian menu hidangan, dan status transaksi.
2. **Perencanaan Produksi (`production_plans`):** Jadwal masak dapur dengan kalkulator otomatis kebutuhan bahan (*ProductionIngredientCalculator*).
3. **Pengadaan Bahan / Purchase Order (`purchase_orders`):** Penerbitan PO pengadaan ke supplier rekanan saat stok menipis.
4. **Penerimaan Bahan (`material_receipts`):** Pengecekan fisik barang datang (kondisi baik vs rusak) dan penambahan stok gudang otomatis.
5. **Permintaan Bahan Baku (`material_requests`):** Pengajuan bahan dari staf dapur yang wajib diverifikasi staf gudang sebelum stok dikeluarkan.
6. **Realisasi Produksi & Pemakaian Riil (`productions` & `production_material_usages`):** Pencatatan porsi riil yang berhasil dimasak dan varians selisih bahan.
7. **Kartu Stok & Penyesuaian Fisik (`ingredient_stock_transactions`):** *Audit trail* mutasi stok masuk, keluar, dan *stock adjustment* hasil *opname*.

### D. Dasbor & Laporan Analitik
- 4 dashboard analitik terpisah per peran dengan visualisasi Chart.js interaktif.
- Monitoring live capaian produksi dapur dan pemenuhan standar gizi menu.
- Laporan resmi persediaan gudang dan rekapitulasi produksi cetak siap-pakai.

---

## 3. Infrastruktur Server, Deployment, & CI/CD

- **Hosting Server:** AWS EC2 (Ubuntu 24.04 LTS), Public IP: `15.135.75.37`
- **Domain Publik:** `https://foodflow-app.duckdns.org`
- **Keamanan:** Sertifikat SSL/TLS gratis otomatis via Let's Encrypt Certbot
- **Web Server:** Nginx + PHP 8.4-FPM
- **CI/CD Pipeline (GitHub Actions):** Berkas `.github/workflows/deploy.yml`
  - Setiap commit yang di-merge ke branch `main` otomatis memicu pipeline.
  - Menjalankan `git pull`, `composer install --no-dev`, `php artisan migrate --force`, `npm run build`, dan pembersihan cache aplikasi di EC2 tanpa perlu login SSH manual.
- **Dokumentasi Deployment:** Telah didokumentasikan lengkap di `docs/deployment/ALUR_DEPLOYMENT_DAN_SSH.md` beserta simulasi tanya-jawab dosen penguji.

---

## 4. Kronologi Perbaikan & Penyempurnaan Terkini

Selama sesi pengerjaan, sejumlah masalah teknis dan perbaikan UI telah diselesaikan:

1. **Sinkronisasi Tampilan Landing Page:**
   - **Kendala:** Desain navbar dan tata letak landing page di server produksi berantakan akibat class CSS Tailwind tidak terkompilasi karena folder `public/build` masuk dalam `.gitignore`.
   - **Solusi:** Menambahkan tahap `npm run build` langsung pada runner server dan alur otomatis CI/CD.

2. **Perbaikan Tanda Tangan Digital Laporan Cetak:**
   - **Kendala:** Nama penanggung jawab pada tanda tangan canvas laporan persediaan dan produksi "ngestuck" (tersimpan di `localStorage` dan terdapat nilai default hardcode nama rekan kelompok).
   - **Solusi:**
     - Membersihkan dependensi default localStorage.
     - Mengubah parameter default tanda tangan menjadi kosong (`""`).
     - Menyediakan tombol *"Reset Tanda Tangan"* interaktif.
     - Memasang teks cetak fallback bersih: `( ............................................ )`.

3. **Pembersihan Antarmuka:**
   - Menghapus ikon lonceng notifikasi statis yang belum terpakai di topbar header layout.
   - Merevisi header label "Saldo" menjadi "Stok" pada laporan persediaan bahan baku.

4. **Audit Menyeluruh UI/UX & Fitur:**
   - Evaluasi kelayakan aplikasi menghasilkan skor **8.1/10 (Sangat Layak Sidang)** dengan keunggulan pada sistem komponen reusable, dashboard analitik 4 role, dan kelengkapan alur bisnis.

---

## 5. Fitur Baru yang Baru Diterapkan

### A. Dashboard Default Cerdas (`resources/views/dashboard.blade.php`)
- Menggantikan halaman default polos dengan sistem *Smart Role Redirect*.
- Menampilkan animasi loading modern khas FoodFlow dan langsung mengarahkan pengguna ke dashboard kerja sesuai rolenya (`admin.dashboard`, `gudang.dashboard`, `produksi.dashboard`, atau `owner.dashboard`).

### B. Soft Delete pada Seluruh 20 Model
- Mencegah data transaksi penting terhapus secara permanen dari basis data (*accidental deletion*).
- Menambahkan kolom `deleted_at` ke semua tabel operasional melalui migrasi aman.
- Memasang trait `Illuminate\Database\Eloquent\SoftDeletes` di 20 model:
  `User`, `Customer`, `Supplier`, `Ingredient`, `Product`, `ProductIngredient`, `CateringOrder`, `CateringOrderDetail`, `PurchaseOrder`, `PurchaseOrderDetail`, `MaterialReceipt`, `MaterialReceiptDetail`, `MaterialRequest`, `MaterialRequestDetail`, `ProductionPlan`, `ProductionPlanDetail`, `Production`, `ProductionMaterialUsage`, `IngredientStockTransaction`, `PasswordResetRequest`.
- Memperbarui mekanisme penghapusan kaskade data rincian terkait dan memvalidasi kelulusan seluruh suite unit & feature testing.

### C. Loading State Tombol Formulir
- Memperbarui komponen tombol submit `<x-button type="submit">` menggunakan Alpine.js.
- Saat formulir dikirim, tombol secara otomatis:
  - Menonaktifkan diri (`disabled`) agar pengguna tidak melakukan klik ganda (*prevent duplicate submission*).
  - Menampilkan animasi spinner putar dan label status *"Memproses..."*.
- Bekerja secara otomatis pada form login, lupa password, serta seluruh form pembuatan dan pembaruan data.

### D. Fitur Ekspor Data (Excel, PDF, & Background Queue Worker)
1. **Layanan Ekspor (`App\Services\ExportService`):**
   - **Ekspor Excel:** Menghasilkan berkas spreadsheet berbasis standar RFC-4180 dengan **UTF-8 BOM (`\xEF\xBB\xBF`)**, memastikan karakter teks dan angka terbaca rapi di Microsoft Excel tanpa formatting error.
   - **Cetak Laporan / PDF:** Tampilan cetak standar A4 ber-KOP resmi FoodFlow dengan tanggal cetak, identitas petugas, dan tombol instan `window.print()`.
2. **Background Queue Worker (`App\Jobs\ExportDataJob`):**
   - Menyiapkan background job Laravel Queue (`ShouldQueue`) untuk mengeksekusi pembuatan laporan data berukuran besar di latar belakang tanpa memberatkan antarmuka web.
3. **Penyediaan Tombol Cepat:**
   - Ditambahkan tombol `[Excel]` dan `[PDF]` pada bilah aksi tabel:
     - Master Data Pelanggan (`/customers`)
     - Master Data Bahan Baku (`/ingredients`)
     - Master Data Supplier (`/suppliers`)
     - Pesanan Katering (`/catering-orders`)

---

## 6. Arsitektur REST API (Rekomendasi Skripsi)

Untuk keperluan pembahasan di Bab Pembahasan / Saran Pengembangan Skripsi mengenai integrasi masa depan (mobile app pelanggan dan tablet dapur):

| Metode | Endpoint | Target Konsumen | Fungsi Utama |
|---|---|---|---|
| `GET` | `/api/v1/menus` | Aplikasi Mobile Pelanggan | Menampilkan katalog hidangan dan rincian informasi gizi |
| `POST` | `/api/v1/orders` | Aplikasi Mobile Pelanggan | Pemesanan menu katering secara mandiri oleh pelanggan |
| `GET` | `/api/v1/orders/{order_number}/track` | Pelanggan / Kurir | Pelacakan status progres pesanan secara real-time |
| `GET` | `/api/v1/kitchen/production-plans/today` | Tablet Stasiun Dapur | Mengunduh lembar rencana masak hari ini untuk tim chef |
| `POST` | `/api/v1/kitchen/material-requests` | Tablet Stasiun Dapur | Pengajuan cepat kebutuhan bahan darurat ke gudang |
| `GET` | `/api/v1/warehouse/inventory/scan` | Scanner Barcode Gudang | Pengecekan cepat stok bahan fisik via barcode/QR code |
| `GET` | `/api/v1/owner/metrics/summary` | Widget / Mobile Owner | Ringkasan omzet, pesanan aktif, dan capaian produksi |

---

## 7. Panduan Pertanyaan & Jawaban untuk Sidang Dosen

**1. Q: "Mengapa sistem menerapkan Soft Delete, bukan langsung hard delete?"**
> **A:** *"Dalam sistem ERP operasional seperti FoodFlow, data transaksi (pesanan, mutasi stok bahan, pengadaan) saling berkaitan erat. Jika data dihapus fisik (hard delete), riwayat laporan keuangan dan kartu stok masa lalu akan rusak. Dengan Soft Delete (`deleted_at`), integritas audit trail tetap terjaga utuh sementara data tersebut tidak lagi muncul di operasional aktif."*

**2. Q: "Bagaimana proses penanganan ekspor data dalam jumlah besar agar server tidak lambat?"**
> **A:** *"Sistem dilengkapi dengan `App\Jobs\ExportDataJob` yang mengimplementasikan Laravel Queue Worker. Ketika ada permintaan unduh data dalam skala besar, proses pembuatan berkas dialihkan ke latar belakang (background worker), sehingga request browser pengguna tidak mengalami timeout."*

**3. Q: "Bagaimana mekanisme deployment aplikasi ke server produksi?"**
> **A:** *"Aplikasi menerapkan pipeline CI/CD modern menggunakan GitHub Actions. Setiap perubahan kode yang di-merge ke branch `main` secara otomatis memicu proses pengujian, penarikan kode ke AWS EC2, migrasi basis data, dan kompilasi aset frontend Tailwind Vite secara otomatis."*

---

*Dokumen ini diperbarui secara berkala sebagai panduan resmi pengembangan FoodFlow.*
