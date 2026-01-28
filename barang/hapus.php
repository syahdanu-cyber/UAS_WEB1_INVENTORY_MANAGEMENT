<?php
// barang/hapus.php
require_once __DIR__ . '/../auth/session_check.php';
requireAdmin(); // Hanya admin yang bisa hapus
require_once __DIR__ . '/../config/database.php';

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: barang.php?error=invalid');
    exit();
}

try {
    $db = getDB();
    
    // Check if barang exists
    $stmt = $db->prepare("SELECT nama_barang FROM barang WHERE id = ?");
    $stmt->execute([$id]);
    $barang = $stmt->fetch();
    
    if (!$barang) {
        header('Location: barang.php?error=notfound');
        exit();
    }
    
    // Delete barang
    $stmt = $db->prepare("DELETE FROM barang WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header('Location: barang.php?success=delete');
    } else {
        header('Location: barang.php?error=delete');
    }
    
} catch (PDOException $e) {
    // Jika ada constraint error (foreign key), redirect dengan error
    if ($e->getCode() == 23000) {
        header('Location: barang.php?error=constraint');
    } else {
        header('Location: barang.php?error=delete');
    }
}

exit();
