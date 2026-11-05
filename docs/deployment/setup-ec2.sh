#!/bin/bash
set -e

# ========================================================
# Script Otomasi Setup Server AWS EC2 (Ubuntu) untuk FoodFlow
# ========================================================

echo ">>> [1/7] Mengupdate sistem Ubuntu..."
sudo apt-get update && sudo apt-get upgrade -y
sudo apt-get install -y software-properties-common curl git unzip ufw

echo ">>> [2/7] Menginstall Nginx Web Server..."
sudo apt-get install -y nginx

echo ">>> [3/7] Menginstall PHP 8.4 dan Ekstensi yang Dibutuhkan..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y \
    php8.4-fpm \
    php8.4-cli \
    php8.4-pgsql \
    php8.4-sqlite3 \
    php8.4-curl \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-bcmath \
    php8.4-zip \
    php8.4-intl \
    php8.4-gd

echo ">>> [4/7] Menginstall Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

echo ">>> [5/7] Menginstall Node.js v20 LTS dan NPM..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

echo ">>> [6/7] Mengonfigurasi Nginx untuk FoodFlow..."
sudo cp /var/www/Foodflow/docs/deployment/nginx-foodflow.conf /etc/nginx/sites-available/foodflow
sudo rm -f /etc/nginx/sites-enabled/default
sudo ln -sf /etc/nginx/sites-available/foodflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm

echo ">>> [7/7] Mengatur Firewall Server..."
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

echo "========================================================"
echo " SETUP SERVER SELESAI!"
echo " Silakan jalankan deploy/deploy.sh untuk build aplikasi."
echo "========================================================"
