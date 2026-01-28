<?php
$page_title = 'Transaksi Barang Masuk';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Hanya admin yang bisa submit transaksi
    if (!canCreate()) {
        header('Location: masuk.php?error=access_denied');
        exit();
    }
    
    $kode = 'TM-' . date('Ymd') . rand(100, 999);
    $barang_id = $_POST['barang_id'];
    $supplier_id = $_POST['supplier_id'];
    $jumlah = (int)$_POST['jumlah'];
    $harga = (float)$_POST['harga_satuan'];
    $total = $jumlah * $harga;
    $tanggal = $_POST['tanggal_masuk'];
    $ket = trim($_POST['keterangan'] ?? '');
    
    $db->beginTransaction();
    try {
        $stmt = $db->prepare("INSERT INTO transaksi_masuk (kode_transaksi, barang_id, supplier_id, jumlah, harga_satuan, total_harga, tanggal_masuk, keterangan, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode, $barang_id, $supplier_id, $jumlah, $harga, $total, $tanggal, $ket, $_SESSION['user_id']]);
        $stmt = $db->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
        $stmt->execute([$jumlah, $barang_id]);
        $db->commit();
        $success = true;
    } catch (Exception $e) {
        $db->rollBack();
        $error = $e->getMessage();
    }
}

$barangs = $db->query("SELECT * FROM barang ORDER BY nama_barang")->fetchAll();
$suppliers = $db->query("SELECT * FROM supplier ORDER BY nama_supplier")->fetchAll();
$transaksis = $db->query("SELECT tm.*, b.nama_barang, s.nama_supplier FROM transaksi_masuk tm JOIN barang b ON tm.barang_id = b.id LEFT JOIN supplier s ON tm.supplier_id = s.id ORDER BY tm.created_at DESC LIMIT 20")->fetchAll();
?>
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li><li class="breadcrumb-item active">Barang Masuk</li></ol></nav>
<?php if(isset($success)): ?><div class="alert alert-success">Transaksi berhasil! Stok telah diupdate.</div><?php endif; ?>
<?php if(isset($error)): ?><div class="alert alert-danger">Error: <?php echo $error; ?></div><?php endif; ?>
<div class="row"><div class="col-lg-5"><div class="card"><div class="card-header"><h5><i class="bi bi-arrow-down-circle me-2"></i>Form Barang Masuk</h5></div><div class="card-body">
<?php if (canCreate()): ?>
<form method="POST"><div class="mb-3"><label>Pilih Barang *</label><select name="barang_id" class="form-select" required><option value="">-- Pilih Barang --</option><?php foreach($barangs as $b): ?><option value="<?php echo $b['id']; ?>"><?php echo $b['nama_barang']; ?> (Stok: <?php echo $b['stok']; ?>)</option><?php endforeach; ?></select></div>
<div class="mb-3"><label>Supplier</label><select name="supplier_id" class="form-select"><option value="">-- Pilih Supplier --</option><?php foreach($suppliers as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo $s['nama_supplier']; ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label>Jumlah *</label><input type="number" name="jumlah" class="form-control" required min="1"></div>
<div class="mb-3"><label>Harga Satuan *</label><input type="number" name="harga_satuan" class="form-control" required min="0" step="0.01"></div>
<div class="mb-3"><label>Tanggal Masuk *</label><input type="date" name="tanggal_masuk" class="form-control" required value="<?php echo date('Y-m-d'); ?>"></div>
<div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
<button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-2"></i>Simpan Transaksi</button>
</form>
<?php else: ?>
<div class="alert alert-warning">
    <i class="bi bi-lock-fill me-2"></i>
    <strong>Akses Terbatas</strong><br>
    Hanya Admin yang dapat melakukan transaksi barang masuk.<br>
    Anda hanya dapat melihat riwayat transaksi.
</div>
<?php endif; ?>
</div></div></div>
<div class="col-lg-7"><div class="card"><div class="card-header"><h5><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi Masuk</h5></div><div class="card-body"><div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Kode</th><th>Barang</th><th>Supplier</th><th>Jumlah</th><th>Tanggal</th></tr></thead><tbody>
<?php foreach($transaksis as $t): ?><tr><td><?php echo $t['kode_transaksi']; ?></td><td><?php echo $t['nama_barang']; ?></td><td><?php echo $t['nama_supplier']; ?></td><td><?php echo $t['jumlah']; ?></td><td><?php echo date('d/m/Y', strtotime($t['tanggal_masuk'])); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>