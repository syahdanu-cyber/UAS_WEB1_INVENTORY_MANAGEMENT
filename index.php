<?php
// index.php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/index.php');
    exit();
}

// Jika belum login, redirect ke login page
header('Location: auth/login.php');
exit();
