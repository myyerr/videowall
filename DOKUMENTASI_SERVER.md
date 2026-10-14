# Dokumentasi Setup & Deployment Server E-Banner

Dokumen ini memuat panduan menyeluruh mulai dari akses web shell awal, instalasi dependensi, konfigurasi basis data, kloning repositori, hingga prosedur pembaruan kode produksi.

---

## 1. Identitas & Akses Server Awal

Akses awal terminal server disediakan melalui antarmuka web shell internal:

- **Alamat Web Shell**: `https://ssh1.sakera.my.id/`
- **Username Web Shell**: `ssh1erepo`
- **Password Web Shell**: `erepo@2026`
- **Hostname Server**: `e-banner`
- **IP Lokal Server**: `10.100.1.19`
- **Domain Publik Aplikasi**: `https://e-banner.sakera.id`

### Langkah Masuk ke Terminal Server
1. Buka browser dan akses alamat `https://ssh1.sakera.my.id/`.
2. Masukkan username `ssh1erepo` dan password `erepo@2026`.
3. Setelah masuk ke console, beralih ke user root jika belum berada di sesi root:
   ```bash
   sudo su
   ```
4. Pastikan prompt terminal aktif menampilkan:
   ```text
   root@e-banner:~#
   ```

---

## 2. Pemeriksaan Kondisi Awal Server (Requirements Check)

Sebelum instalasi, periksa paket yang sudah terpasang dan paket yang belum tersedia:

```bash
php -v        # Cek PHP (belum terpasang)
composer -v   # Cek Composer (belum terpasang)
nginx -v      # Cek Nginx (sudah terpasang versi 1.24)
git -v        # Cek Git (belum terpasang)
mariadb -v    # Cek Basis Data (belum terpasang)
```

Hasil pemeriksaan menunjukkan server baru memiliki Nginx. Paket PHP, Composer, Git, dan MariaDB harus dipasang manual.

---

## 3. Instalasi Paket & Ekstensi Pendukung

Jalankan pembaruan repositori Ubuntu 24.04 dan pasang paket yang dibutuhkan Laravel 12:

### A. Update Sistem & Paket Utilitas
```bash
apt update && apt upgrade -y
apt install -y git curl unzip zip software-properties-common
```

### B. Install PHP 8.3-FPM & Ekstensi Wajib
```bash
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl
```

### C. Install Composer
```bash
apt install -y composer
```
Periksa hasil instalasi:
```bash
composer -v
```

### D. Install & Aktifkan MariaDB Server
```bash
apt install -y mariadb-server
systemctl start mariadb
systemctl enable mariadb
```

---

## 4. Konfigurasi Basis Data (MariaDB)

Masuk ke console MariaDB sebagai root:
```bash
mariadb -u root
```

Jalankan perintah SQL berikut untuk membuat database dan akun user khusus aplikasi:
```sql
CREATE DATABASE videowall_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'videowall_user'@'localhost' IDENTIFIED BY 'PasswordProdi2026!';
GRANT ALL PRIVILEGES ON videowall_db.* TO 'videowall_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Kloning Repositori & Setup Dependensi Laravel

Masuk ke direktori web server `/var/www` dan unduh kode program dari GitHub:

```bash
cd /var/www
git clone https://github.com/myyerr/videowall.git videowall
cd /var/www/videowall
```

*(Catatan: Jika repositori bersifat privat, gunakan Personal Access Token GitHub saat diminta password atau sematkan token langsung pada tautan git clone)*.

Pasang paket pustaka PHP menggunakan Composer:
```bash
composer install --no-dev --optimize-autoloader
```

---

## 6. Konfigurasi Environment (`.env`) & Migrasi Database

Salin file contoh konfigurasi ke file `.env` aktif:
```bash
cp .env.example .env
nano .env
```

Sesuaikan baris-baris kunci berikut:
```ini
APP_NAME="E-Banner Informatika"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://e-banner.sakera.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=videowall_db
DB_USERNAME=videowall_user
DB_PASSWORD=PasswordProdi2026!

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```
Simpan file dengan menekan `Ctrl + O`, `Enter`, lalu keluar dengan `Ctrl + X`.

Jalankan perintah inisialisasi Laravel:
```bash
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```

Perintah `--seed` otomatis mendaftarkan akun administrator default:
- **Email**: `admin@visionwall.local`
- **Password**: `password`

---

## 7. Penyesuaian Limit Upload PHP & Izin Hak Akses Folder

Video latar belakang e-banner berukuran puluhan megabyte. Naikkan batas upload bawaan PHP dari 2 MB menjadi 100 MB:

```bash
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.3/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.3/fpm/php.ini
sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.3/fpm/php.ini
systemctl restart php8.3-fpm
```

Berikan izin kepemilikan folder data kepada pengguna web server (`www-data`):
```bash
chown -R www-data:www-data /var/www/videowall/storage /var/www/videowall/bootstrap/cache
chmod -R 775 /var/www/videowall/storage /var/www/videowall/bootstrap/cache
```

---

## 8. Konfigurasi Virtual Host Nginx

Buat file konfigurasi web server untuk E-Banner:
```bash
nano /etc/nginx/sites-available/videowall
```

Tempelkan konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name _;

    root /var/www/videowall/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Aktifkan konfigurasi dan hapus konfigurasi default Nginx:
```bash
ln -s /etc/nginx/sites-available/videowall /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl restart nginx
```

Buka firewall lokal server untuk port web 80:
```bash
ufw allow 80/tcp
```

---

## 9. Prosedur Pembaruan Kode (Update Revisi & Fitur Baru)

Gunakan alur berikut jika terdapat penambahan fitur atau perbaikan kode program:

### Langkah di Komputer Laptop Pengembang
1. Buat perubahan kode dan uji di lingkungan lokal.
2. Komit dan dorong perubahan ke repositori GitHub:
   ```bash
   git add .
   git commit -m "feat: deskripsi perubahan"
   git push origin main
   ```

### Langkah di Terminal Web Shell Server (`root@e-banner`)
1. Akses web shell di `https://ssh1.sakera.my.id/`.
2. Masuk ke folder aplikasi dan tarik kode terbaru:
   ```bash
   cd /var/www/videowall
   git pull origin main
   ```
3. Jika terdapat penambahan tabel basis data baru, jalankan migrasi:
   ```bash
   php artisan migrate --force
   ```
4. Bersihkan cache aplikasi agar perubahan langsung aktif:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
5. Pastikan hak akses folder storage tetap aman:
   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   ```
Pembaruan kode langsung tayang pada alamat `https://e-banner.sakera.id`.
