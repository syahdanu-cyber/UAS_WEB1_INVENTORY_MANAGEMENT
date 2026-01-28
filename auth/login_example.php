<?php
// auth/login_example.php
require_once __DIR__ . '/session_manager.php';

// Jika sudah login, redirect ke dashboard
if (SessionManager::isLoggedIn()) {
    header('Location: ../dashboard/index.php');
    exit();
}

// Generate CSRF token
$csrfToken = SessionManager::generateCSRFToken();

// Handle error messages
$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty_fields':
            $errorMessage = 'Username dan password harus diisi!';
            break;
        case 'invalid_credentials':
            $attempts = $_GET['attempts'] ?? 0;
            $errorMessage = "Username atau password salah! (Percobaan ke-$attempts dari 5)";
            break;
        case 'too_many_attempts':
            $wait = $_GET['wait'] ?? 15;
            $errorMessage = "Terlalu banyak percobaan login. Tunggu $wait menit.";
            break;
        case 'invalid_token':
            $errorMessage = 'Token CSRF tidak valid!';
            break;
        case 'system_error':
            $errorMessage = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            break;
    }
}

$successMessage = '';
if (isset($_GET['logged_out'])) {
    $successMessage = 'Anda telah berhasil logout.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventory</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .remember-me input {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login Sistem Inventory</h2>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            
            <form action="login_process_updated.php" method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                
                <div class="form-group">
                    <label for="username">Username atau Email</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        required 
                        autofocus
                        autocomplete="username"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <div class="remember-me">
                    <input 
                        type="checkbox" 
                        id="remember_me" 
                        name="remember_me"
                        value="1"
                    >
                    <label for="remember_me">Ingat saya (30 hari)</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Username dan password harus diisi!');
                return false;
            }
            
            // Minimal validation
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
        
        // Clear error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
