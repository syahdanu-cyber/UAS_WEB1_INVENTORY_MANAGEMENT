# 🚀 PANDUAN DEPLOY LENGKAP - Step by Step

## Untuk Memenuhi Ketentuan UAS #8: Deploy/Hosting Online

---

## 📋 Checklist Sebelum Deploy

- [ ] Semua file sudah lengkap
- [ ] Database sudah di-export (database.sql)
- [ ] Kredensial database lokal sudah dicatat
- [ ] File README.md sudah diisi informasi pribadi
- [ ] Backup project di local

---

## 🎯 METODE 1: InfinityFree (PALING MUDAH - RECOMMENDED)

### ⏱️ Estimasi Waktu: 25-30 menit

### Langkah 1: Pendaftaran (5 menit)

1. **Buka Browser** → https://infinityfree.net
2. **Klik "Sign Up"**
3. **Isi Form Pendaftaran:**
   - Email: [email aktif Anda]
   - Password: [buat password kuat]
   - Centang "I agree to the Terms of Service"
4. **Klik "Sign Up"**
5. **Verifikasi Email:**
   - Buka email Anda
   - Klik link verifikasi dari InfinityFree
   - Login ke akun InfinityFree

### Langkah 2: Buat Website (5 menit)

1. **Dashboard InfinityFree** → Klik "Create Account"
2. **Pilih Domain:**
   - **Opsi A (Gratis):** 
     - Pilih subdomain gratis
     - Ketik nama: `inventory-[nama-anda]`
     - Pilih ekstensi: `.rf.gd` atau `.epizy.com`
     - Contoh: `inventory-john.rf.gd`
   - **Opsi B (Custom):**
     - Jika punya domain sendiri, masukkan di sini
3. **Klik "Create Account"**
4. **Tunggu Proses:**
   - Status akan berubah jadi "Active" (1-5 menit)
   - Refresh halaman jika perlu

### Langkah 3: Upload File (10 menit)

1. **Dashboard** → Klik website Anda → **"Control Panel"**
2. **Login ke cPanel** (kredensial otomatis)
3. **File Manager:**
   - Klik "Online File Manager" atau "File Manager"
   - Navigate ke folder `htdocs`
   - **PENTING:** Semua file HARUS di dalam `htdocs`

4. **Upload Files:**
   - **Cara A (Recommended): Upload ZIP**
     ```
     - Buat ZIP dari semua file project
     - Klik "Upload Files" di File Manager
     - Pilih file ZIP
     - Tunggu upload selesai
     - Klik kanan file ZIP → "Extract"
     - Hapus file ZIP setelah extract
     ```
   
   - **Cara B: Upload Manual** (jika file banyak kecil)
     ```
     - Klik "Upload Files"
     - Drag & drop semua folder dan file
     - Tunggu hingga selesai
     ```

5. **Verifikasi Struktur:**
   ```
   htdocs/
   ├── assets/
   ├── auth/
   ├── config/
   ├── dashboard/
   ├── barang/
   ├── supplier/
   ├── transaksi/
   ├── laporan/
   ├── includes/
   ├── index.php
   └── ... (semua file lainnya)
   ```

### Langkah 4: Setup Database (7 menit)

1. **Kembali ke cPanel** → "MySQL Databases"

2. **Buat Database Baru:**
   ```
   Database Name: inventory_db
   Klik "Create Database"
   
   Note: Nama database akan jadi: if0_xxxxx_inventory_db
   Catat nama lengkapnya!
   ```

3. **Buat User Database:**
   ```
   Username: inventory_user
   Password: [buat password kuat - catat!]
   Klik "Create User"
   
   Note: Username akan jadi: if0_xxxxx_inventory_user
   Catat username lengkapnya!
   ```

4. **Assign User ke Database:**
   ```
   Pilih user yang baru dibuat
   Pilih database yang baru dibuat
   Centang "All Privileges"
   Klik "Add User to Database"
   ```

5. **Import Database:**
   - Klik "phpMyAdmin" di cPanel
   - Login otomatis
   - Pilih database Anda di sidebar kiri
   - Tab "Import"
   - **Klik "Choose File"**
   - Pilih `database.sql` dari computer Anda
   - **Klik "Go"** atau "Import"
   - **Tunggu hingga selesai** (muncul notifikasi sukses)
   - **Ulangi untuk `database_remember_tokens.sql`**

### Langkah 5: Konfigurasi File (3 menit)

1. **Edit config/database.php:**
   
   Kembali ke File Manager → `htdocs/config/database.php`
   
   **Klik kanan → Edit** atau **Code Edit**
   
   **Ganti dengan:**
   ```php
   <?php
   // Database Configuration untuk InfinityFree
   
   // Database credentials
   define('DB_HOST', 'sql123.infinityfree.net'); // Lihat di cPanel MySQL Databases
   define('DB_NAME', 'if0_xxxxx_inventory_db'); // Nama database lengkap
   define('DB_USER', 'if0_xxxxx_inventory_user'); // Username lengkap
   define('DB_PASS', 'password_anda'); // Password yang Anda buat tadi
   
   // Create connection
   function getDB() {
       try {
           $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
           $pdo = new PDO($dsn, DB_USER, DB_PASS, [
               PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
               PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
               PDO::ATTR_EMULATE_PREPARES => false
           ]);
           return $pdo;
       } catch (PDOException $e) {
           error_log("Database connection failed: " . $e->getMessage());
           die("Database connection failed. Please contact administrator.");
       }
   }
   ?>
   ```
   
   **Klik "Save Changes"**

2. **Edit config/cookie_config.php:**
   
   File Manager → `htdocs/config/cookie_config.php`
   
   **Cari baris:**
   ```php
   define('COOKIE_SECURE', false);
   ```
   
   **Ganti menjadi:**
   ```php
   define('COOKIE_SECURE', true); // HTTPS sudah otomatis aktif
   ```
   
   **Klik "Save Changes"**

### Langkah 6: Testing (5 menit)

1. **Akses Website:**
   - URL: `https://inventory-[nama-anda].rf.gd`
   - Atau: `https://[nama-anda].epizy.com`

2. **Test Login:**
   ```
   Username: admin
   Password: admin123
   ```

3. **Test Semua Fitur:**
   - [ ] Dashboard muncul
   - [ ] Menu CRUD Barang berfungsi
   - [ ] Menu CRUD Supplier berfungsi
   - [ ] Transaksi bisa ditambahkan
   - [ ] Laporan bisa dilihat
   - [ ] Export PDF berfungsi
   - [ ] Export Excel berfungsi
   - [ ] Logout berhasil

4. **Test Session:**
   - Login → Close browser
   - Buka lagi → Seharusnya masih login (Remember Me)
   - Tunggu 30 menit → Seharusnya auto logout

### Langkah 7: Dokumentasi (2 menit)

**Catat Informasi Penting:**

```
URL APLIKASI ONLINE
===================
URL: https://inventory-[nama-anda].rf.gd
Platform: InfinityFree
Tanggal Deploy: [tanggal hari ini]

KREDENSIAL DEMO
===============
Admin:
- Username: admin
- Password: admin123

Staff:
- Username: staff
- Password: staff123

KREDENSIAL HOSTING
==================
Email: [email hosting Anda]
Password: [password hosting]

DATABASE INFO
=============
Host: sql123.infinityfree.net
Database: if0_xxxxx_inventory_db
Username: if0_xxxxx_inventory_user
Password: [password database]
```

**Simpan informasi ini untuk dikumpulkan bersama UAS!**

---

## 🎯 METODE 2: 000webhost (Alternatif)

### ⏱️ Estimasi Waktu: 30 menit

### Langkah 1: Pendaftaran
1. Kunjungi https://www.000webhost.com
2. Klik "Free Sign Up"
3. Isi email dan password
4. Verifikasi email

### Langkah 2: Buat Website
1. Dashboard → "Create New Website"
2. Pilih "Build Your Own Website"
3. Setup:
   - Website Name: inventory-[nama]
   - Password: [buat password]
4. Klik "Create"

### Langkah 3: Upload Files
1. Dashboard → Website → "Manage"
2. "Files" → "File Manager"
3. Folder `public_html`
4. Upload ZIP atau manual
5. Extract jika ZIP

### Langkah 4: Database
1. "Tools" → "MySQL Manager"
2. "New Database"
   - Name: inventory_db
   - Password: [buat password]
3. "Manage" → "phpMyAdmin"
4. Import `database.sql` dan `database_remember_tokens.sql`

### Langkah 5: Konfigurasi
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'id12345_inventory_db');
define('DB_USER', 'id12345_inventory_db');
define('DB_PASS', 'password_anda');
```

Edit `config/cookie_config.php`:
```php
define('COOKIE_SECURE', true);
```

### Langkah 6: Test
URL: `https://inventory-[nama].000webhostapp.com`

---

## 🎯 METODE 3: Railway.app (Modern - Untuk Advanced)

### ⏱️ Estimasi Waktu: 20 menit

### Prerequisite
- Akun GitHub
- Git installed

### Langkah 1: Push ke GitHub
```bash
cd manajemen_inventory
git init
git add .
git commit -m "Initial commit for UAS Web 1"
git remote add origin https://github.com/[username]/inventory-uas.git
git push -u origin main
```

### Langkah 2: Setup Railway
1. Kunjungi https://railway.app
2. Sign up dengan GitHub
3. "New Project"
4. "Deploy from GitHub repo"
5. Pilih repository `inventory-uas`

### Langkah 3: Add Database
1. Project → "New" → "Database" → "MySQL"
2. Tunggu database ready
3. Copy credentials

### Langkah 4: Environment Variables
1. Project → Settings → Variables
2. Add:
   ```
   DB_HOST=[dari railway]
   DB_NAME=railway
   DB_USER=root
   DB_PASS=[dari railway]
   ```

### Langkah 5: Deploy
- Railway otomatis deploy
- Copy public URL
- Test aplikasi

---

## 📸 Screenshot untuk Dokumentasi

**Ambil screenshot berikut untuk dikumpulkan:**

1. **Halaman Login** (sebelum login)
2. **Dashboard** (setelah login sebagai admin)
3. **Halaman CRUD Barang** (list)
4. **Form Tambah Barang**
5. **Halaman Supplier**
6. **Halaman Transaksi**
7. **Halaman Laporan**
8. **Export PDF** (hasil download)
9. **Export Excel** (hasil download)
10. **Session Monitor** (timer terlihat)

**Tips Screenshot:**
- Pastikan URL terlihat di address bar
- Gunakan full screen untuk tampilan profesional
- Screenshot dalam format PNG atau JPG
- Beri nama file yang jelas: `01_login.png`, `02_dashboard.png`, dst

---

## 📝 Template Laporan Deployment

```markdown
# LAPORAN DEPLOYMENT APLIKASI
## Sistem Manajemen Inventory

### Informasi Deployment
**Tanggal Deploy:** [Tanggal]
**Platform Hosting:** [InfinityFree/000webhost/Railway]
**URL Aplikasi:** [URL lengkap]

### Kredensial
**Admin:**
- Username: admin
- Password: admin123

**Staff:**
- Username: staff  
- Password: staff123

### Proses Deployment
1. Upload file via [metode upload]
2. Import database menggunakan phpMyAdmin
3. Konfigurasi file database.php dan cookie_config.php
4. Testing semua fitur
5. Dokumentasi dengan screenshot

### Hasil Testing
- [x] Login berhasil
- [x] Dashboard tampil normal
- [x] CRUD Barang berfungsi
- [x] CRUD Supplier berfungsi
- [x] Transaksi dapat ditambahkan
- [x] Laporan dapat dilihat
- [x] Export PDF berhasil
- [x] Export Excel berhasil
- [x] Session management berfungsi
- [x] Logout berhasil

### Catatan
[Tulis catatan atau kendala yang dihadapi saat deploy]

### Screenshot
Terlampir: 10 screenshot aplikasi online
```

---

## 🐛 Troubleshooting Deployment

### Error: Database Connection Failed
**Solusi:**
1. Verifikasi kredensial database di config/database.php
2. Pastikan menggunakan hostname yang benar
3. Check database sudah dibuat di cPanel
4. Pastikan user sudah di-assign ke database

### Error: 404 Not Found
**Solusi:**
1. Pastikan file ada di folder `htdocs` (InfinityFree) atau `public_html` (000webhost)
2. Check struktur folder benar
3. Pastikan index.php ada di root

### Error: Cookie Tidak Berfungsi
**Solusi:**
1. Set `COOKIE_SECURE = true` untuk HTTPS
2. Clear browser cache & cookies
3. Test dengan browser incognito

### Error: PDF/Excel Export Gagal
**Solusi:**
1. Check folder permissions (755 atau 777)
2. Pastikan library terinstall
3. Check PHP version (min 7.4)

### Session Timeout Terlalu Cepat
**Solusi:**
1. Check SESSION_TIMEOUT di cookie_config.php
2. Pastikan server time zone benar
3. Test Remember Me function

---

## ✅ Checklist Final Sebelum Pengumpulan

- [ ] Website online dan bisa diakses
- [ ] URL dicatat dan dimasukkan ke README.md
- [ ] Login berfungsi (admin & staff)
- [ ] Semua fitur CRUD tested
- [ ] Laporan PDF & Excel tested
- [ ] Session management tested
- [ ] Screenshot lengkap diambil
- [ ] Informasi deployment didokumentasikan
- [ ] Backup database dari hosting
- [ ] README.md lengkap dengan info pribadi
- [ ] Laporan deployment ditulis

---

## 🎉 Selamat! Aplikasi Sudah Online!

**Langkah Selanjutnya:**
1. ✅ Masukkan URL ke README.md
2. ✅ Screenshot semua fitur
3. ✅ Siapkan dokumentasi
4. ✅ Backup semua file
5. ✅ Siap dikumpulkan!

**Good luck dengan UAS Anda!** 🚀

---

**Tips Terakhir:**
- Test aplikasi di berbagai browser
- Test di mobile juga
- Catat semua kredensial dengan aman
- Jangan lupa update README dengan URL final
- Buat backup before pengumpulan

**Estimasi Total Waktu Deploy: 30-45 menit**
