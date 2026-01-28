-- Database: manajemen_inventory
CREATE DATABASE IF NOT EXISTS manajemen_inventory;
USE manajemen_inventory;

-- Tabel Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori Barang
CREATE TABLE kategori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Supplier
CREATE TABLE supplier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_supplier VARCHAR(20) UNIQUE NOT NULL,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Barang
CREATE TABLE barang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(20) UNIQUE NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    kategori_id INT,
    supplier_id INT,
    satuan VARCHAR(20),
    stok INT DEFAULT 0,
    harga_beli DECIMAL(15,2),
    harga_jual DECIMAL(15,2),
    stok_minimum INT DEFAULT 10,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL
);

-- Tabel Transaksi Masuk
CREATE TABLE transaksi_masuk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(30) UNIQUE NOT NULL,
    barang_id INT NOT NULL,
    supplier_id INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(15,2),
    total_harga DECIMAL(15,2),
    tanggal_masuk DATE NOT NULL,
    keterangan TEXT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Transaksi Keluar
CREATE TABLE transaksi_keluar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(30) UNIQUE NOT NULL,
    barang_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(15,2),
    total_harga DECIMAL(15,2),
    tanggal_keluar DATE NOT NULL,
    tujuan VARCHAR(100),
    keterangan TEXT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert data user default (password: admin123)
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@inventory.com', 'admin'),
('staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Gudang', 'staff@inventory.com', 'staff');

-- Insert data kategori
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Elektronik', 'Barang elektronik dan gadget'),
('Furniture', 'Perabotan kantor dan rumah'),
('Alat Tulis', 'Perlengkapan tulis menulis'),
('Konsumsi', 'Barang konsumsi'),
('Aksesoris', 'Aksesoris dan pelengkap');

-- Insert data supplier
INSERT INTO supplier (kode_supplier, nama_supplier, alamat, telepon, email) VALUES
('SUP001', 'PT Maju Jaya', 'Jl. Sudirman No. 123, Jakarta', '021-12345678', 'info@majujaya.com'),
('SUP002', 'CV Berkah Electronics', 'Jl. Gatot Subroto No. 45, Bandung', '022-87654321', 'sales@berkah.com'),
('SUP003', 'Toko Sejahtera', 'Jl. Ahmad Yani No. 67, Surabaya', '031-23456789', 'contact@sejahtera.com');

-- Insert data barang contoh
INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, satuan, stok, harga_beli, harga_jual, stok_minimum, deskripsi) VALUES
('BRG001', 'Laptop ASUS ROG', 1, 2, 'Unit', 15, 12000000.00, 15000000.00, 5, 'Laptop gaming spesifikasi tinggi'),
('BRG002', 'Mouse Logitech M185', 1, 2, 'Unit', 50, 75000.00, 100000.00, 10, 'Mouse wireless'),
('BRG003', 'Keyboard Mechanical', 1, 2, 'Unit', 30, 350000.00, 450000.00, 10, 'Keyboard gaming RGB'),
('BRG004', 'Kursi Kantor Ergonomis', 2, 1, 'Unit', 20, 800000.00, 1200000.00, 5, 'Kursi kantor dengan sandaran punggung'),
('BRG005', 'Meja Komputer', 2, 1, 'Unit', 12, 1500000.00, 2000000.00, 5, 'Meja komputer minimalis'),
('BRG006', 'Pulpen Pilot', 3, 3, 'Lusin', 100, 30000.00, 45000.00, 20, 'Pulpen tinta hitam'),
('BRG007', 'Kertas A4', 3, 3, 'Rim', 200, 35000.00, 50000.00, 50, 'Kertas HVS 80 gram');

-- Insert transaksi masuk contoh
INSERT INTO transaksi_masuk (kode_transaksi, barang_id, supplier_id, jumlah, harga_satuan, total_harga, tanggal_masuk, keterangan, user_id) VALUES
('TM-2024001', 1, 2, 10, 12000000.00, 120000000.00, '2024-01-15', 'Pembelian laptop untuk stock', 1),
('TM-2024002', 2, 2, 30, 75000.00, 2250000.00, '2024-01-20', 'Restok mouse wireless', 1),
('TM-2024003', 4, 1, 15, 800000.00, 12000000.00, '2024-02-01', 'Pembelian kursi kantor', 1);

-- Insert transaksi keluar contoh
INSERT INTO transaksi_keluar (kode_transaksi, barang_id, jumlah, harga_satuan, total_harga, tanggal_keluar, tujuan, keterangan, user_id) VALUES
('TK-2024001', 1, 3, 15000000.00, 45000000.00, '2024-02-10', 'Kantor Cabang Jakarta', 'Penjualan laptop', 1),
('TK-2024002', 2, 10, 100000.00, 1000000.00, '2024-02-15', 'Toko Retail', 'Penjualan mouse', 1);
