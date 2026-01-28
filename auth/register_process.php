<?php
// auth/register_process.php
session_start();

require_once __DIR__ . '/../config/database.php';

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

// Ambil dan sanitize input
$username = trim($_POST['username'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? 'staff';
$agree = isset($_POST['agree']);

// Validasi input
$errors = [];

// Validasi username
if (empty($username)) {
    $errors[] = 'username_required';
} elseif (strlen($username) < 4) {
    $errors[] = 'username_too_short';
} elseif (strlen($username) > 50) {
    $errors[] = 'username_too_long';
} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = 'username_invalid_chars';
}

// Validasi nama lengkap
if (empty($nama_lengkap)) {
    $errors[] = 'nama_required';
} elseif (strlen($nama_lengkap) > 100) {
    $errors[] = 'nama_too_long';
}

// Validasi email
if (empty($email)) {
    $errors[] = 'email_required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email_invalid';
} elseif (strlen($email) > 100) {
    $errors[] = 'email_too_long';
}

// Validasi password
if (empty($password)) {
    $errors[] = 'password_required';
} elseif (strlen($password) < 6) {
    $errors[] = 'password_too_short';
}

// Validasi konfirmasi password
if ($password !== $confirm_password) {
    $errors[] = 'password_mismatch';
}

// Validasi role
if (!in_array($role, ['admin', 'staff'])) {
    $errors[] = 'invalid_role';
}

// Validasi agreement
if (!$agree) {
    $errors[] = 'agreement_required';
}

// Jika ada error validasi, redirect kembali
if (!empty($errors)) {
    $error_param = $errors[0];
    if ($error_param == 'password_mismatch') {
        header('Location: register.php?error=password_mismatch');
    } else {
        header('Location: register.php?error=invalid_input');
    }
    exit();
}

try {
    $db = getDB();
    
    // Cek apakah username sudah digunakan
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        // Username sudah ada
        header('Location: register.php?error=username_exists&nama_lengkap=' . urlencode($nama_lengkap) . '&email=' . urlencode($email));
        exit();
    }
    
    // Cek apakah email sudah digunakan
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        // Email sudah ada
        header('Location: register.php?error=email_exists&username=' . urlencode($username) . '&nama_lengkap=' . urlencode($nama_lengkap));
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    
    // Insert user baru ke database
    $stmt = $db->prepare("
        INSERT INTO users (username, password, nama_lengkap, email, role, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    if ($stmt->execute([$username, $hashed_password, $nama_lengkap, $email, $role])) {
        // Registrasi berhasil
        
        // Optional: Log activity
        try {
            $user_id = $db->lastInsertId();
            $log_stmt = $db->prepare("
                INSERT INTO activity_log (user_id, action, details, ip_address, created_at) 
                VALUES (?, 'register', 'User baru mendaftar', ?, NOW())
            ");
            $log_stmt->execute([$user_id, $_SERVER['REMOTE_ADDR']]);
        } catch (Exception $e) {
            // Ignore log error
        }
        
        // Redirect ke login dengan pesan sukses
        header('Location: login.php?success=registration');
        exit();
    } else {
        // Registrasi gagal
        header('Location: register.php?error=registration_failed');
        exit();
    }
    
} catch (PDOException $e) {
    // Log error (dalam production, jangan tampilkan error detail ke user)
    error_log("Registration error: " . $e->getMessage());
    
    // Redirect dengan error generic
    header('Location: register.php?error=database_error');
    exit();
}
