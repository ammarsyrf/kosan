-- Migration: Expand invoice status enum to include overdue and cancelled
ALTER TABLE `invoices` 
MODIFY COLUMN `status` ENUM('draft', 'unpaid', 'partially_paid', 'paid', 'void', 'overdue', 'cancelled') NOT NULL DEFAULT 'unpaid';

