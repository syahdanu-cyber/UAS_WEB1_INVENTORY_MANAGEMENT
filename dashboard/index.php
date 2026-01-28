<?php
// dashboard/index.php
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Get statistics
try {
    // Total Barang
    $stmt = $db->query("SELECT COUNT(*) as total FROM barang");
    $total_barang = $stmt->fetch()['total'];
    
    // Total Supplier
    $stmt = $db->query("SELECT COUNT(*) as total FROM supplier");
    $total_supplier = $stmt->fetch()['total'];
    
    // Total Transaksi Masuk (bulan ini)
    $stmt = $db->query("SELECT COUNT(*) as total FROM transaksi_masuk WHERE MONTH(tanggal_masuk) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_masuk) = YEAR(CURRENT_DATE())");
    $transaksi_masuk_bulan_ini = $stmt->fetch()['total'];
    
    // Total Transaksi Keluar (bulan ini)
    $stmt = $db->query("SELECT COUNT(*) as total FROM transaksi_keluar WHERE MONTH(tanggal_keluar) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_keluar) = YEAR(CURRENT_DATE())");
    $transaksi_keluar_bulan_ini = $stmt->fetch()['total'];
    
    // Barang dengan stok rendah
    $stmt = $db->query("SELECT COUNT(*) as total FROM barang WHERE stok <= stok_minimum");
    $stok_rendah = $stmt->fetch()['total'];
    
    // Total nilai inventory
    $stmt = $db->query("SELECT SUM(stok * harga_beli) as total FROM barang");
    $total_nilai = $stmt->fetch()['total'] ?? 0;
    
    // Barang dengan stok rendah (detail)
    $stmt = $db->query("
        SELECT b.*, k.nama_kategori, s.nama_supplier 
        FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id 
        LEFT JOIN supplier s ON b.supplier_id = s.id 
        WHERE b.stok <= b.stok_minimum 
        ORDER BY b.stok ASC 
        LIMIT 10
    ");
    $barang_stok_rendah = $stmt->fetchAll();
    
    // Transaksi terbaru
    $stmt = $db->query("
        SELECT 'masuk' as jenis, tm.kode_transaksi, b.nama_barang, tm.jumlah, tm.tanggal_masuk as tanggal, s.nama_supplier as pihak
        FROM transaksi_masuk tm
        JOIN barang b ON tm.barang_id = b.id
        LEFT JOIN supplier s ON tm.supplier_id = s.id
        ORDER BY tm.created_at DESC
        LIMIT 5
    ");
    $transaksi_terbaru = $stmt->fetchAll();
    
    // Data untuk chart - transaksi per bulan (6 bulan terakhir)
    $stmt = $db->query("
        SELECT 
            DATE_FORMAT(tanggal_masuk, '%Y-%m') as bulan,
            COUNT(*) as jumlah_masuk
        FROM transaksi_masuk
        WHERE tanggal_masuk >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(tanggal_masuk, '%Y-%m')
        ORDER BY bulan
    ");
    $data_masuk = $stmt->fetchAll();
    
    $stmt = $db->query("
        SELECT 
            DATE_FORMAT(tanggal_keluar, '%Y-%m') as bulan,
            COUNT(*) as jumlah_keluar
        FROM transaksi_keluar
        WHERE tanggal_keluar >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(tanggal_keluar, '%Y-%m')
        ORDER BY bulan
    ");
    $data_keluar = $stmt->fetchAll();
    
    // Data untuk pie chart - kategori barang
    $stmt = $db->query("
        SELECT k.nama_kategori, COUNT(b.id) as jumlah
        FROM kategori k
        LEFT JOIN barang b ON k.id = b.kategori_id
        GROUP BY k.id, k.nama_kategori
        HAVING jumlah > 0
        ORDER BY jumlah DESC
    ");
    $data_kategori = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>

<!-- Statistics Cards -->
<?php if (isset($_GET['error']) && $_GET['error'] == 'access_denied'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-shield-exclamation me-2"></i>
        <strong>Akses Ditolak!</strong> Anda tidak memiliki izin untuk mengakses halaman tersebut. Hanya Admin yang dapat melakukan Create/Update/Delete.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isStaff()): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Mode Read-Only</strong> - Anda login sebagai <strong>Staff</strong>. Anda dapat melihat semua data namun tidak dapat menambah, mengubah, atau menghapus data. Hubungi Admin jika memerlukan akses lebih.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card primary">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Total Barang</h6>
                    <h3><?php echo number_format($total_barang); ?></h3>
                </div>
                <div class="stats-icon primary">
                    <i class="bi bi-box"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Total Supplier</h6>
                    <h3><?php echo number_format($total_supplier); ?></h3>
                </div>
                <div class="stats-icon success">
                    <i class="bi bi-truck"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Stok Rendah</h6>
                    <h3><?php echo number_format($stok_rendah); ?></h3>
                </div>
                <div class="stats-icon warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card danger">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Total Nilai Inventory</h6>
                    <h3>Rp <?php echo number_format($total_nilai / 1000000, 1); ?>M</h3>
                </div>
                <div class="stats-icon danger">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>
                Grafik Transaksi (6 Bulan Terakhir)
            </div>
            <div class="card-body">
                <canvas id="transaksiChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>
                Distribusi Kategori Barang
            </div>
            <div class="card-body">
                <canvas id="kategoriChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-circle me-2"></i>Barang Stok Rendah</span>
                <a href="../barang/barang.php" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (empty($barang_stok_rendah)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Semua barang memiliki stok yang cukup!
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Min</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($barang_stok_rendah as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                                <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                                <td><strong><?php echo $item['stok']; ?></strong></td>
                                <td><?php echo $item['stok_minimum']; ?></td>
                                <td>
                                    <?php if ($item['stok'] == 0): ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Rendah</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Transaksi Terbaru</span>
                <a href="../transaksi/masuk.php" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (empty($transaksi_terbaru)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada transaksi
                    </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($transaksi_terbaru as $trans): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($trans['nama_barang']); ?></h6>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($trans['kode_transaksi']); ?> 
                                    | <?php echo htmlspecialchars($trans['pihak']); ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?php echo $trans['jenis'] == 'masuk' ? 'success' : 'danger'; ?>">
                                    <?php echo $trans['jenis'] == 'masuk' ? '+' : '-'; ?> <?php echo $trans['jumlah']; ?>
                                </span>
                                <br>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($trans['tanggal'])); ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Prepare data for charts
const bulanLabels = [
    <?php 
    $bulanMap = [];
    foreach ($data_masuk as $d) {
        $bulanMap[$d['bulan']] = ['masuk' => $d['jumlah_masuk'], 'keluar' => 0];
    }
    foreach ($data_keluar as $d) {
        if (isset($bulanMap[$d['bulan']])) {
            $bulanMap[$d['bulan']]['keluar'] = $d['jumlah_keluar'];
        } else {
            $bulanMap[$d['bulan']] = ['masuk' => 0, 'keluar' => $d['jumlah_keluar']];
        }
    }
    ksort($bulanMap);
    foreach (array_keys($bulanMap) as $bulan) {
        echo "'" . date('M Y', strtotime($bulan . '-01')) . "',";
    }
    ?>
];

const dataMasuk = [<?php foreach ($bulanMap as $data) echo $data['masuk'] . ','; ?>];
const dataKeluar = [<?php foreach ($bulanMap as $data) echo $data['keluar'] . ','; ?>];

// Line Chart - Transaksi
const ctxTransaksi = document.getElementById('transaksiChart').getContext('2d');
new Chart(ctxTransaksi, {
    type: 'line',
    data: {
        labels: bulanLabels,
        datasets: [{
            label: 'Barang Masuk',
            data: dataMasuk,
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.4
        }, {
            label: 'Barang Keluar',
            data: dataKeluar,
            borderColor: '#e74a3b',
            backgroundColor: 'rgba(231, 74, 59, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Pie Chart - Kategori
const ctxKategori = document.getElementById('kategoriChart').getContext('2d');
new Chart(ctxKategori, {
    type: 'doughnut',
    data: {
        labels: [<?php foreach ($data_kategori as $kat) echo "'" . $kat['nama_kategori'] . "',"; ?>],
        datasets: [{
            data: [<?php foreach ($data_kategori as $kat) echo $kat['jumlah'] . ','; ?>],
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b',
                '#858796'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
