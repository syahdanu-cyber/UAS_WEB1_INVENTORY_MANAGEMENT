<?php
require_once __DIR__ . '/../auth/session_check.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

// CARA INSTALL PHPSpreadsheet:
// 1. Composer: composer require phpoffice/phpspreadsheet
// 2. Manual: Download dan extract ke vendor/

$db = getDB();
$jenis = $_GET['jenis'] ?? 'barang';
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Get data
if ($jenis == 'barang') {
    $data = $db->query("SELECT b.*, k.nama_kategori, s.nama_supplier FROM barang b LEFT JOIN kategori k ON b.kategori_id = k.id LEFT JOIN supplier s ON b.supplier_id = s.id ORDER BY b.nama_barang")->fetchAll();
    $title = "Laporan Data Barang";
    $headers = ['No', 'Kode', 'Nama Barang', 'Kategori', 'Supplier', 'Satuan', 'Stok', 'Harga Beli', 'Harga Jual', 'Stok Minimum'];
} elseif ($jenis == 'transaksi_masuk') {
    $stmt = $db->prepare("SELECT tm.*, b.nama_barang, s.nama_supplier FROM transaksi_masuk tm JOIN barang b ON tm.barang_id = b.id LEFT JOIN supplier s ON tm.supplier_id = s.id WHERE tm.tanggal_masuk BETWEEN ? AND ? ORDER BY tm.tanggal_masuk DESC");
    $stmt->execute([$dari, $sampai]);
    $data = $stmt->fetchAll();
    $title = "Laporan Transaksi Masuk";
    $headers = ['No', 'Kode Transaksi', 'Barang', 'Supplier', 'Jumlah', 'Harga Satuan', 'Total Harga', 'Tanggal Masuk'];
} else {
    $stmt = $db->prepare("SELECT tk.*, b.nama_barang FROM transaksi_keluar tk JOIN barang b ON tk.barang_id = b.id WHERE tk.tanggal_keluar BETWEEN ? AND ? ORDER BY tk.tanggal_keluar DESC");
    $stmt->execute([$dari, $sampai]);
    $data = $stmt->fetchAll();
    $title = "Laporan Transaksi Keluar";
    $headers = ['No', 'Kode Transaksi', 'Barang', 'Jumlah', 'Harga Satuan', 'Total Harga', 'Tujuan', 'Tanggal Keluar'];
}

// Check if PHPSpreadsheet available
$spreadsheet_available = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        $spreadsheet_available = true;
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:' . chr(65 + count($headers) - 1) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Date
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d/m/Y'));
        $sheet->mergeCells('A2:' . chr(65 + count($headers) - 1) . '2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        if ($jenis != 'barang') {
            $sheet->setCellValue('A3', 'Periode: ' . date('d/m/Y', strtotime($dari)) . ' - ' . date('d/m/Y', strtotime($sampai)));
            $sheet->mergeCells('A3:' . chr(65 + count($headers) - 1) . '3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $startRow = 5;
        } else {
            $startRow = 4;
        }
        
        // Headers
        $col = 0;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, $startRow, $header);
            $col++;
        }
        
        // Header style
        $headerRange = 'A' . $startRow . ':' . chr(65 + count($headers) - 1) . $startRow;
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4e73df');
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Data
        $row = $startRow + 1;
        $no = 1;
        foreach ($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow(++$col, $row, $no++);
            
            if ($jenis == 'barang') {
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['kode_barang']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['nama_barang']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['nama_kategori']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['nama_supplier']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['satuan']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['stok']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['harga_beli']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['harga_jual']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['stok_minimum']);
            } elseif ($jenis == 'transaksi_masuk') {
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['kode_transaksi']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['nama_barang']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['nama_supplier']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['jumlah']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['harga_satuan']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['total_harga']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, date('d/m/Y', strtotime($item['tanggal_masuk'])));
            } else {
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['kode_transaksi']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['nama_barang']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['jumlah']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['harga_satuan']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['total_harga']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $item['tujuan']);
                $sheet->setCellValueByColumnAndRow(++$col, $row, date('d/m/Y', strtotime($item['tanggal_keluar'])));
            }
            $row++;
        }
        
        // Auto width
        foreach (range('A', chr(65 + count($headers) - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Border
        $dataRange = 'A' . $startRow . ':' . chr(65 + count($headers) - 1) . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan_' . $jenis . '_' . date('Ymd') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}

// Fallback: CSV
$filename = 'laporan_' . $jenis . '_' . date('Ymd') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

// Headers
fputcsv($output, $headers);

// Data
$no = 1;
foreach ($data as $item) {
    if ($jenis == 'barang') {
        fputcsv($output, [$no++, $item['kode_barang'], $item['nama_barang'], $item['nama_kategori'], $item['nama_supplier'], $item['satuan'], $item['stok'], $item['harga_beli'], $item['harga_jual'], $item['stok_minimum']]);
    } elseif ($jenis == 'transaksi_masuk') {
        fputcsv($output, [$no++, $item['kode_transaksi'], $item['nama_barang'], $item['nama_supplier'], $item['jumlah'], $item['harga_satuan'], $item['total_harga'], date('d/m/Y', strtotime($item['tanggal_masuk']))]);
    } else {
        fputcsv($output, [$no++, $item['kode_transaksi'], $item['nama_barang'], $item['jumlah'], $item['harga_satuan'], $item['total_harga'], $item['tujuan'], date('d/m/Y', strtotime($item['tanggal_keluar']))]);
    }
}

fclose($output);
exit;
