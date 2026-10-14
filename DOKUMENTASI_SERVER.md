# Dokumentasi Server & Infrastruktur E-Banner

Dokumen ini memuat spesifikasi teknis, kredensial konfigurasi, perintah pemeliharaan, dan prosedur pengelolaan server produksi E-Banner Prodi Informatika.

---

## 1. Ringkasan Lingkungan Server

| Parameter | Konfigurasi |
|---|---|
| Domain Publik | `https://e-banner.sakera.id` |
| IP Lokal Server | `10.100.1.19` |
| Sistem Operasi | Ubuntu 24.04 LTS (Proxmox VE Virtual Machine) |
| Hostname | `e-banner` |
| Web Server | Nginx 1.24.0 |
| PHP Runtime | PHP 8.3-FPM (`php8.3-fpm`) |
| Basis Data | MariaDB 10.11+ |
| Direktori Aplikasi | `/var/www/videowall` |
| Reverse Proxy / SSL | Cloudflare Proxy (Fleksibel / Full SSL) |

---

## 2. Akses Masuk Server (SSH)

Akses terminal menggunakan akun `root` melalui jaringan lokal prodi atau VPN kampus:

```bash
ssh root@10.100.1.19
```

Jika menggunakan Proxmox Web GUI, buka menu **Node > e-banner > Console (noVNC)**.

---

## 3. Konfigurasi Basis Data (MariaDB)

### Kredensial Basis Data
- **Host**: `127.0.0.1` (Port `3306`)
- **Nama Basis Data**: `videowall_db`
- **Username**: `videowall_user`
- **Password**: `PasswordProdi2026!`

### Perintah Masuk CLI Basis Data
```bash
mariadb -u videowall_user -p videowall_db
```

### Pembuatan Ulang Akun Basis Data (Jika Diperlukan)
```sql
CREATE DATABASE IF NOT EXISTS videowall_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'videowall_user'@'localhost' IDENTIFIED BY 'PasswordProdi2026!';
GRANT ALL PRIVILEGES ON videowall_db.* TO 'videowall_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 4. Konfigurasi Nginx & PHP-FPM

### Lokasi File Konfigurasi
- Konfigurasi Virtual Host: `/etc/nginx/sites-available/videowall`
- Symlink Aktif: `/etc/nginx/sites-enabled/videowall`
- Konfigurasi PHP-FPM: `/etc/php/8.3/fpm/php.ini`
- Pool PHP-FPM: `/etc/php/8.3/fpm/pool.d/www.conf`

### Konfigurasi Nginx Aktif
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

### Parameter Kunci PHP (`/etc/php/8.3/fpm/php.ini`)
- `upload_max_filesize = 100M`
- `post_max_size = 100M`
- `memory_limit = 256M`
- `max_execution_time = 300`

---

## 5. Perintah Manajemen Layanan

### Cek Status Layanan
```bash
systemctl status nginx
systemctl status php8.3-fpm
systemctl status mariadb
```

### Restart Layanan
```bash
systemctl restart nginx
systemctl restart php8.3-fpm
systemctl restart mariadb
```

### Uji Sintaks Konfigurasi Nginx
```bash
nginx -t
```

---

## 6. Prosedur Pembaruan Kode (Deploy / Update)

Jalankan rangkaian perintah berikut di terminal server setiap kali ada pembaruan kode di repositori GitHub:

```bash
cd /var/www/videowall

# 1. Tarik pembaruan kode dari branch main
git pull origin main

# 2. Perbarui dependensi jika composer.json berubah (opsional)
composer install --no-dev --optimize-autoloader

# 3. Jalankan migrasi tabel database jika ada skema baru
php artisan migrate --force

# 4. Bersihkan dan perbarui cache aplikasi
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Pastikan izin kepemilikan folder tetap aman
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

## 7. Pemantauan & File Log

Gunakan perintah `tail` untuk melihat aktivitas dan kendala sistem:

### Log Error Nginx
```bash
tail -f /var/log/nginx/error.log
```

### Log Akses Nginx
```bash
tail -f /var/log/nginx/access.log
```

### Log Error Laravel
```bash
tail -f /var/www/videowall/storage/logs/laravel.log
```

---

## 8. Pencadangan Data (Backup)

### Backup Basis Data ke File SQL
```bash
mysqldump -u videowall_user -p videowall_db > /root/backup_videowall_$(date +%F).sql
```

### Restore Basis Data dari File SQL
```bash
mariadb -u videowall_user -p videowall_db < /root/nama_file_backup.sql
```

### Backup Folder Aset Media
```bash
tar -czvf /root/backup_assets_$(date +%F).tar.gz /var/www/videowall/storage/app/public/assets
```
