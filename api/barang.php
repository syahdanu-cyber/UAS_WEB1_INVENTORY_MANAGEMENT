<?php
// API CRUD Barang
require_once '../config/database.php';
header('Content-Type: application/json');


$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all barang
        $stmt = $db->query('SELECT * FROM barang');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;
    case 'POST':
        // Create barang
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'] ?? '';
        $stok = $input['stok'] ?? 0;
        $harga = $input['harga'] ?? 0;
        $stmt = $db->prepare("INSERT INTO barang (nama, stok, harga) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $stok, $harga])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
        }
        break;
    case 'PUT':
        // Update barang
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $nama = $input['nama'] ?? '';
        $stok = $input['stok'] ?? 0;
        $harga = $input['harga'] ?? 0;
        $query = "UPDATE barang SET nama='$nama', stok=$stok, harga=$harga WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        break;
    case 'DELETE':
        // Delete barang
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $query = "DELETE FROM barang WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}
