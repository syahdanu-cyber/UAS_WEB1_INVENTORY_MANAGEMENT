# Dokumentasi Implementasi Cookie untuk Manajemen Sesi

## 📋 Daftar Isi
1. [Pengenalan](#pengenalan)
2. [Arsitektur](#arsitektur)
3. [File-file yang Ditambahkan](#file-file-yang-ditambahkan)
4. [Instalasi](#instalasi)
5. [Penggunaan Backend](#penggunaan-backend)
6. [Penggunaan Frontend](#penggunaan-frontend)
7. [Fitur-fitur](#fitur-fitur)
8. [Keamanan](#keamanan)
9. [Testing](#testing)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Pengenalan

Implementasi ini menambahkan sistem pengelolaan sesi berbasis cookie yang aman dengan fitur:
- ✅ Cookie yang aman dengan HttpOnly dan SameSite
- ✅ Remember Me dengan token terenkripsi
- ✅ Session timeout otomatis
- ✅ Pengecekan sesi di backend dan frontend
- ✅ Auto-extend session berdasarkan aktivitas user
- ✅ Session monitoring real-time
- ✅ CSRF protection
- ✅ Rate limiting untuk login

---

## 🏗️ Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                              │
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐│
│  │ Session Monitor│  │ Cookie Manager │  │ User Activity  ││
│  │   (JS Class)   │  │   (Helper)     │  │   Tracker      ││
│  └────────┬───────┘  └────────┬───────┘  └────────┬───────┘│
│           │                   │                    │         │
└───────────┼───────────────────┼────────────────────┼─────────┘
            │                   │                    │
            ▼                   ▼                    ▼
    ┌───────────────────────────────────────────────────────┐
    │                  SESSION API                           │
    │  /auth/session_api.php                                │
    │  - check    : Validasi sesi                           │
    │  - extend   : Perpanjang sesi                         │
    │  - info     : Info sesi                               │
    │  - heartbeat: Keep-alive                              │
    └──────────────────────┬────────────────────────────────┘
                           │
                           ▼
    ┌───────────────────────────────────────────────────────┐
    │              SESSION MANAGER (PHP)                     │
    │  /auth/session_manager.php                            │
    │  - Validasi cookie & session                          │
    │  - Timeout management                                 │
    │  - Remember me                                        │
    │  - CSRF protection                                    │
    └──────────────────────┬────────────────────────────────┘
                           │
                           ▼
    ┌───────────────────────────────────────────────────────┐
    │                   DATABASE                             │
    │  - users                                              │
    │  - remember_tokens                                    │
    │  - activity_log                                       │
    └───────────────────────────────────────────────────────┘
```

---

## 📁 File-file yang Ditambahkan

### Backend (PHP)
1. **config/cookie_config.php**
   - Konfigurasi cookie yang aman
   - Helper functions untuk cookie management

2. **auth/session_manager.php**
   - Class utama untuk manajemen sesi
   - Integrasi cookie dengan session
   - Remember me functionality
   - Session validation

3. **auth/session_api.php**
   - REST API untuk pengecekan sesi dari frontend
   - Endpoint: check, extend, info, heartbeat

4. **auth/login_process_updated.php**
   - Login process yang menggunakan SessionManager
   - Rate limiting
   - CSRF protection

5. **auth/logout_updated.php**
   - Logout dengan cleanup cookie yang proper

### Frontend (JavaScript)
1. **assets/js/session-monitor.js**
   - SessionMonitor class
   - CookieManager helper
   - SessionStorage helper
   - Auto session checking
   - Activity monitoring

### Database
1. **database_remember_tokens.sql**
   - Tabel untuk remember me tokens
   - Auto-cleanup expired tokens

### Contoh Implementasi
1. **auth/login_example.php**
   - Halaman login dengan CSRF protection
   - Remember me checkbox

2. **dashboard/dashboard_with_session_monitor.php**
   - Contoh penggunaan SessionMonitor
   - Real-time session timer
   - Session warning modal

---

## 🚀 Instalasi

### Langkah 1: Import Database
```sql
-- Import tabel remember_tokens
mysql -u username -p database_name < database_remember_tokens.sql
```

### Langkah 2: Update File yang Ada

Ganti file lama dengan file baru:
```bash
# Backup file lama
cp auth/login_process.php auth/login_process.php.backup
cp auth/logout.php auth/logout.php.backup

# Gunakan file baru
cp auth/login_process_updated.php auth/login_process.php
cp auth/logout_updated.php auth/logout.php
```

### Langkah 3: Update Include Session Manager

Di setiap halaman yang memerlukan authentication, ganti:
```php
// Dari:
require_once 'auth/session_check.php';

// Menjadi:
require_once 'auth/session_manager.php';
SessionManager::requireLogin();
```

### Langkah 4: Konfigurasi Cookie

Edit `config/cookie_config.php`:
```php
// Untuk production dengan HTTPS
define('COOKIE_SECURE', true);

// Untuk development dengan HTTP
define('COOKIE_SECURE', false);

// Atur domain jika diperlukan
define('COOKIE_DOMAIN', '.yourdomain.com');
```

---

## 🔧 Penggunaan Backend

### Login dengan Remember Me
```php
require_once 'auth/session_manager.php';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']);
    
    // Validasi user...
    
    if ($userValid) {
        SessionManager::setLogin($user, $rememberMe);
        header('Location: dashboard.php');
        exit();
    }
}
```

### Proteksi Halaman
```php
require_once 'auth/session_manager.php';

// Require login
SessionManager::requireLogin();

// Require admin
SessionManager::requireAdmin();

// Check role
if (SessionManager::isAdmin()) {
    // Admin only code
}
```

### Validasi Session
```php
// Cek apakah user login
if (SessionManager::isLoggedIn()) {
    // User logged in
}

// Validasi lengkap dengan cookie check
if (SessionManager::validate()) {
    // Session valid
}
```

### CSRF Protection
```php
// Generate token untuk form
$token = SessionManager::generateCSRFToken();
?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $token ?>">
    <!-- form fields -->
</form>

<?php
// Validasi token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SessionManager::validateCSRFToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    // Process form
}
```

### Logout
```php
require_once 'auth/session_manager.php';

SessionManager::destroy();
header('Location: login.php');
exit();
```

---

## 💻 Penggunaan Frontend

### Inisialisasi Session Monitor
```html
<!-- Include library -->
<script src="/assets/js/session-monitor.js"></script>

<script>
// Initialize dengan konfigurasi default
const monitor = new SessionMonitor();

// Atau dengan custom options
const monitor = new SessionMonitor({
    checkInterval: 30000,        // Check setiap 30 detik
    warningTime: 300,            // Warning 5 menit sebelum timeout
    apiEndpoint: '/auth/session_api.php',
    autoExtend: true,            // Auto extend saat user aktif
    
    // Custom handlers
    onSessionExpired: function(data) {
        alert('Sesi berakhir!');
        window.location.href = data.redirect;
    },
    
    onSessionWarning: function(timeRemaining) {
        const minutes = Math.floor(timeRemaining / 60);
        console.log(`Sesi akan berakhir dalam ${minutes} menit`);
    }
});
</script>
```

### Check Session Manual
```javascript
// Check session validity
const isValid = await monitor.checkSession();

// Get session info
const info = await monitor.getSessionInfo();
console.log(info);
// Output: {
//   user_id: 1,
//   username: 'admin',
//   nama_lengkap: 'Administrator',
//   role: 'admin',
//   time_remaining: 1800
// }
```

### Extend Session Manual
```javascript
// Perpanjang session
const extended = await monitor.extendSession();
if (extended) {
    console.log('Session extended');
}
```

### Send Heartbeat
```javascript
// Kirim heartbeat untuk keep-alive
const alive = await monitor.sendHeartbeat();
```

### Cookie Management
```javascript
// Get cookie
const value = CookieManager.get('cookie_name');

// Set cookie
CookieManager.set('cookie_name', 'value', 7, {
    secure: true,
    httponly: true,
    samesite: 'Lax'
});

// Delete cookie
CookieManager.delete('cookie_name');

// Check if exists
if (CookieManager.exists('cookie_name')) {
    // Cookie exists
}
```

### Session Storage Helper
```javascript
// Set data
SessionStorage.set('key', { data: 'value' });

// Get data
const data = SessionStorage.get('key');

// Remove data
SessionStorage.remove('key');

// Clear all
SessionStorage.clear();
```

---

## ✨ Fitur-fitur

### 1. Cookie yang Aman
- **HttpOnly**: Cookie tidak dapat diakses via JavaScript
- **Secure**: Cookie hanya dikirim via HTTPS (production)
- **SameSite**: Perlindungan terhadap CSRF attack
- **Path & Domain**: Scope cookie yang tepat

### 2. Remember Me
- Token terenkripsi SHA-256
- Selector untuk identifikasi
- Expiry 30 hari (configurable)
- Auto-cleanup token expired
- One token per user

### 3. Session Timeout
- **Inactivity Timeout**: 30 menit tanpa aktivitas
- **Absolute Timeout**: 24 jam maksimal
- Auto logout saat timeout
- Warning sebelum timeout

### 4. Session Monitoring
- Real-time session check
- Auto-extend berdasarkan user activity
- Session timer display
- Warning modal
- Heartbeat system

### 5. Security Features
- CSRF token protection
- Session regeneration
- IP validation (optional)
- User agent validation
- Rate limiting login
- Activity logging

### 6. Frontend Integration
- Session check via AJAX
- Auto logout redirect
- Activity tracking
- Configurable warning
- Cookie helpers

---

## 🔒 Keamanan

### Cookie Security
```php
// config/cookie_config.php mengatur:
- HttpOnly: true     // Mencegah XSS
- Secure: true       // HTTPS only (production)
- SameSite: 'Lax'   // Mencegah CSRF
```

### Session Security
```php
// Regenerate session ID secara berkala
session_regenerate_id(true);

// Strict mode
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
```

### Remember Me Security
- Token di-hash dengan SHA-256
- Selector terpisah untuk identifikasi
- Stored hash comparison dengan hash_equals()
- Auto delete expired tokens
- One token per user (no multiple devices by default)

### CSRF Protection
```php
// Generate token
$token = SessionManager::generateCSRFToken();

// Validate token
if (!SessionManager::validateCSRFToken($_POST['csrf_token'])) {
    die('Invalid token');
}
```

### Rate Limiting
```php
// login_process_updated.php
// Max 5 attempts dalam 15 menit
if ($_SESSION['login_attempts'] >= 5) {
    // Block login
}
```

---

## 🧪 Testing

### Test Backend Session

**Test 1: Login Normal**
```php
// test_login.php
require_once 'auth/session_manager.php';

$user = [
    'id' => 1,
    'username' => 'testuser',
    'nama_lengkap' => 'Test User',
    'role' => 'admin',
    'email' => 'test@example.com'
];

SessionManager::setLogin($user, false);

if (SessionManager::isLoggedIn()) {
    echo "✅ Login successful\n";
    print_r(SessionManager::getSessionInfo());
} else {
    echo "❌ Login failed\n";
}
```

**Test 2: Remember Me**
```php
// test_remember_me.php
SessionManager::setLogin($user, true);

// Check cookie
if (isset($_COOKIE['remember_token'])) {
    echo "✅ Remember token set\n";
} else {
    echo "❌ Remember token not set\n";
}
```

**Test 3: Session Validation**
```php
// test_validation.php
if (SessionManager::validate()) {
    echo "✅ Session valid\n";
} else {
    echo "❌ Session invalid\n";
}
```

### Test Frontend Session Monitor

**Test via Browser Console**
```javascript
// Test 1: Check session
monitor.checkSession().then(result => {
    console.log('Session check:', result);
});

// Test 2: Get session info
monitor.getSessionInfo().then(info => {
    console.log('Session info:', info);
});

// Test 3: Extend session
monitor.extendSession().then(result => {
    console.log('Session extended:', result);
});

// Test 4: Heartbeat
monitor.sendHeartbeat().then(alive => {
    console.log('Heartbeat:', alive);
});
```

### Test Cookie Management
```javascript
// Test cookie helpers
CookieManager.set('test_cookie', 'test_value', 1);
console.log('Cookie value:', CookieManager.get('test_cookie'));
console.log('Cookie exists:', CookieManager.exists('test_cookie'));
CookieManager.delete('test_cookie');
console.log('After delete:', CookieManager.exists('test_cookie'));
```

### Test dengan cURL

**Check Session API**
```bash
# Login first
curl -X POST http://localhost/auth/login_process_updated.php \
  -d "username=admin&password=admin123" \
  -c cookies.txt

# Check session
curl -X GET http://localhost/auth/session_api.php?action=check \
  -b cookies.txt

# Extend session
curl -X POST http://localhost/auth/session_api.php \
  -d "action=extend" \
  -b cookies.txt

# Get session info
curl -X GET http://localhost/auth/session_api.php?action=info \
  -b cookies.txt
```

---

## 🔧 Troubleshooting

### Problem 1: Session tidak tersimpan
**Gejala**: User logout otomatis setelah refresh
**Solusi**:
```php
// Check session path writable
echo session_save_path(); // Pastikan directory ini writable

// Check di php.ini:
session.save_path = "/tmp"
session.gc_probability = 1
session.gc_divisor = 100
```

### Problem 2: Cookie tidak ter-set
**Gejala**: Cookie tidak muncul di browser
**Solusi**:
```php
// Pastikan tidak ada output sebelum setcookie()
// Pastikan COOKIE_SECURE sesuai dengan protocol
define('COOKIE_SECURE', false); // Untuk HTTP (development)
define('COOKIE_SECURE', true);  // Untuk HTTPS (production)
```

### Problem 3: CORS error di session API
**Gejala**: API call gagal dengan CORS error
**Solusi**:
```php
// Tambahkan di session_api.php
header('Access-Control-Allow-Origin: https://yourdomain.com');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST');
```

### Problem 4: Remember Me tidak bekerja
**Gejala**: User tidak auto-login setelah close browser
**Solusi**:
```sql
-- Check tabel remember_tokens
SELECT * FROM remember_tokens WHERE user_id = 1;

-- Check expired
SELECT * FROM remember_tokens WHERE expires_at < NOW();

-- Delete expired manually
DELETE FROM remember_tokens WHERE expires_at < NOW();
```

### Problem 5: Session timeout terlalu cepat
**Gejala**: User logout sebelum 30 menit
**Solusi**:
```php
// Tingkatkan timeout di cookie_config.php
define('SESSION_TIMEOUT', 3600); // 1 jam

// Atau di php.ini:
session.gc_maxlifetime = 3600
```

### Problem 6: Frontend monitor error
**Gejala**: Console error saat check session
**Solusi**:
```javascript
// Check API endpoint path
const monitor = new SessionMonitor({
    apiEndpoint: '/auth/session_api.php' // Pastikan path benar
});

// Check credentials
fetch('/auth/session_api.php', {
    credentials: 'include' // PENTING untuk cookie
});
```

---

## 📊 Activity Log

Semua aktivitas session dicatat di tabel `activity_log`:

```sql
SELECT * FROM activity_log 
WHERE user_id = 1 
ORDER BY created_at DESC 
LIMIT 10;
```

Event yang dicatat:
- `login` - User berhasil login
- `logout` - User logout
- `session_expired` - Session timeout
- `invalid_token` - Token tidak valid
- `csrf_failure` - CSRF validation gagal

---

## 🎯 Best Practices

1. **Selalu gunakan HTTPS di production**
2. **Set COOKIE_SECURE = true di production**
3. **Regenerate session ID secara berkala**
4. **Implementasi rate limiting untuk sensitive operations**
5. **Log semua security events**
6. **Cleanup expired tokens secara otomatis**
7. **Validate session di setiap request**
8. **Gunakan CSRF token untuk form submissions**
9. **Monitor session activity**
10. **Regular security audit**

---

## 📝 Changelog

### Version 1.0.0 (2026-01-11)
- ✅ Initial implementation
- ✅ Secure cookie configuration
- ✅ Session manager with validation
- ✅ Remember me functionality
- ✅ Frontend session monitor
- ✅ Session API endpoints
- ✅ CSRF protection
- ✅ Rate limiting
- ✅ Activity logging
- ✅ Auto-cleanup expired tokens

---

## 📞 Support

Jika ada pertanyaan atau masalah:
1. Check troubleshooting section
2. Review activity log
3. Enable debug mode untuk detail error
4. Contact developer

---

## 📄 License

Proprietary - Internal Use Only

---

**Dibuat dengan ❤️ untuk Sistem Manajemen Inventory**
