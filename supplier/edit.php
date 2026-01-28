<?php
$page_title = 'Edit Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
$db = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: supplier.php?error=invalid'); exit(); }
$stmt = $db->prepare("SELECT * FROM supplier WHERE id = ?");
$stmt->execute([$id]);
$supplier = $stmt->fetch();
if (!$supplier) { header('Location: supplier.php?error=notfound'); exit(); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode_supplier']);
    $nama = trim($_POST['nama_supplier']);
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $stmt = $db->prepare("UPDATE supplier SET kode_supplier=?, nama_supplier=?, alamat=?, telepon=?, email=? WHERE id=?");
    if ($stmt->execute([$kode, $nama, $alamat, $telepon, $email, $id])) {
        header('Location: supplier.php?success=edit');
        exit();
    }
}
?>
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li><li class="breadcrumb-item"><a href="supplier.php">Supplier</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
<div class="card"><div class="card-header"><h5><i class="bi bi-pencil me-2"></i>Edit Supplier</h5></div><div class="card-body">
<form method="POST"><div class="row">
<div class="col-md-6 mb-3"><label>Kode Supplier *</label><input type="text" name="kode_supplier" class="form-control" required value="<?php echo htmlspecialchars($supplier['kode_supplier']); ?>"></div>
<div class="col-md-6 mb-3"><label>Nama Supplier *</label><input type="text" name="nama_supplier" class="form-control" required value="<?php echo htmlspecialchars($supplier['nama_supplier']); ?>"></div>
<div class="col-md-12 mb-3"><label>Alamat</label><textarea name="alamat" class="form-control" rows="2"><?php echo htmlspecialchars($supplier['alamat']); ?></textarea></div>
<div class="col-md-6 mb-3"><label>Telepon</label><input type="text" name="telepon" class="form-control" value="<?php echo htmlspecialchars($supplier['telepon']); ?>"></div>
<div class="col-md-6 mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($supplier['email']); ?>"></div>
</div><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update</button><a href="supplier.php" class="btn btn-secondary"><i class="bi bi-x-circle me-2"></i>Batal</a>
</form></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
