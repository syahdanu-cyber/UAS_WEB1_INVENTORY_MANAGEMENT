# Sistem Manajemen Inventory

Sistem Manajemen Inventory adalah aplikasi web berbasis PHP untuk mengelola stok barang, supplier, transaksi masuk/keluar, dan laporan inventory yang lengkap.

## 📋 Fitur Utama

### ✅ Memenuhi Ketentuan UAS Web 1:

1. **Backend & Frontend Terintegrasi** ✓
   - PHP untuk backend dengan PDO
   - Bootstrap 5 untuk frontend responsive
   - jQuery & JavaScript untuk interaktivitas

2. **Dashboard Lengkap** ✓
   - Statistik real-time (total barang, supplier, transaksi)
   - Grafik transaksi 6 bulan terakhir (Chart.js)
   - Grafik distribusi kategori barang
   - Alert barang stok rendah
   - Riwayat transaksi terbaru

3. **Sistem Laporan dengan Export** ✓
   - Export ke PDF (menggunakan TCPDF)
   - Export ke Excel (menggunakan PHPSpreadsheet)
   - Filter laporan berdasarkan periode
   - Laporan barang, supplier, dan transaksi

4. **CRUD Lengkap** ✓
   - **Barang**: Create, Read, Update, Delete dengan validasi
   - **Supplier**: Manajemen data supplier
   - **Transaksi Masuk**: Pencatatan barang masuk
   - **Transaksi Keluar**: Pencatatan barang keluar

5. **Session & Cookies Management** ✓
   - Login system dengan password hashing
   - Session timeout (30 menit)
   - Remember me functionality
   - Session validation di setiap halaman
   - CSRF protection

6. **Studi Kasus Nyata** ✓
   - Sistem inventory management untuk bisnis retail/gudang
   - Tracking stok barang real-time
   - Manajemen supplier
   - Pencatatan transaksi masuk/keluar

7. **Individual Project** ✓
   - Dikerjakan individual, bukan kelompok

8. **Ready untuk Deployment** ✓
   - Struktur project terorganisir
   - Database SQL siap import
   - Panduan deployment lengkap

## 🚀 Teknologi yang Digunakan

- **Backend**: PHP 7.4+ dengan PDO
- **Database**: MySQL/MariaDB
- **Frontend**: 
  - Bootstrap 5.3
  - jQuery 3.7
  - Chart.js 4.4
  - DataTables
  - Bootstrap Icons
- **Export**: 
  - TCPDF (PDF)
  - PHPSpreadsheet (Excel)

## 📁 Struktur Direktori

```
manajemen_inventory/
│
├── assets/
│   ├── css/          # Stylesheet files
│   ├── js/           # JavaScript files
│   └── img/          # Images & logo
│
├── config/
│   └── database.php  # Database configuration
│
├── auth/
│   ├── login.php              # Login page
│   ├── login_process.php      # Login handler
│   ├── logout.php             # Logout handler
│   └── session_check.php      # Session management
│
├── dashboard/
│   ├── index.php      # Main dashboard
│   └── statistik.php  # Statistics page
│
├── barang/
│   ├── barang.php    # List barang
│   ├── tambah.php    # Add barang
│   ├── edit.php      # Edit barang
│   ├── hapus.php     # Delete barang
│   └── proses.php    # Process handler
│
├── supplier/
│   ├── supplier.php  # List supplier
│   ├── tambah.php    # Add supplier
│   ├── edit.php      # Edit supplier
│   └── hapus.php     # Delete supplier
│
├── transaksi/
│   ├── masuk.php     # Incoming transactions
│   ├── keluar.php    # Outgoing transactions
│   └── proses.php    # Transaction handler
│
├── laporan/
│   ├── laporan.php      # Report page
│   ├── export_pdf.php   # PDF export
│   └── export_excel.php # Excel export
│
├── includes/
│   ├── header.php    # Header component
│   ├── sidebar.php   # Sidebar navigation
│   └── footer.php    # Footer component
│
├── index.php         # Landing page
├── .htaccess         # Apache configuration
├── database.sql      # Database schema
└── README.md         # This file
```

## 🔧 Instalasi

### Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.3+
- Apache/Nginx web server
- Extension PHP yang diperlukan:
  - PDO
  - pdo_mysql
  - mbstring
  - gd (untuk PDF)
  - zip (untuk Excel)

### Langkah Instalasi

#### 1. Clone atau Download Project

```bash
# Clone repository (jika menggunakan git)
git clone https://github.com/username/manajemen-inventory.git

# Atau download dan extract ZIP file
```

#### 2. Setup Database

```sql
-- Buka phpMyAdmin atau MySQL client
-- Buat database baru
CREATE DATABASE manajemen_inventory;

-- Import file database.sql
mysql -u root -p manajemen_inventory < database.sql
```

Atau melalui phpMyAdmin:
1. Buka phpMyAdmin
2. Buat database baru: `manajemen_inventory`
3. Import file `database.sql`

#### 3. Konfigurasi Database

Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Sesuaikan dengan username MySQL Anda
define('DB_PASS', '');              // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'manajemen_inventory');
```

#### 4. Setup Permissions

```bash
# Berikan permission untuk direktori assets
chmod -R 755 assets/
chmod -R 755 uploads/  # Jika ada folder uploads
```

#### 5. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/manajemen_inventory/
```

### Default Login Credentials

**Admin:**
- Username: `admin`
- Password: `admin123`

**Staff:**
- Username: `staff`
- Password: `admin123`

**⚠️ PENTING:** Segera ubah password default setelah login pertama kali!

## 🌐 Deployment ke Hosting

### Option 1: Shared Hosting (cPanel)

1. **Upload Files**
   - Compress semua file menjadi ZIP
   - Login ke cPanel
   - Buka File Manager
   - Upload ke `public_html/` atau subdirectory
   - Extract file ZIP

2. **Create Database**
   - Buka MySQL Databases di cPanel
   - Buat database baru
   - Buat user dan password
   - Assign user ke database dengan ALL PRIVILEGES

3. **Import Database**
   - Buka phpMyAdmin
   - Pilih database yang dibuat
   - Import file `database.sql`

4. **Update Configuration**
   - Edit `config/database.php`
   - Update credentials database

5. **Set Permissions**
   - Set permission folder `assets/` ke 755
   - Set permission files ke 644

### Option 2: VPS/Cloud Server

#### Menggunakan Ubuntu/Debian:

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install LAMP Stack
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-gd php-zip -y

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2

# Clone/Upload project
cd /var/www/html
sudo git clone [your-repo-url] manajemen_inventory
# Atau upload via SFTP

# Set permissions
sudo chown -R www-data:www-data /var/www/html/manajemen_inventory
sudo chmod -R 755 /var/www/html/manajemen_inventory

# Create database
sudo mysql -u root -p
CREATE DATABASE manajemen_inventory;
CREATE USER 'inventory_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON manajemen_inventory.* TO 'inventory_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database
mysql -u inventory_user -p manajemen_inventory < /var/www/html/manajemen_inventory/database.sql

# Configure Apache (optional - untuk virtual host)
sudo nano /etc/apache2/sites-available/inventory.conf
```

### Option 3: Free Hosting Recommendations

1. **InfinityFree**
   - URL: https://infinityfree.net
   - PHP & MySQL support
   - Free subdomain
   - No ads

2. **000webhost**
   - URL: https://www.000webhost.com
   - 300 MB disk space
   - PHP & MySQL support

3. **Heroku** (dengan ClearDB MySQL add-on)
   - URL: https://heroku.com
   - Free tier available
   - Requires Git deployment

## 📊 Panduan Penggunaan

### Login

1. Akses halaman login
2. Masukkan username dan password
3. Centang "Ingat saya" untuk auto-login (opsional)
4. Klik tombol Login

### Dashboard

Dashboard menampilkan:
- Total barang, supplier, transaksi
- Grafik transaksi 6 bulan terakhir
- Distribusi kategori barang
- Alert barang stok rendah
- Riwayat transaksi terbaru

### Manajemen Barang

**Tambah Barang:**
1. Menu Barang → Tambah Barang
2. Isi form (kode, nama, kategori, supplier, dll)
3. Klik Simpan

**Edit Barang:**
1. Klik tombol Edit pada data barang
2. Update data yang diperlukan
3. Klik Update

**Hapus Barang:**
1. Klik tombol Hapus
2. Konfirmasi penghapusan

### Transaksi Barang Masuk

1. Menu Transaksi → Barang Masuk
2. Pilih barang dari dropdown
3. Pilih supplier
4. Masukkan jumlah dan harga
5. Isi tanggal dan keterangan
6. Klik Simpan
7. Stok barang otomatis bertambah

### Transaksi Barang Keluar

1. Menu Transaksi → Barang Keluar
2. Pilih barang dari dropdown
3. Masukkan jumlah (tidak boleh melebihi stok)
4. Isi tujuan dan keterangan
5. Klik Simpan
6. Stok barang otomatis berkurang

### Generate Laporan

1. Menu Laporan
2. Pilih jenis laporan (Barang/Supplier/Transaksi)
3. Set periode tanggal
4. Klik "Tampilkan"
5. Klik "Export PDF" atau "Export Excel"

## 🔐 Keamanan

Aplikasi ini mengimplementasikan:

✅ Password hashing (bcrypt)
✅ Session management dengan timeout
✅ CSRF protection
✅ Prepared statements (mencegah SQL injection)
✅ Input validation & sanitization
✅ XSS protection
✅ Role-based access control

## 🛠️ Troubleshooting

### Error: Connection failed

**Solusi:**
- Pastikan MySQL service berjalan
- Cek kredensial database di `config/database.php`
- Pastikan database sudah dibuat dan imported

### Error: Session timeout terus menerus

**Solusi:**
- Cek `session.gc_maxlifetime` di php.ini
- Pastikan session directory writable
- Clear browser cookies

### Halaman tidak menampilkan style/CSS

**Solusi:**
- Pastikan path CSS benar
- Clear browser cache
- Cek permission folder assets/

### Export PDF tidak berfungsi

**Solusi:**
- Install library TCPDF via Composer:
  ```bash
  composer require tecnickcom/tcpdf
  ```
- Atau download manual dan extract ke folder `vendor/`

### Export Excel tidak berfungsi

**Solusi:**
- Install PHPSpreadsheet via Composer:
  ```bash
  composer require phpoffice/phpspreadsheet
  ```

## 📝 Catatan Pengembangan

### Menambah Fitur Baru

1. Buat file baru di folder yang sesuai
2. Include header dan footer component
3. Gunakan session_check untuk proteksi
4. Follow coding standards yang ada

### Best Practices

- Selalu gunakan prepared statements
- Validasi input di frontend dan backend
- Sanitize output dengan `htmlspecialchars()`
- Log aktivitas penting
- Backup database secara rutin

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Author

**Nama Mahasiswa:** [Nama Anda]
**NIM:** [NIM Anda]
**Mata Kuliah:** Web Programming 1
**Dosen:** [Nama Dosen]

## 📞 Contact & Support

Untuk pertanyaan atau issue:
- Email: [email@example.com]
- GitHub Issues: [repository-url]/issues

## 🎯 Checklist UAS

- [x] Backend & Frontend terintegrasi
- [x] Dashboard dengan statistik
- [x] Sistem laporan PDF & Excel
- [x] CRUD lengkap (Barang, Supplier, Transaksi)
- [x] Session & cookies management
- [x] Studi kasus nyata (Inventory Management)
- [x] Dikerjakan individual
- [x] Siap untuk deployment/hosting

## 🔄 Update Log

### Version 1.0.0 (2024)
- Initial release
- Implementasi semua fitur dasar
- Dashboard dengan chart
- Export PDF & Excel
- Session management
- CRUD lengkap

---

**Terima kasih telah menggunakan Sistem Manajemen Inventory!** 🎉
