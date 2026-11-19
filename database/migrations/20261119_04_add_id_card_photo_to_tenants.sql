-- Add ID card photo upload path to tenants table
ALTER TABLE `tenants` 
ADD COLUMN `id_card_photo` VARCHAR(255) NULL AFTER `id_card_number`;
