# 📚 Dokumentasi Teknis, Arsitektur, & Stack Teknologi FoodFlow

> **Aplikasi:** FoodFlow — Sistem Informasi Manajemen Stok dan Evaluasi Gizi MBG & Catering  
> **Status Implementasi:** Production-Ready (Selesai Urutan 1 s.d. 16 & Urutan 18)  
> **Target Pengguna / Role:** Admin / Staff Operasional, Staff Gudang / Inventory, Staff Produksi / Ahli Gizi, dan Owner / Manager  

---

## 🏛️ 1. Filosofi & Pola Arsitektur Utama

### A. Monolith MVC (Model-View-Controller)
- **Konsep:** Seluruh komponen aplikasi (business logic, database queries, otorisasi, dan tampilan antarmuka) berada dalam satu repositori dan runtime tunggal.
- **Mengapa Monolith?** Sesuai materi kuliah (*Chapter 01 & Bab 02*), untuk aplikasi skala internal operasional perusahaan / katering, monolith memberikan:
  - Kecepatan pengembangan (*development velocity*) maksimal.
  - Bebas dari overhead latensi jaringan antar microservices.
  - Satu transaksi database ACID utuh tanpa memerlukan distributed transaction (2PC/Saga).
- **Implementasi:**
  - **Model:** Berada di [`app/Models/`](../app/Models/) (17 Eloquent Model).
  - **View:** Berada di [`resources/views/`](../resources/views/) (80+ template Blade responsif).
  - **Controller:** Berada di [`app/Http/Controllers/`](../app/Http/Controllers/) (14 Controller terstruktur rapi per domain dan peran).

### B. Multi-Page Application (MPA)
- **Konsep:** Setiap perpindahan menu / rute menggunakan navigasi HTTP standar (SSR - *Server-Side Rendering*) yang merender HTML utuh dari server.
- **Keunggulan di FoodFlow:**
  - Keamanan session browser yang kokoh (anti XSS/CSRF token bawaan Laravel).
  - Tidak membebani memori browser staf katering dengan bundle JavaScript SPA (React/Vue) yang berat.

---

## 🎨 2. Stack Frontend & Tampilan Antarmuka

| Teknologi | Versi / Tipe | Peran di FoodFlow | Contoh Implementasi di Kode |
|---|---|---|---|
| **Blade Template** | Laravel Native | Layout inheritance, modular UI components | `layouts/app.blade.php`, `<x-modal>`, `<x-primary-button>` |
| **Tailwind CSS** | v3.4 | Utility-first CSS framework untuk UI bersih, responsif, & print-ready | Tampilan tabel, kartu status warna katering, breakpoint mobile/desktop |
| **Alpine.js** | v3.x | Reaktivitas mikro di sisi klien tanpa perlu compile SPA | Form dinamis tambah baris bahan/menu, kalkulasi grand total otomatis |

### Penerapan Khusus Alpine.js di FoodFlow:
1. **Dynamic Form Repeater:**
   - [Purchase Order Create](../resources/views/purchase-orders/create.blade.php) & [Catering Order Create](../resources/views/catering-orders/create.blade.php): Staf bisa menambah / menghapus baris item hidangan secara dinamis (`items.push({})`, `items.splice(index, 1)`).
2. **Realtime Price Calculation:**
   - Subtotal dan grand total otomatis terkalkulasi saat kuantitas atau harga satuan diubah (`x-model.number="item.quantity"`).
3. **Interactive Modals:**
   - Konfirmasi reset password admin, status update, dan approval gudang menggunakan directive `x-data="{ open: false }"`, `@click`, `x-show`, dan `x-transition`.

---

## ⚙️ 3. Backend & Logika Bisnis (Laravel & PHP)

| Komponen | Spesifikasi | Keterangan & Letak di Kode |
|---|---|---|
| **Bahasa Pemrograman** | PHP 8.4 | Menggunakan Typed Properties, Return Types, Constructor Property Promotion, dan Enum murni. |
| **Framework** | Laravel 11/12 (Framework 13.x dev) | Memanfaatkan fitur modern Laravel: Invokable Controllers, Form Requests, Route Model Binding. |
| **PHP Enums** | Native Backed Enums | [`app/Enums/UserRole.php`](../app/Enums/UserRole.php) (`admin`, `gudang`, `produksi`, `owner`), [`UserStatus.php`](../app/Enums/UserStatus.php), [`PurchaseOrderStatus.php`](../app/Enums/PurchaseOrderStatus.php), [`MaterialCondition.php`](../app/Enums/MaterialCondition.php). |
| **Service Layer** | Domain Services | Menjaga controller tetap tipis (*skinny controller, rich service*):<br>• [`MaterialReceiptService.php`](../app/Services/MaterialReceiptService.php) — Verifikasi fisik bahan & mutasi stok IN.<br>• [`ProductionIngredientCalculator.php`](../app/Services/ProductionIngredientCalculator.php) — Kalkulasi kebutuhan bahan resep dapur.<br>• [`UnitConverter.php`](../app/Services/UnitConverter.php) — Konversi gram-kg, ml-liter. |

---

## 🗄️ 4. Basis Data, Supabase PostgreSQL, & Eloquent ORM

### A. Basis Data PostgreSQL (Supabase)
- **Host:** Cloud PostgreSQL Hosted on Supabase (ap-south-1).
- **Keunggulan:** ACID Compliant murni, dukungan tipe data presisi tinggi `numeric(12,2)` dan `numeric(12,3)` untuk satuan takaran dapur (misal 0.250 kg).
- **Koneksi:** Menggunakan driver `pgsql` dengan mode SSL `require`.

### B. Eloquent ORM & Relasi
Codebase FoodFlow menerapkan 4 jenis relasi Eloquent relasional:

1. **One-to-Many (`hasMany` / `belongsTo`):**
   - `PurchaseOrder` ➡️ `PurchaseOrderDetail`
   - `MaterialReceipt` ➡️ `MaterialReceiptDetail`
   - `Customer` ➡️ `CateringOrder`
   - `ProductionPlan` ➡️ `ProductionPlanDetail`
2. **Many-to-Many dengan Pivot Data (`belongsToMany`):**
   - `Product` ↔️ `Ingredient` melalui pivot table `product_ingredients`.
   - Mengambil kolom pivot: `->withPivot(['quantity_per_unit', 'unit'])->withTimestamps()`.
3. **Has-Many-Through:**
   - `ProductionPlan` ➡️ `Production` melalui perantara `ProductionPlanDetail`.
4. **Local Query Scopes (Reusable Query):**
   - `Ingredient::scopeActive()`, `scopeLowStock()`, `scopeOutOfStock()`, `scopeNeedsRestock()`.
   - `PurchaseOrder::scopeStatus()`, `scopeOrderDate()`.
   - `ProductionPlan::scopeStatus()`, `scopeProductionDate()`.

---

## 🚀 5. Kinerja & Optimasi Query Database

1. **Eager Loading (`with()`) untuk Mencegah N+1 Problem:**
   - Menghindari ratusan query terpisah saat looping data tabel index.
   - Contoh di `PurchaseOrderController`:
     ```php
     PurchaseOrder::query()->with(['vendor', 'creator'])->withCount('details')->paginate(10);
     ```
2. **Deep Nested Eager Loading:**
   - Pada halaman detail penerimaan:
     ```php
     MaterialReceipt::with(['purchaseOrder.vendor', 'receiver', 'details.ingredient']);
     ```
3. **Aggregate Aggregation (`withCount` & `withSum`):**
   - Menghitung total porsi rencana produksi tanpa memuat semua objek model ke memori:
     ```php
     ProductionPlan::withSum('details as total_portions', 'planned_quantity');
     ```
4. **Pagination dengan Preservasi Query String:**
   - Diterapkan pada **13 Controller index**:
     ```php
     $items = $query->latest()->paginate(10)->withQueryString();
     ```
   - Fitur `withQueryString()` memastikan filter tanggal, dropdown status, dan kata kunci pencarian tetap aktif saat user membuka halaman 2, 3, dst.

---

## 🔒 6. Autentikasi, Keamanan, & Otorisasi Multi-Role

### A. Autentikasi (Laravel Breeze)
- Berbasis Cookie / Session terenkripsi (bukan JWT yang rentan dicuri di localStorage).
- Registrasi akun publik ditutup; pendaftaran akun staf dipusatkan secara eksklusif ke tangan Super Admin di [`Admin/UserController.php`](../app/Http/Controllers/Admin/UserController.php).
- **Password Reset Approval Flow:** Jika staf lupa password, permintaan diajukan ke sistem dan harus disetujui secara manual oleh Admin di antarmuka Admin Approval, baru password digenerate ulang.

### B. Otorisasi Multi-Role
- Matriks Hak Akses 4 Role:
  1. **Admin:** Akses penuh operasional, master data, kelola user, buat PO, dan persetujuan.
  2. **Gudang:** Master bahan baku, monitoring stok, penerimaan bahan supplier, stock opname fisik, verifikasi pengeluaran bahan ke dapur.
  3. **Produksi:** Master menu hidangan, penetapan komposisi resep & nilai gizi, rencana produksi dapur, pencatatan pemakaian riil bahan masak.
  4. **Owner / Manajer:** Monitoring capaian produksi, evaluasi kepatuhan gizi menu MBG, cetak laporan eksekutif resmi (PDF/Print).
- **Custom Middleware:** [`app/Http/Middleware/EnsureUserHasRole.php`](../app/Http/Middleware/EnsureUserHasRole.php) memvalidasi peran pengguna dan memastikan status user `aktif` sebelum request diproses.

---

## 🛡️ 7. Integritas Transaksi & Mutasi Stok (Zero-Leak)

### A. Database Transactions (`DB::transaction`)
Diterapkan pada seluruh proses yang melibatkan perubahan data pada lebih dari 1 tabel:
- Membuat Purchase Order + baris detail PO.
- Verifikasi pengeluaran bahan dapur + pemotongan stok gudang + pencatatan kartu stok.
- Penerimaan bahan supplier (BR-04) + penambahan saldo stok fisik.

### B. Pessimistic Locking (`lockForUpdate`)
- Diterapkan pada mutasi stok di [`MaterialReceiptService.php`](../app/Services/MaterialReceiptService.php#L75) dan [`StockCardController.php`](../app/Http/Controllers/StockCardController.php#L89).
- **Fungsi:** Mengunci baris data bahan di database saat stok sedang ditambah/dikurangi sehingga tidak terjadi *race condition* jika ada dua transaksi bersamaan.

### C. Idempotent Auto-Numbering
Penomoran transaksi operasional menggunakan format terstandarisasi dengan fallback retry loop untuk mencegah nomor ganda:
- Pesanan Catering: `ORD-YYYYMMDD-XXXX`
- Rencana Produksi: `PP-YYYYMMDD-XXXX`
- Purchase Order: `PO-YYYYMMDD-XXXX`
- Tanda Terima Bahan: `RC-YYYYMMDD-XXXX`
- Permintaan Bahan: `REQ-YYYYMMDD-XXXX`
- Realisasi Produksi: `PRD-YYYYMMDD-XXXX`

---

## 📊 8. Pemetaan Materi Kuliah vs Kode Sumber FoodFlow

| Bab Materi | Topik Utama Kuliah | Status di FoodFlow | Bukti File di Kode Sumber |
|---|---|---|---|
| **Chapter 01** | Arsitektur Web Modern (Monolith MVC, MPA vs SPA) | ✅ **100% Sesuai** | Repositori utuh, tidak ada layer SPA terpisah |
| **Bab 02** | HTTP Protocol, MVC, Routing, Controller | ✅ **100% Sesuai** | [`routes/web.php`](../routes/web.php), 14 Controllers |
| **Bab 03** | Blade Components, Tailwind CSS, Alpine.js | ✅ **100% Sesuai** | [`resources/views/layouts/app.blade.php`](../resources/views/layouts/app.blade.php), Alpine dynamic tables |
| **Bab 04** | Desain Database Relasional, Migrasi, PostgreSQL | ✅ **100% Sesuai** | Skema PostgreSQL di Supabase, [`database/migrations/`](../database/migrations/) |
| **Bab 05** | Eloquent ORM, Relationships, Pagination, Scopes | ✅ **100% Sesuai** | 17 Models di [`app/Models/`](../app/Models/), `paginate()->withQueryString()` di 13 controller |
| **Bab 05** | Eager Loading vs Lazy Loading (Pencegahan N+1) | ✅ **100% Sesuai** | `with()`, `load()`, `withCount()`, batch preload di `PurchaseOrderController` |
| **Bab 06** | Validasi Input, Form Request Class | ✅ **100% Sesuai** | 19 Form Request class di [`app/Http/Requests/`](../app/Http/Requests/) dengan validasi array & pesan kustom |
| **Bab 07** | Autentikasi Session, Multi-Role, Middleware Otorisasi | ✅ **100% Sesuai** | Laravel Breeze, [`EnsureUserHasRole.php`](../app/Http/Middleware/EnsureUserHasRole.php) |
| **Bab 08** | Pengolahan Data Transaksional, Atomic Lock, Idempotent | ✅ **100% Sesuai** | `DB::transaction()`, `lockForUpdate()`, penomoran otomatis unik |
| **Bab 08** | Laporan Eksekutif Print-Ready / PDF | ✅ **100% Sesuai** | [`resources/views/owner/reports/`](../resources/views/owner/reports/) dengan kop surat & `@media print` |
