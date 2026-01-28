<?php
// auth/login_process_updated.php

/**
 * Login Process dengan Cookie Integration
 * File ini menggantikan login_process.php lama
 */

require_once __DIR__ . '/session_manager.php';

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Validasi CSRF token jika ada
if (isset($_POST['csrf_token'])) {
    if (!SessionManager::validateCSRFToken($_POST['csrf_token'])) {
        header('Location: login.php?error=invalid_token');
        exit();
    }
}

// Ambil dan sanitize input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']);

// Validasi input
if (empty($username) || empty($password)) {
    header('Location: login.php?error=empty_fields');
    exit();
}

// Rate limiting sederhana (opsional)
session_start();
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt'] = time();
}

// Reset counter jika sudah lebih dari 15 menit
if (time() - $_SESSION['last_attempt'] > 900) {
    $_SESSION['login_attempts'] = 0;
}

// Cek apakah terlalu banyak percobaan
if ($_SESSION['login_attempts'] >= 5) {
    $timeLeft = 900 - (time() - $_SESSION['last_attempt']);
    header('Location: login.php?error=too_many_attempts&wait=' . ceil($timeLeft / 60));
    exit();
}

try {
    $db = getDB();
    
    // Query user berdasarkan username atau email
    $stmt = $db->prepare("
        SELECT * FROM users 
        WHERE username = ? OR email = ? 
        LIMIT 1
    ");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        // Reset login attempts
        $_SESSION['login_attempts'] = 0;
        
        // Set login session dengan SessionManager
        SessionManager::setLogin($user, $rememberMe);
        
        // Update last login di database
        $updateStmt = $db->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // Redirect ke dashboard
        header('Location: ../dashboard/index.php');
        exit();
        
    } else {
        // Login gagal - increment attempts
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt'] = time();
        
        header('Location: login.php?error=invalid_credentials&attempts=' . $_SESSION['login_attempts']);
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: login.php?error=system_error');
    exit();
}
