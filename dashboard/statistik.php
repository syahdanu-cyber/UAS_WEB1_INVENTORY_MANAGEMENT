<?php
$page_title = 'Statistik';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
$db = getDB();

// Get statistics
$total_barang = $db->query("SELECT COUNT(*) as total FROM barang")->fetch()['total'];
$total_supplier = $db->query("SELECT COUNT(*) as total FROM supplier")->fetch()['total'];
$total_transaksi_masuk = $db->query("SELECT COUNT(*) as total, SUM(total_harga) as nilai FROM transaksi_masuk")->fetch();
$total_transaksi_keluar = $db->query("SELECT COUNT(*) as total, SUM(total_harga) as nilai FROM transaksi_keluar")->fetch();
$total_nilai_inventory = $db->query("SELECT SUM(stok * harga_beli) as total FROM barang")->fetch()['total'] ?? 0;

// Top 10 barang terlaris
$top_barang = $db->query("
    SELECT b.nama_barang, SUM(tk.jumlah) as total_keluar 
    FROM transaksi_keluar tk 
    JOIN barang b ON tk.barang_id = b.id 
    GROUP BY b.id 
    ORDER BY total_keluar DESC 
    LIMIT 10
")->fetchAll();

// Supplier dengan transaksi terbanyak
$top_supplier = $db->query("
    SELECT s.nama_supplier, COUNT(tm.id) as total_transaksi, SUM(tm.total_harga) as total_nilai
    FROM supplier s
    JOIN transaksi_masuk tm ON s.id = tm.supplier_id
    GROUP BY s.id
    ORDER BY total_transaksi DESC
    LIMIT 10
")->fetchAll();
?>

<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Dashboard</a></li><li class="breadcrumb-item active">Statistik</li></ol></nav>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card primary">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Total Barang</h6>
                    <h3><?php echo number_format($total_barang); ?></h3>
                </div>
                <div class="stats-icon primary"><i class="bi bi-box"></i></div>
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
                <div class="stats-icon success"><i class="bi bi-truck"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Total Transaksi Masuk</h6>
                    <h3><?php echo number_format($total_transaksi_masuk['total']); ?></h3>
                    <small>Nilai: Rp <?php echo number_format($total_transaksi_masuk['nilai']/1000000, 1); ?>M</small>
                </div>
                <div class="stats-icon warning"><i class="bi bi-arrow-down-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card danger">
            <div class="stats-card-body">
                <div class="stats-info">
                    <h6>Total Transaksi Keluar</h6>
                    <h3><?php echo number_format($total_transaksi_keluar['total']); ?></h3>
                    <small>Nilai: Rp <?php echo number_format($total_transaksi_keluar['nilai']/1000000, 1); ?>M</small>
                </div>
                <div class="stats-icon danger"><i class="bi bi-arrow-up-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><h5><i class="bi bi-trophy me-2"></i>Top 10 Barang Terlaris</h5></div>
            <div class="card-body">
                <canvas id="topBarangChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><h5><i class="bi bi-star me-2"></i>Top Supplier (Transaksi Terbanyak)</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>No</th><th>Supplier</th><th>Transaksi</th><th>Total Nilai</th></tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($top_supplier as $s): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $s['nama_supplier']; ?></td>
                                <td><span class="badge bg-primary"><?php echo $s['total_transaksi']; ?></span></td>
                                <td>Rp <?php echo number_format($s['total_nilai'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const topBarangCtx = document.getElementById('topBarangChart').getContext('2d');
new Chart(topBarangCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach($top_barang as $b) echo "'" . addslashes($b['nama_barang']) . "',"; ?>],
        datasets: [{
            label: 'Jumlah Terjual',
            data: [<?php foreach($top_barang as $b) echo $b['total_keluar'] . ','; ?>],
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
