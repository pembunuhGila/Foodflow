# 📘 Panduan Alur Deployment & Tanya Jawab Penguji (FoodFlow)

Dokumen ini berisi rangkuman konsep deployment, alasan penggunaan SSH, perintah praktikum, dan materi tanya-jawab untuk persiapan sidang/evaluasi bersama dosen penguji.

---

## 1. Konsep & Alur Kerja Deployment

Deployment aplikasi FoodFlow memiliki 3 tahapan utama dari laptop pengembang hingga tayang di internet:

```
[ Laptop / Komputer Lokal ]
        │
        │ 1. git push origin main
        ▼
   [ GitHub Repository ]
        │
        │ 2. Koneksi Remote via SSH Key (.pem)
        ▼
[ Server AWS EC2 Ubuntu ]
   (/var/www/Foodflow)
        │
        │ 3. git pull & build & reload services
        ▼
[ Pengguna Publik / Penguji ]
 (https://foodflow-app.duckdns.org)
```

---

## 2. Kenapa Tetap Membutuhkan SSH?

### Pertanyaan:
> *"Kenapa kita tetap harus menggunakan koneksi SSH padahal kita mengembangkan kode di laptop sendiri?"*

### Penjelasan:
- **Komputer Terpisah:** Laptop kamu (lingkungan pengembangan lokal) dan Server AWS EC2 (lingkungan produksi di cloud) adalah dua mesin fisik yang terpisah di internet.
- **Protokol Keamanan:** SSH (*Secure Shell*) adalah jalur terowongan terenkripsi berstandar industri untuk mengakses dan mengontrol terminal server jarak jauh secara aman tanpa membuka celah password publik.
- **Kunci Rahasia (`.pem`):** Otentikasi menggunakan pasangan kunci kriptografi asimetris (RSA Private Key), sehingga hanya pemilik file `FoodFlow.pem` yang diizinkan mengelola server.

---

## 3. Langkah Praktik Deployment Manual via Terminal

Bila dosen meminta demonstrasi langsung pembaruan server dari terminal:

### Langkah 1: Masuk ke Server EC2 via SSH
Jalankan dari PowerShell atau Terminal laptop:
```bash
ssh -i C:\deploy\FoodFlow.pem ubuntu@15.135.75.37
```

### Langkah 2: Masuk ke Direktori Aplikasi
```bash
cd /var/www/Foodflow
```

### Langkah 3: Sinkronisasi Kode & Optimasi Sistem
```bash
# Tarik kode terbaru dari branch utama
git pull origin main

# Kompilasi frontend bila ada perubahan CSS/JS Tailwind
npm run build

# Jalankan migrasi database bila ada tabel baru
php artisan migrate --force

# Bersihkan & perbarui cache framework Laravel
php artisan optimize:clear
php artisan view:cache

# Muat ulang service web server & PHP
sudo systemctl reload php8.4-fpm
```

### Langkah 4: Keluar dari Server
```bash
exit
```

---

## 4. Perbedaan: Manual Deployment vs CI/CD (GitHub Actions)

| Aspek | Deployment Manual (SSH) | Otomatisasi CI/CD (GitHub Actions) |
|---|---|---|
| **Pemicu** | Developer menjalankan SSH manual lewat terminal | Otomatis terpicu setiap ada `git push` ke branch `main` |
| **Eksekutor** | Developer manusia dari laptop | Runner cloud milik GitHub Actions |
| **Keamanan Kunci** | File `.pem` disimpan di lokal laptop | Disimpan di fitur terenkripsi *GitHub Secrets* |
| **Uji Kode** | Manual (`php artisan test`) | Pipeline otomatis membatalkan deploy jika ada test gagal |
| **File Konfigurasi**| Tidak ada (perintah manual) | Didefinisikan di `.github/workflows/deploy.yml` |

---

## 5. Kisi-kisi Jawaban Saat Ditanya Dosen Penguji

### Q1: *"Bagaimana arsitektur server FoodFlow di-hosting?"*
> **Jawaban:**  
> *"FoodFlow di-hosting menggunakan layanan cloud **AWS EC2** berspesifikasi Ubuntu Server, dengan web server **Nginx**, runtime **PHP 8.4-FPM**, database **MySQL**, serta domain yang diarahkan via dynamic DNS (**DuckDNS**) terlindungi sertifikat SSL/HTTPS dari **Let's Encrypt**."*

### Q2: *"Bagaimana cara kalian mengamankan akses server?"*
> **Jawaban:**  
> *"Kami menonaktifkan autentikasi password biasa pada SSH server. Akses administratif hanya dapat dilakukan menggunakan **SSH Key Pair (`.pem`)**, dan port firewall di AWS Security Group dibatasi hanya untuk port HTTP (80), HTTPS (443), dan SSH (22)."*

### Q3: *"Apakah kalian sudah menerapkan CI/CD?"*
> **Jawaban:**  
> *"Ya, kami telah menyiapkan alur kerja **CI/CD berbasis GitHub Actions** pada file konfigurasi `.github/workflows/deploy.yml`. Alur kerjanya: setiap kali kode di-merge ke branch `main`, GitHub Actions secara otomatis menguji kode, menghubungkan SSH ke EC2 menggunakan rahasia terenkripsi (*Repository Secrets*), melakukan `git pull`, kompilasi aset via Vite, migrasi basis data, dan me-reload PHP-FPM."*
