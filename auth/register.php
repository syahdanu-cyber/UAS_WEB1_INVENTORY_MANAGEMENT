<?php
// auth/register.php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit();
}

$error_message = '';
$success_message = '';

if (isset($_GET['success'])) {
    $success_message = 'Registrasi berhasil! Silakan login dengan akun Anda.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Manajemen Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card" style="max-width: 500px;">
            <div class="login-header">
                <img src="../assets/img/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
                <h2>Registrasi Akun Baru</h2>
                <p>Daftar untuk menggunakan sistem inventory</p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php 
                    if ($_GET['error'] == 'username_exists') {
                        echo 'Username sudah digunakan!';
                    } elseif ($_GET['error'] == 'email_exists') {
                        echo 'Email sudah digunakan!';
                    } elseif ($_GET['error'] == 'password_mismatch') {
                        echo 'Password dan Konfirmasi Password tidak sama!';
                    } elseif ($_GET['error'] == 'invalid_input') {
                        echo 'Semua field wajib diisi dengan benar!';
                    } else {
                        echo 'Terjadi kesalahan! Silakan coba lagi.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="registerForm" action="register_process.php" method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-fill"></i> Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Masukkan username" required minlength="4" maxlength="50"
                               value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>">
                        <div class="invalid-feedback">Username wajib diisi (minimal 4 karakter)</div>
                        <small class="text-muted">Username akan digunakan untuk login</small>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="nama_lengkap" class="form-label">
                            <i class="bi bi-person-badge-fill"></i> Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                               placeholder="Masukkan nama lengkap" required maxlength="100"
                               value="<?php echo isset($_GET['nama_lengkap']) ? htmlspecialchars($_GET['nama_lengkap']) : ''; ?>">
                        <div class="invalid-feedback">Nama lengkap wajib diisi</div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill"></i> Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="contoh@email.com" required maxlength="100"
                               value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                        <div class="invalid-feedback">Email wajib diisi dengan format yang benar</div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill"></i> Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Password wajib diisi (minimal 6 karakter)</div>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="bi bi-lock-fill"></i> Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Ulangi password" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye-fill" id="toggleConfirmIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Konfirmasi password harus sama dengan password</div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="role" class="form-label">
                            <i class="bi bi-shield-fill"></i> Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="staff" selected>Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback">Role wajib dipilih</div>
                        <small class="text-muted">Staff: akses terbatas | Admin: akses penuh</small>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="agree" name="agree" required>
                    <label class="form-check-label" for="agree">
                        Saya setuju dengan syarat dan ketentuan yang berlaku <span class="text-danger">*</span>
                    </label>
                    <div class="invalid-feedback">Anda harus menyetujui syarat dan ketentuan</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-plus-fill"></i> Daftar Sekarang
                </button>
                
                <div class="text-center">
                    <small class="text-muted">
                        Sudah punya akun? <a href="login.php" class="text-primary">Login di sini</a>
                    </small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Toggle confirm password visibility
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPassword = document.getElementById('confirm_password');
            const icon = document.getElementById('toggleConfirmIcon');
            
            if (confirmPassword.type === 'password') {
                confirmPassword.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                confirmPassword.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        });

        // Form validation
        (function() {
            'use strict';
            const form = document.getElementById('registerForm');
            
            form.addEventListener('submit', function(event) {
                // Check if passwords match
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    event.preventDefault();
                    event.stopPropagation();
                    document.getElementById('confirm_password').setCustomValidity('Password tidak sama');
                } else {
                    document.getElementById('confirm_password').setCustomValidity('');
                }
                
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
            
            // Real-time password match validation
            document.getElementById('confirm_password').addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirmPassword = this.value;
                
                if (password !== confirmPassword) {
                    this.setCustomValidity('Password tidak sama');
                } else {
                    this.setCustomValidity('');
                }
            });
        })();

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
