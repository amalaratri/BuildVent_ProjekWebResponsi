-- Database BuildVent
CREATE DATABASE IF NOT EXISTS buildvent;
USE buildvent;

-- Tabel Users (Pengguna)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'operator') DEFAULT 'operator',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Supplier
CREATE TABLE IF NOT EXISTS supplier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_supplier VARCHAR(100) NOT NULL,
    kontak VARCHAR(50),
    alamat TEXT,
    email VARCHAR(100),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Barang
CREATE TABLE IF NOT EXISTS barang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(20) UNIQUE NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    kategori_id INT,
    supplier_id INT,
    satuan VARCHAR(20) NOT NULL,
    stok INT DEFAULT 0,
    stok_minimum INT DEFAULT 0,
    harga DECIMAL(15,2) DEFAULT 0,
    lokasi VARCHAR(100),
    deskripsi TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id)
);

-- Tabel Transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(20) UNIQUE NOT NULL,
    tanggal DATETIME NOT NULL,
    jenis ENUM('masuk', 'keluar') NOT NULL,
    barang_id INT NOT NULL,
    jumlah INT NOT NULL,
    supplier_id INT,
    keterangan TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert data awal users (password: admin123 dan operator123)
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$12$QIqPgvznZ8yc8HVB1GzfW.9dhX1nT9IBQcSy9fWF3B/qwzgj7GGHu', 'Administrator', 'admin@buildvent.com', 'admin'),
('operator', '$2y$12$.bx9RtAdqPTI6eEsTV39WeOcHhulKItM.HRkcil7AGoH1G.Oz2z22', 'Operator Gudang', 'operator@buildvent.com', 'operator');

-- Insert data kategori
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Semen', 'Berbagai jenis semen untuk konstruksi'),
('Besi & Baja', 'Material besi dan baja untuk struktur'),
('Cat & Finishing', 'Cat dan material finishing'),
('Hardware', 'Peralatan dan aksesoris kecil'),
('Material Dasar', 'Material dasar seperti pasir, kerikil');

-- Insert data supplier
INSERT INTO supplier (nama_supplier, kontak, alamat, email) VALUES
('PT Semen Indonesia', '021-1234567', 'Jakarta Selatan', 'info@semenindonesia.com'),
('CV Baja Mandiri', '021-7654321', 'Bekasi', 'sales@bajamandiri.com'),
('Toko Cat Jaya', '021-9876543', 'Depok', 'order@catjaya.com'),
('UD Hardware Sejahtera', '021-5555666', 'Tangerang', 'info@hardwaresejahtera.com');

-- Insert data barang contoh
INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, satuan, stok, stok_minimum, harga, lokasi) VALUES
('BRG001', 'Semen Portland 50kg', 1, 1, 'Sak', 150, 20, 65000, 'Gudang A - Rak 1'),
('BRG002', 'Besi Beton 12mm x 12m', 2, 2, 'Batang', 80, 15, 85000, 'Gudang B - Area 1'),
('BRG003', 'Cat Tembok Putih 25kg', 3, 3, 'Kaleng', 45, 10, 320000, 'Gudang A - Rak 3'),
('BRG004', 'Paku 5cm', 4, 4, 'Kg', 25, 5, 18000, 'Gudang C - Rak 2');
