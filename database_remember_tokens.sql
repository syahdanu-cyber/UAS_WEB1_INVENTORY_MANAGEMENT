-- SQL untuk tabel remember_tokens
-- Tambahkan ini ke database yang sudah ada

-- Tabel untuk menyimpan remember me tokens
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `selector` (`selector`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update tabel activity_log untuk menambahkan user_agent
ALTER TABLE `activity_log` 
ADD COLUMN `user_agent` VARCHAR(255) NULL AFTER `ip_address`;

-- Index untuk performa
CREATE INDEX idx_expires_at ON remember_tokens(expires_at);
CREATE INDEX idx_selector ON remember_tokens(selector);

-- Event untuk cleanup token yang expired (opsional, memerlukan MySQL Event Scheduler)
-- Aktifkan dengan: SET GLOBAL event_scheduler = ON;

DELIMITER $$

CREATE EVENT IF NOT EXISTS cleanup_expired_tokens
ON SCHEDULE EVERY 1 DAY
DO BEGIN
    DELETE FROM remember_tokens WHERE expires_at < NOW();
END$$

DELIMITER ;
