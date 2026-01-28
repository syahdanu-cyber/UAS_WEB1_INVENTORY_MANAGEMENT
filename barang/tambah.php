<?php
// barang/tambah.php
$page_title = 'Tambah Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

// Hanya admin yang bisa akses halaman ini
requireAdmin();

$db = getDB();

// Get kategori dan supplier untuk dropdown
try {
    $kategoris = $db->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
    $suppliers = $db->query("SELECT * FROM supplier ORDER BY nama_supplier")->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $kategori_id = $_POST['kategori_id'];
    $supplier_id = $_POST['supplier_id'];
    $satuan = trim($_POST['satuan']);
    $stok = (int)$_POST['stok'];
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok_minimum = (int)$_POST['stok_minimum'];
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    // Validation
    $errors = [];
    if (empty($kode)) $errors[] = "Kode barang wajib diisi";
    if (empty($nama)) $errors[] = "Nama barang wajib diisi";
    if (empty($satuan)) $errors[] = "Satuan wajib diisi";
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO barang 
                (kode_barang, nama_barang, kategori_id, supplier_id, satuan, stok, harga_beli, harga_jual, stok_minimum, deskripsi) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$kode, $nama, $kategori_id, $supplier_id, $satuan, $stok, $harga_beli, $harga_jual, $stok_minimum, $deskripsi])) {
                header('Location: barang.php?success=add');
                exit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Kode barang sudah digunakan!";
            } else {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="barang.php">Data Barang</a></li>
        <li class="breadcrumb-item active">Tambah Barang</li>
    </ol>
</nav>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Error:</strong>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Barang Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                    <input type="text" name="kode_barang" class="form-control" 
                           placeholder="Contoh: BRG001" required 
                           value="<?php echo isset($_POST['kode_barang']) ? htmlspecialchars($_POST['kode_barang']) : ''; ?>">
                    <div class="invalid-feedback">Kode barang wajib diisi</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control" 
                           placeholder="Contoh: Laptop ASUS ROG" required
                           value="<?php echo isset($_POST['nama_barang']) ? htmlspecialchars($_POST['nama_barang']) : ''; ?>">
                    <div class="invalid-feedback">Nama barang wajib diisi</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoris as $k): ?>
                            <option value="<?php echo $k['id']; ?>"
                                    <?php echo (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $k['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?php echo $s['id']; ?>"
                                    <?php echo (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $s['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['nama_supplier']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" 
                           placeholder="Contoh: Unit, Pcs, Box" required
                           value="<?php echo isset($_POST['satuan']) ? htmlspecialchars($_POST['satuan']) : ''; ?>">
                    <div class="invalid-feedback">Satuan wajib diisi</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stok" class="form-control" 
                           placeholder="0" min="0" value="<?php echo isset($_POST['stok']) ? $_POST['stok'] : '0'; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-control" 
                           placeholder="10" min="0" value="<?php echo isset($_POST['stok_minimum']) ? $_POST['stok_minimum'] : '10'; ?>">
                    <small class="text-muted">Alert akan muncul jika stok kurang dari nilai ini</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="number" name="harga_beli" class="form-control" 
                           placeholder="0" min="0" step="0.01" value="<?php echo isset($_POST['harga_beli']) ? $_POST['harga_beli'] : ''; ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control" 
                           placeholder="0" min="0" step="0.01" value="<?php echo isset($_POST['harga_jual']) ? $_POST['harga_jual'] : ''; ?>">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" 
                              placeholder="Deskripsi barang (opsional)"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan
                </button>
                <a href="barang.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
