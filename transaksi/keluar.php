<?php
$page_title = 'Transaksi Barang Keluar';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Hanya admin yang bisa submit transaksi
    if (!canCreate()) {
        header('Location: keluar.php?error=access_denied');
        exit();
    }
    
    $kode = 'TK-' . date('Ymd') . rand(100, 999);
    $barang_id = $_POST['barang_id'];
    $jumlah = (int)$_POST['jumlah'];
    $harga = (float)$_POST['harga_satuan'];
    $total = $jumlah * $harga;
    $tanggal = $_POST['tanggal_keluar'];
    $tujuan = trim($_POST['tujuan'] ?? '');
    $ket = trim($_POST['keterangan'] ?? '');
    
    // Check stok
    $stmt = $db->prepare("SELECT stok, nama_barang FROM barang WHERE id = ?");
    $stmt->execute([$barang_id]);
    $barang = $stmt->fetch();
    
    if ($barang && $barang['stok'] >= $jumlah) {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("INSERT INTO transaksi_keluar (kode_transaksi, barang_id, jumlah, harga_satuan, total_harga, tanggal_keluar, tujuan, keterangan, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$kode, $barang_id, $jumlah, $harga, $total, $tanggal, $tujuan, $ket, $_SESSION['user_id']]);
            $stmt = $db->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
            $stmt->execute([$jumlah, $barang_id]);
            $db->commit();
            $success = true;
        } catch (Exception $e) {
            $db->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = "Stok tidak mencukupi! Stok tersedia: " . ($barang['stok'] ?? 0);
    }
}

$barangs = $db->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang")->fetchAll();
$transaksis = $db->query("SELECT tk.*, b.nama_barang FROM transaksi_keluar tk JOIN barang b ON tk.barang_id = b.id ORDER BY tk.created_at DESC LIMIT 20")->fetchAll();
?>
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li><li class="breadcrumb-item active">Barang Keluar</li></ol></nav>
<?php if(isset($success)): ?><div class="alert alert-success">Transaksi berhasil! Stok telah diupdate.</div><?php endif; ?>
<?php if(isset($error)): ?><div class="alert alert-danger">Error: <?php echo $error; ?></div><?php endif; ?>
<div class="row"><div class="col-lg-5"><div class="card"><div class="card-header"><h5><i class="bi bi-arrow-up-circle me-2"></i>Form Barang Keluar</h5></div><div class="card-body">
<?php if (canCreate()): ?>
<form method="POST"><div class="mb-3"><label>Pilih Barang *</label><select name="barang_id" class="form-select" required id="barangSelect"><option value="">-- Pilih Barang --</option><?php foreach($barangs as $b): ?><option value="<?php echo $b['id']; ?>" data-stok="<?php echo $b['stok']; ?>"><?php echo $b['nama_barang']; ?> (Stok: <?php echo $b['stok']; ?>)</option><?php endforeach; ?></select><small class="text-muted" id="stokInfo"></small></div>
<div class="mb-3"><label>Jumlah *</label><input type="number" name="jumlah" class="form-control" required min="1" id="jumlahInput"></div>
<div class="mb-3"><label>Harga Satuan *</label><input type="number" name="harga_satuan" class="form-control" required min="0" step="0.01"></div>
<div class="mb-3"><label>Tanggal Keluar *</label><input type="date" name="tanggal_keluar" class="form-control" required value="<?php echo date('Y-m-d'); ?>"></div>
<div class="mb-3"><label>Tujuan</label><input type="text" name="tujuan" class="form-control" placeholder="Misal: Kantor Cabang, Toko, dll"></div>
<div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
<button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-2"></i>Simpan Transaksi</button>
</form>
<?php else: ?>
<div class="alert alert-warning">
    <i class="bi bi-lock-fill me-2"></i>
    <strong>Akses Terbatas</strong><br>
    Hanya Admin yang dapat melakukan transaksi barang keluar.<br>
    Anda hanya dapat melihat riwayat transaksi.
</div>
<?php endif; ?>
</div></div></div>
<div class="col-lg-7"><div class="card"><div class="card-header"><h5><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi Keluar</h5></div><div class="card-body"><div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Kode</th><th>Barang</th><th>Jumlah</th><th>Tujuan</th><th>Tanggal</th></tr></thead><tbody>
<?php foreach($transaksis as $t): ?><tr><td><?php echo $t['kode_transaksi']; ?></td><td><?php echo $t['nama_barang']; ?></td><td><?php echo $t['jumlah']; ?></td><td><?php echo $t['tujuan']; ?></td><td><?php echo date('d/m/Y', strtotime($t['tanggal_keluar'])); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div></div></div>
<script>
document.getElementById('barangSelect').addEventListener('change', function(){
    var stok = this.options[this.selectedIndex].dataset.stok;
    document.getElementById('stokInfo').textContent = stok ? 'Stok tersedia: ' + stok : '';
    document.getElementById('jumlahInput').max = stok;
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>