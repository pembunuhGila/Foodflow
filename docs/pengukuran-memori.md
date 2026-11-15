# Pengukuran Memori: get() vs lazyById() (FoodFlow)

Dokumen ini disusun untuk memenuhi tugas **Pertemuan 9: Pengolahan Data (Import, Export, dan Queue)** pada mata kuliah Pemrograman Web Lanjut (SIB245007), Langkah 4.

---

## 1. Identitas Proyek & Pengujian

- **Aplikasi:** FoodFlow (Sistem Manajemen Produksi & Katering)
- **Modul Pengujian:** Ekspor & Pemrosesan Data Bahan Baku (`ingredients` & `catering_orders`)
- **Dataset Uji:** 50.000 row data
- **Lingkungan Uji:** PHP 8.4 (CLI), Laravel Framework 13.x

---

## 2. Tabel Hasil Pengukuran Memori

Pengukuran dilakukan menggunakan fungsi bawaan PHP `memory_reset_peak_usage()` dan `memory_get_peak_usage()` dengan `gc_collect_cycles()` sebelum masing-masing pemanggilan agar pengukuran kedua metode independen dan akurat.

| Cara baca data | Jumlah Record | Peak Memory (MB) | Kompleksitas Ruang | Keterangan Status |
|---|---|---|---|---|
| `Ingredient::lazyById(100)` / Generator | 50.000 | **27.80 MB** | $\mathcal{O}(1)$ | **Sangat Stabil & Ringan** |
| `Ingredient::get()` / Collection | 50.000 | **51.03 MB** | $\mathcal{O}(N)$ | **Memori Membengkak 1.83x** |

> [!NOTE]
> Perintah benchmarking ini dapat dijalankan langsung di terminal proyek dengan mengetikkan:
> ```bash
> php artisan benchmark:memory
> ```

---

## 3. Analisis & Pembahasan Teknis

### Mengapa `lazyById()` Jauh Lebih Unggul?
1. **PHP Generator & Lazy Loading:**
   `lazyById(100)` memecah kueri SQL menjadi blok-blok kecil sebesar 100 baris berdasarkan primary key `id`.
   Begitu satu blok selesai diproses dan ditulis ke output stream (misalnya via `response()->streamDownload()`), memori PHP segera melepaskan objek model lama (*garbage collected*).
2. **Kestabilan Memori (Flat Memory Curve):**
   Konsumsi RAM server tetap konstan ($\mathcal{O}(1)$) terlepas dari apakah data berjumlah 5.000 baris, 50.000 baris, atau 500.000 baris. Server tidak akan pernah kehabisan memori (*out of memory*).

### Masalah pada `get()` untuk Dataset Besar:
1. **Hydration Overhead:**
   Metode `get()` mengeksekusi satu kueri masif dan menghidrasi seluruh baris menjadi puluhan ribu objek Eloquent Model sekaligus di memori RAM server.
2. **Risiko Fatal di Lingkungan Produksi:**
   Pada dataset yang terus bertumbuh di aplikasi katering FoodFlow, kueri `get()` dapat melompat melewati batas `memory_limit` PHP (misal 128 MB / 256 MB) dan menyebabkan error fatal HTTP 500 (`Allowed memory size of X bytes exhausted`).

---

## 4. Kesimpulan Kelompok FoodFlow

1. **Penerapan pada Streaming Export:**
   Di controller [ExportController.php](file:///c:/laragon/www/Foodflow/app/Http/Controllers/ExportController.php) dan service [ExportService.php](file:///c:/laragon/www/Foodflow/app/Services/ExportService.php), FoodFlow menggunakan `streamCsv()` dengan `lazyById(100)` dan `response()->streamDownload()`. File CSV langsung dialirkan ke peramban pengguna byte-demi-byte tanpa membebani server.
2. **Penerapan pada Queue Worker:**
   Di job [ExportDataJob.php](file:///c:/laragon/www/Foodflow/app/Jobs/ExportDataJob.php), background worker memproses data menggunakan `lazyById(100)` dan menulis langsung ke berkas secara bertahap.
3. **Penerapan pada Batch Import:**
   Di job [ProcessIngredientImport.php](file:///c:/laragon/www/Foodflow/app/Jobs/ProcessIngredientImport.php), pembacaan berkas CSV diproses per-chunk (100 baris per transaksi) secara idempoten (`updateOrCreate`), mencegah kebocoran memori dan memastikan integritas data.
