<?php
// API CRUD Supplier
require_once '../config/database.php';
header('Content-Type: application/json');


$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $db->query('SELECT * FROM supplier');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'] ?? '';
        $alamat = $input['alamat'] ?? '';
        $telepon = $input['telepon'] ?? '';
        $stmt = $db->prepare("INSERT INTO supplier (nama, alamat, telepon) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $alamat, $telepon])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
        }
        break;
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $nama = $input['nama'] ?? '';
        $alamat = $input['alamat'] ?? '';
        $telepon = $input['telepon'] ?? '';
        $stmt = $db->prepare("UPDATE supplier SET nama=?, alamat=?, telepon=? WHERE id=?");
        if ($stmt->execute([$nama, $alamat, $telepon, $id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        break;
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM supplier WHERE id=?");
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
