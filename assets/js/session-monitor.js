/**
 * Session Manager - Frontend
 * Library untuk mengelola dan memonitor session di frontend
 */

class SessionMonitor {
    constructor(options = {}) {
        this.options = {
            checkInterval: options.checkInterval || 60000, // Check setiap 60 detik
            warningTime: options.warningTime || 300, // Warning 5 menit sebelum timeout
            apiEndpoint: options.apiEndpoint || '/auth/session_api.php',
            onSessionExpired: options.onSessionExpired || this.defaultSessionExpired,
            onSessionWarning: options.onSessionWarning || this.defaultSessionWarning,
            autoExtend: options.autoExtend !== false // Default true
        };
        
        this.checkTimer = null;
        this.warningShown = false;
        
        this.init();
    }
    
    /**
     * Initialize session monitor
     */
    init() {
        // Check session immediately
        this.checkSession();
        
        // Set interval check
        this.startMonitoring();
        
        // Monitor user activity untuk auto-extend
        if (this.options.autoExtend) {
            this.monitorUserActivity();
        }
        
        // Tambahkan event listener untuk visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.checkSession();
            }
        });
    }
    
    /**
     * Start monitoring session
     */
    startMonitoring() {
        if (this.checkTimer) {
            clearInterval(this.checkTimer);
        }
        
        this.checkTimer = setInterval(() => {
            this.checkSession();
        }, this.options.checkInterval);
    }
    
    /**
     * Stop monitoring
     */
    stopMonitoring() {
        if (this.checkTimer) {
            clearInterval(this.checkTimer);
            this.checkTimer = null;
        }
    }
    
    /**
     * Check session validity via API
     */
    async checkSession() {
        try {
            const response = await fetch(this.options.apiEndpoint + '?action=check', {
                method: 'GET',
                credentials: 'include', // Penting untuk mengirim cookie
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.valid) {
                this.handleValidSession(data.session_info);
            } else {
                this.handleInvalidSession(data);
            }
            
        } catch (error) {
            console.error('Session check error:', error);
        }
    }
    
    /**
     * Handle valid session
     */
    handleValidSession(sessionInfo) {
        const timeRemaining = sessionInfo.time_remaining;
        
        // Check if warning should be shown
        if (timeRemaining <= this.options.warningTime && !this.warningShown) {
            this.warningShown = true;
            this.options.onSessionWarning(timeRemaining);
        }
        
        // Reset warning if time is extended
        if (timeRemaining > this.options.warningTime && this.warningShown) {
            this.warningShown = false;
        }
        
        // Update UI if needed
        this.updateSessionUI(sessionInfo);
    }
    
    /**
     * Handle invalid session
     */
    handleInvalidSession(data) {
        this.stopMonitoring();
        this.options.onSessionExpired(data);
    }
    
    /**
     * Extend session manually
     */
    async extendSession() {
        try {
            const response = await fetch(this.options.apiEndpoint + '?action=extend', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.warningShown = false;
                console.log('Session extended');
                return true;
            }
            
            return false;
            
        } catch (error) {
            console.error('Extend session error:', error);
            return false;
        }
    }
    
    /**
     * Send heartbeat to keep session alive
     */
    async sendHeartbeat() {
        try {
            const response = await fetch(this.options.apiEndpoint + '?action=heartbeat', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            return data.alive;
            
        } catch (error) {
            console.error('Heartbeat error:', error);
            return false;
        }
    }
    
    /**
     * Monitor user activity
     */
    monitorUserActivity() {
        let activityTimer = null;
        const activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart'];
        
        const handleActivity = () => {
            if (activityTimer) {
                clearTimeout(activityTimer);
            }
            
            // Extend session setelah user activity
            activityTimer = setTimeout(() => {
                this.sendHeartbeat();
            }, 5000); // Delay 5 detik setelah aktivitas
        };
        
        activityEvents.forEach(event => {
            document.addEventListener(event, handleActivity, { passive: true });
        });
    }
    
    /**
     * Update session UI
     */
    updateSessionUI(sessionInfo) {
        // Update session timer display jika ada
        const timerElement = document.getElementById('session-timer');
        if (timerElement) {
            const minutes = Math.floor(sessionInfo.time_remaining / 60);
            const seconds = sessionInfo.time_remaining % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        // Update user info jika ada
        const userInfoElement = document.getElementById('user-info');
        if (userInfoElement) {
            userInfoElement.textContent = sessionInfo.nama_lengkap;
        }
    }
    
    /**
     * Default session expired handler
     */
    defaultSessionExpired(data) {
        alert('Sesi Anda telah berakhir. Silakan login kembali.');
        window.location.href = data.redirect || '/auth/login.php';
    }
    
    /**
     * Default session warning handler
     */
    defaultSessionWarning(timeRemaining) {
        const minutes = Math.floor(timeRemaining / 60);
        
        if (confirm(`Sesi Anda akan berakhir dalam ${minutes} menit. Perpanjang sesi?`)) {
            this.extendSession();
        }
    }
    
    /**
     * Get current session info
     */
    async getSessionInfo() {
        try {
            const response = await fetch(this.options.apiEndpoint + '?action=info', {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                return data.data;
            }
            
            return null;
            
        } catch (error) {
            console.error('Get session info error:', error);
            return null;
        }
    }
    
    /**
     * Destroy session monitor
     */
    destroy() {
        this.stopMonitoring();
    }
}

/**
 * Cookie Helper Functions
 */
const CookieManager = {
    /**
     * Get cookie value
     */
    get(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    },
    
    /**
     * Set cookie
     */
    set(name, value, days = 7, options = {}) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        
        let cookie = `${name}=${value}; expires=${date.toUTCString()}; path=${options.path || '/'}`;
        
        if (options.domain) {
            cookie += `; domain=${options.domain}`;
        }
        
        if (options.secure) {
            cookie += '; secure';
        }
        
        if (options.sameSite) {
            cookie += `; samesite=${options.sameSite}`;
        }
        
        document.cookie = cookie;
    },
    
    /**
     * Delete cookie
     */
    delete(name, options = {}) {
        this.set(name, '', -1, options);
    },
    
    /**
     * Check if cookie exists
     */
    exists(name) {
        return this.get(name) !== null;
    }
};

/**
 * Session Storage Helper (untuk data yang tidak perlu di-cookie)
 */
const SessionStorage = {
    /**
     * Set item
     */
    set(key, value) {
        try {
            sessionStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            console.error('SessionStorage set error:', e);
            return false;
        }
    },
    
    /**
     * Get item
     */
    get(key, defaultValue = null) {
        try {
            const item = sessionStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.error('SessionStorage get error:', e);
            return defaultValue;
        }
    },
    
    /**
     * Remove item
     */
    remove(key) {
        try {
            sessionStorage.removeItem(key);
            return true;
        } catch (e) {
            console.error('SessionStorage remove error:', e);
            return false;
        }
    },
    
    /**
     * Clear all
     */
    clear() {
        try {
            sessionStorage.clear();
            return true;
        } catch (e) {
            console.error('SessionStorage clear error:', e);
            return false;
        }
    }
};

// Export untuk digunakan di module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SessionMonitor, CookieManager, SessionStorage };
}
