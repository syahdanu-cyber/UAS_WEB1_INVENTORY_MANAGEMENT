<?php
// auth/logout_updated.php

/**
 * Logout Process dengan Cookie Cleanup
 */

require_once __DIR__ . '/session_manager.php';

// Log activity sebelum logout
if (SessionManager::isLoggedIn()) {
    SessionManager::logActivity($_SESSION['user_id'], 'logout', 'User logged out');
}

// Destroy session dan hapus semua cookie
SessionManager::destroy();

// Redirect ke login page
header('Location: login.php?logged_out=1');
exit();
