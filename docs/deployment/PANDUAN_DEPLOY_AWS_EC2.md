# ☁️ Panduan Lengkap Deploy FoodFlow ke AWS EC2 (Free Tier)

Panduan ini disusun langkah demi langkah agar Anda dapat mendeploy aplikasi FoodFlow ke server **AWS EC2 (Gratis 12 Bulan)** dengan cepat dan otomatis.

---

## 📋 DAFTAR ISI
1. [Langkah 1: Membuat Server EC2 di AWS Console](#langkah-1-membuat-server-ec2-di-aws-console)
2. [Langkah 2: Menghubungkan Laptop ke Server (SSH)](#langkah-2-menghubungkan-laptop-ke-server-ssh)
3. [Langkah 3: Menjalankan Script Otomasi Server](#langkah-3-menjalankan-script-otomasi-server)
4. [Langkah 4: Menyiapkan .env & Build Pertama](#langkah-4-menyiapkan-env--build-pertama)
5. [Langkah 5: Buka Aplikasi di Browser](#langkah-5-buka-aplikasi-di-browser)
6. [Update Kodingan di Masa Depan (Re-Deploy)](#update-kodingan-di-masa-depan-re-deploy)

---

## Langkah 1: Membuat Server EC2 di AWS Console

1. Buka browser dan login ke **[AWS Management Console](https://aws.amazon.com/console/)**.
2. Pada kolom pencarian di bagian atas, ketik **EC2** dan pilih menu **EC2**.
3. Di dashboard EC2, klik tombol oranye **Launch Instance**.
4. Isi formulir konfigurasi server sebagai berikut:
   - **Name and tags**: Isi nama server, contoh: `FoodFlow-Production`.
   - **Application and OS Images (Amazon Machine Image)**:
     - Pilih **Ubuntu**.
     - Pastikan versi yang dipilih: **Ubuntu Server 24.04 LTS (HVM)** atau **22.04 LTS** (ada badge *Free tier eligible*).
   - **Instance type**:
     - Pilih **t2.micro** (atau **t3.micro** jika akun Anda berada di region tertentu). Keduanya berlabel *Free tier eligible*.
   - **Key pair (login)**:
     - Klik **Create new key pair**.
     - Beri nama key: `foodflow-key`.
     - Key pair type: **RSA**.
     - Private key file format: **.pem**.
     - Klik **Create key pair**. File `foodflow-key.pem` akan otomatis ter-download ke laptop Anda. **Simpan file ini baik-baik!**
   - **Network settings**:
     - Centang **Allow SSH traffic from Anywhere (0.0.0.0/0)**.
     - Centang **Allow HTTP traffic from the internet** (Port 80).
     - Centang **Allow HTTPS traffic from the internet** (Port 443).
   - **Configure storage**:
     - Biarkan default **8 GiB** atau ubah ke **20 GiB gp3** (AWS Free Tier memberikan kuota disk gratis hingga 30 GB).
5. Klik tombol **Launch Instance** di panel sebelah kanan bawah.
6. Tunggu sekitar 1–2 menit hingga status instance berubah menjadi **Running**.
7. Klik instance yang baru dibuat, lalu salin **Public IPv4 address** (contoh: `54.254.120.45`).

---

## Langkah 2: Menghubungkan Laptop ke Server (SSH)

1. Buka **PowerShell** atau **Terminal** di laptop Anda.
2. Masuk ke folder tempat file `.pem` Anda tersimpan (misalnya folder `Downloads`):
   ```powershell
   cd ~/Downloads
   ```
3. Hubungkan ke server AWS Anda menggunakan perintah SSH:
   ```bash
   ssh -i "foodflow-key.pem" ubuntu@IP_PUBLIC_AWS_KAMU
   ```
   *(Ganti `IP_PUBLIC_AWS_KAMU` dengan IP Public EC2 Anda, misal: `ubuntu@54.254.120.45`).*
4. Jika muncul pertanyaan `Are you sure you want to continue connecting (yes/no/[fingerprint])?`, ketik **yes** lalu tekan **Enter**.
5. Anda sekarang sudah masuk ke dalam terminal server Linux Ubuntu AWS! 🎉

---

## Langkah 3: Menjalankan Script Otomasi Server

Di terminal Ubuntu AWS Anda, jalankan perintah berikut untuk meng-clone repository dan menjalankan setup server otomatis:

```bash
# 1. Clone repository Foodflow ke /var/www/Foodflow
sudo git clone https://github.com/pembunuhGila/Foodflow.git /var/www/Foodflow

# 2. Ubah hak milik folder ke user ubuntu
sudo chown -R ubuntu:ubuntu /var/www/Foodflow

# 3. Masuk ke folder project
cd /var/www/Foodflow

# 4. Berikan izin eksekusi script otomasi
chmod +x deploy/*.sh

# 5. Jalankan script setup (menginstall Nginx, PHP 8.4, Composer, Node.js, & Firewall)
./deploy/setup-ec2.sh
```

> ⏳ *Proses ini memerlukan waktu sekitar 2–4 menit untuk menginstall seluruh paket.*

---

## Langkah 4: Menyiapkan .env & Build Pertama

Setelah setup server selesai:

1. Buat file `.env` produksi dari template yang sudah disiapkan:
   ```bash
   cp deploy/.env.production.example .env
   ```

2. Buka dan edit file `.env` untuk memasukkan URL server Anda:
   ```bash
   nano .env
   ```
   - Ubah baris `APP_URL=http://IP_PUBLIC_AWS_KAMU` sesuai IP Public AWS Anda.
   - Konfigurasi Supabase PostgreSQL sudah otomatis terisi dan siap pakai.
   - Simpan perubahan dengan menekan tombol **Ctrl + O**, lalu **Enter**, kemudian **Ctrl + X**.

3. Generate Application Key Laravel:
   ```bash
   php artisan key:generate
   ```

4. Jalankan script deploy untuk membuild aset dan cache aplikasi:
   ```bash
   ./deploy/deploy.sh
   ```

---

## Langkah 5: Buka Aplikasi di Browser

Buka browser di laptop Anda dan akses alamat IP server:
👉 **`http://IP_PUBLIC_AWS_KAMU`**

Aplikasi **FoodFlow** Anda sekarang sudah resmi aktif dan live di cloud AWS! 🚀

---

## 🔄 Update Kodingan di Masa Depan (Re-Deploy)

Setiap kali Anda atau teman tim Anda melakukan `git push` ke branch `main`, Anda cukup login SSH ke server dan menjalankan satu perintah ini:

```bash
cd /var/www/Foodflow
./deploy/deploy.sh
```

Script ini akan otomatis melakukan:
- `git pull origin main`
- Update dependency Composer
- Re-build aset Vite (Tailwind & Alpine)
- Jalankan migrasi database
- Membersihkan dan me-refresh cache Laravel
- Me-reload server Nginx & PHP-FPM
