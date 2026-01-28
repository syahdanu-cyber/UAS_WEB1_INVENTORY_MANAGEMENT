<?php
// auth/session_api.php

/**
 * API untuk pengecekan session dari frontend
 * Endpoint ini dapat dipanggil via AJAX untuk validasi session
 */

header('Content-Type: application/json');

require_once __DIR__ . '/session_manager.php';

// CORS headers (sesuaikan dengan kebutuhan)
header('Access-Control-Allow-Credentials: true');

// Tangani request
$action = $_GET['action'] ?? $_POST['action'] ?? 'check';

switch ($action) {
    case 'check':
        checkSession();
        break;
        
    case 'extend':
        extendSession();
        break;
        
    case 'info':
        getSessionInfo();
        break;
        
    case 'heartbeat':
        heartbeat();
        break;
        
    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

/**
 * Check session validity
 */
function checkSession() {
    $isValid = SessionManager::validate();
    
    if ($isValid) {
        jsonResponse([
            'valid' => true,
            'message' => 'Session is valid',
            'session_info' => SessionManager::getSessionInfo()
        ]);
    } else {
        jsonResponse([
            'valid' => false,
            'message' => 'Session is invalid or expired',
            'redirect' => '/auth/login.php'
        ], 401);
    }
}

/**
 * Extend session (update last activity)
 */
function extendSession() {
    if (SessionManager::validate()) {
        $_SESSION['last_activity'] = time();
        
        jsonResponse([
            'success' => true,
            'message' => 'Session extended',
            'time_remaining' => SESSION_TIMEOUT
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Session invalid',
            'redirect' => '/auth/login.php'
        ], 401);
    }
}

/**
 * Get detailed session info
 */
function getSessionInfo() {
    if (!SessionManager::validate()) {
        jsonResponse([
            'error' => 'Not authenticated',
            'redirect' => '/auth/login.php'
        ], 401);
        return;
    }
    
    $info = SessionManager::getSessionInfo();
    
    jsonResponse([
        'success' => true,
        'data' => $info
    ]);
}

/**
 * Heartbeat - keep session alive
 */
function heartbeat() {
    if (SessionManager::validate()) {
        $_SESSION['last_activity'] = time();
        
        jsonResponse([
            'alive' => true,
            'timestamp' => time(),
            'time_remaining' => SESSION_TIMEOUT - (time() - $_SESSION['last_activity'])
        ]);
    } else {
        jsonResponse([
            'alive' => false,
            'message' => 'Session expired',
            'redirect' => '/auth/login.php'
        ], 401);
    }
}

/**
 * Helper function untuk JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}
