<?php
require_once __DIR__ . '/../auth/session_check.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

// CARA INSTALL TCPDF:
// 1. Composer: composer require tecnickcom/tcpdf
// 2. Manual: Download dari https://tcpdf.org/ dan extract ke vendor/

// Check if TCPDF available
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $tcpdf_available = class_exists('TCPDF');
} else {
    $tcpdf_available = false;
}

$db = getDB();
$jenis = $_GET['jenis'] ?? 'barang';
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Get data
if ($jenis == 'barang') {
    $data = $db->query("SELECT b.*, k.nama_kategori, s.nama_supplier FROM barang b LEFT JOIN kategori k ON b.kategori_id = k.id LEFT JOIN supplier s ON b.supplier_id = s.id ORDER BY b.nama_barang")->fetchAll();
    $title = "Laporan Data Barang";
} elseif ($jenis == 'transaksi_masuk') {
    $stmt = $db->prepare("SELECT tm.*, b.nama_barang, s.nama_supplier FROM transaksi_masuk tm JOIN barang b ON tm.barang_id = b.id LEFT JOIN supplier s ON tm.supplier_id = s.id WHERE tm.tanggal_masuk BETWEEN ? AND ? ORDER BY tm.tanggal_masuk DESC");
    $stmt->execute([$dari, $sampai]);
    $data = $stmt->fetchAll();
    $title = "Laporan Transaksi Masuk";
} else {
    $stmt = $db->prepare("SELECT tk.*, b.nama_barang FROM transaksi_keluar tk JOIN barang b ON tk.barang_id = b.id WHERE tk.tanggal_keluar BETWEEN ? AND ? ORDER BY tk.tanggal_keluar DESC");
    $stmt->execute([$dari, $sampai]);
    $data = $stmt->fetchAll();
    $title = "Laporan Transaksi Keluar";
}

if ($tcpdf_available) {
    // Generate PDF using TCPDF
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
    $pdf->SetCreator('Sistem Inventory');
    $pdf->SetTitle($title);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, 'Tanggal: ' . date('d/m/Y'), 0, 1, 'C');
    if ($jenis != 'barang') {
        $pdf->Cell(0, 5, 'Periode: ' . date('d/m/Y', strtotime($dari)) . ' - ' . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
    }
    $pdf->Ln(5);
    
    // Table
    $html = '<table border="1" cellpadding="4"><thead><tr style="background-color:#4e73df;color:white;">';
    if ($jenis == 'barang') {
        $html .= '<th>No</th><th>Kode</th><th>Nama</th><th>Kategori</th><th>Stok</th><th>Harga Beli</th><th>Harga Jual</th>';
    } elseif ($jenis == 'transaksi_masuk') {
        $html .= '<th>No</th><th>Kode</th><th>Barang</th><th>Supplier</th><th>Jumlah</th><th>Total</th><th>Tanggal</th>';
    } else {
        $html .= '<th>No</th><th>Kode</th><th>Barang</th><th>Jumlah</th><th>Total</th><th>Tujuan</th><th>Tanggal</th>';
    }
    $html .= '</tr></thead><tbody>';
    
    $no = 1;
    foreach ($data as $row) {
        $html .= '<tr>';
        $html .= '<td>' . $no++ . '</td>';
        if ($jenis == 'barang') {
            $html .= '<td>' . $row['kode_barang'] . '</td><td>' . $row['nama_barang'] . '</td><td>' . $row['nama_kategori'] . '</td><td>' . $row['stok'] . '</td><td>Rp ' . number_format($row['harga_beli'], 0, ',', '.') . '</td><td>Rp ' . number_format($row['harga_jual'], 0, ',', '.') . '</td>';
        } elseif ($jenis == 'transaksi_masuk') {
            $html .= '<td>' . $row['kode_transaksi'] . '</td><td>' . $row['nama_barang'] . '</td><td>' . $row['nama_supplier'] . '</td><td>' . $row['jumlah'] . '</td><td>Rp ' . number_format($row['total_harga'], 0, ',', '.') . '</td><td>' . date('d/m/Y', strtotime($row['tanggal_masuk'])) . '</td>';
        } else {
            $html .= '<td>' . $row['kode_transaksi'] . '</td><td>' . $row['nama_barang'] . '</td><td>' . $row['jumlah'] . '</td><td>Rp ' . number_format($row['total_harga'], 0, ',', '.') . '</td><td>' . $row['tujuan'] . '</td><td>' . date('d/m/Y', strtotime($row['tanggal_keluar'])) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('laporan_' . $jenis . '.pdf', 'I');
    
} else {
    // Fallback: Generate simple HTML PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="laporan_' . $jenis . '.pdf"');
    
    echo "<html><head><style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#4e73df;color:white;}</style></head><body>";
    echo "<h2>" . $title . "</h2>";
    echo "<p>Tanggal: " . date('d/m/Y') . "</p>";
    if ($jenis != 'barang') {
        echo "<p>Periode: " . date('d/m/Y', strtotime($dari)) . " - " . date('d/m/Y', strtotime($sampai)) . "</p>";
    }
    echo "<p><strong>NOTE:</strong> Install TCPDF untuk PDF yang lebih baik. Run: <code>composer require tecnickcom/tcpdf</code></p>";
    echo "<table><thead><tr>";
    
    if ($jenis == 'barang') {
        echo "<th>No</th><th>Kode</th><th>Nama</th><th>Kategori</th><th>Stok</th><th>Harga Beli</th><th>Harga Jual</th>";
    } elseif ($jenis == 'transaksi_masuk') {
        echo "<th>No</th><th>Kode</th><th>Barang</th><th>Supplier</th><th>Jumlah</th><th>Total</th><th>Tanggal</th>";
    } else {
        echo "<th>No</th><th>Kode</th><th>Barang</th><th>Jumlah</th><th>Total</th><th>Tujuan</th><th>Tanggal</th>";
    }
    echo "</tr></thead><tbody>";
    
    $no = 1;
    foreach ($data as $row) {
        echo "<tr><td>" . $no++ . "</td>";
        if ($jenis == 'barang') {
            echo "<td>" . $row['kode_barang'] . "</td><td>" . $row['nama_barang'] . "</td><td>" . $row['nama_kategori'] . "</td><td>" . $row['stok'] . "</td><td>Rp " . number_format($row['harga_beli'], 0, ',', '.') . "</td><td>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>";
        } elseif ($jenis == 'transaksi_masuk') {
            echo "<td>" . $row['kode_transaksi'] . "</td><td>" . $row['nama_barang'] . "</td><td>" . $row['nama_supplier'] . "</td><td>" . $row['jumlah'] . "</td><td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td><td>" . date('d/m/Y', strtotime($row['tanggal_masuk'])) . "</td>";
        } else {
            echo "<td>" . $row['kode_transaksi'] . "</td><td>" . $row['nama_barang'] . "</td><td>" . $row['jumlah'] . "</td><td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td><td>" . $row['tujuan'] . "</td><td>" . date('d/m/Y', strtotime($row['tanggal_keluar'])) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "<p style='margin-top:20px;'><i>Dicetak pada: " . date('d/m/Y H:i:s') . "</i></p>";
    echo "</body></html>";
}
