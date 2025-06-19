# 🏗️ Buildvent - Sistem Informasi Manajemen Inventaris Barang

**Buildvent** adalah aplikasi web berbasis PHP yang dirancang untuk memudahkan proses manajemen data barang, kategori, supplier, dan laporan dalam suatu sistem inventaris. Aplikasi ini sangat cocok digunakan oleh usaha kecil, toko, gudang, atau instansi yang membutuhkan pencatatan stok barang secara digital dan efisien.

## 🎯 Tujuan Aplikasi

- Menyediakan sistem inventaris barang berbasis web
- Mempermudah proses pengelolaan stok dan kategori barang
- Mengelola data supplier dengan terstruktur
- Menyediakan laporan inventaris yang rapi dan mudah dipahami
- Menyederhanakan proses pencatatan dan monitoring barang masuk/keluar

## 🧩 Fitur Utama

### 1. Manajemen Pengguna
- Registrasi akun pengguna
- Login dan logout aman
- Hak akses berbasis sesi pengguna

### 2. Dashboard
- Menampilkan ringkasan data barang, kategori, dan supplier
- Akses cepat ke fitur utama

### 3. Barang
- Tambah, edit, dan hapus data barang
- Informasi barang: nama, kategori, harga, stok, dan supplier
- Pencarian barang berdasarkan kata kunci

### 4. Kategori
- Tambah dan kelola kategori barang
- Mengelompokkan barang berdasarkan jenis

### 5. Supplier
- Kelola data supplier
- Informasi: nama supplier, alamat, dan kontak

### 6. Laporan
- Menampilkan semua data barang beserta stoknya
- Bisa dijadikan basis pencatatan stok manual atau digital

## 📁 Struktur Proyek

```
buildvent/
│
├── index.php # Halaman utama redirect login
├── login.php # Form login pengguna
├── register.php # Registrasi pengguna
├── logout.php # Logout session pengguna
│
├── dashboard.php # Tampilan dashboard utama
├── barang.php # Daftar dan kelola data barang
├── barang_form.php # Form tambah/edit barang
├── kategori.php # Kelola kategori
├── supplier.php # Data supplier
├── laporan.php # Halaman laporan inventaris
│
├── config/ # Konfigurasi database (jika tersedia)
├── assets/ # Folder berisi CSS, JS, gambar (opsional)
└── database.sql # Struktur database MySQL (jika tersedia)

```

## 🛠️ Teknologi yang Digunakan

- **PHP Native**
- **HTML + CSS**
- **MySQL / MariaDB**
- **Apache (XAMPP, Laragon, dll)**

## ⚙️ Cara Instalasi dan Menjalankan Proyek

1. **Ekstrak ke `htdocs` (jika pakai XAMPP)**
2. **Import `database.sql` via phpMyAdmin**
3. **Edit file koneksi di `config/database.php`**
4. **Akses via browser:**
   ```
   http://localhost/buildvent/index.php
   ```

## 🧪 Akun Contoh

- **Email:** admin@mail.com  
- **Password:** admin123

## 👨‍💻 Kontributor

- Amala Ratri Nugraheni - 2317051007 - Kelas A  
- Al Farinsqi Nayuga - 2317051012 - Kelas A  
- Lifia Anasywa - 2317051022 - Kelas A  
- Syauqi Rahmat - 2317051084 - Kelas A  

---

Terima kasih telah menggunakan **Buildvent**! 🚀
