# 🚀 Quick Start - Implementasi Cookie Management

## Langkah Cepat Implementasi

### 1️⃣ Import Database (1 menit)
```bash
mysql -u root -p nama_database < database_remember_tokens.sql
```

### 2️⃣ Update Login Process (30 detik)
```bash
# Backup file lama
cp auth/login_process.php auth/login_process.php.old

# Gunakan file baru
cp auth/login_process_updated.php auth/login_process.php
```

### 3️⃣ Update Logout (30 detik)
```bash
cp auth/logout.php auth/logout.php.old
cp auth/logout_updated.php auth/logout.php
```

### 4️⃣ Update Session Check di Semua Halaman (2 menit)

Ganti di SEMUA file yang memerlukan login:
```php
// ❌ HAPUS INI:
require_once 'auth/session_check.php';
requireLogin();

// ✅ GANTI DENGAN INI:
require_once 'auth/session_manager.php';
SessionManager::requireLogin();
```

File yang perlu diupdate:
- ✅ dashboard/index.php
- ✅ barang/barang.php
- ✅ barang/tambah.php
- ✅ barang/edit.php
- ✅ supplier/supplier.php
- ✅ transaksi/masuk.php
- ✅ transaksi/keluar.php
- ✅ laporan/laporan.php
- ✅ Semua file yang butuh authentication

### 5️⃣ Tambahkan Session Monitor ke Layout (1 menit)

Di file `includes/header.php`, tambahkan:
```php
<!-- Session Monitor -->
<script src="/assets/js/session-monitor.js"></script>
<script>
// Initialize session monitor
const sessionMonitor = new SessionMonitor({
    checkInterval: 60000,  // Check setiap 60 detik
    warningTime: 300,      // Warning 5 menit sebelum timeout
    autoExtend: true       // Auto extend saat user aktif
});
</script>
```

### 6️⃣ Test Implementasi (2 menit)

**Test 1: Login Normal**
1. Buka browser
2. Login ke sistem
3. Check cookie di DevTools (F12 → Application → Cookies)
4. Harus ada: `PHPSESSID`, `session_validation`

**Test 2: Remember Me**
1. Login dengan checkbox "Remember Me"
2. Check cookie `remember_token` (expires 30 hari)
3. Close browser
4. Buka lagi → harus auto-login

**Test 3: Session Timeout**
1. Login
2. Tunggu 30 menit (atau ubah timeout ke 1 menit untuk test)
3. Refresh halaman → harus redirect ke login

**Test 4: Frontend Monitor**
1. Login
2. Buka Console (F12)
3. Ketik: `sessionMonitor.getSessionInfo()`
4. Harus tampil info session

---

## 📋 Checklist Instalasi

- [ ] Database table `remember_tokens` dibuat
- [ ] File `config/cookie_config.php` sudah ada
- [ ] File `auth/session_manager.php` sudah ada
- [ ] File `auth/session_api.php` sudah ada
- [ ] File `assets/js/session-monitor.js` sudah ada
- [ ] File `auth/login_process.php` sudah diupdate
- [ ] File `auth/logout.php` sudah diupdate
- [ ] Semua halaman protected sudah menggunakan `SessionManager::requireLogin()`
- [ ] Session monitor sudah ditambahkan ke header/layout
- [ ] Cookie config sudah disesuaikan (SECURE untuk production)
- [ ] Test login berhasil
- [ ] Test remember me berhasil
- [ ] Test session timeout berhasil
- [ ] Test frontend monitor berhasil

---

## ⚙️ Konfigurasi Penting

### Development (HTTP)
```php
// config/cookie_config.php
define('COOKIE_SECURE', false); // PENTING untuk development
```

### Production (HTTPS)
```php
// config/cookie_config.php
define('COOKIE_SECURE', true); // WAJIB untuk production
```

### Timeout Settings
```php
// config/cookie_config.php
define('SESSION_TIMEOUT', 1800);          // 30 menit inaktivitas
define('SESSION_ABSOLUTE_TIMEOUT', 86400); // 24 jam maksimal
define('COOKIE_REMEMBER_LIFETIME', 2592000); // 30 hari remember me
```

---

## 🎨 Tambahan UI (Opsional)

### Display Session Timer
```html
<!-- Di header -->
<div style="position: fixed; top: 10px; right: 10px; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <strong id="user-info">Loading...</strong><br>
    Sisa waktu: <span id="session-timer">--:--</span>
</div>

<script>
setInterval(async function() {
    const info = await sessionMonitor.getSessionInfo();
    if (info) {
        document.getElementById('user-info').textContent = info.nama_lengkap;
        const minutes = Math.floor(info.time_remaining / 60);
        const seconds = info.time_remaining % 60;
        document.getElementById('session-timer').textContent = 
            `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }
}, 1000);
</script>
```

---

## 🐛 Debug Mode

Jika ada masalah, aktifkan debug:

```php
// Di awal file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check session
var_dump($_SESSION);

// Check cookies
var_dump($_COOKIE);

// Check session status
echo 'Session Status: ' . session_status() . "\n";
echo 'Session ID: ' . session_id() . "\n";
```

```javascript
// Di console browser
console.log('Cookies:', document.cookie);

sessionMonitor.getSessionInfo().then(info => {
    console.log('Session Info:', info);
});
```

---

## 📞 Troubleshooting Cepat

**❌ Cookie tidak ter-set**
→ Pastikan tidak ada output sebelum `session_start()` atau `setcookie()`

**❌ Session hilang setelah redirect**
→ Check `session.save_path` writable

**❌ Remember me tidak bekerja**
→ Check tabel `remember_tokens` ada dan terisi

**❌ Frontend monitor error**
→ Check path API endpoint benar: `/auth/session_api.php`

**❌ CORS error**
→ Pastikan `credentials: 'include'` di fetch

---

## ✅ Done!

Setelah semua checklist selesai, sistem cookie management siap digunakan!

**Testing URL:**
- Login: `http://localhost/auth/login.php`
- Dashboard: `http://localhost/dashboard/index.php`
- Session API: `http://localhost/auth/session_api.php?action=check`

**Happy Coding! 🎉**
