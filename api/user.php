<?php
// API CRUD User (contoh sederhana)
require_once '../config/database.php';
header('Content-Type: application/json');


$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $db->query('SELECT id, username, role FROM users');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = password_hash($input['password'] ?? '', PASSWORD_DEFAULT);
        $nama_lengkap = $input['nama_lengkap'] ?? '';
        $role = $input['role'] ?? 'user';
        $stmt = $db->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $password, $nama_lengkap, $role])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
        }
        break;
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $username = $input['username'] ?? '';
        $role = $input['role'] ?? 'user';
        $stmt = $db->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        if ($stmt->execute([$username, $role, $id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        break;
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM users WHERE id=?");
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
