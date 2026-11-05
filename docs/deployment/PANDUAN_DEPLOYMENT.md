# ☁️ Panduan Lengkap Deploy FoodFlow ke AWS EC2 (Free Tier) & DuckDNS Gratis

Panduan ini menyediakan dua metode deployment yang lengkap:
* **Metode 1:** Setup dari Awal Banget Menggunakan Terminal Laptop (SSH ke Ubuntu) — *Cara Developer Asli*
* **Metode 2:** Deploy Instan Tanpa Terminal Laptop (Menggunakan Browser AWS EC2 Instance Connect)

---

## 🌐 BAGIAN A: Persiapan Domain Gratis DuckDNS (1 Menit)

Sebelum masuk ke server, siapkan domain gratisnya terlebih dahulu:
1. Buka browser dan buka: **[duckdns.org](https://www.duckdns.org/)**
2. Login menggunakan akun **Google** atau **GitHub** kamu.
3. Di kolom **domains**, ketik nama subdomain yang diinginkan, misal: `foodflow` (hasil domain: `foodflow.duckdns.org`).
4. Klik tombol **add domain**.
5. *Biarkan tab DuckDNS terbuka*, nanti kita masukkan IP Public server AWS di sini.

---

## 🚀 METODE 1: SETUP DARI AWAL BANGET PAKAI TERMINAL (SSH UBUNTU)

Metode ini adalah alur manual lengkap dari pembuatan instance, download key `.pem`, SSH dari laptop (PowerShell / Git Bash), hingga konfigurasi server Ubuntu dari nol.

---

### Langkah 1: Buat Instance AWS EC2 di Console
1. Login ke **[AWS Management Console](https://aws.amazon.com/console/)** $\rightarrow$ pilih layanan **EC2**.
2. Klik tombol oranye **Launch Instance**.
3. Konfigurasi instance:
   * **Name**: `FoodFlow-Server`
   * **AMI (OS)**: Pilih **Ubuntu** $\rightarrow$ **Ubuntu Server 24.04 LTS** (atau 22.04 LTS) berlabel *Free tier eligible*.
   * **Instance Type**: Pilih **t2.micro** (atau **t3.micro**, 1 vCPU, 1 GB RAM, *Free tier eligible*).
   * **Key Pair (Login)**:
     * Klik **Create new key pair**.
     * Key pair name: `foodflow-key`
     * Key pair type: **RSA**
     * Private key file format: **.pem**
     * Klik **Create key pair**. File `foodflow-key.pem` akan otomatis ter-download ke laptopmu. Pindahkan file ini ke folder yang aman (misal `C:\Users\NamaKamu\.ssh\` atau folder `Downloads`).
   * **Network Settings (Firewall / Security Group)**:
     * Centang ✅ **Allow SSH traffic from anywhere** (0.0.0.0/0)
     * Centang ✅ **Allow HTTP traffic from the internet** (Port 80)
     * Centang ✅ **Allow HTTPS traffic from the internet** (Port 443)
   * **Configure Storage**:
     * Ubah dari 8 GiB menjadi **20 GiB gp3** (AWS Free Tier memberikan kuota disk SSD gratis hingga 30 GB).
4. Klik tombol **Launch Instance**.
5. Tunggu 1 menit sampai status instance menjadi **Running**.
6. Klik instance tersebut, lalu salin **Public IPv4 address** (contoh: `54.254.120.45`).
7. **Hubungkan IP ke DuckDNS:** Kembali ke tab DuckDNS, paste IP tersebut ke kolom *current ip* pada domain `foodflow`, lalu klik **update ip**.

---

### Langkah 2: Masuk ke Server Ubuntu via Terminal Laptop (SSH)

1. Buka **Command Prompt (CMD)**, **PowerShell**, atau **Git Bash** di laptopmu.
2. Jalankan perintah SSH langsung menggunakan lokasi file key kamu:
   ```bash
   ssh -o ServerAliveInterval=60 -i "C:\deploy\foodFlow.pem" ubuntu@15.135.75.37
   ```
   > 💡 **Keterangan Perintah:**
   > - `-o ServerAliveInterval=60` : Mencegah koneksi terputus/freeze otomatis saat dibiarkan lama.
   > - `-i "C:\deploy\foodFlow.pem"` : Lokasi file kunci sertifikat AWS EC2 di laptopmu.
   > - `ubuntu@15.135.75.37` : User default Ubuntu dan IP Public server AWS kamu (atau gunakan domain `ubuntu@foodflow.duckdns.org`).

3. Jika muncul pertanyaan `Are you sure you want to continue connecting (yes/no)?`, ketik **yes** lalu tekan **Enter**.
4. Begitu berhasil masuk, kamu akan melihat sambutan:
   ```text
   Welcome to Ubuntu 24.04.5 LTS
   ubuntu@ip-172-31-45-244:~$
   ```

5. **MASUK KE FOLDER APLIKASI FOODFLOW (WAJIB DILAKUKAN):**  
   Saat baru login, kamu berada di folder home (`/home/ubuntu`). Jangan jalankan `git pull` di sini karena akan error *fatal: not a git repository*.  
   **Pindah ke folder aplikasi web FoodFlow:**
   ```bash
   cd /var/www/Foodflow
   ```
   *(Prompt terminal akan berubah menjadi: `ubuntu@ip-172-31-45-244:/var/www/Foodflow$`)*.

---

### Langkah 3: Menjalankan Update / Deploy Script

Setelah berada di dalam `/var/www/Foodflow`:

* **Jika menggunakan struktur terbaru (Branch testing):**
  ```bash
  git fetch origin
  git checkout testing
  git pull origin testing
  chmod +x docs/deployment/*.sh
  ./docs/deployment/deploy.sh
  ```

* **Jika masih di branch main lama:**
  ```bash
  git pull origin main
  ./deploy/deploy.sh
  ```

---

### Langkah 4: Setup Swap Memory 2 GB *(SANGAT PENTING!)*
> ⚠️ **Catatan Kritis:** Server Free Tier AWS (t2.micro) memiliki RAM 1 GB. Saat menjalankan `npm run build` atau `composer install`, memori 1 GB bisa kehabisan dan server akan freeze/macet jika tidak memiliki Swap Memory.

Jalankan perintah ini di terminal Ubuntu untuk membuat Swap 2 GB:
```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```
*(Cek dengan perintah `free -h`, sekarang total memori servermu menjadi 3 GB: 1 GB RAM + 2 GB Swap)*.

---

### Langkah 4: Update Sistem & Install Web Stack (Nginx, PHP 8.4, Node.js, Composer)

Jalankan perintah instalasi paket secara berurutan:

```bash
# 1. Update package manager Ubuntu
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip ufw certbot python3-certbot-nginx

# 2. Install Nginx Web Server
sudo apt install -y nginx

# 3. Tambahkan PPA PHP Ondrej & Install PHP 8.4 beserta ekstensi wajib Laravel
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.4-fpm php8.4-cli php8.4-pgsql php8.4-sqlite3 php8.4-curl php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-zip php8.4-intl php8.4-gd

# 4. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# 5. Install Node.js v20 LTS & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

### Langkah 5: Clone Repositori FoodFlow (Branch Testing)

```bash
# 1. Clone repository ke /var/www/Foodflow
sudo git clone -b testing https://github.com/pembunuhGila/Foodflow.git /var/www/Foodflow

# 2. Berikan izin hak akses folder ke user ubuntu
sudo chown -R ubuntu:ubuntu /var/www/Foodflow

# 3. Masuk ke folder project
cd /var/www/Foodflow
```

---

### Langkah 6: Konfigurasi File Environment (.env)

```bash
# 1. Salin template .env produksi
cp docs/deployment/.env.production.example .env

# 2. Edit file .env
nano .env
```
Di dalam nano editor, sesuaikan baris berikut:
```env
APP_NAME=FoodFlow
APP_ENV=production
APP_DEBUG=false
APP_URL=http://foodflow.duckdns.org
```
*(Catatan: Konfigurasi Database Supabase PostgreSQL sudah otomatis terkonfigurasi di dalam file template tersebut).*  
* Tekan **Ctrl + O**, tekan **Enter** (untuk simpan).  
* Tekan **Ctrl + X** (untuk keluar dari nano).

---

### Langkah 7: Install Dependencies, Build Frontend & Migrate Database

Jalankan perintah build aplikasi:
```bash
# 1. Install dependensi PHP (Composer)
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Generate application key
php artisan key:generate --force

# 3. Build frontend assets (Vite / Tailwind / Alpine)
npm install --no-audit --no-fund
npm run build

# 4. Jalankan migrasi database di cloud Supabase
php artisan migrate --force

# 5. Optimasi cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Atur izin akses folder storage & bootstrap/cache ke user web server (www-data)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

### Langkah 8: Konfigurasi Nginx Web Server

1. Salin konfigurasi Nginx FoodFlow:
   ```bash
   sudo cp docs/deployment/nginx-foodflow.conf /etc/nginx/sites-available/foodflow
   ```
2. Edit file konfigurasi Nginx untuk memasukkan domain DuckDNS:
   ```bash
   sudo nano /etc/nginx/sites-available/foodflow
   ```
   Ubah baris `server_name`:
   ```nginx
   server_name foodflow.duckdns.org IP_PUBLIC_AWS_KAMU;
   ```
   *(Tekan **Ctrl + O**, **Enter**, lalu **Ctrl + X**)*.

3. Aktifkan konfigurasi Nginx dan restart layanan:
   ```bash
   sudo rm -f /etc/nginx/sites-enabled/default
   sudo ln -sf /etc/nginx/sites-available/foodflow /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   sudo systemctl restart php8.4-fpm
   ```

4. Buka port firewall di server:
   ```bash
   sudo ufw allow OpenSSH
   sudo ufw allow 'Nginx Full'
   sudo ufw --force enable
   ```

---

### Langkah 9: Pasang SSL HTTPS Gratis (Gembok Hijau)

Agar web bisa dibuka dengan `https://` yang aman:
```bash
sudo certbot --nginx -d foodflow.duckdns.org
```
* Masukkan email kamu untuk notifikasi pembaruan sertifikat.
* Ketik **Y** lalu tekan **Enter**.
* Certbot akan otomatis mengonfigurasi Nginx agar otomatis redirect dari HTTP ke HTTPS.

---

### Langkah 10: Buka Aplikasi di Browser! 🚀

Buka browser di laptopmu dan akses:  
👉 **`https://foodflow.duckdns.org`**

Aplikasi **FoodFlow** sekarang sudah resmi live di internet!

---

## 🔄 CARA UPDATE DI MASA DEPAN (Re-Deploy Cukup 1 Perintah)

Setiap kali kamu atau tim melakukan perubahan kode di branch `testing` atau `main`:
1. Masuk SSH dari terminal laptop:
   ```bash
   ssh -o ServerAliveInterval=60 -i "C:\deploy\foodFlow.pem" ubuntu@15.135.75.37
   ```
2. Pindah ke folder aplikasi:
   ```bash
   cd /var/www/Foodflow
   ```
3. Jalankan script update:
   * Jika di branch testing:
     ```bash
     ./docs/deployment/deploy.sh
     ```
   * Jika di branch main lama:
     ```bash
     ./deploy/deploy.sh
     ```
Script `deploy.sh` akan otomatis:
* Melakukan `git pull` terbaru
* Menginstall dependensi composer baru
* Menjalankan `npm run build`
* Menjalankan `php artisan migrate`
* Membersihkan dan me-refresh seluruh cache Laravel
* Merestart Nginx & PHP-FPM secara otomatis!

---

## 🖥️ METODE 2: ALTERNATIF TANPA TERMINAL LAPTOP (EC2 Instance Connect)

Jika kamu sedang tidak membawa laptop yang ada file `.pem`-nya:
1. Buka browser $\rightarrow$ login ke **AWS Console** $\rightarrow$ buka menu **EC2**.
2. Centang instance servermu $\rightarrow$ klik tombol **Connect** di bagian atas.
3. Pilih **EC2 Instance Connect** $\rightarrow$ klik tombol oranye **Connect**.
4. Terminal Linux Ubuntu akan langsung terbuka di tab browsermu tanpa butuh file key pair `.pem`!
5. Kamu tinggal menjalankan langkah 3 sampai langkah 9 di atas langsung di dalam browser tersebut.
