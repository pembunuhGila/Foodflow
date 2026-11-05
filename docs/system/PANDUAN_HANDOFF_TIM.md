# PANDUAN HANDOFF KRONOLOGIS: ALUR URUTAN STEP-BY-STEP & PROMPT LENGKAP (FOODFLOW)

Dokumen ini adalah panduan resmi alur kerja tim pengembang **FoodFlow** yang disusun **berdasarkan urutan logis alur rantai pasok sistem (kronologis)**. Alur ini menjelaskan secara tepat siapa yang harus mengerjakan apa, kapan harus mulai, nama branch resmi, pesan commit Bahasa Indonesia, serta **template prompt lengkap siap pakai** untuk Antigravity di setiap tahapannya.

---

## 📊 1. STATUS SISTEM SAAT INI (SUDAH SELESAI & MERGED ✅)

Modul-modul dasar dan transaksi berikut telah **100% selesai, lulus 278 Tests, dan berada di branch `main`**:
1. Autentikasi Multi-Role, Staf, Password Reset Approval, & Logout Confirmation.
2. Master Bahan Baku Mentah (`ingredients`) & Master Vendor/Supplier (`vendors`).
3. Master Pelanggan Catering (`customers`).
4. Master Menu Hidangan Sehat & Nilai Gizi Terproteksi (`products`).
5. Peracik Komposisi Resep Menu Katering (`product_ingredients` & Recipe Builder).
6. Perencanaan Produksi & Penjadwalan Masak Harian Dapur (`production_plans` & `production_plan_details`).
7. **Langkah 1 (Syafrida)**: Kontrol Batas Minimum Stok & Badge Alert Gudang (`feat/ingredients-min-stock` — Merged PR #14 ✅).
8. **Langkah 2 (Izam)**: Skema Database & Model Pesanan Catering (`feat/orders-migration-model` — Merged PR #12 ✅).
9. **Langkah 3 (Izam)**: Formulir Pembuatan Pesanan Catering Pelanggan (`feat/orders-create-form` — Merged PR #15 ✅).
10. **Langkah 4 (Izam)**: Tabel Daftar & Filter Status Pesanan Catering (`feat/orders-list-status` — Merged PR #16 ✅).
11. **Langkah 5 (Izam)**: Detail Pesanan, Ringkasan Invoice & Aksi Batal (`feat/orders-detail-view` — Merged PR #17 ✅).
12. **Langkah 6 (Syafrida)**: Kalkulator Kebutuhan Bahan Dapur & Kitchen Sheet (`feat/production-ingredient-calculator` — Merged PR #18 ✅).
13. **Langkah 7 (Syafrida)**: Tampilan Jadwal Masak & Monitoring Produksi Dapur (`feat/production-plan-list` — Merged PR #25 ✅).
14. **Langkah 8 (Syafrida)**: Form Pengajuan Permintaan Bahan Mentah Dapur ke Gudang (`feat/material-requests-create` — Merged PR #26 ✅).
15. **Langkah 9 (Yola)**: Formulir Pembuatan Purchase Order ke Supplier (`feat/purchase-orders-create` — Merged PR #19 ✅).
16. **Langkah 10 (Yola)**: Monitoring Daftar Purchase Order ke Supplier (`feat/purchase-orders-list` — DONE ✅).
17. **Langkah 11 (Yola)**: Penerimaan Bahan Supplier & Pemeriksaan Fisik (`feat/material-receipts-form` — DONE ✅).
18. **Langkah 12 (Yola)**: Pencatatan Transaksi Stok Masuk / Stok IN (`feat/material-receipt-stock-in` — DONE ✅).
19. **Langkah 13 (Muti)**: Verifikasi Permintaan Bahan & Transaksi Stok Keluar Gudang (`feat/material-requests-verify-stock-out` — Merged PR #23 ✅).
20. **Langkah 14 (Muti)**: Pencatatan Realisasi Porsi Masak Matang & Status Selesai Dapur (`feat/production-realization-record` — Merged PR #24 ✅).
21. **Langkah 15 (Muti)**: Pencatatan Penggunaan Bahan Baku Riil Dapur (`feat/production-material-usage` — DONE ✅).
22. **Langkah 16 (Yola)**: Buku Kartu Mutasi Stok Bahan & Penyesuaian Fisik / Adjustment (`feat/stock-cards-adjustment` — DONE ✅).
23. **Langkah 18 (Izam)**: Monitoring Evaluasi Gizi & Pusat Cetak Laporan PDF Owner (`feat/owner-nutrition-monitoring-reports` — DONE ✅).

---

## 🎯 2. KLARIFIKASI BATASAN RUANG LINGKUP (IN-SCOPE VS OUT-OF-SCOPE)

Berdasarkan telaah mendalam terhadap **`docs/PRD.md`** dan **`docs/UseCaseSPEC.md`**, seluruh pengembang wajib mematuhi batasan istilah dan fungsionalitas berikut:

### A. Pemeriksaan Kondisi Bahan Diterima (Bukan "Inspeksi Mutu Lab")
* **In-Scope (UC-05, UC-06, FR-21, FR-22)**:
  * Staf Gudang mencatat barang tiba dari supplier dan memeriksa kondisinya menggunakan pilihan status sederhana: **`Baik`**, **`Rusak`**, **`Kedaluwarsa`**, atau **`Tidak Sesuai`**.
  * Sesuai **BR-04**, hanya bahan berstatus **`Baik`** yang menambah stok siap pakai. Bahan rusak/kedaluwarsa dicatat kuantitasnya dan tidak masuk stok.
* **Out-of-Scope (PRD Bagian 5.2)**:
  * Tidak ada modul terpisah untuk Quality Control (QC) laboratorium/saintifik.
  * Tidak ada modul analisis food waste terpisah atau audit log terperinci.

### B. Pencatatan Transaksi Mutasi Stok (Bukan "Edit Saldo Bebas")
* **In-Scope (UC-07, UC-08, FR-24, FR-25, BR-03)**:
  * Stok bahan baku selalu dihitung dari riwayat mutasi transaksi: `Stok Tersedia = total IN - total OUT +/- ADJUSTMENT`.
  * Penambahan stok terjadi lewat transaksi **Stok Masuk (`IN`)** saat penerimaan PO disetujui. Pengurangan stok terjadi lewat **Stok Keluar (`OUT`)** saat permintaan bahan dapur diverifikasi.
  * Koreksi selisih fisik vs sistem dicatat sebagai transaksi **`ADJUSTMENT`** (FR-26).
* **Out-of-Scope (PRD Bagian 5.2)**:
  * Tidak ada modul akuntansi atau perhitungan Harga Pokok Produksi (HPP).
  * Pengguna tidak boleh mengedit langsung angka saldo stok tanpa membuat record transaksi mutasi.

---

## 🛠️ 3. PRASYARAT & KONFIGURASI SEBELUM MEMULAI

Setiap anggota tim **WAJIB** menjalankan langkah ini sebelum mengetik prompt baru:

### A. Sinkronisasi Git Lokal
```bash
git checkout main
git pull origin main
```

### B. Konfigurasi Database Supabase di File `.env`
Pastikan file `.env` mengarah ke database Supabase yang sama:
```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-south-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.lokcpqfttsbjzvfdhimn
DB_PASSWORD=arsipdunia123
DB_SSLMODE=require
```

### C. Build Aset Frontend & Bersihkan Cache
```bash
composer install
npm install
npm run build
php artisan optimize:clear
php artisan migrate
php artisan test --compact
```
*(Hasil verifikasi saat ini harus menunjukkan **278 passed**).*

---

### 🔑 Akun Login Pengujian Manual di Browser (`php artisan serve`)

| Role | Email Login | Password Default | Hak Akses Utama |
|---|---|---|---|
| **Admin** | `admin@foodflow.test` | `password` | Kelola Staf, Reset Password, Menu (Gizi Terproteksi), Supplier, Pelanggan, Order, PO |
| **Gudang** | `gudang@foodflow.test` | `password` | Persediaan Bahan Baku, Penerimaan Supplier, Mutasi Stok, Verifikasi Permintaan Bahan |
| **Produksi** | `produksi@foodflow.test` | `password` | Menu Makanan & Nilai Gizi Penuh, Resep, Jadwal Produksi Dapur, Permintaan Bahan, Realisasi Masak |
| **Owner** | `owner@foodflow.test` | `password` | Monitoring Eksekutif (Produksi, Stok, Gizi) & Cetak Laporan PDF (Read-Only) |

---

# 🚀 PANDUAN URUTAN EKSEKUSI SISTEM (STEP-BY-STEP)

Berikut adalah pembagian urutan kerja yang adil, seimbang, dan runtut secara kronologis rantai pasok sistem:

| Urutan | Penanggung Jawab | Modul / Fitur | Branch Resmi | Status |
|:---:|:---|:---|:---|:---:|
| **1** | **Syafrida** | Kontrol Batas Minimum Stok & Badge Alert Gudang | `feat/ingredients-min-stock` | **DONE (PR #14)** ✅ |
| **2** | **Izam** | Skema Database & Model Pesanan Catering | `feat/orders-migration-model` | **DONE (PR #12)** ✅ |
| **3** | **Izam** | Formulir Input Pesanan Catering (Alpine.js) | `feat/orders-create-form` | **DONE (PR #15)** ✅ |
| **4** | **Izam** | Tabel Daftar & Filter Status Pesanan Catering | `feat/orders-list-status` | **DONE (PR #16)** ✅ |
| **5** | **Izam** | Detail Pesanan, Ringkasan Invoice & Aksi Batal | `feat/orders-detail-view` | **DONE (PR #17)** ✅ |
| **6** | **Syafrida** | Kalkulator Kebutuhan Bahan Dapur & Kitchen Sheet | `feat/production-ingredient-calculator` | **DONE (PR #18)** ✅ |
| **7** | **Syafrida** | Tampilan Jadwal Masak & Monitoring Produksi Dapur | `feat/production-plan-list` | **DONE (PR #25)** ✅ |
| **8** | **Syafrida** | Form Permintaan Bahan Mentah Dapur ke Gudang | `feat/material-requests-create` | **DONE (PR #26)** ✅ |
| **9** | **Yola** | Form Pembuatan Purchase Order Pengadaan ke Supplier | `feat/purchase-orders-create` | **DONE (PR #19)** ✅ |
| **10** | **Yola** | Monitoring Daftar Purchase Order ke Supplier | `feat/purchase-orders-list` | **DONE** ✅ |
| **11** | **Yola** | Penerimaan Bahan Supplier & Pemeriksaan Fisik | `feat/material-receipts-form` | **DONE** ✅ |
| **12** | **Yola** | Pencatatan Transaksi Stok Masuk (Stok IN) | `feat/material-receipt-stock-in` | **DONE** ✅ |
| **13** | **Muti** | Verifikasi Permintaan Bahan & Stok Keluar (Stok OUT) | `feat/material-requests-verify-stock-out` | **DONE (PR #23)** ✅ |
| **14** | **Muti** | Pencatatan Realisasi Porsi Masak & Status Selesai | `feat/production-realization-record` | **DONE (PR #24)** ✅ |
| **15** | **Muti** | Pencatatan Penggunaan Bahan Aktual Dapur | `feat/production-material-usage` | **DONE** ✅ |
| **16** | **Yola** | Kartu Mutasi Stok Bahan Baku & Stock Adjustment | `feat/stock-cards-adjustment` | **DONE** ✅ |
| **17** | **Maharaja Zaki (Aku)** | Dashboard Monitoring Capaian Produksi Owner | `feat/owner-production-monitoring` | **DONE** ✅ |
| **18** | **Izam** | Monitoring Evaluasi Gizi & Cetak Laporan PDF Owner | `feat/owner-nutrition-monitoring-reports` | **DONE** ✅ |

---

## 🟢 BAGIAN A: URUTAN YANG SUDAH SELESAI (DONE ✅)

### 📌 URUTAN 1 (SYAFRIDA): Batas Minimum Stok & Badge Alert Gudang — DONE ✅
- **Branch**: `feat/ingredients-min-stock` (PR #14)
- **Ringkasan**: Menambahkan kolom `current_stock` nullable, query scope `lowStock()`, helper `isLowStock()`, indikator badge alert pada tabel bahan, dan widget peringatan restock di dashboard gudang.

### 📌 URUTAN 2 (IZAM): Skema Database & Model Pesanan Catering — DONE ✅
- **Branch**: `feat/orders-migration-model` (PR #12)
- **Ringkasan**: Membuat migrasi tabel `catering_orders` dan `catering_order_details`, relasi Eloquent ke Customer, User, dan Product, serta auto-numbering `ORD-YYYYMMDD-XXXX`.

### 📌 URUTAN 3 (IZAM): Formulir Input Pesanan Catering — DONE ✅
- **Branch**: `feat/orders-create-form` (PR #15)
- **Ringkasan**: Membuat form pemesanan dengan baris dinamis Alpine.js, validasi item pesanan, dan perhitungan otomatis subtotal serta total harga.

### 📌 URUTAN 4 (IZAM): Tabel Daftar & Filter Status Pesanan — DONE ✅
- **Branch**: `feat/orders-list-status` (PR #16)
- **Ringkasan**: Membuat halaman index daftar pesanan dengan filter status operasional, rentang tanggal, pencarian, dan quick status update endpoint.

### 📌 URUTAN 5 (IZAM): Detail Pesanan & Ringkasan Invoice — DONE ✅
- **Branch**: `feat/orders-detail-view` (PR #17)
- **Ringkasan**: Menampilkan rincian pesanan, informasi PIC pelanggan, pratinjau invoice ringkas dengan tata letak cetak, edit form, dan kontrol pembatalan.

### 📌 URUTAN 6 (SYAFRIDA): Kalkulator Kebutuhan Bahan Dapur — DONE ✅
- **Branch**: `feat/production-ingredient-calculator` (PR #18)
- **Ringkasan**: Membuat service `UnitConverter` dan `ProductionIngredientCalculator` untuk menghitung kebutuhan bahan dapur, perbandingan stok gudang (kondisi 1-4), dan tampilan lembar kerja dapur (`production-plans.ingredients`).

### 📌 URUTAN 7 (SYAFRIDA): Tampilan Jadwal Masak & Monitoring Produksi Dapur — DONE ✅
- **Branch**: `feat/production-plan-list` (PR #25)
- **Ringkasan**: Redesign halaman index rencana produksi dapur dengan summary kartu status (Draft, Scheduled, In Progress, Completed), filter tanggal masak, filter status, empty state, pagination, dan tombol pintas menuju Lembar Kerja Bahan Dapur. Dilengkapi 12 feature tests pada `ProductionPlanListTest` (100% PASS).

### 📌 URUTAN 8 (SYAFRIDA): Form Permintaan Bahan Mentah Dapur ke Gudang — DONE ✅
- **Branch**: `feat/material-requests-create` (PR #26)
- **Ringkasan**: Membuat skema tabel `material_requests` dan `material_request_details` dengan auto-numbering `REQ-YYYYMMDD-XXXX`. Form pengajuan bahan mendukung 2 metode (Metode 1: dari Rencana Produksi via kalkulator resep, dan Metode 2: Permintaan Tambahan dapur dengan baris dinamis Alpine.js).

### 📌 URUTAN 9 (YOLA): Form Pembuatan Purchase Order ke Supplier — DONE ✅
- **Branch**: `feat/purchase-orders-create` (PR #19)
- **Ringkasan**: Membuat migrasi tabel `purchase_orders` dan `purchase_order_details`, model, form pembuatan PO dengan baris dinamis bahan, pemilihan supplier, dan auto-generate nomor PO `PO-YYYYMMDD-XXXX`.

### 📌 URUTAN 13 (MUTI): Verifikasi Permintaan Bahan & Transaksi Stok Keluar (Stok OUT) — DONE ✅
- **Branch**: `feat/material-requests-verify-stock-out` (PR #23)
- **Ringkasan**: Menyediakan antarmuka verifikasi permintaan bahan dapur oleh Gudang/Admin. Saat disetujui, sistem otomatis mencatatkan mutasi stok keluar (`OUT`) pada tabel `ingredient_stock_transactions` (**BR-05**) dan memotong `current_stock` secara atomic menggunakan database transaction. Dilengkapi 12 feature tests pada `MaterialRequestVerifyTest` (100% PASS).

### 📌 URUTAN 14 (MUTI): Pencatatan Realisasi Porsi Masak & Status Selesai — DONE ✅
- **Branch**: `feat/production-realization-record` (PR #24)
- **Ringkasan**: Membuat skema tabel `productions` dan formulir pencatatan realisasi porsi hidangan matang hasil masak dapur. Staf menginput porsi aktual vs rencana; jika seluruh menu tercapai, status rencana produksi otomatis diperbarui menjadi `completed`. Dilengkapi 19 feature tests pada `ProductionRealizationTest` (100% PASS).

### 📌 URUTAN 10 (YOLA): Monitoring Daftar Purchase Order ke Supplier — DONE ✅
- **Branch**: `feat/purchase-orders-list`
- **Ringkasan**: Menampilkan daftar nomor PO, vendor, tanggal pemesanan, estimasi kirim, status PO, pencarian dinamis nomor PO dan nama vendor, serta form update status pesanan pengadaan (Draft, Ordered, Received, Cancelled) khusus Admin. Dilengkapi 8 feature tests pada `PurchaseOrderListTest` (100% PASS).

### 📌 URUTAN 11 & 12 (YOLA): Penerimaan Bahan Supplier, Pemeriksaan Fisik & Transaksi Stok IN — DONE ✅
- **Branch**: `feat/material-receipts-form` & `feat/material-receipt-stock-in`
- **Ringkasan**: Membuat skema tabel `material_receipts` dan `material_receipt_details` dengan auto-numbering `RC-YYYYMMDD-XXXX`. Formulir penerimaan terhubung dengan PO atau mandiri, verifikasi kondisi fisik (Baik, Rusak, Kedaluwarsa, Tidak Sesuai). Sesuai **BR-04**, hanya bahan berstatus `Baik` yang mencatat transaksi mutasi stok masuk (`IN`) pada `ingredient_stock_transactions` dan menambah saldo `current_stock` gudang. Dilengkapi 6 feature tests pada `MaterialReceiptTest` & `MaterialReceiptStockInTest` (100% PASS).

### 📌 URUTAN 15 (MUTI): Pencatatan Penggunaan Bahan Baku Riil Dapur — DONE ✅
- **Branch**: `feat/production-material-usage`
- **Ringkasan**: Membuat skema tabel `production_material_usages` untuk membandingkan takaran standar resep (kebutuhan rencana) dengan kuantitas aktual bahan yang dihabiskan saat memasak di dapur (FR-35, UC-18). Menghitung selisih efisiensi dapur (+ boros / - hemat) beserta catatan bahan tumpah atau sisa. Dilengkapi 5 feature tests pada `ProductionMaterialUsageTest` (100% PASS).

### 📌 URUTAN 16 (YOLA): Buku Kartu Mutasi Stok Bahan & Stock Adjustment — DONE ✅
- **Branch**: `feat/stock-cards-adjustment`
- **Ringkasan**: Menyediakan Buku Kartu Mutasi Stok Bahan Baku untuk memantau kronologi pergerakan stok (IN, OUT, Adjustment) dengan filter bahan baku & rentang tanggal (FR-24, FR-29, FR-38). Menyediakan formulir Stock Adjustment (FR-26, BR-03) untuk koreksi selisih hasil opname fisik gudang dengan alasan wajib diisi. Dilengkapi 7 feature tests pada `StockCardAdjustmentTest` (100% PASS).

### 📌 URUTAN 18 (IZAM): Monitoring Evaluasi Gizi & Cetak Laporan PDF Owner — DONE ✅
- **Branch**: `feat/owner-nutrition-monitoring-reports`
- **Ringkasan**: Menyediakan halaman Monitoring & Evaluasi Gizi Menu (UC-11, FR-39) untuk memantau profil kalori, protein, lemak, karbohidrat, dan serat pangan menu MBG. Menyediakan Pusat Cetak Laporan Eksekutif Resmi (UC-12, FR-40) mencakup Laporan Rekapitulasi Produksi dan Laporan Mutasi Persediaan Bahan Gudang dengan layout cetak bersih (`@media print`), kop surat FoodFlow, dan kolom tanda tangan penanggung jawab. Dilengkapi 6 feature tests pada `OwnerReportPrintTest` (100% PASS).

---

## 🟡 BAGIAN B: URUTAN BERIKUTNYA YANG HARUS DIKERJAKAN

Satu-satunya modul yang tersisa sebelum sistem 100% rampung:

---

### 📌 URUTAN 17 (MAHARAJA ZAKI / AKU): Dashboard Monitoring Capaian Produksi Owner — DONE ✅
- **Penanggung Jawab**: Maharaja Zaki (Lead Developer)
- **Branch**: `feat/owner-production-monitoring`
- **Pesan Commit**: `feat: dashboard monitoring live capaian produksi masak dapur untuk owner`
- **Dokumen Acuan**: FR-37, UC-10
- **Ringkasan**: Menyediakan dashboard monitoring live capaian produksi dapur khusus Owner & Admin (FR-37 & UC-10). Menampilkan Ringkasan Eksekutif (Total porsi rencana vs porsi realisasi aktual, persentase capaian efisiensi dapur, jumlah batch produksi aktif, dan efisiensi variansi bahan baku riil), Tabel Pemantauan Realtime per batch masak lengkap dengan progress bar porsi dan status efisiensi bahan, filter periode waktu (Hari Ini, Minggu Ini, Bulan Ini, Semua), filter status operasional, serta otorisasi ketat (Role Owner & Admin diizinkan, Gudang & Produksi ditolak 403 Forbidden). Dilengkapi 9 feature tests pada `OwnerProductionMonitoringTest` (100% PASS).
