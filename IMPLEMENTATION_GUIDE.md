# PANDUAN IMPLEMENTASI LENGKAP
## Sistem Manajemen Inventory - UAS Web Programming 1

File ini berisi panduan lengkap untuk mengimplementasikan file-file yang belum dibuat secara otomatis.

---

## FILE YANG SUDAH TERSEDIA

✅ Database Schema (database.sql)
✅ Konfigurasi Database (config/database.php)
✅ Session Management (auth/session_check.php)
✅ Login System (auth/login.php, login_process.php, logout.php)
✅ Dashboard dengan Charts (dashboard/index.php)
✅ Components (includes/header.php, sidebar.php, footer.php)
✅ CSS Files (style.css, dashboard.css, login.css)
✅ JavaScript Files (main.js, validation.js)
✅ README.md (Dokumentasi lengkap)

---

## FILE YANG PERLU DIBUAT

### 1. BARANG MODULE

#### File: barang/barang.php (List Barang - CRUD Read)
```php
<?php
$page_title = 'Data Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$stmt = $db->query("
    SELECT b.*, k.nama_kategori, s.nama_supplier 
    FROM barang b 
    LEFT JOIN kategori k ON b.kategori_id = k.id 
    LEFT JOIN supplier s ON b.supplier_id = s.id 
    ORDER BY b.created_at DESC
");
$barang_list = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-box"></i> Data Barang</h5>
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="barangTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($barang_list as $item): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $item['kode_barang']; ?></td>
                    <td><?php echo $item['nama_barang']; ?></td>
                    <td><?php echo $item['nama_kategori']; ?></td>
                    <td><?php echo $item['stok']; ?></td>
                    <td>
                        <?php if ($item['stok'] <= $item['stok_minimum']): ?>
                            <span class="badge bg-warning">Rendah</span>
                        <?php else: ?>
                            <span class="badge bg-success">Aman</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="hapus.php?id=<?php echo $item['id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Yakin hapus?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$('#barangTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

#### File: barang/tambah.php (CRUD Create)
```php
<?php
$page_title = 'Tambah Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$kategoris = $db->query("SELECT * FROM kategori")->fetchAll();
$suppliers = $db->query("SELECT * FROM supplier")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $kategori_id = $_POST['kategori_id'];
    $supplier_id = $_POST['supplier_id'];
    $satuan = $_POST['satuan'];
    $stok = $_POST['stok'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok_minimum = $_POST['stok_minimum'];
    
    $stmt = $db->prepare("INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, satuan, stok, harga_beli, harga_jual, stok_minimum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$kode, $nama, $kategori_id, $supplier_id, $satuan, $stok, $harga_beli, $harga_jual, $stok_minimum])) {
        header('Location: barang.php?success=add');
        exit();
    }
}
?>

<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-plus-circle"></i> Tambah Barang</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategoris as $k): ?>
                            <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kategori']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Pilih Supplier</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['nama_supplier']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Satuan</label>
                    <input type="text" name="satuan" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Stok Awal</label>
                    <input type="number" name="stok" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Harga Beli</label>
                    <input type="number" name="harga_beli" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="barang.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

#### File: barang/edit.php (CRUD Update)
Sama seperti tambah.php, tapi:
- Ambil data berdasarkan ID dari URL
- Pre-fill form dengan data yang ada
- Query UPDATE instead of INSERT

#### File: barang/hapus.php (CRUD Delete)
```php
<?php
require_once __DIR__ . '/../auth/session_check.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? 0;
$db = getDB();

$stmt = $db->prepare("DELETE FROM barang WHERE id = ?");
if ($stmt->execute([$id])) {
    header('Location: barang.php?success=delete');
} else {
    header('Location: barang.php?error=delete');
}
exit();
```

---

### 2. SUPPLIER MODULE

File-file ini mirip dengan barang module:
- supplier/supplier.php (list)
- supplier/tambah.php (add)
- supplier/edit.php (edit)
- supplier/hapus.php (delete)

Fields untuk supplier:
- kode_supplier
- nama_supplier
- alamat
- telepon
- email

---

### 3. TRANSAKSI MODULE

#### File: transaksi/masuk.php
```php
<?php
$page_title = 'Transaksi Barang Masuk';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_transaksi = 'TM-' . date('Ymd') . rand(100, 999);
    $barang_id = $_POST['barang_id'];
    $supplier_id = $_POST['supplier_id'];
    $jumlah = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];
    $total_harga = $jumlah * $harga_satuan;
    $tanggal = $_POST['tanggal_masuk'];
    $keterangan = $_POST['keterangan'];
    
    $db->beginTransaction();
    try {
        // Insert transaksi
        $stmt = $db->prepare("INSERT INTO transaksi_masuk (kode_transaksi, barang_id, supplier_id, jumlah, harga_satuan, total_harga, tanggal_masuk, keterangan, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode_transaksi, $barang_id, $supplier_id, $jumlah, $harga_satuan, $total_harga, $tanggal, $keterangan, $_SESSION['user_id']]);
        
        // Update stok
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
$transaksis = $db->query("
    SELECT tm.*, b.nama_barang, s.nama_supplier 
    FROM transaksi_masuk tm
    JOIN barang b ON tm.barang_id = b.id
    LEFT JOIN supplier s ON tm.supplier_id = s.id
    ORDER BY tm.created_at DESC
    LIMIT 20
")->fetchAll();
?>

<!-- Form dan Tabel di sini -->
```

#### File: transaksi/keluar.php
Sama seperti masuk.php tapi:
- Gunakan tabel `transaksi_keluar`
- Update stok dengan MINUS (-)
- Validasi stok tidak boleh minus

---

### 4. LAPORAN MODULE

#### File: laporan/laporan.php
```php
<?php
$page_title = 'Laporan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$jenis_laporan = $_GET['jenis'] ?? 'barang';
$dari_tanggal = $_GET['dari'] ?? date('Y-m-01');
$sampai_tanggal = $_GET['sampai'] ?? date('Y-m-d');

$data = [];
if ($jenis_laporan == 'barang') {
    $data = $db->query("SELECT b.*, k.nama_kategori, s.nama_supplier FROM barang b LEFT JOIN kategori k ON b.kategori_id = k.id LEFT JOIN supplier s ON b.supplier_id = s.id")->fetchAll();
} elseif ($jenis_laporan == 'transaksi_masuk') {
    $stmt = $db->prepare("SELECT tm.*, b.nama_barang, s.nama_supplier FROM transaksi_masuk tm JOIN barang b ON tm.barang_id = b.id LEFT JOIN supplier s ON tm.supplier_id = s.id WHERE tm.tanggal_masuk BETWEEN ? AND ?");
    $stmt->execute([$dari_tanggal, $sampai_tanggal]);
    $data = $stmt->fetchAll();
}
?>

<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-file-text"></i> Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label>Jenis Laporan</label>
                <select name="jenis" class="form-select">
                    <option value="barang">Data Barang</option>
                    <option value="transaksi_masuk">Transaksi Masuk</option>
                    <option value="transaksi_keluar">Transaksi Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?php echo $dari_tanggal; ?>">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?php echo $sampai_tanggal; ?>">
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
        
        <div class="mb-3">
            <a href="export_pdf.php?jenis=<?php echo $jenis_laporan; ?>&dari=<?php echo $dari_tanggal; ?>&sampai=<?php echo $sampai_tanggal; ?>" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="export_excel.php?jenis=<?php echo $jenis_laporan; ?>&dari=<?php echo $dari_tanggal; ?>&sampai=<?php echo $sampai_tanggal; ?>" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Export Excel
            </a>
        </div>
        
        <!-- Tampilkan tabel data di sini -->
    </div>
</div>
```

#### File: laporan/export_pdf.php
```php
<?php
require_once __DIR__ . '/../auth/session_check.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

// Install TCPDF via Composer: composer require tecnickcom/tcpdf
require_once __DIR__ . '/../vendor/autoload.php';

$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('Sistem Inventory');
$pdf->SetTitle('Laporan Inventory');
$pdf->AddPage();

// Ambil data dari database sesuai parameter
$db = getDB();
$jenis = $_GET['jenis'] ?? 'barang';

$html = '<h1>Laporan ' . ucfirst($jenis) . '</h1>';
$html .= '<table border="1" cellpadding="5">';
$html .= '<tr><th>No</th><th>Kode</th><th>Nama</th><th>Stok</th></tr>';

// Loop data dan masukkan ke HTML
// ...

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('laporan.pdf', 'I');
```

#### File: laporan/export_excel.php
```php
<?php
require_once __DIR__ . '/../auth/session_check.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

// Install PHPSpreadsheet: composer require phpoffice/phpspreadsheet
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Kode');
$sheet->setCellValue('C1', 'Nama');
$sheet->setCellValue('D1', 'Stok');

// Ambil data dan isi ke cells
$db = getDB();
// ... query data

$row = 2;
// foreach data, isi cells

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan.xlsx"');
$writer->save('php://output');
```

---

### 5. DASHBOARD STATISTIK

#### File: dashboard/statistik.php
```php
<?php
$page_title = 'Statistik';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Query data statistik yang lebih detail
// - Top 10 barang terlaris
// - Grafik pendapatan per bulan
// - Supplier dengan transaksi terbanyak
// - dll
?>

<!-- Tampilkan berbagai grafik dan statistik -->
```

---

### 6. FILE TAMBAHAN

#### File: index.php (Landing Page)
```php
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/index.php');
    exit();
}
header('Location: auth/login.php');
exit();
```

#### File: .htaccess
```apache
# Prevent directory listing
Options -Indexes

# Custom error pages
ErrorDocument 404 /404.php

# PHP settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

---

## LIBRARIES YANG DIPERLUKAN

### Install via Composer

```bash
# Install Composer terlebih dahulu
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install dependencies
composer require tecnickcom/tcpdf
composer require phpoffice/phpspreadsheet
```

Atau download manual:
- TCPDF: https://tcpdf.org/
- PHPSpreadsheet: https://phpspreadsheet.readthedocs.io/

---

## CHECKLIST IMPLEMENTASI

### Fase 1: Setup Dasar
- [x] Database schema
- [x] Config database
- [x] Session management
- [x] Login system
- [x] Layout components

### Fase 2: Module Utama
- [ ] CRUD Barang (Create, Read, Update, Delete)
- [ ] CRUD Supplier
- [ ] Transaksi Masuk (dengan update stok)
- [ ] Transaksi Keluar (dengan validasi stok)

### Fase 3: Dashboard & Laporan
- [x] Dashboard dengan statistik
- [x] Charts (Chart.js)
- [ ] Halaman statistik detail
- [ ] Export PDF (TCPDF)
- [ ] Export Excel (PHPSpreadsheet)

### Fase 4: Testing & Deployment
- [ ] Testing semua fitur
- [ ] Fix bugs
- [ ] Deploy ke hosting
- [ ] Final check

---

## TIPS IMPLEMENTASI

1. **Mulai dari yang mudah**: Implementasikan CRUD barang dulu sebagai template
2. **Copy-paste dan modifikasi**: Gunakan kode barang untuk supplier
3. **Test incremental**: Test setiap fitur setelah dibuat
4. **Backup rutin**: Backup database dan kode secara berkala
5. **Dokumentasi**: Catat setiap perubahan yang dibuat

---

## TROUBLESHOOTING UMUM

### Library TCPDF/PHPSpreadsheet tidak ditemukan
```bash
composer require tecnickcom/tcpdf
composer require phpoffice/phpspreadsheet
```

### Error Permission Denied
```bash
chmod -R 755 folder_name
```

### Session tidak tersimpan
Pastikan `session.save_path` dalam php.ini dapat ditulis.

---

**Selamat mengerjakan! Semoga sukses dengan UAS Web Programming 1! 🚀**
