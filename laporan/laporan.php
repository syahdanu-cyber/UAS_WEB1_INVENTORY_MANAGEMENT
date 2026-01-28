<?php
$page_title = 'Laporan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
$db = getDB();

$jenis = $_GET['jenis'] ?? 'barang';
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$data = [];
$columns = [];

if ($jenis == 'barang') {
    $data = $db->query("SELECT b.*, k.nama_kategori, s.nama_supplier FROM barang b LEFT JOIN kategori k ON b.kategori_id = k.id LEFT JOIN supplier s ON b.supplier_id = s.id ORDER BY b.nama_barang")->fetchAll();
    $columns = ['Kode', 'Nama Barang', 'Kategori', 'Supplier', 'Stok', 'Harga Beli', 'Harga Jual', 'Status'];
} elseif ($jenis == 'transaksi_masuk') {
    $stmt = $db->prepare("SELECT tm.*, b.nama_barang, s.nama_supplier FROM transaksi_masuk tm JOIN barang b ON tm.barang_id = b.id LEFT JOIN supplier s ON tm.supplier_id = s.id WHERE tm.tanggal_masuk BETWEEN ? AND ? ORDER BY tm.tanggal_masuk DESC");
    $stmt->execute([$dari, $sampai]);
    $data = $stmt->fetchAll();
    $columns = ['Kode Transaksi', 'Barang', 'Supplier', 'Jumlah', 'Harga Satuan', 'Total', 'Tanggal'];
} elseif ($jenis == 'transaksi_keluar') {
    $stmt = $db->prepare("SELECT tk.*, b.nama_barang FROM transaksi_keluar tk JOIN barang b ON tk.barang_id = b.id WHERE tk.tanggal_keluar BETWEEN ? AND ? ORDER BY tk.tanggal_keluar DESC");
    $stmt->execute([$dari, $sampai]);
    $data = $stmt->fetchAll();
    $columns = ['Kode Transaksi', 'Barang', 'Jumlah', 'Harga Satuan', 'Total', 'Tujuan', 'Tanggal'];
}
?>

<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li><li class="breadcrumb-item active">Laporan</li></ol></nav>

<div class="card mb-4">
    <div class="card-header"><h5><i class="bi bi-filter me-2"></i>Filter Laporan</h5></div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Jenis Laporan</label>
                <select name="jenis" class="form-select" onchange="this.form.submit()">
                    <option value="barang" <?php echo $jenis=='barang'?'selected':''; ?>>Data Barang</option>
                    <option value="transaksi_masuk" <?php echo $jenis=='transaksi_masuk'?'selected':''; ?>>Transaksi Masuk</option>
                    <option value="transaksi_keluar" <?php echo $jenis=='transaksi_keluar'?'selected':''; ?>>Transaksi Keluar</option>
                </select>
            </div>
            <?php if ($jenis != 'barang'): ?>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?php echo $dari; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?php echo $sampai; ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Hasil Laporan</h5>
        <div>
            <a href="export_pdf.php?jenis=<?php echo $jenis; ?>&dari=<?php echo $dari; ?>&sampai=<?php echo $sampai; ?>" class="btn btn-danger btn-sm" target="_blank">
                <i class="bi bi-file-pdf me-1"></i>Export PDF
            </a>
            <a href="export_excel.php?jenis=<?php echo $jenis; ?>&dari=<?php echo $dari; ?>&sampai=<?php echo $sampai; ?>" class="btn btn-success btn-sm">
                <i class="bi bi-file-excel me-1"></i>Export Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="laporanTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php foreach($columns as $col): ?>
                            <th><?php echo $col; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="<?php echo count($columns)+1; ?>" class="text-center">Tidak ada data</td></tr>
                    <?php else: ?>
                        <?php $no=1; foreach($data as $row): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <?php if ($jenis == 'barang'): ?>
                                    <td><?php echo $row['kode_barang']; ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['nama_kategori']; ?></td>
                                    <td><?php echo $row['nama_supplier']; ?></td>
                                    <td><?php echo $row['stok']; ?></td>
                                    <td>Rp <?php echo number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if($row['stok']<=0): ?><span class="badge bg-danger">Habis</span>
                                        <?php elseif($row['stok']<=$row['stok_minimum']): ?><span class="badge bg-warning">Rendah</span>
                                        <?php else: ?><span class="badge bg-success">Aman</span><?php endif; ?>
                                    </td>
                                <?php elseif ($jenis == 'transaksi_masuk'): ?>
                                    <td><?php echo $row['kode_transaksi']; ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['nama_supplier']; ?></td>
                                    <td><?php echo $row['jumlah']; ?></td>
                                    <td>Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                                <?php elseif ($jenis == 'transaksi_keluar'): ?>
                                    <td><?php echo $row['kode_transaksi']; ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td><?php echo $row['jumlah']; ?></td>
                                    <td>Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $row['tujuan']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_keluar'])); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#laporanTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
        pageLength: 50
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
