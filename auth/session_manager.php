<?php
// auth/session_manager.php

/**
 * Session Manager dengan Cookie Integration
 * Mengelola sesi pengguna dengan cookie yang aman
 */

require_once __DIR__ . '/../config/cookie_config.php';
require_once __DIR__ . '/../config/database.php';

class SessionManager {
    
    /**
     * Inisialisasi session
     */
    public static function init() {
        configureSecureSession();
    }
    
    /**
     * Cek apakah user sudah login
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['username']) &&
               isset($_SESSION['session_token']);
    }
    
    /**
     * Cek timeout session
     */
    public static function checkTimeout() {
        // Cek timeout inaktivitas
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            
            if ($elapsed > SESSION_TIMEOUT) {
                self::destroy();
                return false;
            }
        }
        
        // Cek timeout absolut
        if (isset($_SESSION['login_time'])) {
            $elapsed = time() - $_SESSION['login_time'];
            
            if ($elapsed > SESSION_ABSOLUTE_TIMEOUT) {
                self::destroy();
                return false;
            }
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Set login session
     */
    public static function setLogin($user, $rememberMe = false) {
        // Generate session token
        $sessionToken = bin2hex(random_bytes(32));
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        $_SESSION['session_token'] = $sessionToken;
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Set cookie untuk session validation
        setSecureCookie('session_validation', $sessionToken, 0);
        
        // Handle remember me
        if ($rememberMe) {
            self::setRememberMe($user['id']);
        }
        
        // Regenerate session ID untuk keamanan
        session_regenerate_id(true);
        
        // Log activity
        self::logActivity($user['id'], 'login', 'User logged in successfully');
        
        return true;
    }
    
    /**
     * Set remember me cookie
     */
    private static function setRememberMe($userId) {
        $token = bin2hex(random_bytes(32));
        $selector = bin2hex(random_bytes(16));
        $hashedToken = hash('sha256', $token);
        
        // Simpan ke database
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO remember_tokens (user_id, selector, token, expires_at) 
                VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
                ON DUPLICATE KEY UPDATE 
                    selector = VALUES(selector),
                    token = VALUES(token),
                    expires_at = VALUES(expires_at)
            ");
            $stmt->execute([$userId, $selector, $hashedToken]);
            
            // Set cookie (selector:token)
            $cookieValue = $selector . ':' . $token;
            setSecureCookie(
                COOKIE_REMEMBER_TOKEN, 
                $cookieValue, 
                time() + COOKIE_REMEMBER_LIFETIME,
                ['httponly' => true]
            );
            
            return true;
        } catch (PDOException $e) {
            error_log("Remember me error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check remember me cookie dan auto-login
     */
    public static function checkRememberMe() {
        if (!hasSecureCookie(COOKIE_REMEMBER_TOKEN)) {
            return false;
        }
        
        $cookieValue = getSecureCookie(COOKIE_REMEMBER_TOKEN);
        $parts = explode(':', $cookieValue);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        list($selector, $token) = $parts;
        $hashedToken = hash('sha256', $token);
        
        try {
            $db = getDB();
            
            // Cari token di database
            $stmt = $db->prepare("
                SELECT rt.*, u.* 
                FROM remember_tokens rt
                JOIN users u ON rt.user_id = u.id
                WHERE rt.selector = ? AND rt.expires_at > NOW()
            ");
            $stmt->execute([$selector]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                deleteSecureCookie(COOKIE_REMEMBER_TOKEN);
                return false;
            }
            
            // Verifikasi token
            if (!hash_equals($result['token'], $hashedToken)) {
                deleteSecureCookie(COOKIE_REMEMBER_TOKEN);
                return false;
            }
            
            // Token valid, login user
            self::setLogin($result, true);
            
            return true;
        } catch (PDOException $e) {
            error_log("Check remember me error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validasi session dengan cookie
     */
    public static function validate() {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        // Validasi session token dengan cookie
        $cookieToken = getSecureCookie('session_validation');
        if (!$cookieToken || $cookieToken !== $_SESSION['session_token']) {
            self::destroy();
            return false;
        }
        
        // Validasi IP address (opsional, bisa dimatikan jika ada masalah)
        // if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        //     self::destroy();
        //     return false;
        // }
        
        // Validasi user agent
        if (isset($_SESSION['user_agent']) && 
            $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            self::destroy();
            return false;
        }
        
        // Check timeout
        if (!self::checkTimeout()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Destroy session dan hapus semua cookie
     */
    public static function destroy() {
        // Hapus remember token dari database
        if (self::isLoggedIn() && hasSecureCookie(COOKIE_REMEMBER_TOKEN)) {
            try {
                $db = getDB();
                $stmt = $db->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            } catch (PDOException $e) {
                error_log("Delete remember token error: " . $e->getMessage());
            }
        }
        
        // Hapus semua session variables
        $_SESSION = array();
        
        // Hapus session cookie
        if (isset($_COOKIE[session_name()])) {
            deleteSecureCookie(session_name());
        }
        
        // Hapus custom cookies
        deleteSecureCookie('session_validation');
        deleteSecureCookie(COOKIE_REMEMBER_TOKEN);
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Check role
     */
    public static function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Check if admin
     */
    public static function isAdmin() {
        return self::hasRole('admin');
    }
    
    /**
     * Check if staff
     */
    public static function isStaff() {
        return self::hasRole('staff');
    }
    
    /**
     * Require login
     */
    public static function requireLogin() {
        if (!self::validate()) {
            header('Location: /auth/login.php');
            exit();
        }
    }
    
    /**
     * Require admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: /dashboard/index.php?error=access_denied');
            exit();
        }
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get session info for frontend
     */
    public static function getSessionInfo() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'nama_lengkap' => $_SESSION['nama_lengkap'],
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email'],
            'last_activity' => $_SESSION['last_activity'],
            'time_remaining' => SESSION_TIMEOUT - (time() - $_SESSION['last_activity'])
        ];
    }
    
    /**
     * Log activity
     */
    private static function logActivity($userId, $action, $details = '') {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO activity_log (user_id, action, details, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log("Log activity error: " . $e->getMessage());
        }
    }
}

// Initialize session
SessionManager::init();

// Check remember me jika belum login
if (!SessionManager::isLoggedIn()) {
    SessionManager::checkRememberMe();
}
