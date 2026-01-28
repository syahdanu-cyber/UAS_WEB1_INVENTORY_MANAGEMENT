# 🔒 PANDUAN ROLE-BASED ACCESS CONTROL (RBAC)

## 🎯 OVERVIEW

Sistem sekarang memiliki kontrol akses berbasis role yang ketat:
- **ADMIN**: Full access (Create, Read, Update, Delete)
- **STAFF**: Read-only access (hanya bisa melihat data)

---

## 👥 PERBEDAAN AKSES ADMIN vs STAFF

### 🔑 ADMIN (Full Access)

✅ **Data Barang**
- ✅ Lihat data barang
- ✅ Tambah barang baru
- ✅ Edit barang
- ✅ Hapus barang

✅ **Data Supplier**
- ✅ Lihat data supplier
- ✅ Tambah supplier baru
- ✅ Edit supplier
- ✅ Hapus supplier

✅ **Transaksi**
- ✅ Lihat riwayat transaksi
- ✅ Input transaksi barang masuk
- ✅ Input transaksi barang keluar

✅ **Laporan**
- ✅ Lihat laporan
- ✅ Export PDF
- ✅ Export Excel

✅ **Dashboard & Statistik**
- ✅ Lihat semua data
- ✅ Lihat grafik dan statistik

---

### 👁️ STAFF (Read-Only)

✅ **Data Barang**
- ✅ Lihat data barang
- ❌ Tombol "Tambah Barang" disembunyikan
- ❌ Tombol "Edit" disembunyikan
- ❌ Tombol "Hapus" disembunyikan
- 👁️ Badge "View Only" ditampilkan

✅ **Data Supplier**
- ✅ Lihat data supplier
- ❌ Tombol "Tambah Supplier" disembunyikan
- ❌ Tombol "Edit" disembunyikan
- ❌ Tombol "Hapus" disembunyikan
- 👁️ Badge "View Only" ditampilkan

✅ **Transaksi**
- ✅ Lihat riwayat transaksi masuk
- ✅ Lihat riwayat transaksi keluar
- ❌ Form input transaksi disembunyikan
- ⚠️ Notifikasi: "Hanya Admin yang dapat melakukan transaksi"

✅ **Laporan**
- ✅ Lihat laporan
- ✅ Export PDF
- ✅ Export Excel

✅ **Dashboard & Statistik**
- ✅ Lihat semua data
- ✅ Lihat grafik dan statistik
- ℹ️ Notifikasi info: "Mode Read-Only"

---

## 🛡️ IMPLEMENTASI KEAMANAN

### 1. **Session Check Functions**

Di file `auth/session_check.php` ditambahkan:

```php
// Cek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Cek apakah user adalah staff
function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}

// Require admin (redirect jika bukan admin)
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../dashboard/index.php?error=access_denied');
        exit();
    }
}

// Permission functions
function canCreate() { return isAdmin(); }
function canEdit() { return isAdmin(); }
function canDelete() { return isAdmin(); }
function canView() { return isLoggedIn(); }
```

---

### 2. **Frontend Protection (UI)**

Sembunyikan tombol untuk staff:

```php
<?php if (canCreate()): ?>
    <a href="tambah.php" class="btn btn-primary">Tambah Data</a>
<?php endif; ?>

<?php if (canEdit()): ?>
    <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit</a>
<?php endif; ?>

<?php if (canDelete()): ?>
    <a href="hapus.php?id=<?php echo $id; ?>" class="btn btn-danger">Hapus</a>
<?php endif; ?>

<?php if (!canEdit() && !canDelete()): ?>
    <span class="badge bg-secondary">View Only</span>
<?php endif; ?>
```

---

### 3. **Backend Protection**

Setiap halaman CRUD dilindungi:

**Halaman Tambah (CREATE):**
```php
// barang/tambah.php
requireAdmin(); // Hanya admin yang bisa akses
```

**Halaman Edit (UPDATE):**
```php
// barang/edit.php
requireAdmin(); // Hanya admin yang bisa akses
```

**Halaman Hapus (DELETE):**
```php
// barang/hapus.php
requireAdmin(); // Hanya admin yang bisa akses
```

**Form Processing:**
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!canCreate()) {
        header('Location: index.php?error=access_denied');
        exit();
    }
    // Process form...
}
```

---

## 📁 FILE YANG DIUPDATE

### ✅ Core Files
1. `auth/session_check.php` - Tambah fungsi RBAC

### ✅ Data Barang (4 files)
2. `barang/barang.php` - Hide buttons untuk staff
3. `barang/tambah.php` - requireAdmin()
4. `barang/edit.php` - requireAdmin()
5. `barang/hapus.php` - requireAdmin()

### ✅ Data Supplier (4 files)
6. `supplier/supplier.php` - Hide buttons untuk staff
7. `supplier/tambah.php` - requireAdmin()
8. `supplier/edit.php` - requireAdmin()
9. `supplier/hapus.php` - requireAdmin()

### ✅ Transaksi (2 files)
10. `transaksi/masuk.php` - Hide form untuk staff
11. `transaksi/keluar.php` - Hide form untuk staff

### ✅ Dashboard
12. `dashboard/index.php` - Notifikasi akses staff

**TOTAL: 12 files updated**

---

## 🧪 TESTING

### Test dengan ADMIN:
```
1. Login sebagai admin (username: admin, password: admin123)
2. Buka Data Barang
   ✅ Tombol "Tambah Barang" muncul
   ✅ Tombol "Edit" muncul
   ✅ Tombol "Hapus" muncul
3. Klik Tambah Barang
   ✅ Bisa akses halaman tambah
   ✅ Bisa submit form
4. Buka Transaksi Barang Masuk
   ✅ Form input muncul
   ✅ Bisa submit transaksi
```

### Test dengan STAFF:
```
1. Login sebagai staff (username: staff, password: admin123)
2. Dashboard
   ℹ️ Notifikasi "Mode Read-Only" muncul
3. Buka Data Barang
   ❌ Tombol "Tambah Barang" TIDAK muncul
   ❌ Tombol "Edit" TIDAK muncul
   ❌ Tombol "Hapus" TIDAK muncul
   👁️ Badge "View Only" muncul
4. Coba akses langsung: /barang/tambah.php
   ❌ Redirect ke dashboard dengan pesan "Akses Ditolak"
5. Buka Transaksi Barang Masuk
   ❌ Form input TIDAK muncul
   ⚠️ Muncul pesan: "Hanya Admin yang dapat melakukan transaksi"
   ✅ Tabel riwayat tetap muncul (read-only)
6. Buka Laporan
   ✅ Bisa lihat laporan
   ✅ Bisa export PDF/Excel
```

---

## 🚨 KEAMANAN BERLAPIS

### Layer 1: UI/Frontend
- Tombol disembunyikan untuk staff
- Form tidak ditampilkan
- Badge "View Only" sebagai indikator

### Layer 2: Backend/Server
- `requireAdmin()` di setiap halaman CRUD
- Permission check di form processing
- Redirect otomatis jika akses tidak sah

### Layer 3: Session
- Role tersimpan di session
- Session timeout (30 menit)
- Regenerate session ID saat login

---

## 📊 MATRIX AKSES

| Fitur | Admin | Staff |
|-------|-------|-------|
| **Dashboard** | ✅ Full | ✅ View Only |
| **Statistik** | ✅ Full | ✅ View Only |
| **Barang - Lihat** | ✅ | ✅ |
| **Barang - Tambah** | ✅ | ❌ |
| **Barang - Edit** | ✅ | ❌ |
| **Barang - Hapus** | ✅ | ❌ |
| **Supplier - Lihat** | ✅ | ✅ |
| **Supplier - Tambah** | ✅ | ❌ |
| **Supplier - Edit** | ✅ | ❌ |
| **Supplier - Hapus** | ✅ | ❌ |
| **Transaksi - Lihat** | ✅ | ✅ |
| **Transaksi - Input Masuk** | ✅ | ❌ |
| **Transaksi - Input Keluar** | ✅ | ❌ |
| **Laporan - Lihat** | ✅ | ✅ |
| **Laporan - Export** | ✅ | ✅ |

---

## 🔐 DEFAULT ACCOUNTS

### Admin Account:
```
Username: admin
Password: admin123
Role: admin
Access: Full (CRUD)
```

### Staff Account:
```
Username: staff
Password: admin123
Role: staff
Access: Read-Only
```

---

## ⚙️ CUSTOMIZATION

### Menambah Role Baru

1. **Update database** - Tambah role di tabel users
2. **Update session_check.php** - Tambah fungsi role baru
```php
function isManager() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'manager';
}
```
3. **Update permission functions** - Sesuaikan akses
```php
function canCreate() {
    return isAdmin() || isManager(); // Manager bisa create
}
```

### Mengubah Permission

Edit fungsi di `auth/session_check.php`:
```php
// Contoh: Staff bisa create tapi tidak bisa delete
function canCreate() {
    return isAdmin() || isStaff();
}

function canDelete() {
    return isAdmin(); // Hanya admin
}
```

---

## 📝 BEST PRACTICES

1. ✅ Selalu gunakan `requireAdmin()` di halaman CRUD
2. ✅ Gunakan `canCreate()`, `canEdit()`, `canDelete()` di UI
3. ✅ Validasi permission di backend (PHP)
4. ✅ Jangan hanya hide UI, protect backend juga
5. ✅ Log activity untuk audit trail
6. ✅ Test dengan kedua role (admin & staff)
7. ✅ Gunakan HTTPS di production

---

## 🎉 KESIMPULAN

Sistem sekarang memiliki RBAC yang ketat:
- ✅ Admin: Full access
- ✅ Staff: Read-only
- ✅ UI protected (tombol disembunyikan)
- ✅ Backend protected (redirect jika tidak sah)
- ✅ Session-based authorization
- ✅ Notifikasi yang jelas untuk user

**KEAMANAN TERJAMIN!** 🔒

---

