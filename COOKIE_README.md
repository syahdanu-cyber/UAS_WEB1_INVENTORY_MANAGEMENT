# 🍪 Implementasi Cookie untuk Manajemen Sesi

Sistem manajemen sesi berbasis cookie yang aman dengan pengecekan di backend (PHP) dan frontend (JavaScript).

## 📦 File-file yang Ditambahkan

### Backend (PHP)
```
config/
  └── cookie_config.php           # Konfigurasi cookie yang aman

auth/
  ├── session_manager.php         # Class manajemen sesi utama
  ├── session_api.php             # REST API untuk pengecekan sesi
  ├── login_process_updated.php  # Login dengan cookie integration
  └── logout_updated.php          # Logout dengan cleanup cookie

database_remember_tokens.sql      # SQL untuk tabel remember tokens
```

### Frontend (JavaScript)
```
assets/js/
  └── session-monitor.js          # Library monitoring sesi dari frontend
```

### Dokumentasi & Contoh
```
COOKIE_IMPLEMENTATION_GUIDE.md    # Dokumentasi lengkap
COOKIE_QUICK_START.md             # Panduan cepat instalasi

auth/
  └── login_example.php           # Contoh halaman login

dashboard/
  └── dashboard_with_session_monitor.php  # Contoh penggunaan monitor
```

## ✨ Fitur Utama

### 🔒 Keamanan
- ✅ Cookie HttpOnly (mencegah XSS)
- ✅ Cookie Secure untuk HTTPS
- ✅ SameSite untuk CSRF protection
- ✅ Session token validation
- ✅ CSRF token protection
- ✅ Rate limiting login
- ✅ Activity logging

### 📱 Fitur Sesi
- ✅ Remember Me (30 hari)
- ✅ Session timeout (30 menit inaktivitas)
- ✅ Auto-extend saat user aktif
- ✅ Real-time session monitoring
- ✅ Session warning sebelum timeout
- ✅ Multi-device support (optional)

### 🎯 Backend Features
- ✅ SessionManager class yang powerful
- ✅ REST API untuk pengecekan sesi
- ✅ Cookie management helpers
- ✅ Remember token dengan enkripsi
- ✅ Auto-cleanup expired tokens

### 💻 Frontend Features
- ✅ SessionMonitor class
- ✅ Auto session check
- ✅ Activity tracking
- ✅ Cookie helpers
- ✅ Session storage helpers
- ✅ Customizable callbacks

## 🚀 Quick Start

### 1. Import Database
```bash
mysql -u root -p database_name < database_remember_tokens.sql
```

### 2. Update File Backend
```php
// Di semua halaman yang butuh authentication, ganti:
require_once 'auth/session_check.php';
requireLogin();

// Menjadi:
require_once 'auth/session_manager.php';
SessionManager::requireLogin();
```

### 3. Tambahkan Monitor di Frontend
```html
<!-- Di layout/header -->
<script src="/assets/js/session-monitor.js"></script>
<script>
const monitor = new SessionMonitor({
    checkInterval: 60000,
    warningTime: 300,
    autoExtend: true
});
</script>
```

### 4. Konfigurasi
```php
// config/cookie_config.php
define('COOKIE_SECURE', false); // Development (HTTP)
// atau
define('COOKIE_SECURE', true);  // Production (HTTPS)
```

## 📖 Dokumentasi

- **Lengkap**: Baca `COOKIE_IMPLEMENTATION_GUIDE.md`
- **Quick Start**: Baca `COOKIE_QUICK_START.md`

## 🔧 Penggunaan

### Backend (PHP)

**Login dengan Remember Me:**
```php
require_once 'auth/session_manager.php';

$user = getUser($username); // Your user fetch logic
SessionManager::setLogin($user, true); // true = remember me
```

**Proteksi Halaman:**
```php
require_once 'auth/session_manager.php';

SessionManager::requireLogin();  // Require any logged in user
SessionManager::requireAdmin();  // Require admin only
```

**Validasi Session:**
```php
if (SessionManager::validate()) {
    // Session valid
}

if (SessionManager::isAdmin()) {
    // Admin only code
}
```

**Logout:**
```php
SessionManager::destroy();
```

### Frontend (JavaScript)

**Initialize Monitor:**
```javascript
const monitor = new SessionMonitor({
    checkInterval: 60000,
    warningTime: 300,
    autoExtend: true,
    onSessionExpired: (data) => {
        window.location.href = data.redirect;
    }
});
```

**Manual Check:**
```javascript
await monitor.checkSession();
const info = await monitor.getSessionInfo();
await monitor.extendSession();
await monitor.sendHeartbeat();
```

**Cookie Management:**
```javascript
CookieManager.set('name', 'value', 7);
const value = CookieManager.get('name');
CookieManager.delete('name');
```

## 🔍 API Endpoints

```
GET  /auth/session_api.php?action=check      - Validasi sesi
POST /auth/session_api.php?action=extend     - Perpanjang sesi
GET  /auth/session_api.php?action=info       - Info sesi
POST /auth/session_api.php?action=heartbeat  - Keep-alive
```

## 📊 Database Schema

```sql
remember_tokens
├── id (PK)
├── user_id (FK to users)
├── selector (unique identifier)
├── token (hashed token)
├── expires_at
└── created_at

activity_log (updated)
├── ... existing fields
└── user_agent (new)
```

## ⚙️ Configuration

```php
// Session Timeouts
SESSION_TIMEOUT = 1800           // 30 menit
SESSION_ABSOLUTE_TIMEOUT = 86400 // 24 jam

// Cookie Lifetimes
COOKIE_SESSION_LIFETIME = 0      // Browser session
COOKIE_REMEMBER_LIFETIME = 2592000 // 30 hari

// Security
COOKIE_HTTPONLY = true
COOKIE_SECURE = false (dev) / true (prod)
COOKIE_SAMESITE = 'Lax'
```

## 🧪 Testing

**Backend:**
```php
// Check if implementation works
require_once 'auth/session_manager.php';
var_dump(SessionManager::isLoggedIn());
var_dump(SessionManager::getSessionInfo());
```

**Frontend:**
```javascript
// In browser console
sessionMonitor.getSessionInfo().then(console.log);
```

**cURL:**
```bash
# Login
curl -X POST http://localhost/auth/login_process.php \
  -d "username=admin&password=admin123" \
  -c cookies.txt

# Check session
curl -X GET http://localhost/auth/session_api.php?action=check \
  -b cookies.txt
```

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Cookie tidak ter-set | Pastikan tidak ada output sebelum setcookie() |
| Session hilang | Check session.save_path writable |
| Remember me gagal | Check tabel remember_tokens exists |
| CORS error | Set Access-Control-Allow-Credentials: true |
| Monitor error | Check API endpoint path benar |

## 📈 Best Practices

1. ✅ Selalu gunakan HTTPS di production
2. ✅ Set COOKIE_SECURE = true di production
3. ✅ Regenerate session ID berkala
4. ✅ Implement rate limiting
5. ✅ Log security events
6. ✅ Regular cleanup expired tokens
7. ✅ Monitor session activity
8. ✅ Use CSRF tokens

## 🔐 Security Checklist

- [x] HttpOnly cookies
- [x] Secure cookies (HTTPS)
- [x] SameSite attribute
- [x] Token encryption (SHA-256)
- [x] CSRF protection
- [x] Session regeneration
- [x] Rate limiting
- [x] Activity logging
- [x] Timeout management
- [x] XSS prevention

## 📝 Migration Notes

**Dari session_check.php ke session_manager.php:**
```php
// Old
require_once 'auth/session_check.php';
requireLogin();
isAdmin();

// New
require_once 'auth/session_manager.php';
SessionManager::requireLogin();
SessionManager::isAdmin();
```

## 🎯 Next Steps

1. Import database schema
2. Update semua file PHP yang menggunakan session
3. Tambahkan session monitor ke frontend
4. Test semua fitur
5. Deploy ke production dengan COOKIE_SECURE=true

## 📞 Support

- Baca dokumentasi lengkap di `COOKIE_IMPLEMENTATION_GUIDE.md`
- Check troubleshooting section
- Review activity logs untuk debugging

---

**Version**: 1.0.0  
**Last Updated**: 2026-01-11  
**Author**: System Administrator  
**License**: Proprietary
