<?php
// auth/session_check.php

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timeout session (30 menit)
define('SESSION_TIMEOUT', 1800);

// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Fungsi untuk mengecek session timeout
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];
        
        if ($elapsed > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

// Fungsi untuk require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../auth/login.php');
        exit();
    }
    
    if (!checkSessionTimeout()) {
        header('Location: ../auth/login.php?timeout=1');
        exit();
    }
}

// Fungsi untuk mengecek role user
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Fungsi untuk mengecek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Fungsi untuk mengecek apakah user adalah staff
function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}

// Fungsi untuk require admin access (redirect jika bukan admin)
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../dashboard/index.php?error=access_denied');
        exit();
    }
}

// Fungsi untuk cek permission (return boolean, tidak redirect)
function canCreate() {
    return isAdmin();
}

function canEdit() {
    return isAdmin();
}

function canDelete() {
    return isAdmin();
}

function canView() {
    return isLoggedIn(); // Semua user yang login bisa view
}

// Fungsi untuk require role tertentu
function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        header('Location: ../dashboard/index.php?access_denied=1');
        exit();
    }
}

// Fungsi untuk set session login
function setLoginSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['last_activity'] = time();
    $_SESSION['login_time'] = time();
    
    // Set cookie "remember me" jika diminta
    if (isset($_POST['remember_me'])) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 hari
        
        // Simpan token di database (opsional untuk keamanan lebih)
        // Di production, sebaiknya token disimpan di database
    }
}

// Fungsi untuk logout
function logout() {
    // Hapus semua session
    $_SESSION = array();
    
    // Hapus cookie session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Hapus cookie remember me
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Destroy session
    session_destroy();
}

// Fungsi untuk generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi untuk validasi CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fungsi untuk logging aktivitas (opsional)
function logActivity($action, $details = '') {
    if (isLoggedIn()) {
        require_once __DIR__ . '/../config/database.php';
        $db = getDB();
        
        try {
            $stmt = $db->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR']
            ]);
        } catch (PDOException $e) {
            // Log error tapi jangan stop aplikasi
            error_log("Log activity error: " . $e->getMessage());
        }
    }
}

// Auto-check session pada setiap page load
if (isLoggedIn()) {
    checkSessionTimeout();
}
