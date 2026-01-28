<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-box-seam"></i> Inventory System</h4>
        <p>Manajemen Stok Barang</p>
    </div>
    
    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-category">MAIN MENU</div>
        <div class="menu-item">
            <a href="../dashboard/index.php" class="menu-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <!-- Statistik -->
        <div class="menu-item">
            <a href="../dashboard/statistik.php" class="menu-link <?php echo ($current_page == 'statistik') ? 'active' : ''; ?>">
                <i class="bi bi-graph-up"></i>
                <span>Statistik</span>
            </a>
        </div>
        
        <!-- Master Data -->
        <div class="menu-category">MASTER DATA</div>
        
        <!-- Barang -->
        <div class="menu-item">
            <a href="../barang/barang.php" class="menu-link <?php echo ($current_page == 'barang' || $current_page == 'tambah' || $current_page == 'edit') ? 'active' : ''; ?>">
                <i class="bi bi-box"></i>
                <span>Data Barang</span>
            </a>
        </div>
        
        <!-- Supplier -->
        <div class="menu-item">
            <a href="../supplier/supplier.php" class="menu-link <?php echo (strpos($_SERVER['PHP_SELF'], 'supplier') !== false) ? 'active' : ''; ?>">
                <i class="bi bi-truck"></i>
                <span>Data Supplier</span>
            </a>
        </div>
        
        <!-- Transaksi -->
        <div class="menu-category">TRANSAKSI</div>
        
        <!-- Barang Masuk -->
        <div class="menu-item">
            <a href="../transaksi/masuk.php" class="menu-link <?php echo ($current_page == 'masuk') ? 'active' : ''; ?>">
                <i class="bi bi-arrow-down-circle"></i>
                <span>Barang Masuk</span>
            </a>
        </div>
        
        <!-- Barang Keluar -->
        <div class="menu-item">
            <a href="../transaksi/keluar.php" class="menu-link <?php echo ($current_page == 'keluar') ? 'active' : ''; ?>">
                <i class="bi bi-arrow-up-circle"></i>
                <span>Barang Keluar</span>
            </a>
        </div>
        
        <!-- Laporan -->
        <div class="menu-category">LAPORAN</div>
        
        <!-- Laporan -->
        <div class="menu-item">
            <a href="../laporan/laporan.php" class="menu-link <?php echo (strpos($_SERVER['PHP_SELF'], 'laporan') !== false && $current_page != 'export_pdf' && $current_page != 'export_excel') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan</span>
            </a>
        </div>
        
        <!-- User Management (hanya untuk admin) -->
        <?php if (hasRole('admin')): ?>
        <div class="menu-category">PENGATURAN</div>
        
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-people"></i>
                <span>Manajemen User</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-gear"></i>
                <span>Pengaturan Sistem</span>
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Logout -->
        <div class="menu-category">AKUN</div>
        <div class="menu-item">
            <a href="../auth/logout.php" class="menu-link" onclick="return confirm('Yakin ingin logout?')">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>
