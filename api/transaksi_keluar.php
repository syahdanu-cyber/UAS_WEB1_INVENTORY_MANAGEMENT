<?php
// API CRUD Transaksi Keluar
require_once '../config/database.php';
header('Content-Type: application/json');


$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $db->query('SELECT * FROM transaksi_keluar');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $barang_id = $input['barang_id'] ?? 0;
        $jumlah = $input['jumlah'] ?? 0;
        $tanggal_keluar = $input['tanggal_keluar'] ?? date('Y-m-d');
        $stmt = $db->prepare("INSERT INTO transaksi_keluar (barang_id, jumlah, tanggal_keluar) VALUES (?, ?, ?)");
        if ($stmt->execute([$barang_id, $jumlah, $tanggal_keluar])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
        }
        break;
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $barang_id = $input['barang_id'] ?? 0;
        $jumlah = $input['jumlah'] ?? 0;
        $tanggal_keluar = $input['tanggal_keluar'] ?? date('Y-m-d');
        $stmt = $db->prepare("UPDATE transaksi_keluar SET barang_id=?, jumlah=?, tanggal_keluar=? WHERE id=?");
        if ($stmt->execute([$barang_id, $jumlah, $tanggal_keluar, $id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        break;
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM transaksi_keluar WHERE id=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}
