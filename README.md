
# 📦 Buildvent

**Buildvent** adalah aplikasi web berbasis PHP Native yang digunakan untuk membantu pengelolaan inventaris barang secara sederhana dan efisien. Sistem ini mencakup fitur-fitur penting seperti manajemen data barang, kategori, supplier, serta laporan berbasis web untuk membantu pelacakan stok dan aktivitas logistik.
Proyek ini dirancang agar mudah dipahami dan digunakan oleh pemula yang ingin mempelajari bagaimana membangun sistem CRUD berbasis web dengan PHP dan MySQL, tanpa menggunakan framework berat.

---

## 🚀 Fitur Utama

Berikut adalah beberapa fitur inti yang tersedia dalam aplikasi Buildvent:

- 🔐 **Autentikasi Pengguna**
  - Login, register, dan logout
  - Validasi data input pengguna
- 📦 **Manajemen Barang**
  - Tambah, ubah, hapus, dan tampilkan data barang
  - Input stok, harga, dan keterangan barang
- 🗂️ **Manajemen Kategori**
  - Pengelompokan barang berdasarkan kategori
- 🤝 **Manajemen Supplier**
  - Tambah dan kelola data supplier yang menyediakan barang
- 📊 **Dashboard Ringkasan**
  - Statistik jumlah barang, kategori, dan supplier
- 🧾 **Laporan Inventaris**
  - Menampilkan data dalam bentuk laporan sederhana
  - Filter data berdasarkan waktu (opsional jika ditambahkan)

---

## 📸 Cuplikan Antarmuka (Optional)

> Tambahkan gambar berikut ini jika tersedia di folder `assets/img/`

| Halaman    | Cuplikan                         |
|------------|----------------------------------|
| Login      | ![Login](assets/img/login.png)   |
| Dashboard  | ![Dashboard](assets/img/dashboard.png) |
| Data Barang| ![Barang](assets/img/barang.png) |
| Laporan    | ![Laporan](assets/img/laporan.png) |

---

## 🛠️ Teknologi yang Digunakan

- **PHP Native** (tanpa framework)
- **MySQL / MariaDB** untuk database
- **HTML + CSS**
- **Bootstrap** (jika digunakan)
- **XAMPP / Laragon** untuk server lokal

---

## 📁 Struktur Folder Utama

Setiap file dan folder dalam proyek memiliki peran sebagai berikut:

- `index.php`  
  Halaman utama (autentikasi otomatis)  
- `login.php`  
  Form login pengguna  
- `register.php`  
  Form registrasi pengguna baru  
- `logout.php`  
  Logout session  
- `dashboard.php`  
  Halaman ringkasan informasi  
- `barang.php`  
  Daftar dan manajemen barang  
- `barang_form.php`  
  Form tambah/edit barang  
- `kategori.php`  
  CRUD kategori barang  
- `supplier.php`  
  Data supplier  
- `laporan.php`  
  Halaman laporan inventaris  
- `config/`  
  Konfigurasi database (jika ada)  
- `assets/`  
  Berisi CSS, JS, dan gambar  
- `database.sql`  
  File import database (jika disertakan)  

---

## ⚙️ Cara Instalasi dan Menjalankan Proyek

### 1. Clone Proyek

```bash
git clone https://github.com/username/buildvent.git
```

### 2. Letakkan Proyek di Direktori Web Server

Jika menggunakan **XAMPP**, pindahkan ke folder `htdocs`:
```bash
cp -r buildvent/ C:/xampp/htdocs/
```

### 3. Import Database

- Buka `phpMyAdmin`
- Buat database baru (misalnya: `buildvent`)
- Import file `database.sql` (jika tersedia)

### 4. Konfigurasi Koneksi Database

Edit file di `config/database.php` atau sesuai struktur proyek:

```php
<?php
$host = 'localhost';
$dbname = 'buildvent';
$username = 'root';
$password = ''; // default XAMPP
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
?>
```

### 5. Jalankan di Browser

Akses melalui browser:
```
http://localhost/buildvent/
```

---

## 👤 Akun Default (Opsional)

Jika kamu sudah mengisi data login di awal, gunakan kredensial berikut:

| Email             | Password   |
|------------------|------------|
| admin@mail.com   | admin123   |

> ⚠️ Sangat disarankan untuk mengganti password setelah login pertama kali demi keamanan.

---

## ✨ Kontribusi

Kami terbuka untuk kontribusi dan pengembangan lebih lanjut. Kamu bisa:

- Mengirim Pull Request untuk fitur baru
- Membuka *Issue* jika menemukan bug
- Membantu dokumentasi

### Langkah Kontribusi:

1. Fork repositori ini
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahanmu (`git commit -m 'Tambah fitur X'`)
4. Push ke branch milikmu (`git push origin fitur-baru`)
5. Ajukan Pull Request ke repositori utama

---

## 📬 Kontak & Kredit

Dikembangkan oleh: 
- Syauqi Rahmat 2317051084
- Amala Ratri Nugraheni 2317051007
- Al Farinsqi Nayuga 2317051012
- Lifia Anasywa 2317051022
  
---

Terima kasih sudah menggunakan Buildvent! 🎉
