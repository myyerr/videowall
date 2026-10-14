# Buku Panduan Operasional E-Banner Informatika

Panduan langkah demi langkah penggunaan sistem e-banner dan videowall untuk operator, staf, dan pengelola kegiatan.

---

## Alur Kerja Sistem

Pengoperasian e-banner mengikuti tiga tahapan utama:
1. **Daftarkan Layar TV**: Tentukan TV fisik yang akan menampilkan banner.
2. **Buat Request Konten**: Tentukan jam tayang, pilih background, dan atur tata letak teks informasi.
3. **Tayang Otomatis**: Layar TV memutar banner tepat pada jam mulai dan kembali ke mode standby saat jam selesai.

---

## Langkah 1: Akses Website & Login

1. Buka browser pada komputer atau laptop melalui alamat:
   ```text
   https://e-banner.sakera.id
   ```
2. Masukkan akun administrator:
   - **Email**: `admin@visionwall.local`
   - **Password**: `password`
3. Klik tombol **Log in** untuk masuk ke dashboard utama.

Pengguna baru dapat membuat akun mandiri melalui tautan **Belum punya akun? Daftar**.

---

## Langkah 2: Menyiapkan Layar TV (Display)

Sebelum menjadwalkan konten, daftarkan lokasi fisik layar TV ke dalam sistem:

1. Buka menu **Kelola Display** di bilah navigasi kiri.
2. Pada form **Tambah Display Baru**, isi:
   - **Nama Display**: contoh `TV Ruang Prodi`, `Lobby Utama`, atau `Lab TIA`.
   - **Lokasi**: contoh `Gedung RKB Lantai 2`.
3. Klik tombol **Tambah Display**.

Sistem memberikan sebuah **Device Token** acak untuk setiap display yang didaftarkan. Token ini menjadi kunci akses penayangan di perangkat TV fisik.

---

## Langkah 3: Menyiapkan Aset Visual (Asset Manager)

Siapkan logo dan video atau gambar latar belakang sebelum membuat pengajuan konten:

1. Buka menu **Asset Manager** di bilah navigasi kiri.
2. Tentukan kategori aset:
   - **Logo**: File lambang instansi atau organisasi (format PNG transparan, WebP, atau SVG).
   - **Video Background**: File video animasi latar berulang (format MP4 atau WebM, disarankan resolusi 1080p, ukuran file 5 MB sampai 20 MB).
   - **Image Background**: File gambar statis beresolusi tinggi (format JPG, PNG, atau WebP).
3. Klik tombol **Upload Asset Baru** pada kotak kategori yang dipilih.
4. Tuliskan nama aset, pilih file dari komputer, lalu klik **Simpan Asset**.

---

## Langkah 4: Membuat Request Konten & Jadwal Tayang

1. Buka menu **Jadwal & Request**, lalu klik tombol **Buat Request**.
2. Form terdiri dari tiga tahapan berurutan:

### Tahap 1: Informasi Dasar & Jadwal
- **Pilih Display**: Tentukan layar TV target tempat banner akan tayang.
- **Judul Konten**: Tuliskan nama kegiatan (contoh: `Kuliah Tamu AI 2026`).
- **Tanggal Tayang**: Pilih tanggal penayangan.
- **Jam Mulai & Jam Selesai**: Tentukan durasi penayangan banner aktif.
- Klik **Lanjut: Background & Logo**.

#### Peringatan Bentrok Jadwal
Sistem melarang dua konten berbeda tayang pada display dan rentang waktu yang sama. Jika waktu bertabrakan dengan jadwal lama, sistem mengeluarkan pesan gagal dan membunyikan alarm peringatan audio. Pilih jam yang masih kosong atau hentikan jadwal lama terlebih dahulu.

### Tahap 2: Background & Logo
- Pilih satu latar belakang: **Video Background**, **Gambar Background**, atau warna polos.
- Pilih logo yang ingin dipasang pada layar (dapat mencentang lebih dari satu logo).
- Klik **Lanjut: Konten & Posisi**.

### Tahap 3: Penyusunan Teks & Layout Visual
- Ketikkan baris teks informasi pada baris konten (contoh: baris 1 berisi judul acara, baris 2 berisi tanggal dan tempat, baris 3 berisi nama narasumber).
- Pilih warna teks untuk masing-masing baris.
- Klik tombol **Fullscreen Editor** untuk menata posisi elemen visual secara presisi:
  - Tarik teks atau logo ke posisi yang diinginkan menggunakan klik-tahan mouse (*drag and drop*).
  - Ubah ukuran elemen dengan menarik titik kotak di sudut elemen.
  - Periksa keselarasan elemen menggunakan garis bantu tengah (*snap lines*).
- Klik tombol **Selesai Edit** di bilah bawah editor.
- Klik tombol hijau **Simpan & Publikasikan Konten**.

---

## Langkah 5: Cara Kerja Penayangan Otomatis

Operator tidak perlu menekan tombol sakelar manual saat jadwal tiba. Sistem bekerja secara terjadwal:

- **Sebelum Jam Mulai (Status: Draft)**: Layar TV tetap menampilkan jam digital mode standby.
- **Tepat Jam Mulai (Status: Active)**: Layar TV secara otomatis berganti menampilkan banner kegiatan lengkap dengan video latar belakang dan teks yang sudah diatur.
- **Setelah Jam Selesai (Status: Expired)**: Banner otomatis berhenti berputar dan layar TV kembali ke tampilan jam digital standby.

Jika ada agenda darurat yang perlu disudahi lebih awal, klik tombol **Stop / Expire Sekarang** pada tabel jadwal di dashboard.

---

## Langkah 6: Cara Menampilkan di Layar TV / Smart TV Fisik

Buka browser bawaan Smart TV, Android Box, atau PC mini yang terhubung ke monitor TV.

### Opsi A: Satu Layar TV Tunggal
1. Di komputer operator, buka menu **Kelola Display**.
2. Salin tautan pada kolom **URL Layar** di baris display bersangkutan:
   ```text
   https://e-banner.sakera.id/display/{TOKEN_DISPLAY}
   ```
3. Buka tautan tersebut di browser perangkat TV.

---

### Opsi B: Multi-Monitor / Grid Videowall (Gabungan 2 atau 4 Layar TV)
Gunakan mode ini jika prodi menggabungkan beberapa TV fisik menjadi satu videowall besar tanpa membeli unit hardware video controller tambahan:

1. Buka menu **Kelola Display**, lalu klik tombol **Mode Multi-Monitor / Grid**.
2. Pilih format susunan monitor:
   - **Matrix 2x2 (4 Layar)**: Gabungan 2 kolom x 2 baris TV.
   - **Banner 2x1 (2 Layar Horizontal)**: Gabungan 2 TV berjajar ke samping.
   - **Tower 1x2 (2 Layar Vertikal)**: Gabungan 2 TV bertumpuk atas-bawah.
   - **Matrix 3x3 (9 Layar)**: Gabungan 3 kolom x 3 baris TV.
3. Dialog menampilkan daftar tautan khusus untuk setiap posisi monitor:
   - **TV 1 (Kiri Atas)**: Buka tautan berakhiran `?grid=2x2&col=0&row=0`
   - **TV 2 (Kanan Atas)**: Buka tautan berakhiran `?grid=2x2&col=1&row=0`
   - **TV 3 (Kiri Bawah)**: Buka tautan berakhiran `?grid=2x2&col=0&row=1`
   - **TV 4 (Kanan Bawah)**: Buka tautan berakhiran `?grid=2x2&col=1&row=1`
4. Masing-masing monitor otomatis memotong dan menampilkan porsi videonya sendiri secara terkoordinasi.
5. Putaran video di semua layar diselaraskan secara otomatis oleh jam master server melalui jaringan, menjaga gambar tetap utuh dan seirama.

---

## Langkah 7: Tombol Pengendalian di Layar TV

Gunakan remote mouse atau keyboard nirkabel pada perangkat TV:
- **Layar Penuh (Fullscreen)**: Tekan tombol **F11** pada keyboard.
- **Aktivasi Suara (Audio Unmute)**: Browser mematikan audio secara otomatis (*autoplay policy*). Cukup klik mouse satu kali di area layar TV untuk membuka suara banner.
- **Mute / Unmute Cepat**: Tekan tombol huruf **M** pada keyboard untuk mematikan atau menghidupkan kembali suara.
- **Kursor Mouse**: Kursor mouse otomatis menghilang dari layar setelah dua detik tanpa sentuhan agar gambar e-banner bersih.
