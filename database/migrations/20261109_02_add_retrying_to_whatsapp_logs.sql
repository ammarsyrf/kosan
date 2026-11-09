-- Migration: Expand whatsapp_logs status enum and seed reminder settings
ALTER TABLE `whatsapp_logs`
MODIFY COLUMN `status` ENUM('pending', 'sent', 'failed', 'retrying') NOT NULL DEFAULT 'pending';

-- Seed default reminder schedule settings if not exist
INSERT INTO `settings` (`key`, `value`, `group`, `description`)
VALUES
    ('fonnte_enabled', '0', 'notification', 'Aktifkan integrasi Fonnte WhatsApp (1 = aktif, 0 = mati)'),
    ('reminder_h7_active', '1', 'notification', 'Aktifkan reminder H-7 sebelum jatuh tempo'),
    ('reminder_h3_active', '1', 'notification', 'Aktifkan reminder H-3 sebelum jatuh tempo'),
    ('reminder_h1_active', '1', 'notification', 'Aktifkan reminder H-1 sebelum jatuh tempo'),
    ('reminder_due_active', '1', 'notification', 'Aktifkan reminder pada hari H jatuh tempo'),
    ('reminder_overdue_active', '1', 'notification', 'Aktifkan reminder keterlambatan H+1 setelah jatuh tempo')
ON DUPLICATE KEY UPDATE `updated_at` = NOW();
