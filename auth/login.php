<?php
// auth/login.php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit();
}

$error_message = '';
if (isset($_GET['timeout'])) {
    $error_message = 'Sesi Anda telah berakhir. Silakan login kembali.';
} elseif (isset($_GET['error'])) {
    $error_message = 'Username atau password salah!';
} elseif (isset($_GET['logout'])) {
    $error_message = 'Anda telah berhasil logout.';
} elseif (isset($_GET['success']) && $_GET['success'] == 'registration') {
    $error_message = 'Registrasi berhasil! Silakan login dengan akun baru Anda.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-footer">
                <div class="text-center">
                    <small class="text-muted">
                        Belum punya akun? <a href="register.php" class="text-primary">Daftar di sini</a>
                    </small>
                </div>
            </div>

            <form id="loginForm" action="login_process.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person-fill"></i> Username
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Masukkan username" required autofocus>
                    <div class="invalid-feedback">Username wajib diisi!</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill"></i> Password
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                        <div class="invalid-feedback">Password wajib diisi!</div>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                    <label class="form-check-label" for="remember_me">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>

            <div class="login-footer">
                <hr class="my-3">
                <div class="text-center">
                    <small class="text-muted">
                        Belum punya akun? <a href="register.php" class="text-primary">Daftar di sini</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/validation.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const form = this;
            
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });

        // Auto dismiss alert after 5 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    </script>
</body>
</html>
