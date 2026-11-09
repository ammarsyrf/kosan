-- Migration: M6 Finance and Maintenance Management
-- Expands expense_categories, expenses, maintenance_requests, and maintenance_updates

-- 1. Add is_active and updated_at to expense_categories
ALTER TABLE `expense_categories`
ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `description`,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 2. Add deleted_at to expenses for audit trail / soft-deletion
ALTER TABLE `expenses`
ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;

-- 3. Update maintenance_requests priority and status enums to match PRD specifications
ALTER TABLE `maintenance_requests`
MODIFY COLUMN `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
MODIFY COLUMN `status` ENUM('submitted', 'reviewed', 'in_progress', 'waiting', 'resolved', 'rejected') NOT NULL DEFAULT 'submitted';

-- 4. Add is_internal to maintenance_updates for internal note separation
ALTER TABLE `maintenance_updates`
ADD COLUMN `is_internal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `notes`;
