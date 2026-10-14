# Manual Book & Panduan Penggunaan E-Banner

Panduan ini berisi instruksi operasional untuk pengelola, dosen, dan operator dalam mengoperasikan sistem Digital Signage & Videowall E-Banner Prodi Informatika.

---

## 1. Akses Sistem & Autentikasi

### Alamat Web
Akses aplikasi melalui browser komputer, laptop, atau tablet:
```text
https://e-banner.sakera.id
```

### Akun Administrator Awal
- **Email**: `admin@visionwall.local`
- **Password**: `password`

Operator baru dapat mendaftar akun mandiri melalui menu **Daftar Akun** pada halaman login.

---

## 2. Navigasi Menu Utama

Sistem memiliki tiga modul utama pada bilah navigasi kiri:
1. **Jadwal & Request**: Mengelola kalender tayang, membuat pengajuan banner, dan memantau status konten.
2. **Kelola Display**: Mendaftarkan monitor/TV fisik dan mengambil tautan penayangan (single display maupun grid videowall).
3. **Asset Manager**: Mengunggah dan mengarsipkan file logo, video background, serta gambar latar belakang.

---

## 3. Modul Asset Manager

Gunakan modul ini untuk menyiapkan materi visual sebelum membuat jadwal konten.

### Jenis Aset
- **Logo**: File gambar transparan dengan format PNG, SVG, atau WebP.
- **Background Video**: File video dengan format MP4, WebM, atau MOV.
- **Background Gambar**: File gambar dengan format JPG, JPEG, PNG, atau WebP.

### Standar Rekomendasi Media
- **Resolusi Video/Gambar**: 1920 x 1080 piksel (aspek rasio 16:9).
- **Bitrate Video**: 3 hingga 5 Mbps.
- **Ukuran File Maksimal**: 100 MB per file.
- **Catatan Bitrate**: Ukuran file video di bawah 20 MB mempercepat waktu muat awal pada perangkat Smart TV.

### Langkah Unggah Aset
1. Buka menu **Asset Manager**.
2. Klik tombol **Upload Asset Baru** pada kategori aset yang diinginkan.
3. Masukkan nama identitas aset, pilih file dari komputer, lalu klik **Simpan Asset**.

---

## 4. Modul Kelola Display

Modul ini memetakan layar fisik TV atau videowall di lingkungan prodi ke dalam sistem.

### Menambahkan Display Baru
1. Buka menu **Kelola Display**.
2. Masukkan nama display pada form input (contoh: `TV Koridor Lt.2`, `Videowall Lobby Utama`).
3. Masukkan deskripsi lokasi fisik.
4. Klik tombol **Tambah Display**.

### Tautan Penayangan (URL Screen)
Setiap display memiliki token acak unik untuk menjaga keamanan tautan penayangan.
- Klik tombol **Buka Layar TV** untuk membuka halaman display di browser perangkat TV.
- Format URL penayangan:
  ```text
  https://e-banner.sakera.id/display/{DEVICE_TOKEN}
  ```

---

## 5. Mode Multi-Monitor / Grid Videowall (Tanpa Hardware Tambahan)

Sistem memecah satu video berukuran besar ke beberapa monitor secara otomatis menggunakan parameter URL browser, tanpa memerlukan unit hardware video processor tambahan.

### Pilihan Preset Grid
- **Matrix 2x2 (4 Layar)**: Susunan 2 kolom x 2 baris (total 4 monitor).
- **Banner 2x1 (2 Layar Horizontal)**: Susunan 2 kolom x 1 baris memanjang ke samping.
- **Tower 1x2 (2 Layar Vertikal)**: Susunan 1 kolom x 2 baris bertumpuk atas-bawah.
- **Matrix 3x3 (9 Layar)**: Susunan 3 kolom x 3 baris (total 9 monitor).

### Langkah Menghubungkan Monitor Grid
1. Buka menu **Kelola Display**.
2. Klik tombol **Mode Multi-Monitor / Grid** pada baris display yang dipilih.
3. Pilih tab preset (contoh: `Matrix 2x2`).
4. Salin tautan masing-masing kotak (*tile*):
   - Monitor 1 (Kiri Atas): `.../display/{TOKEN}?grid=2x2&col=0&row=0`
   - Monitor 2 (Kanan Atas): `.../display/{TOKEN}?grid=2x2&col=1&row=0`
   - Monitor 3 (Kiri Bawah): `.../display/{TOKEN}?grid=2x2&col=0&row=1`
   - Monitor 4 (Kanan Bawah): `.../display/{TOKEN}?grid=2x2&col=1&row=1`
5. Buka tautan tersebut pada browser masing-masing Smart TV atau Mini PC yang terhubung ke monitor.
6. Tekan tombol **F11** pada keyboard masing-masing layar untuk tampilan layar penuh (*fullscreen*).

### Mesin Sinkronisasi Otomatis
Sistem menggunakan sinkronisasi waktu master berbasis milidetik. Semua layar menyamakan putaran video secara otomatis. Jika salah satu monitor mengalami jeda jaringan, sistem menyesuaikan kecepatan putar video secara halus tanpa mengulang video dari awal.

---

## 6. Modul Jadwal & Request Konten

Modul ini mengatur materi informasi yang tayang pada display target.

### Tahap 1: Pengaturan Jadwal
1. Buka menu **Jadwal & Request**, lalu klik **Buat Request**.
2. Pilih display target.
3. Masukkan judul agenda atau kegiatan.
4. Tentukan tanggal penayangan, jam mulai, dan jam selesai.

#### Sistem Validasi Konflik Otomatis
Sistem memeriksa bentrok jadwal secara otomatis saat form disimpan:
- Jika jam tayang bertabrakan dengan agenda lain pada display dan tanggal yang sama, sistem menolak pengajuan dan membunyikan alarm peringatan audio.
- Operator harus memilih jam lain atau menonaktifkan agenda lama terlebih dahulu.

### Tahap 2: Pemilihan Media Latar & Logo
1. Pilih latar belakang: **Video Background**, **Gambar Background**, atau **Warna Solid**.
2. Pilih logo instansi atau organisasi yang ingin ditampilkan pada layar (dapat memilih lebih dari satu logo).

### Tahap 3: Penyusunan Teks & Editor Tata Letak
1. Masukkan baris teks informasi (contoh: teks sambutan, judul seminar, nama narasumber).
2. Tentukan warna teks untuk setiap baris.
3. Klik tombol **Fullscreen Editor** untuk menata posisi:
   - Geser teks dan logo langsung menggunakan mouse (*drag and drop*).
   - Ubah ukuran elemen melalui kotak penarik di sudut elemen.
4. Klik tombol **Selesai Edit** pada bilah bawah editor.
5. Klik **Simpan & Publikasikan Konten**.

---

## 7. Status Siklus Hidup Konten

Setiap konten memiliki status operasional otomatis:
1. **Draft**: Konten tersimpan tetapi belum memasuki rentang jam penayangan.
2. **Active**: Konten sedang aktif tayang pada layar display sesuai jadwal saat ini.
3. **Expired**: Waktu tayang konten telah berakhir. Konten otomatis berhenti tayang dan layar kembali ke tampilan **Standby Mode**.
4. **Berhenti Paksa**: Operator dapat menekan tombol **Stop / Expire Sekarang** pada tabel jadwal untuk menghentikan penayangan sebelum jam selesai.

---

## 8. Panduan Operasional di Perangkat TV

Saat halaman display terbuka pada Smart TV atau PC TV:
- **Layar Penuh**: Tekan tombol **F11** pada keyboard atau remote mouse.
- **Suara / Audio**: Kebijakan browser mematikan suara otomatis (*autoplay mute*). Klik mouse satu kali pada layar atau tekan tombol apa saja pada remote untuk mengaktifkan audio banner.
- **Mute / Unmute**: Tekan tombol huruf **M** pada keyboard untuk membisukan atau menyalakan suara.
- **Kursor Mouse**: Kursor otomatis menghilang setelah dua detik tanpa gerakan agar tidak mengganggu tampilan visual.
