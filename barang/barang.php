<?php
// barang/barang.php
$page_title = 'Data Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Get all barang with joins
try {
    $stmt = $db->query("
        SELECT b.*, k.nama_kategori, s.nama_supplier 
        FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id 
        LEFT JOIN supplier s ON b.supplier_id = s.id 
        ORDER BY b.created_at DESC
    ");
    $barang_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Barang</li>
    </ol>
</nav>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        <?php 
        if ($_GET['success'] == 'add') echo 'Barang berhasil ditambahkan!';
        elseif ($_GET['success'] == 'edit') echo 'Barang berhasil diupdate!';
        elseif ($_GET['success'] == 'delete') echo 'Barang berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Terjadi kesalahan! Silakan coba lagi.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Data Barang</h5>
        <?php if (canCreate()): ?>
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah Barang
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="barangTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($barang_list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($item['kode_barang']); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($item['nama_kategori'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['nama_supplier'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                        <td><strong><?php echo $item['stok']; ?></strong></td>
                        <td>Rp <?php echo number_format($item['harga_beli'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                        <td>
                            <?php 
                            if ($item['stok'] <= 0) {
                                echo '<span class="badge bg-danger">Habis</span>';
                            } elseif ($item['stok'] <= $item['stok_minimum']) {
                                echo '<span class="badge bg-warning">Rendah</span>';
                            } else {
                                echo '<span class="badge bg-success">Aman</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (canEdit() || canDelete()): ?>
                            <?php if (canEdit()): ?>
                            <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (canDelete()): ?>
                            <a href="hapus.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus barang <?php echo htmlspecialchars($item['nama_barang']); ?>?')" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="badge bg-secondary">View Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#barangTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
