<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../auth/session_check.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - Sistem Manajemen Inventory</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- jQuery (untuk DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables (opsional, untuk tabel dengan fitur lengkap) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        /* Custom styles bisa ditambahkan di sini */
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar akan diinclude dari sidebar.php -->
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- Sidebar Overlay untuk mobile -->
        <div class="sidebar-overlay"></div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                </div>
                
                <div class="header-right">
                    <!-- Notifications (opsional) -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark" type="button" id="notificationDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="badge bg-danger badge-sm">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                            <li><h6 class="dropdown-header">Notifikasi</h6></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                                5 barang stok rendah
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-box-seam text-info"></i>
                                Transaksi masuk baru
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-clipboard-check text-success"></i>
                                Laporan bulanan siap
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Lihat semua</a></li>
                        </ul>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark p-0" type="button" id="userDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                                </div>
                                <div class="user-details">
                                    <h6><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></h6>
                                    <p><?php echo ucfirst($_SESSION['role']); ?></p>
                                </div>
                                <i class="bi bi-chevron-down ms-2"></i>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-person me-2"></i> Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-gear me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="../auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
