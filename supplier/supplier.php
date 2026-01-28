<?php
// supplier/supplier.php
$page_title = 'Data Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Get all suppliers
try {
    $stmt = $db->query("SELECT * FROM supplier ORDER BY nama_supplier ASC");
    $supplier_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Supplier</li>
    </ol>
</nav>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>
        <?php 
        if ($_GET['success'] == 'add') echo 'Supplier berhasil ditambahkan!';
        elseif ($_GET['success'] == 'edit') echo 'Supplier berhasil diupdate!';
        elseif ($_GET['success'] == 'delete') echo 'Supplier berhasil dihapus!';
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
        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Data Supplier</h5>
        <?php if (canCreate()): ?>
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah Supplier
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="supplierTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($supplier_list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($item['kode_supplier']); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['nama_supplier']); ?></td>
                        <td><?php echo htmlspecialchars($item['alamat'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['telepon'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['email'] ?? '-'); ?></td>
                        <td>
                            <?php if (canEdit() || canDelete()): ?>
                            <?php if (canEdit()): ?>
                            <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (canDelete()): ?>
                            <a href="hapus.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus supplier <?php echo htmlspecialchars($item['nama_supplier']); ?>?')" title="Hapus">
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
    $('#supplierTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        order: [[1, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
