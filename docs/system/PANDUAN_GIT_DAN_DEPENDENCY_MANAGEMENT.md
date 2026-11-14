# 📚 Panduan Konsep Git, .gitignore, dan Dependency Management (FoodFlow)

Dokumen ini disusun sebagai panduan teknis dan referensi ilmiah (khususnya untuk persiapan menghadapi pertanyaan Dosen Penguji Sidang Tugas Akhir / Skripsi) mengenai alasan mengapa direktori `vendor/`, `node_modules/`, berkas `.env`, serta artefak kompilasi **tidak boleh dimasukkan (di-commit/push) ke dalam repositori Git**.

---

## 📑 Daftar Isi
1. [Prinsip Dasar Version Control System (VCS)](#1-prinsip-dasar-version-control-system-vcs)
2. [Anatomi Berkas yang Diabaikan (`.gitignore`)](#2-anatomi-berkas-yang-diabaikan-gitignore)
3. [Mengapa Folder `vendor/` & `node_modules/` Haram Di-Commit?](#3-mengapa-folder-vendor--node_modules-haram-di-commit)
4. [Peran Manifest (`*.json`) vs Lockfile (`*.lock`)](#4-peran-manifest-json-vs-lockfile-lock)
5. [Bagaimana Aplikasi Berjalan di Server Tanpa Folder Vendor di Git?](#5-bagaimana-aplikasi-berjalan-di-server-tanpa-folder-vendor-di-git)
6. [Bahaya Keamanan Berkas `.env`](#6-bahaya-keamanan-berkas-env)
7. [Simulasi Tanya Jawab dengan Dosen Penguji Sidang](#7-simulasi-tanya-jawab-dengan-dosen-penguji-sidang)

---

## 1. Prinsip Dasar Version Control System (VCS)

Dalam rekayasa perangkat lunak modern, sistem kendali versi seperti **Git** dirancang khusus untuk melacak **kode sumber asli buatan pengembang (*first-party source code*)**, bukan pustaka pihak ketiga (*third-party dependencies*) ataupun berkas hasil kompilasi (*build artifacts*).

Prinsip dasarnya:
> *"Simpan resepnya di Git, biarkan mesin yang memasak bahan-bahannya di lingkungan masing-masing."*

- **Resep (Di-commit ke Git):** Berkas kode aplikasi (`app/`, `resources/`, `routes/`, `database/`), berkas konfigurasi (`composer.json`, `package.json`), serta berkas pengunci versi (`composer.lock`, `package-lock.json`).
- **Bahan Masakan / Hasil Olahan (Dikecualikan lewat `.gitignore`):** Pustaka unduhan (`vendor/`, `node_modules/`), berkas cache (`.phpunit.cache`, `storage/framework/cache`), dan berkas konfigurasi privat (`.env`).

---

## 2. Anatomi Berkas yang Diabaikan (`.gitignore`)

Di dalam akar repositori FoodFlow, terdapat berkas konfigurasi [`.gitignore`](file:///c:/laragon/www/Foodflow/.gitignore) yang memberitahu Git untuk secara otomatis mengabaikan berkas/folder berikut:

| Berkas / Direktori di `.gitignore` | Fungsi & Isi | Alasan Dikecualikan |
|---|---|---|
| `/vendor` | Berisi seluruh package PHP (Laravel Framework, Carbon, Socialite, dll.) | Diunduh otomatis oleh Composer; ukuran besar (100MB+); tidak boleh dilacak Git. |
| `/node_modules` | Berisi seluruh pustaka JavaScript & plugin Tailwind CSS | Diunduh otomatis oleh NPM; ukuran sangat besar (200MB+); ribuan berkas kecil. |
| `.env` & `.env.*` | Konfigurasi database, secret key, Google Client Secret, dll. | Sangat rahasia (*sensitive credentials*); berbeda antara komputer lokal vs server cloud. |
| `/public/build` | Hasil kompilasi CSS & JS (Vite/Tailwind) | Dibangun otomatis oleh runner server saat CI/CD (`npm run build`). |
| `/public/storage` | Tautan simbolik (*symlink*) ke folder unggahan | Merupakan symlink dinamis sistem operasi. |
| `*.log` & `storage/logs/` | Catatan error dan debugging runtime | Berubah setiap detik di server; tidak relevan untuk kode sumber. |
| `/.phpunit.cache` & `.phpunit.result.cache` | Cache eksekusi pengujian otomatis PHPUnit | Berkas sementara lokal pengembang. |
| `/.idea`, `/.vscode`, `/.zed` | Konfigurasi editor/IDE masing-masing developer | Preferensi pribadi; tidak boleh membebani anggota tim lain. |

---

## 3. Mengapa Folder `vendor/` & `node_modules/` Haram Di-Commit?

Ada 5 alasan standar industri mengapa memasukkan direktori dependensi ke Git merupakan **anti-pattern (kesalahan fatal)**:

### 1. Pembengkakan Ukuran Repositori (*Repository Bloat*)
- Direktori `vendor` dan `node_modules` biasanya terdiri dari **30.000 hingga 80.000 berkas kecil** dengan total ukuran 200MB – 600MB.
- Setiap kali pengembang menjalankan `git clone` atau `git pull`, proses transfer data akan memakan waktu sangat lama dan memboroskan kuota internet tim.

### 2. Inkonsistensi Binary Lintas Sistem Operasi (*Platform Incompatibility*)
- Beberapa package Composer dan modul NPM melakukan kompilasi biner asli (*native binary compilation*) yang terikat pada OS saat dipasang.
- Paket yang di-install di **Windows 11** milik pengembang lokal belum tentu dapat dijalankan di server produksi **Ubuntu 24.04 Linux (AWS EC2)**.
- Dengan membiarkan `composer install` dan `npm install` berjalan langsung di server Linux, pustaka yang terpasang dijamin 100% kompatibel dengan kernel Linux server.

### 3. Merusak Riwayat Commit & Code Review (*Dirty Diff*)
- Jika folder `vendor` di-commit, setiap kali kita menambah 1 library kecil, `git status` dan `git diff` akan memunculkan **perubahan pada ribuan baris file pihak ketiga**.
- Akibatnya, perubahan kode asli buatan mahasiswa akan "tenggelam" di antara ribuan baris kode milik pihak ketiga, menyulitkan dosen atau tim lain dalam mereview kode.

### 4. Tabrakan Git yang Sangat Rumit (*Merge Conflicts Nightmare*)
- Jika dua mahasiswa sama-sama meng-update package dan meng-commit folder `vendor`, Git akan mengalami konflik pada ratusan berkas internal vendor yang hampir mustahil diselesaikan secara manual.

### 5. Pelanggaran Lisensi & Keamanan (*License & Security Vulnerabilities*)
- Beberapa pustaka memiliki lisensi yang melarang redistribusi kode secara mentah di repositori publik.
- Membiarkan dependency manager mengunduh langsung dari sumber resmi (Packagist & NPM Registry) memastikan package yang didapat memiliki checksum SHA yang tervalidasi dan aman dari manipulasi.

---

## 4. Peran Manifest (`*.json`) vs Lockfile (`*.lock`)

Sering kali timbul pertanyaan: *"Jika folder vendor tidak di-push, bagaimana tim dan server tahu versi pustaka yang harus di-download?"*

Jawabannya adalah melalui dua pasang berkas:

### A. Di Ekosistem PHP (Composer):
1. **`composer.json` (Manifest):**
   - Menyatakan daftar pustaka yang dibutuhkan dan aturan rentang versinya.
   - Contoh: `"laravel/framework": "^12.0"`, `"laravel/socialite": "^5.18"`.
2. **`composer.lock` (Lockfile — WAJIB Di-Commit ke Git):**
   - Mengunci **versi eksak hingga ke nomor rilis terkecil dan hash commit** dari setiap package yang terpasang saat pengembang menjalankannya.
   - Ketika server produksi menjalankan `composer install`, Composer **tidak akan mencari versi baru**, melainkan membaca `composer.lock` dan mengunduh versi yang persis 100% sama dengan yang ada di komputer mahasiswa.

### B. Di Ekosistem Frontend (NPM):
1. **`package.json` (Manifest):** Mendefinisikan pustaka frontend (Tailwind CSS, Alpine.js, Chart.js, Vite).
2. **`package-lock.json` (Lockfile — WAJIB Di-Commit ke Git):** Mengunci pohon dependensi (*dependency tree*) eksak dari modul Node.js.

---

## 5. Bagaimana Aplikasi Berjalan di Server Tanpa Folder Vendor di Git?

Alur kerja profesional (yang telah kita terapkan di FoodFlow) bekerja sebagai berikut:

```
┌────────────────────────┐                   ┌────────────────────────┐
│ Komputer Pengembang    │                   │ Repositori GitHub      │
│ (Localhost / Laragon)  │                   │ (Source Code Only)     │
│                        │   git push        │                        │
│ • Kode PHP & Blade     ├──────────────────►│ • app/ & resources/    │
│ • composer.json & lock │                   │ • composer.json & lock │
│ • package.json & lock  │                   │ • package.json & lock  │
│ [vendor di-ignore ❌]  │                   │ [tanpa vendor ❌]      │
└────────────────────────┘                   └───────────┬────────────┘
                                                         │
                                                         │ GitHub Actions CI/CD
                                                         │ (Trigger saat push ke main)
                                                         ▼
                                             ┌────────────────────────┐
                                             │ Server Produksi AWS    │
                                             │ (Ubuntu 24.04 EC2)     │
                                             │                        │
                                             │ 1. git pull            │
                                             │ 2. composer install    │
                                             │ 3. npm run build       │
                                             │ 4. php artisan migrate │
                                             │                        │
                                             │ [vendor di-generate di │
                                             │ server Linux secara    │
                                             │ otomatis & bersih! ✅] │
                                             └────────────────────────┘
```

Perintah di GitHub Actions [`.github/workflows/deploy.yml`](file:///c:/laragon/www/Foodflow/.github/workflows/deploy.yml) mengeksekusi kompilasi langsung di AWS:
```bash
cd /var/www/Foodflow
git pull origin main
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan view:cache
sudo systemctl reload php8.4-fpm
```

---

## 6. Bahaya Keamanan Berkas `.env`

Berkas `.env` memuat rahasia dapur sistem:
- Password basis data MySQL/PostgreSQL
- `APP_KEY` (kunci enkripsi sesi pengguna dan cookie)
- `GOOGLE_CLIENT_SECRET` (kunci API otentikasi OAuth)
- Kredensial mail server dan API pembayaran

**Mengapa tidak boleh masuk ke Git?**
1. Jika repositori bersifat publik (atau akun pengembang diretas), pihak luar dapat langsung mengambil alih basis data dan membobol data pelanggan.
2. Setiap lingkungan memiliki konfigurasi berbeda:
   - Di lokal pengembang: `APP_ENV=local`, `APP_DEBUG=true`, `DB_HOST=127.0.0.1`
   - Di server AWS: `APP_ENV=production`, `APP_DEBUG=false`, `DB_HOST=aws-0-ap-south-1.pooler.supabase.com`
3. Sebagai gantinya, Git hanya melacak [`.env.example`](file:///c:/laragon/www/Foodflow/.env.example) yang berisi template nama-nama variabel kosong tanpa nilai rahasia.

---

## 7. Simulasi Tanya Jawab dengan Dosen Penguji Sidang

### Q1: *"Mas/Mba, saya periksa repositori GitHub Anda, kenapa tidak ada folder vendor dan node_modules? Bukankah tanpa itu Laravel tidak bisa jalan?"*
> **Jawaban Mahasiswa:**
> *"Benar Pak/Bu. Folder `vendor` dan `node_modules` memang sengaja kami masukkan ke dalam berkas `.gitignore` sesuai standar baku industri software engineering.*
> *Folder tersebut tidak perlu disimpan di Git karena ukurannya sangat besar dan platform-dependent. Sebagai gantinya, kami meng-commit berkas `composer.json`, `composer.lock`, `package.json`, dan `package-lock.json`.*
> *Ketika aplikasi dipasang di komputer lain atau di server cloud AWS EC2, kami cukup menjalankan `composer install` dan `npm install`. Composer dan NPM akan membaca berkas lockfile tersebut dan mengunduh paket dependensi yang versinya 100% identik dengan lingkungan pengembangan."*

---

### Q2: *"Apa perbedaan antara composer.json dengan composer.lock? Mengapa dua-duanya harus ada di Git?"*
> **Jawaban Mahasiswa:**
> *"`composer.json` berfungsi sebagai dokumen manifes yang mendefinisikan pustaka apa saja yang kita butuhkan beserta rentang versinya, misalnya `^12.0` yang artinya versi 12 ke atas.*
> *Sedangkan `composer.lock` mencatat versi eksak spesifik hingga ke nomor patch dan commit hash yang berhasil terpasang saat pengembang mengujinya pertama kali.*
> *Keduanya wajib masuk ke Git agar seluruh anggota tim pengembang dan server produksi memiliki versi pustaka yang persis sama, sehingga tidak terjadi masalah klasik 'di laptop saya jalan, tapi di laptop teman atau server kok error'."*

---

### Q3: *"Bagaimana dengan file .env? Kenapa tidak ada di GitHub dan bagaimana server Anda bisa terkoneksi ke database?"*
> **Jawaban Mahasiswa:**
> *"Berkas `.env` mengandung kredensial sangat rahasia seperti password basis data, Google OAuth Secret, dan APP_KEY enkripsi. Mengunggah `.env` ke Git adalah celah keamanan fatal (*security breach*).*
> *Untuk itu, di Git kami hanya menyertakan `.env.example` sebagai cetak biru. Sementara berkas `.env` produksi diisi langsung secara manual dan aman di dalam server AWS EC2, terlindungi di balik izin hak akses file sistem operasi Linux."*

---

### Q4: *"Jika folder vendor tidak di-push, bagaimana pipeline CI/CD GitHub Actions Anda bisa menjalankan aplikasi di server AWS?"*
> **Jawaban Mahasiswa:**
> *"Pada berkas alur kerja GitHub Actions `.github/workflows/deploy.yml`, setiap kali ada kode baru yang di-merge ke branch `main`, robot CI/CD kami akan login ke server AWS EC2 melalui secure SSH. Di server tersebut, robot secara otomatis menarik kode sumber terbaru (`git pull`), mengompilasi aset antarmuka Tailwind (`npm run build`), menjalankan migrasi database (`php artisan migrate --force`), dan membersihkan cache aplikasi. Dengan demikian, server membangun lingkungannya sendiri secara mandiri tanpa membebani repositori Git."*

---

*Dokumen ini merupakan bagian dari standar arsitektur dan tata kelola teknis resmi FoodFlow.*
