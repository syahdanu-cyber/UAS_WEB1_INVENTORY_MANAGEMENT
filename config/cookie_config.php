<?php
// config/cookie_config.php

/**
 * Konfigurasi Cookie untuk Manajemen Sesi
 * File ini berisi pengaturan cookie yang aman dan terstandarisasi
 */

// Konfigurasi Cookie
define('COOKIE_DOMAIN', ''); // Kosongkan untuk default domain
define('COOKIE_PATH', '/');
define('COOKIE_SECURE', false); // Set true jika menggunakan HTTPS
define('COOKIE_HTTPONLY', true); // Mencegah akses JavaScript ke cookie
define('COOKIE_SAMESITE', 'Lax'); // Lax, Strict, atau None

// Nama Cookie
define('COOKIE_SESSION_NAME', 'PHPSESSID');
define('COOKIE_REMEMBER_TOKEN', 'remember_token');
define('COOKIE_USER_PREFERENCE', 'user_pref');

// Durasi Cookie (dalam detik)
define('COOKIE_SESSION_LIFETIME', 0); // 0 = sampai browser ditutup
define('COOKIE_REMEMBER_LIFETIME', 2592000); // 30 hari
define('COOKIE_PREFERENCE_LIFETIME', 31536000); // 1 tahun

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 menit inaktivitas
define('SESSION_ABSOLUTE_TIMEOUT', 86400); // 24 jam maksimal

/**
 * Konfigurasi session dengan cookie yang aman
 */
function configureSecureSession() {
    // Jangan start session jika sudah aktif
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => COOKIE_SESSION_LIFETIME,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ]);
    
    // Set session name
    session_name(COOKIE_SESSION_NAME);
    
    // Konfigurasi tambahan untuk keamanan
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    
    // Start session
    session_start();
    
    // Regenerate session ID secara berkala untuk keamanan
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Regenerate setiap 30 menit
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

/**
 * Set cookie yang aman
 */
function setSecureCookie($name, $value, $expire = 0, $options = []) {
    $default_options = [
        'expires' => $expire,
        'path' => COOKIE_PATH,
        'domain' => COOKIE_DOMAIN,
        'secure' => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE
    ];
    
    $options = array_merge($default_options, $options);
    
    return setcookie($name, $value, $options);
}

/**
 * Hapus cookie
 */
function deleteSecureCookie($name) {
    return setSecureCookie($name, '', time() - 3600);
}

/**
 * Ambil nilai cookie
 */
function getSecureCookie($name, $default = null) {
    return $_COOKIE[$name] ?? $default;
}

/**
 * Cek apakah cookie ada
 */
function hasSecureCookie($name) {
    return isset($_COOKIE[$name]);
}
