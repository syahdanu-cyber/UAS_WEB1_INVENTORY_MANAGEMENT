<?php
// dashboard/dashboard_with_session_monitor.php

require_once __DIR__ . '/../auth/session_manager.php';

// Require login
SessionManager::requireLogin();

// Get session info
$sessionInfo = SessionManager::getSessionInfo();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Inventory</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .session-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 14px;
            z-index: 1000;
        }
        .session-timer {
            color: #28a745;
            font-weight: bold;
        }
        .session-timer.warning {
            color: #ffc107;
        }
        .session-timer.danger {
            color: #dc3545;
        }
        .session-warning-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .session-warning-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
        }
        .session-warning-content h3 {
            margin-top: 0;
            color: #dc3545;
        }
        .session-warning-buttons {
            margin-top: 20px;
        }
        .session-warning-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-extend {
            background: #28a745;
            color: white;
        }
        .btn-logout {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Session Info Display -->
    <div class="session-info">
        <div>
            <strong id="user-info"><?= htmlspecialchars($sessionInfo['nama_lengkap']) ?></strong>
            <span class="badge badge-<?= $sessionInfo['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                <?= ucfirst($sessionInfo['role']) ?>
            </span>
        </div>
        <div>
            Sisa waktu: <span id="session-timer" class="session-timer">--:--</span>
        </div>
    </div>

    <!-- Session Warning Modal -->
    <div id="sessionWarningModal" class="session-warning-modal">
        <div class="session-warning-content">
            <h3>⚠️ Sesi Akan Berakhir</h3>
            <p>Sesi Anda akan berakhir dalam <strong id="warningTime">5</strong> menit.</p>
            <p>Apakah Anda ingin memperpanjang sesi?</p>
            <div class="session-warning-buttons">
                <button class="btn-extend" onclick="extendSession()">Perpanjang Sesi</button>
                <button class="btn-logout" onclick="logout()">Logout</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Dashboard</h1>
        <p>Selamat datang, <?= htmlspecialchars($sessionInfo['nama_lengkap']) ?>!</p>
        
        <!-- Your dashboard content here -->
        <div class="dashboard-content">
            <!-- Content -->
        </div>
    </div>

    <!-- Include Session Monitor -->
    <script src="../assets/js/session-monitor.js"></script>
    <script>
        // Initialize Session Monitor
        const sessionMonitor = new SessionMonitor({
            checkInterval: 30000, // Check setiap 30 detik
            warningTime: 300, // Warning 5 menit sebelum timeout
            autoExtend: true, // Auto extend saat ada aktivitas
            
            // Custom session expired handler
            onSessionExpired: function(data) {
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                window.location.href = data.redirect || '/auth/login.php';
            },
            
            // Custom session warning handler
            onSessionWarning: function(timeRemaining) {
                showSessionWarning(timeRemaining);
            }
        });

        // Show session warning modal
        function showSessionWarning(timeRemaining) {
            const minutes = Math.floor(timeRemaining / 60);
            document.getElementById('warningTime').textContent = minutes;
            document.getElementById('sessionWarningModal').style.display = 'flex';
        }

        // Hide session warning modal
        function hideSessionWarning() {
            document.getElementById('sessionWarningModal').style.display = 'none';
        }

        // Extend session
        async function extendSession() {
            const success = await sessionMonitor.extendSession();
            if (success) {
                hideSessionWarning();
                alert('Sesi berhasil diperpanjang!');
            }
        }

        // Logout
        function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = '../auth/logout_updated.php';
            }
        }

        // Update timer display
        setInterval(async function() {
            const info = await sessionMonitor.getSessionInfo();
            if (info && info.time_remaining) {
                const minutes = Math.floor(info.time_remaining / 60);
                const seconds = info.time_remaining % 60;
                const timerElement = document.getElementById('session-timer');
                
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                // Change color based on time remaining
                timerElement.className = 'session-timer';
                if (info.time_remaining <= 300) { // 5 minutes
                    timerElement.className = 'session-timer danger';
                } else if (info.time_remaining <= 600) { // 10 minutes
                    timerElement.className = 'session-timer warning';
                }
            }
        }, 1000);

        // Keyboard shortcut untuk extend session (Ctrl+E)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                sessionMonitor.extendSession();
            }
        });

        // Log session info on load (for debugging)
        sessionMonitor.getSessionInfo().then(info => {
            console.log('Session Info:', info);
        });
    </script>
</body>
</html>
