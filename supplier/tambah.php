<?php
// supplier/tambah.php
$page_title = 'Tambah Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

// Hanya admin yang bisa akses halaman ini
requireAdmin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode_supplier']);
    $nama = trim($_POST['nama_supplier']);
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    $errors = [];
    if (empty($kode)) $errors[] = "Kode supplier wajib diisi";
    if (empty($nama)) $errors[] = "Nama supplier wajib diisi";
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO supplier (kode_supplier, nama_supplier, alamat, telepon, email) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$kode, $nama, $alamat, $telepon, $email])) {
                header('Location: supplier.php?success=add');
                exit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Kode supplier sudah digunakan!";
            } else {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="supplier.php">Data Supplier</a></li>
        <li class="breadcrumb-item active">Tambah Supplier</li>
    </ol>
</nav>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h5><i class="bi bi-plus-circle me-2"></i>Tambah Supplier</h5></div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Kode Supplier *</label>
                    <input type="text" name="kode_supplier" class="form-control" required value="<?php echo $_POST['kode_supplier'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nama Supplier *</label>
                    <input type="text" name="nama_supplier" class="form-control" required value="<?php echo $_POST['nama_supplier'] ?? ''; ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"><?php echo $_POST['alamat'] ?? ''; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="<?php echo $_POST['telepon'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan</button>
            <a href="supplier.php" class="btn btn-secondary"><i class="bi bi-x-circle me-2"></i>Batal</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
