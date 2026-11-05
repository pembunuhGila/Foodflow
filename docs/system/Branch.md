# DAFTAR BRANCH, ROADMAP PRIORITAS & ARSITEKTUR TEKNIS (FOODFLOW)

Dokumen ini adalah acuan resmi urutan pengerjaan (*roadmap*), matriks pembagian tugas tim pengembang, penamaan branch standar, serta **rekam jejak arsitektur teknis** (middleware, form request, controller, model, dan aturan bisnis) untuk setiap branch aplikasi FoodFlow.

---

## 👥 MATRIKS PEMBAGIAN TUGAS TIM & STATUS TERKINI

### 👤 1. Maharaja Zaki (Lead Developer / Aku)
> **Tanggung Jawab**: Arsitektur Inti, Autentikasi Multi-Role, Master Bahan Baku & Supplier, Master Menu & Resep Gizi, Fondasi Rencana Produksi, serta Dashboard Monitoring Produksi Owner.
- [x] `feat/auth-multi-role` ✅ *(Merged to `main`)*
- [x] `feat/user-management` ✅ *(Merged to `main`)*
- [x] `feat/password-reset-admin-approval` ✅ *(Merged to `main`)*
- [x] `feat/logout-confirmation` ✅ *(Merged to `main`)*
- [x] `feat/ingredients-migration-model` ✅ *(Merged to `main`)*
- [x] `feat/ingredients-crud` ✅ *(Merged to `main`)*
- [x] `feat/suppliers-crud` ✅ *(Merged to `main`)*
- [x] `feat/products-crud` ✅ *(Merged to `main` via PR #9)*
- [x] `feat/product-recipe-builder` ✅ *(Merged to `main` via PR #10)*
- [x] `feat/production-plan-create` ✅ *(Merged to `main` via PR #11)*
- [ ] `feat/owner-production-monitoring` ⏳ **(Langkah 17 — Dashboard Live Monitoring Capaian Produksi Dapur untuk Owner/Manager)**

---

### 👤 2. Izam — Divisi Admin Penjualan & Pengadaan Bahan (Siklus Pesanan Katering & Pelaporan Gizi)
> **Tanggung Jawab**: Master Pelanggan Catering, Siklus Lengkap Transaksi Pesanan Pelanggan (Catering Orders), dan Evaluasi Kepatuhan Gizi serta Cetak Laporan PDF Owner.
- [x] `feat/customers-crud` ✅ *(Merged to `main` via PR #8)*
- [x] `feat/orders-migration-model` ✅ *(Merged to `main` via PR #12)*
- [x] `feat/orders-create-form` ✅ *(Merged to `main` via PR #15)*
- [x] `feat/orders-list-status` ✅ *(Merged to `main` via PR #16)*
- [x] `feat/orders-detail-view` ✅ *(Merged to `main` via PR #17)*
- [ ] `feat/owner-nutrition-monitoring-reports` ⏳ **(Langkah 18 — Monitoring Evaluasi Kepatuhan Gizi Menu Hidangan Sehat & Cetak Laporan PDF Owner)**

---

### 👤 3. Syafrida — Divisi Produksi & Gizi (Kalkulasi Kebutuhan & Jadwal Masak)
> **Tanggung Jawab**: Kontrol Ambang Batas Stok Bahan, Agregasi Kebutuhan Bahan Dapur, Pemantauan Jadwal Masak, dan Permintaan Bahan Baku.
- [x] `feat/ingredients-min-stock` ✅ *(Merged to `main` via PR #14 — Langkah 1)*
- [x] `feat/production-ingredient-calculator` ✅ *(Merged to `main` via PR #18 — Langkah 6)*
- [ ] `feat/production-plan-list` ⏳ **(TUGAS SYAFRIDA SEKARANG: Langkah 7 — Tampilan Jadwal Masak & Monitoring Dapur)**
- [ ] `feat/material-requests-create` ⏳ **(TUGAS SYAFRIDA BERIKUTNYA: Langkah 8 — Form Pengajuan Permintaan Bahan Mentah ke Gudang)**

---

### 👤 4. Yola — Divisi Gudang & Pengadaan Bahan Baku (Siklus Masuk & Mutasi Stok)
> **Tanggung Jawab**: Pengadaan Purchase Order ke Supplier, Penerimaan Fisik Bahan, Pemeriksaan Kondisi Barang Tiba, Pencatatan Stok Masuk (IN), dan Kartu Mutasi Stok Gudang.
- [x] `feat/purchase-orders-create` ✅ *(Merged to `main` via PR #19 — Langkah 9)*
- [ ] `feat/purchase-orders-list` ⏳ **(Langkah 10 — Monitoring PO & Konfirmasi Pengadaan Supplier)**
- [ ] `feat/material-receipts-form` ⏳ **(Langkah 11 — Formulir Penerimaan Bahan & Pemeriksaan Kondisi Fisik Sederhana)**
- [ ] `feat/material-receipt-stock-in` ⏳ **(Langkah 12 — Pencatatan Otomatis Transaksi Stok Masuk / Stok IN)**
- [ ] `feat/stock-cards-adjustment` ⏳ **(Langkah 16 — Buku Kartu Mutasi Stok Bahan Baku & Formulir Penyesuaian Fisik / Adjustment)**

---

### 👤 5. Muti — Divisi Operasional Masak Dapur & Pelaksanaan Produksi
> **Tanggung Jawab**: Verifikasi Permintaan Bahan Produksi di Gudang (Stok OUT), Pencatatan Realisasi Hasil Masak Porsi Matang, dan Pencatatan Pemakaian Bahan Baku Riil.
- [ ] `feat/material-requests-verify-stock-out` ⏳ **(Langkah 13 — Verifikasi Permintaan Bahan Dapur & Pencatatan Transaksi Stok Keluar / OUT)**
- [ ] `feat/production-realization-record` ⏳ **(Langkah 14 — Pencatatan Realisasi Porsi Matang Hasil Masak & Status Selesai)**
- [ ] `feat/production-material-usage` ⏳ **(Langkah 15 — Pencatatan Penggunaan Bahan Baku Riil Saat Masak vs Rencana)**

---

## 🎯 KLARIFIKASI BATASAN RUANG LINGKUP (IN-SCOPE VS OUT-OF-SCOPE)

Berdasarkan telaah dokumen **`docs/PRD.md`** dan **`docs/UseCaseSPEC.md`**:

1. **Pemeriksaan Kondisi Bahan Diterima (Bukan "Inspeksi Mutu Lab")**:
   - **In-Scope (UC-05, UC-06, FR-21, FR-22)**: Petugas gudang mencatat barang tiba dari supplier dan memeriksa kondisinya menggunakan pilihan status sederhana: **`Baik`**, **`Rusak`**, **`Kedaluwarsa`**, atau **`Tidak Sesuai`**. Sesuai **BR-04**, hanya bahan berstatus **`Baik`** yang menambah stok siap pakai.
   - **Out-of-Scope (PRD 5.2)**: Tidak ada modul terpisah untuk Quality Control (QC) laboratorium saintifik, analisis food waste terpisah, atau audit log terperinci.
2. **Pencatatan Transaksi Mutasi Stok (Bukan "Edit Saldo Bebas")**:
   - **In-Scope (UC-07, UC-08, FR-24, FR-25, BR-03)**: Stok bahan baku selalu dihitung dari riwayat mutasi transaksi: `Stok Tersedia = total IN - total OUT +/- ADJUSTMENT`. Penambahan stok terjadi lewat transaksi **Stok Masuk (`IN`)** saat penerimaan PO disetujui. Pengurangan stok terjadi lewat **Stok Keluar (`OUT`)** saat permintaan bahan dapur diverifikasi. Koreksi selisih fisik vs sistem dicatat sebagai transaksi **`ADJUSTMENT`** (FR-26).
   - **Out-of-Scope (PRD 5.2)**: Tidak ada modul akuntansi atau perhitungan Harga Pokok Produksi (HPP).

---

## 🔗 RANTAI KETERGANTUNGAN DATA & URUTAN SISTEM (KRONOLOGIS)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 1: FONDASI DATA UTAMA & PESANAN (ZAKI & IZAM) - [SELESAI 100% ✅]      │
│ 1. Bahan Baku & Supplier : ZAKI [SELESAI & MERGED ✅]                       │
│ 2. Pelanggan Catering    : IZAM (feat/customers-crud) [SELESAI & MERGED ✅] │
│ 3. Menu Makanan & Gizi   : ZAKI (feat/products-crud) [SELESAI & MERGED ✅]  │
│ 4. Resep Komposisi Menu  : ZAKI (feat/product-recipe-builder) [MERGED ✅]   │
│ 5. Rencana Jadwal Masak  : ZAKI (feat/production-plan-create) [MERGED ✅]   │
│ 6. Siklus Order Catering : IZAM (Step 2 s/d 5) [SELESAI & MERGED ✅]         │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Menu, Resep, Order, & Rencana Sudah Siap)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 2: KALKULASI, JADWAL & PERMINTAAN BAHAN (SYAFRIDA) 🍲 📋               │
│ 7. SYAFRIDA Step 1 : feat/ingredients-min-stock [SELESAI & MERGED ✅]       │
│ 8. SYAFRIDA Step 6 : feat/production-ingredient-calculator [MERGED ✅]      │
│ 9. SYAFRIDA Step 7 : feat/production-plan-list ⏳ (SIAP DIKERJAKAN)        │
│ 10. SYAFRIDA Step 8: feat/material-requests-create ⏳ (Permintaan Bahan)    │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Kebutuhan Bahan Diketahui & PO Diterbitkan)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 3: PENGADAAN & PENERIMAAN GUDANG (YOLA) 📦 🚚                         │
│ 11. YOLA Step 9 : feat/purchase-orders-create [SELESAI & MERGED ✅]         │
│ 12. YOLA Step 10: feat/purchase-orders-list ⏳ (Monitoring PO Supplier)     │
│ 13. YOLA Step 11: feat/material-receipts-form ⏳ (Penerimaan & Cek Fisik)   │
│ 14. YOLA Step 12: feat/material-receipt-stock-in ⏳ (Transaksi Stok Masuk IN)│
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Bahan Tersedia di Gudang Siap Diambil)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 4: OPERASIONAL DAPUR & MUTASI STOK KELUAR (MUTI) 🍳 🍽️                │
│ 15. MUTI Step 13: feat/material-requests-verify-stock-out ⏳ (Stok OUT)     │
│ 16. MUTI Step 14: feat/production-realization-record ⏳ (Catat Porsi Matang)│
│ 17. MUTI Step 15: feat/production-material-usage ⏳ (Bahan Terpakai Aktual) │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Mutasi Stok Lengkap & Data Produksi Riil)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 5: KARTU STOK & MONITORING EKSEKUTIF OWNER (YOLA, ZAKI, IZAM) 📊 📑   │
│ 18. YOLA Step 16: feat/stock-cards-adjustment ⏳ (Kartu Stok & Adjustment)   │
│ 19. ZAKI Step 17: feat/owner-production-monitoring ⏳ (Live Pantau Produksi) │
│ 20. IZAM Step 18: feat/owner-nutrition-monitoring-reports ⏳ (Evaluasi Gizi  │
│                   & Cetak Laporan PDF Owner)                                │
└─────────────────────────────────────────────────────────────────────────────┘
```