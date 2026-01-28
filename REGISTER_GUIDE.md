# 📝 PANDUAN FITUR REGISTER

## 🎯 OVERVIEW

Fitur Register memungkinkan user baru untuk mendaftar akun sendiri tanpa perlu admin menambahkan manual.

---

## 📁 FILE YANG DIBUAT

### 1. **auth/register.php** (Halaman Form Register)
- Form pendaftaran user baru
- Validasi frontend (JavaScript)
- UI menggunakan Bootstrap 5
- Password visibility toggle
- Real-time password match validation

### 2. **auth/register_process.php** (Logic Pendaftaran)
- Validasi backend (PHP)
- Cek username & email duplicate
- Password hashing (bcrypt)
- Insert ke database
- Activity logging (optional)

### 3. **auth/login.php** (Updated)
- Tambah link "Daftar di sini"
- Success message setelah register

---

## 🚀 CARA MENGGUNAKAN

### 1. **Akses Halaman Register**

```
URL: http://localhost/manajemen_inventory/auth/register.php
```

Atau klik link "Daftar di sini" di halaman login.

---

### 2. **Isi Form Pendaftaran**

Form yang harus diisi:
- ✅ **Username** (4-50 karakter, huruf/angka/underscore)
- ✅ **Nama Lengkap** (maksimal 100 karakter)
- ✅ **Email** (format email valid)
- ✅ **Password** (minimal 6 karakter)
- ✅ **Konfirmasi Password** (harus sama dengan password)
- ✅ **Role** (Staff atau Admin)
- ✅ **Setuju dengan syarat & ketentuan** (checkbox)

---

### 3. **Contoh Pengisian**

```
Username:           john_doe
Nama Lengkap:       John Doe
Email:              john@example.com
Password:           johndoe123
Konfirmasi Password: johndoe123
Role:               Staff
[✓] Setuju dengan syarat & ketentuan
```

Klik **"Daftar Sekarang"**

---

### 4. **Setelah Berhasil Register**

Anda akan diarahkan ke halaman login dengan pesan:
```
✅ "Registrasi berhasil! Silakan login dengan akun baru Anda."
```

Login dengan username & password yang baru dibuat:
```
Username: john_doe
Password: johndoe123
```

---

## 🔒 VALIDASI & KEAMANAN

### ✅ Validasi Frontend (JavaScript)
- Username minimal 4 karakter
- Password minimal 6 karakter
- Password dan Konfirmasi harus sama (real-time check)
- Email harus format valid
- Semua field wajib diisi
- Agreement harus dicentang

### ✅ Validasi Backend (PHP)
- Username: 4-50 karakter, hanya huruf/angka/underscore
- Cek duplicate username
- Cek duplicate email
- Email validation dengan filter_var()
- Password minimal 6 karakter
- Password hashing dengan bcrypt (cost: 10)
- Role harus 'admin' atau 'staff'

### ✅ Keamanan
- Password di-hash dengan bcrypt sebelum disimpan
- PDO prepared statements (SQL injection prevention)
- Input sanitization dengan trim()
- XSS protection dengan htmlspecialchars()
- HTTPS ready (uncomment di .htaccess untuk production)

---

## ⚠️ ERROR MESSAGES

### ❌ Username sudah digunakan
```
Error: "Username sudah digunakan!"
Solusi: Gunakan username lain
```

### ❌ Email sudah digunakan
```
Error: "Email sudah digunakan!"
Solusi: Gunakan email lain atau login jika sudah punya akun
```

### ❌ Password tidak sama
```
Error: "Password dan Konfirmasi Password tidak sama!"
Solusi: Ketik ulang password yang sama di kedua field
```

### ❌ Input tidak valid
```
Error: "Semua field wajib diisi dengan benar!"
Solusi: Cek semua field dan pastikan sudah diisi sesuai aturan
```

---

## 🗄️ DATABASE

Data user baru akan disimpan di tabel `users`:

```sql
INSERT INTO users (
    username, 
    password,           -- Hashed dengan bcrypt
    nama_lengkap, 
    email, 
    role,              -- 'admin' atau 'staff'
    created_at, 
    updated_at
) VALUES (...)
```

---

## 📊 FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────┐
│                  USER AKSES                         │
│              register.php                           │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
        ┌─────────────────────┐
        │   Isi Form:         │
        │   - Username        │
        │   - Nama Lengkap    │
        │   - Email           │
        │   - Password        │
        │   - Confirm Pass    │
        │   - Role            │
        └─────────┬───────────┘
                  │
                  ▼
        ┌─────────────────────┐
        │  Klik "Daftar"      │
        └─────────┬───────────┘
                  │
                  ▼
        ┌─────────────────────────────────┐
        │  POST to register_process.php   │
        └─────────┬───────────────────────┘
                  │
                  ▼
        ┌─────────────────────┐
        │  Validasi Input     │
        │  - Cek kosong       │
        │  - Cek format       │
        │  - Cek panjang      │
        └─────────┬───────────┘
                  │
         ┌────────┴────────┐
         │                 │
    [INVALID]         [VALID]
         │                 │
         ▼                 ▼
   ┌──────────┐    ┌────────────────┐
   │ Redirect │    │ Cek Duplicate  │
   │ + Error  │    │ Username/Email │
   └──────────┘    └────────┬───────┘
                            │
                   ┌────────┴────────┐
                   │                 │
              [DUPLICATE]        [UNIQUE]
                   │                 │
                   ▼                 ▼
            ┌──────────┐    ┌────────────────┐
            │ Redirect │    │ Hash Password  │
            │ + Error  │    │ (bcrypt)       │
            └──────────┘    └────────┬───────┘
                                     │
                                     ▼
                            ┌────────────────┐
                            │ INSERT INTO    │
                            │ users table    │
                            └────────┬───────┘
                                     │
                                     ▼
                            ┌────────────────┐
                            │ Log Activity   │
                            │ (optional)     │
                            └────────┬───────┘
                                     │
                                     ▼
                            ┌────────────────┐
                            │ Redirect to    │
                            │ login.php      │
                            │ + Success Msg  │
                            └────────────────┘
                                     │
                                     ▼
                            ┌────────────────┐
                            │ User Login     │
                            │ dengan akun    │
                            │ baru           │
                            └────────────────┘
```

---

## 🎨 FITUR UI/UX

### ✨ Password Visibility Toggle
- Icon mata untuk show/hide password
- Berlaku untuk Password dan Konfirmasi Password

### ✨ Real-time Validation
- Password match check saat mengetik
- Invalid feedback langsung muncul
- Form validation Bootstrap 5

### ✨ Auto-dismiss Alert
- Success/error message otomatis hilang setelah 5 detik

### ✨ Responsive Design
- Mobile-friendly
- Bootstrap 5 responsive grid

---

## 🔗 INTEGRASI

### Link di Login Page
```php
<a href="register.php">Daftar di sini</a>
```

### Link di Register Page
```php
<a href="login.php">Login di sini</a>
```

---

## 🧪 TESTING

### Test Case 1: Registrasi Normal
```
1. Buka register.php
2. Isi semua field dengan data valid
3. Klik "Daftar Sekarang"
4. Harus redirect ke login dengan success message
5. Login dengan akun baru
6. Harus berhasil masuk dashboard
```

### Test Case 2: Username Duplicate
```
1. Register dengan username yang sudah ada (misal: admin)
2. Harus muncul error "Username sudah digunakan!"
3. Data tidak masuk database
```

### Test Case 3: Email Duplicate
```
1. Register dengan email yang sudah ada
2. Harus muncul error "Email sudah digunakan!"
```

### Test Case 4: Password Mismatch
```
1. Isi password: "test123"
2. Isi confirm: "test456"
3. Harus muncul error "Password tidak sama!"
4. Submit button tidak akan proses
```

### Test Case 5: Input Validation
```
1. Username < 4 karakter → Error
2. Password < 6 karakter → Error
3. Email tidak valid → Error
4. Checkbox tidak dicentang → Error
```

---

## 📝 CUSTOMIZATION

### Ubah Role Default
Edit `register.php` line ~80:
```php
<option value="staff" selected>Staff</option>
<option value="admin">Admin</option>
```

### Ubah Password Minimum Length
Edit `register.php` line ~65:
```php
minlength="6"  // Ubah angka ini
```

Dan `register_process.php` line ~36:
```php
} elseif (strlen($password) < 6) {  // Ubah angka ini
```

### Disable Role Selection (Auto Staff)
Edit `register.php`, hapus select role dan set hidden:
```php
<input type="hidden" name="role" value="staff">
```

### Email Verification (Advanced)
Tambahkan kolom `email_verified` dan `verification_token` di tabel users, kemudian kirim email verifikasi setelah register.

---

## 📞 TROUBLESHOOTING

### ❌ Error: "Call to undefined function password_hash()"
**Solusi:** Update PHP ke versi 5.5 atau lebih baru

### ❌ Error: "Column 'role' doesn't exist"
**Solusi:** Pastikan tabel users sudah punya kolom role (sudah ada di database.sql)

### ❌ Register berhasil tapi tidak bisa login
**Solusi:** Cek apakah password hashing berhasil di database (harus dimulai dengan $2y$)

### ❌ Email/Username duplicate tidak terdeteksi
**Solusi:** Pastikan query check duplicate dijalankan sebelum insert

---

## ✅ CHECKLIST IMPLEMENTASI

```
☑️ File register.php dibuat
☑️ File register_process.php dibuat
☑️ File login.php updated (tambah link register)
☑️ Database tabel users sudah ada
☑️ Test registrasi user baru
☑️ Test login dengan akun baru
☑️ Test validasi (username duplicate, email duplicate, dll)
☑️ Test password hashing (cek di database)
```

---

## 🎉 SELESAI!

Fitur Register sudah lengkap dan siap digunakan!

**URL Register:**
```
http://localhost/manajemen_inventory/auth/register.php
```

Selamat mencoba! 🚀
