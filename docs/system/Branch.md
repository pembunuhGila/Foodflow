# DAFTAR BRANCH, ROADMAP PRIORITAS & ARSITEKTUR TEKNIS (FOODFLOW)

Dokumen ini adalah acuan resmi urutan pengerjaan (*roadmap*), matriks pembagian tugas tim pengembang, penamaan branch standar, serta **rekam jejak arsitektur teknis** (middleware, form request, controller, model, dan aturan bisnis) untuk setiap branch aplikasi FoodFlow.

---

## 👥 MATRIKS PEMBAGIAN TUGAS TIM & STATUS TERKINI

### 👤 1. Maharaja Zaki (Lead Developer / Aku)
> **Tanggung Jawab**: Arsitektur Inti, Autentikasi Multi-Role, Master Bahan Baku & Supplier, Master Menu & Resep Gizi, Fondasi Rencana Produksi, Dashboard Monitoring Produksi Owner, Socialite Google SSO, Digital Signature, dan AWS Cloud Deployment.
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
- [x] `feat/owner-production-monitoring` ✅ *(Merged to `main`)*
- [x] `feat/socialite-auth` ✅ *(Merged to `main` via PR #40)*
- [x] `feat/demo-role-switcher` ✅ *(Merged to `main` via PR #42)*
- [x] `feat/landing-page` ✅ *(Merged to `main` via PR #43)*
- [x] `feat/landing-routing-and-login-back` ✅ *(Merged to `main` via PR #44)*
- [x] `feat/landing-erp-revamp` ✅ *(Aktif di branch & GitHub)*
- [x] `feat/report-digital-signature` ✅ *(Merged to `main` & aktif di GitHub)*

---

### 👤 2. Izam — Divisi Admin Penjualan & Pengadaan Bahan (Siklus Pesanan Katering & Pelaporan Gizi)
> **Tanggung Jawab**: Master Pelanggan Catering, Siklus Lengkap Transaksi Pesanan Pelanggan (Catering Orders), Evaluasi Kepatuhan Gizi, dan Cetak Laporan PDF Owner.
- [x] `feat/customers-crud` ✅ *(Merged to `main` via PR #8)*
- [x] `feat/orders-migration-model` ✅ *(Merged to `main` via PR #12)*
- [x] `feat/orders-create-form` ✅ *(Merged to `main` via PR #15)*
- [x] `feat/orders-list-status` ✅ *(Merged to `main` via PR #16)*
- [x] `feat/orders-detail-view` ✅ *(Merged to `main` via PR #17)*
- [x] `feat/owner-nutrition-monitoring-reports` ✅ *(Merged to `main`)*
- [x] `feat/report-portrait-pagination` ✅ *(Merged to `main` via PR #41)*

---

### 👤 3. Syafrida — Divisi Produksi & Gizi (Kalkulasi Kebutuhan & Jadwal Masak)
> **Tanggung Jawab**: Kontrol Ambang Batas Stok Bahan, Agregasi Kebutuhan Bahan Dapur, Pemantauan Jadwal Masak, dan Permintaan Bahan Baku.
- [x] `feat/ingredients-min-stock` ✅ *(Merged to `main` via PR #14 — Langkah 1)*
- [x] `feat/production-ingredient-calculator` ✅ *(Merged to `main` via PR #18 — Langkah 6)*
- [x] `feat/production-plan-list` ✅ *(Merged to `main` via PR #25 — Langkah 7)*
- [x] `feat/material-requests-create` ✅ *(Merged to `main` via PR #26 — Langkah 8)*

---

### 👤 4. Yola — Divisi Gudang & Pengadaan Bahan Baku (Siklus Masuk & Mutasi Stok)
> **Tanggung Jawab**: Pengadaan Purchase Order ke Supplier, Penerimaan Fisik Bahan, Pemeriksaan Kondisi Barang Tiba, Pencatatan Stok Masuk (IN), dan Kartu Mutasi Stok Gudang.
- [x] `feat/purchase-orders-create` ✅ *(Merged to `main` via PR #19 — Langkah 9)*
- [x] `feat/purchase-orders-list` ✅ *(Merged to `main` — Langkah 10)*
- [x] `feat/material-receipts-form` ✅ *(Merged to `main` — Langkah 11)*
- [x] `feat/stock-cards-adjustment` ✅ *(Merged to `main` — Langkah 16)*

---

### 👤 5. Muti — Divisi Operasional Masak Dapur & Pelaksanaan Produksi
> **Tanggung Jawab**: Verifikasi Permintaan Bahan Produksi di Gudang (Stok OUT), Pencatatan Realisasi Hasil Masak Porsi Matang, dan Pencatatan Pemakaian Bahan Baku Riil.
- [x] `feat/material-requests-verify-stock-out` ✅ *(Merged to `main` via PR #23 — Langkah 13)*
- [x] `feat/production-realization-record` ✅ *(Merged to `main` via PR #24 — Langkah 14)*
- [x] `feat/production-material-usage` ✅ *(Merged to `main` — Langkah 15)*

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
│ FASE 2: KALKULASI, JADWAL & PERMINTAAN BAHAN (SYAFRIDA) - [SELESAI 100% ✅] │
│ 7. SYAFRIDA Step 1 : feat/ingredients-min-stock [SELESAI & MERGED ✅]       │
│ 8. SYAFRIDA Step 6 : feat/production-ingredient-calculator [MERGED ✅]      │
│ 9. SYAFRIDA Step 7 : feat/production-plan-list [SELESAI & MERGED ✅]        │
│ 10. SYAFRIDA Step 8: feat/material-requests-create [SELESAI & MERGED ✅]    │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Kebutuhan Bahan Diketahui & PO Diterbitkan)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 3: PENGADAAN & PENERIMAAN GUDANG (YOLA) - [SELESAI 100% ✅]            │
│ 11. YOLA Step 9 : feat/purchase-orders-create [SELESAI & MERGED ✅]         │
│ 12. YOLA Step 10: feat/purchase-orders-list [SELESAI & MERGED ✅]           │
│ 13. YOLA Step 11: feat/material-receipts-form [SELESAI & MERGED ✅]         │
│ 14. YOLA Step 12: feat/material-receipt-stock-in [SELESAI & MERGED ✅]      │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Bahan Tersedia di Gudang Siap Diambil)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 4: OPERASIONAL DAPUR & MUTASI STOK KELUAR (MUTI) - [SELESAI 100% ✅]   │
│ 15. MUTI Step 13: feat/material-requests-verify-stock-out [MERGED ✅]       │
│ 16. MUTI Step 14: feat/production-realization-record [SELESAI & MERGED ✅]  │
│ 17. MUTI Step 15: feat/production-material-usage [SELESAI & MERGED ✅]      │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Mutasi Stok Lengkap & Data Produksi Riil)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 5: KARTU STOK & MONITORING EKSEKUTIF OWNER - [SELESAI 100% ✅]         │
│ 18. YOLA Step 16: feat/stock-cards-adjustment [SELESAI & MERGED ✅]         │
│ 19. ZAKI Step 17: feat/owner-production-monitoring [SELESAI & MERGED ✅]    │
│ 20. IZAM Step 18: feat/owner-nutrition-monitoring-reports [MERGED ✅]       │
│ 21. IZAM / ZAKI : feat/report-portrait-pagination & digital-signature ✅   │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (Sistem Lengkap, Teruji & Siap Rilis)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ FASE 6: PUBLIC LAUNCH, CLOUD DEPLOYMENT & DIGITAL SIGNATURE [SELESAI 100% ✅]│
│ 22. Google SSO OAuth 2.0 : feat/socialite-auth (PR #40) [MERGED ✅]         │
│ 23. 1-Click Role Switcher: feat/demo-role-switcher (PR #42) [MERGED ✅]     │
│ 24. Public Landing Page  : feat/landing-page & revamp [MERGED & PUSHED ✅]  │
│ 25. Digital Signature    : feat/report-digital-signature [MERGED & PUSHED ✅│
│ 26. Cloud Production     : AWS EC2 Ubuntu 24.04 + SSL DuckDNS [LIVE ✅]     │
└─────────────────────────────────────────────────────────────────────────────┘
```