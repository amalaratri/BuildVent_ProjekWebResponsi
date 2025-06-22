# 🏗️ Buildvent - Sistem Informasi Manajemen Inventaris Barang

**BuildVent** adalah aplikasi web berbasis PHP yang dirancang untuk memudahkan proses manajemen data barang, kategori, supplier, dan laporan dalam suatu sistem inventaris. Aplikasi ini sangat cocok digunakan oleh usaha kecil, toko, gudang, atau instansi yang membutuhkan pencatatan stok barang secara digital dan efisien.

---

## 🎯 Tujuan Aplikasi

- Menyediakan sistem inventaris barang berbasis web  
- Mempermudah proses pengelolaan stok dan kategori barang  
- Mengelola data supplier dengan terstruktur  
- Menyediakan laporan inventaris yang rapi dan mudah dipahami  
- Menyederhanakan proses pencatatan dan monitoring barang masuk/keluar  

---

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

### 7. 📌 Manajemen Akun Supplier (Fitur Baru)
- Tambah, edit, dan hapus akun supplier  
- Fitur baru ini terdiri dari:
  - `supplier_accounts.php`
  - `supplier_account_form.php`  
- Memberikan fleksibilitas tambahan untuk mengelola akun-akun supplier secara langsung

---

## 📁 Struktur Proyek
```
buildvent/
│
├── index.php
├── login.php
├── register.php
├── logout.php
│
├── dashboard.php
├── barang.php
├── barang_form.php
├── kategori.php
├── supplier.php
├── laporan.php
│
├── supplier_accounts.php 
├── supplier_account_form.php 
│
├── config/
│ └── database.php
│ └── session.php
│
├── includes/
│ └── header.php
│
├── assets/
│ └── css/
│ └── style.css
│
├── app/
│ └── globals.css
│
└── database.sql
```

---

## 🛠️ Teknologi yang Digunakan

- **PHP Native**
- **HTML + CSS**
- **MySQL**
- **Apache (XAMPP, Laragon, dll)**

---

## ⚙️ Cara Instalasi dan Menjalankan Proyek

1. **Ekstrak folder ke `htdocs` (jika menggunakan XAMPP)**
2. **Import `database.sql` melalui phpMyAdmin**
3. **Ubah konfigurasi koneksi di `config/database.php` sesuai dengan setting MySQL lokal**
4. **Akses aplikasi melalui browser:**

---

## 🧪 Akun Contoh

- **Username:** admin  
- **Password:** password  

---

## 👨‍💻 Kontributor

- Amala Ratri Nugraheni - 2317051007 - Kelas A  
- Al Farinsqi Nayuga - 2317051012 - Kelas A  
- Lifia Anasywa - 2317051022 - Kelas A  
- Syauqi Rahmat - 2317051084 - Kelas A  

---

Terima kasih telah menggunakan **BuildVent**! 🚀
