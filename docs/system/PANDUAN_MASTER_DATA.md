# Panduan Implementasi Master Data Fondasi (FoodFlow)

Dokumen ini menjelaskan dokumentasi teknis dan alur pengerjaan pada modul Master Data Fondasi:
1. Master Bahan Baku (`feat/ingredients-migration-model` & `feat/ingredients-crud`) — UC-04
2. Master Data Supplier (`feat/suppliers-crud`) — UC-25
3. Master Data Pelanggan Catering (`feat/customers-crud`) — UC-26 & FR-10

---

## 📌 1. Klarifikasi Arsitektur Penting (Informasi Kandungan Gizi)

> [!IMPORTANT]
> **Penempatan Informasi Gizi (FR-11 & PRD Bagian 10):**
> Informasi kandungan gizi (Kalori dalam kkal, Protein dalam gram, Lemak dalam gram, dan Karbohidrat dalam gram) **TIDAK diinput pada saat mendata bahan baku mentah**.
> 
> Rekap kandungan gizi diinput dan dihitung secara terpusat pada saat **pencatatan Master Menu / Produk Makanan Catering (`products`)** oleh **Staff Produksi / Ahli Gizi** (Modul 3: `feat/products-crud` & `feat/product-nutrition-form`).
> 
> Dengan demikian, Master Bahan Baku (`ingredients`) murni berfokus pada:
> - **Nama Bahan Baku** (`name`)
> - **Kategori Bahan** (`category`, contoh: Karbohidrat, Protein Hewani, Sayuran, Bumbu)
> - **Satuan Ukuran Baku** (`unit`, contoh: kg, gram, liter, butir, ikat, pcs)
> - **Batas Stok Minimum** (`minimum_stock`, untuk trigger alert persediaan menipis sesuai BR-06)
> - **Catatan Spesifikasi Mutu / Penanganan** (`notes`)
> - **Status Ketersediaan** (`is_active`, aktif digunakan atau dinonaktifkan sesuai BR-02)

---

## 🏛️ 2. Skema Database & Model Bahan Baku (`feat/ingredients-migration-model`)

### A. Tabel `ingredients` (Supabase PostgreSQL)
- `id` (bigint, auto-increment primary key)
- `name` (varchar, nama bahan baku unik)
- `category` (varchar, nullable, kategori pengelompokan dapur)
- `unit` (varchar, satuan unit baku sistem)
- `minimum_stock` (decimal 12,2, default 0, batas minimum stok)
- `notes` (text, nullable, catatan spesifikasi/penyimpanan)
- `is_active` (boolean, default true, status operasional bahan)
- `created_at`, `updated_at` (timestamps)

### B. Eloquent Model (`App\Models\Ingredient`)
- Casts: `minimum_stock` ke desimal 2 angka, `is_active` ke boolean.
- Scope: `scopeActive($query)` untuk mengambil bahan berstatus aktif.
- Helper: 
  - `isActive(): bool`: memeriksa keaktifan bahan.
  - `isLowStock($currentStock): bool`: mengecek apakah persediaan saat ini sudah menyentuh atau di bawah batas minimum stok (BR-06).

### C. Seeder Bahan Baku Katering
File [`database/seeders/IngredientSeeder.php`](../database/seeders/IngredientSeeder.php) menyediakan 8 data bahan baku katering sehat:
1. *Beras Merah Organik* (Karbohidrat, kg, min: 25)
2. *Dada Ayam Fillet Segar* (Protein Hewani, kg, min: 20)
3. *Telur Ayam Ras* (Protein Hewani, butir, min: 100)
4. *Minyak Zaitun Extra Virgin* (Bumbu & Minyak, liter, min: 5)
5. *Tempe Kedelai Murni* (Protein Nabati, kg, min: 10)
6. *Sayur Bayam Segar* (Sayuran, ikat, min: 30)
7. *Wortel Berastagi* (Sayuran, kg, min: 15)
8. *Bawang Putih Kupas* (Bumbu & Minyak, kg, min: 8)

---

## 🖥️ 3. Tampilan Antarmuka & Controller Bahan Baku (`feat/ingredients-crud`)

### A. Hak Akses Sesuai Matriks PRD Bagian 3
| Role | Akses Rute | Keterangan |
|---|---|---|
| **Admin** | **Kelola (CRUD Penuh)** | Dapat melihat list, detail, menambah, mengubah, toggle status, dan menghapus bahan. |
| **Gudang** | **Kelola (CRUD Penuh)** | Memiliki hak penuh yang sama dengan Admin untuk operasional gudang. |
| **Produksi** | **Lihat (Read-Only)** | Hanya dapat melihat daftar (`index`) dan detail (`show`) untuk menyusun resep menu. Akses tambah/edit diblokir (**403 Forbidden**). |
| **Owner** | **Lihat (Read-Only)** | Hanya dapat memantau data bahan baku. Akses mutasi diblokir (**403 Forbidden**). |

### B. Daftar Rute Web (`routes/web.php`)
```php
// Read-only (Admin, Gudang, Produksi, Owner)
Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');

// Mutasi / Form (Khusus Admin & Gudang)
Route::get('/ingredients/create', [IngredientController::class, 'create'])->name('ingredients.create');
Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
Route::get('/ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit');
Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
Route::patch('/ingredients/{ingredient}/toggle-status', [IngredientController::class, 'toggleStatus'])->name('ingredients.toggle-status');
Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
```

---

## 🏢 4. Modul Master Data Supplier (`feat/suppliers-crud`)

### A. Aturan Bisnis & Ruang Lingkup (UC-25 & FR-09)
- **Primary Actor**: Admin / Staff Operasional mengelola data supplier bahan baku.
- **BR-08**: Supplier adalah entitas data mitra bisnis eksternal, bukan akun pengguna aplikasi (tidak memiliki login).
- **BR-02**: Jika supplier sudah terikat dengan pesanan pengadaan (tabel `purchase_orders`), maka penghapusan data akan **menonaktifkan (soft toggle)** status kemitraan tanpa menghapus data secara fisik dari database, sehingga riwayat PO tetap utuh dan valid.

### B. Hak Akses Sesuai Matriks PRD Bagian 3
| Role | Hak Akses | Keterangan |
|---|---|---|
| **Admin** | **Kelola (CRUD Penuh)** | Tambah, ubah, hapus, dan toggle status aktif supplier. |
| **Gudang** | **Lihat (Read-Only)** | Melihat daftar & kontak supplier untuk keperluan penerimaan barang. |
| **Owner** | **Lihat (Read-Only)** | Memantau daftar supplier mitra katering. |
| **Produksi** | **– (Ditolak / 403)** | Bagian dapur tidak memiliki akses ke data mitra supplier. |

### C. Skema Database & Model (`App\Models\Supplier`)
- Model `Supplier` memetakan langsung ke tabel **`vendors`** di Supabase PostgreSQL (`protected $table = 'vendors';`), yang menjadi foreign key untuk `purchase_orders.vendor_id`.
- Kolom tabel `vendors`:
  - `id` (bigint, auto-increment)
  - `name` (varchar, nama supplier/PT/CV/toko)
  - `phone` (varchar, nomor telepon/WhatsApp)
  - `email` (varchar, email korespondensi)
  - `address` (text, alamat gudang/kantor pemasok)
  - `notes` (text, spesialisasi bahan baku / catatan kerjasama)
  - `is_active` (boolean, status aktif kemitraan)
  - `created_at`, `updated_at` (timestamps)

### D. Daftar Rute Web (`routes/web.php`)
```php
// Read-only (Admin, Gudang, Owner)
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');

// Mutasi / Form (Khusus Admin)
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::patch('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
```

---

## 👥 5. Modul Master Data Pelanggan Catering (`feat/customers-crud`)

### A. Aturan Bisnis & Ruang Lingkup (UC-26 & FR-10)
- **Primary Actor**: Admin / Staff Operasional mengelola data pelanggan katering (sekolah, kantor, instansi, klinik, komunitas).
- **BR-02 & UC-26 Alt Flow 6a**: Jika pelanggan sudah memiliki riwayat pesanan katering (`catering_orders` atau `orders`), maka proses hapus data akan **menonaktifkan (soft toggle)** akun pelanggan tanpa menghapus record fisik dari database, sehingga riwayat pesanan pelanggan tetap utuh dan valid.

### B. Hak Akses Sesuai Matriks PRD Bagian 3
| Role | Hak Akses | Keterangan |
|---|---|---|
| **Admin** | **Kelola (CRUD Penuh)** | Tambah, ubah, hapus, dan toggle status langganan pelanggan. |
| **Owner** | **Lihat (Read-Only)** | Memantau daftar pelanggan & persebaran konsumen katering. |
| **Gudang** | **– (Ditolak / 403)** | Staff gudang tidak mengelola data konsumen catering. |
| **Produksi** | **– (Ditolak / 403)** | Staff dapur tidak berwenang mengelola data transaksi pemesan. |

### C. Skema Database & Model (`App\Models\Customer`)
- Model `Customer` memetakan ke tabel **`customers`** di Supabase PostgreSQL.
- Kolom tabel `customers`:
  - `id` (bigint, auto-increment)
  - `name` (varchar, nama pelanggan / sekolah / instansi pemesan)
  - `phone` (varchar, nomor telepon/WhatsApp PIC pemesan)
  - `email` (varchar, email korespondensi invoice/pesanan)
  - `address` (text, alamat pengiriman pesanan katering)
  - `notes` (text, catatan khusus, preferensi menu, pantangan alergi)
  - `is_active` (boolean, status aktif langganan pemesan)
  - `created_at`, `updated_at` (timestamps)

### D. Daftar Rute Web (`routes/web.php`)
```php
// Read-only (Admin, Owner)
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

// Mutasi / Form (Khusus Admin)
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
```

### E. Seeder Pelanggan Catering
File [`database/seeders/CustomerSeeder.php`](../database/seeders/CustomerSeeder.php) menyediakan 5 data pemesan katering sehat:
1. *SDIT Insan Mandiri* (Sekolah, program makan siang bergizi 150 porsi anak sekolah)
2. *PT Citra Pratama Perkasa* (Korporat, makan siang harian 80 box karyawan kantor)
3. *Klinik Rawat Inap Medika Sehat* (Pasien rawat inap, menu khusus rendah garam & tinggi protein)
4. *Biro Kepegawaian Daerah (BKD)* (Instansi dinas, paket prasmanan rapat & pelatihan)
5. *Yayasan Lansia Kasih Ibu* (Panti lansia, makanan lunak bernutrisi seimbang)

---

## 🥗 6. Modul Master Menu Makanan & Rekap Kandungan Gizi (`feat/products-crud`)

### A. Aturan Bisnis & Ruang Lingkup (UC-23, FR-11, FR-12, FR-13)
- **Primary Actor**: 
  - **Staff Produksi / Ahli Gizi**: Berwenang penuh mengelola data menu hidangan katering sekaligus menginput dan memperbarui rincian nilai gizi hidangan.
  - **Admin / Staff Operasional**: Mengelola informasi umum menu (nama, satuan, harga jual, deskripsi, status aktif) dan **hanya melihat (read-only)** informasi gizi. Saat Admin memperbarui menu, sistem memproteksi nilai gizi agar tidak berubah/tertimpa.
- **Ditiadakannya Kategori Menu**: Struktur menu disederhanakan tanpa pengelompokan kategori yang kaku, sehingga menu dapat fleksibel dipilih untuk berbagai variasi pesanan katering.
- **Klarifikasi Rekap Gizi (PRD Bagian 10)**: Kandungan nutrisi per porsi dikelola eksklusif oleh Ahli Gizi / Produksi pada menu makanan (`products`), mencakup:
  - **Kalori**: Total energi dalam kilokalori (kkal).
  - **Protein**: Kandungan protein murni dalam gram (g).
  - **Lemak**: Lemak total dalam gram (g).
  - **Karbohidrat**: Karbohidrat total dalam gram (g).
  - **Serat Pangan**: Serat pangan sayur/buah dalam gram (g, opsional).
- **Proteksi Integritas (BR-02 & UC-23 Alt Flow 6a)**: Jika menu sudah pernah digunakan pada pesanan pelanggan (`catering_order_details`) atau rencana produksi (`productions` / `production_plan_details`), menu **tidak dapat dihapus permanen** melainkan otomatis dinonaktifkan (`is_active = false`) agar riwayat pesanan pelanggan dan audit gizi masa lalu tetap terjaga.

### B. Hak Akses Sesuai Matriks PRD Bagian 3
| Role | Hak Akses Menu Umum | Hak Akses Rincian Gizi | Keterangan |
|---|---|---|---|
| **Produksi** | **Kelola (CRUD Penuh)** | **Kelola (Input & Edit Penuh)** | Ahli gizi & staf dapur menentukan standar nutrisi porsi. |
| **Admin** | **Kelola (CRUD Penuh)** | **Lihat Saja (Read-Only)** | Admin mengelola harga & nama, nilai gizi diproteksi read-only. |
| **Gudang** | **Lihat (Read-Only)** | **Lihat Saja (Read-Only)** | Melihat menu untuk referensi penyiapan kebutuhan bahan. |
| **Owner** | **Lihat (Read-Only)** | **Lihat Saja (Read-Only)** | Memantau daftar menu katering, harga jual, dan profil gizi. |

### C. Skema Database & Model (`App\Models\Product`)
- Tabel: `products` di PostgreSQL Supabase.
- Kolom:
  - `id` (bigint, auto-increment)
  - `name` (varchar, nama menu masakan katering)
  - `description` (text, deskripsi menu & saran penyajian)
  - `unit` (varchar, satuan takaran: porsi, box, pax, dll.)
  - `selling_price` (decimal 12,2, harga jual per porsi)
  - `is_active` (boolean, status ketersediaan menu)
  - `calories` (decimal 8,2, kalori dalam kkal)
  - `protein` (decimal 8,2, protein dalam gram)
  - `fat` (decimal 8,2, lemak dalam gram)
  - `carbohydrates` (decimal 8,2, karbohidrat dalam gram)
  - `dietary_fiber` (decimal 8,2, serat pangan dalam gram)
  - `created_at`, `updated_at` (timestamps)

### D. Daftar Rute Web (`routes/web.php`)
```php
// Read-only (Admin, Produksi, Gudang, Owner)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Mutasi / Form (Khusus Admin & Produksi)
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
```

### E. Seeder Menu Katering Sehat
File [`database/seeders/ProductSeeder.php`](../database/seeders/ProductSeeder.php) menyediakan 6 hidangan katering sehat lengkap dengan nilai gizi:
1. *Paket Nasi Beras Merah Dada Ayam Panggang* (Paket Sehat - 485 kkal, 38.5g P, 8.5g L, 58g K, 6.2g S)
2. *Sup Ikan Kakap Bening Rempah Herbal* (Lauk Rendah Lemak - 240 kkal, 32g P, 4.2g L, 12.5g K, 2.8g S)
3. *Pepes Tahu Jamur Tiram Kukus Daun Pisang* (Lauk Rendah Lemak - 165 kkal, 14.2g P, 5.1g L, 15g K, 4.6g S)
4. *Tumis Sayur Pelangi (Bayam, Jagung Manis, Wortel)* (Sayuran & Serat - 110 kkal, 4.5g P, 2.2g L, 18g K, 6.8g S)
5. *Salad Quinoa Campur Edamame & Dressing Lemon* (Paket Sehat - 310 kkal, 15.8g P, 7.8g L, 42g K, 8.5g S)
6. *Pudding Chia Seed Susu Kedelai Stroberi* (Snack Sehat - 145 kkal, 6.2g P, 4g L, 21g K, 5.8g S)

---

## 🍲 8. Peracik Komposisi Resep Menu Katering (`feat/product-recipe-builder`)

### A. Aturan Bisnis & Ruang Lingkup (FR-08)
- **Fungsi**: Menghubungkan menu hidangan matang (`products`) dengan takaran bahan baku mentah (`ingredients`) yang dibutuhkan untuk memproduksi 1 porsi hidangan.
- **Tabel Pivot Database**: `product_ingredients` di PostgreSQL Supabase (kolom: `product_id`, `ingredient_id`, `quantity_per_unit`, `unit`, `timestamps`).
- **Hak Akses**:
  - `role:admin,produksi`: Berwenang meracik dan mengubah komposisi resep (`GET /products/{product}/recipe` & `PUT /products/{product}/recipe`).
  - `role:gudang,owner`: Hanya dapat melihat resep bahan pada halaman detail menu hidangan (`products.show`).
- **Artisan Form Request**: `App\Http\Requests\UpdateProductRecipeRequest` (validasi minimal 1 bahan, distinct bahan baku agar tidak ada input bahan ganda, takaran > 0).
- **Proteksi Integritas (BR-02)**: Menu yang sudah terhubung dengan resep bahan baku tidak dapat dihapus permanen (*hard delete*), melainkan dialihkan menjadi penonaktifan (*soft deactivation*).
- **Feature Test**: [`tests/Feature/ProductRecipeTest.php`](../tests/Feature/ProductRecipeTest.php) (9 tests).

---

## 📋 9. Perencanaan Produksi & Penjadwalan Masak Harian (`feat/production-plan-create`)

### A. Aturan Bisnis & Ruang Lingkup (UC-14 & UC-15, FR-18)
- **Fungsi**: Menyusun jadwal masak katering harian, menentukan tanggal perencanaan, target tanggal masak di dapur, catatan operasional/shift, dan menentukan target porsi hidangan yang akan diproduksi.
- **Tabel Database**:
  - `production_plans`: `plan_number` unik (format `PLN-YYYYMMDD-XXXX`), `plan_date`, `production_date`, `status`, `notes`, `created_by`, `timestamps`.
  - `production_plan_details`: `production_plan_id`, `product_id`, `planned_quantity`, `notes`, `timestamps`.
- **Enum Status**: `App\Enums\ProductionPlanStatus` (`draft`, `scheduled`, `in_progress`, `completed`, `cancelled`).
- **Hak Akses**:
  - `role:admin,produksi`: Membuat, mengubah, mengedit status, dan membatalkan rencana produksi.
  - `role:gudang,owner`: Melihat daftar jadwal masak dan detail porsi rencana produksi (read-only).
- **Deteksi Kesiapan Resep (UC-14 Alt 6a)**:
  - Form dan detail rencana produksi secara otomatis mendeteksi jika ada menu masakan yang belum diracik resep bahan bakunya, lalu memunculkan indikator peringatan bahwa kebutuhan bahan belum dapat dihitung otomatis + tombol pintas ke *Recipe Builder*.
- **Feature Test**: [`tests/Feature/ProductionPlanTest.php`](../tests/Feature/ProductionPlanTest.php) (13 tests).

---

## 🧪 10. Automated Testing & Quality Control Terkini

Seluruh fungsionalitas fondasi master data, peracik resep, dan jadwal produksi dilindungi oleh Feature Tests:
1. Master Bahan Baku: [`tests/Feature/IngredientCrudTest.php`](../tests/Feature/IngredientCrudTest.php) & [`tests/Feature/IngredientValidationTest.php`](../tests/Feature/IngredientValidationTest.php) (15 tests).
2. Master Supplier / Vendor: [`tests/Feature/SupplierCrudTest.php`](../tests/Feature/SupplierCrudTest.php) (11 tests).
3. Master Pelanggan Catering: [`tests/Feature/CustomerCrudTest.php`](../tests/Feature/CustomerCrudTest.php) (11 tests).
4. Master Menu Hidangan: [`tests/Feature/ProductCrudTest.php`](../tests/Feature/ProductCrudTest.php) (13 tests).
5. Peracik Komposisi Resep Menu: [`tests/Feature/ProductRecipeTest.php`](../tests/Feature/ProductRecipeTest.php) (9 tests).
6. Perencanaan Produksi: [`tests/Feature/ProductionPlanTest.php`](../tests/Feature/ProductionPlanTest.php) (13 tests).
7. Autentikasi Multi-Role & Staf: 47 tests.

Jalankan test suite lengkap dengan:
```bash
php artisan test --compact
```
Hasil Terkini: **119 tests PASS (468 assertions, 0 errors, 0 failures)**.  
Format Pint: **Passed (Clean)**.
