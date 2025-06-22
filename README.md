# 🏗️ Buildvent - Sistem Informasi Manajemen Inventaris Barang

**BuildVent** adalah aplikasi web berbasis PHP yang dirancang untuk memudahkan proses manajemen data barang, kategori, supplier, dan laporan dalam suatu sistem inventaris. Aplikasi ini sangat cocok digunakan oleh usaha kecil, toko, gudang, atau instansi yang membutuhkan pencatatan stok barang secara digital dan efisien.

---

## 📌 Latar Belakang

Di banyak institusi kecil dan menengah, pencatatan stok barang masih dilakukan secara manual. Hal ini dapat menyebabkan kesalahan data, kehilangan informasi penting, serta menyulitkan proses audit atau pengecekan stok. BuildVent hadir sebagai solusi praktis dengan fitur-fitur dasar yang lengkap untuk menunjang aktivitas manajemen inventaris.

---

## 🎯 Tujuan Aplikasi

- Menyediakan sistem inventaris barang berbasis web yang mudah digunakan.
- Mempermudah proses pencatatan stok masuk dan keluar secara real-time.
- Meminimalkan kesalahan pencatatan dengan tampilan data yang terstruktur.
- Mempermudah proses pelacakan kategori dan asal barang dari supplier.
- Menyediakan laporan rekapitulasi stok dan inventaris yang dapat diakses kapan saja.

---

## 🧩 Fitur Utama

### 1. 🔐 Manajemen Pengguna
- Registrasi akun baru
- Login aman dengan verifikasi password
- Logout & sesi pengguna aktif

### 2. 📊 Dashboard
- Tampilan ringkasan jumlah barang, kategori, supplier
- Navigasi cepat ke menu utama

### 3. 📦 Modul Barang
- Tambah data barang baru
- Edit dan hapus barang
- Fitur pencarian barang
- Informasi detail: nama barang, kategori, harga, jumlah stok, dan supplier

### 4. 🗂️ Modul Kategori
- Pengelompokan barang berdasarkan jenis
- Tambah, edit, dan hapus kategori

### 5. 🚚 Modul Supplier
- Penyimpanan data supplier
- Kolom data: nama supplier, alamat, kontak
- Hubungkan barang dengan supplier terkait

### 6. 📋 Modul Laporan
- Menampilkan seluruh data barang dan stoknya
- Bisa diunduh atau dijadikan acuan laporan manual
- Rekap sederhana untuk audit stok

### 7. 🆕 Manajemen Akun Supplier (Fitur Baru)
Fitur baru yang membedakan versi ini dari sebelumnya:

- Modul tambahan:
  - `supplier_accounts.php`
  - `supplier_account_form.php`
- Tujuan:
  - Mengelola informasi akun-akun login untuk supplier (jika suatu saat ingin melibatkan supplier sebagai pengguna)
  - Meningkatkan keamanan dan transparansi dalam pengelolaan data oleh pihak luar
- Fitur:
  - Buat akun baru untuk supplier
  - Edit data akun yang sudah ada
  - Hapus akun yang tidak digunakan
  - Username, password, dan relasi dengan data supplier

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

- **PHP Native** — Untuk membangun logika backend aplikasi
- **HTML & CSS** — Untuk membangun tampilan frontend
- **MySQL** — Sebagai sistem manajemen basis data
- **XAMPP / Laragon** — Untuk menjalankan aplikasi secara lokal

---

## ⚙️ Cara Instalasi dan Menjalankan Proyek

1. Ekstrak folder `buildvent/` ke dalam direktori `htdocs` (jika menggunakan XAMPP).
2. Jalankan XAMPP dan aktifkan **Apache** dan **MySQL**.
3. Buka phpMyAdmin dan **import file `database.sql`** untuk membuat struktur database.
4. Buka file `config/database.php` dan sesuaikan konfigurasi koneksi jika perlu.
5. Akses aplikasi dari browser:

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
