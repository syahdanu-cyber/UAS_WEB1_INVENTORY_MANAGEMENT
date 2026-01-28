<?php
require_once __DIR__ . '/../auth/session_check.php';
requireAdmin(); // Hanya admin yang bisa hapus
require_once __DIR__ . '/../config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: supplier.php?error=invalid'); exit(); }
try {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM supplier WHERE id = ?");
    if ($stmt->execute([$id])) {
        header('Location: supplier.php?success=delete');
    } else {
        header('Location: supplier.php?error=delete');
    }
} catch (PDOException $e) {
    header('Location: supplier.php?error=constraint');
}
exit();
