-- Add pharmacy_id column to admin_users table for pharmacy assignment
-- This allows pharmacy_admin users to be assigned to specific pharmacies

ALTER TABLE `admin_users` 
ADD COLUMN `pharmacy_id` int(11) DEFAULT NULL AFTER `phone`,
ADD INDEX `idx_admin_users_pharmacy` (`pharmacy_id`);

-- Add foreign key constraint to ensure data integrity
ALTER TABLE `admin_users`
ADD CONSTRAINT `fk_admin_users_pharmacy` 
FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Update existing pharmacy_admin users to be assigned to pharmacy_id = 1 (Keza Pharma)
-- This assumes pharmacy_id = 1 exists in the pharmacies table
UPDATE `admin_users` 
SET `pharmacy_id` = 1 
WHERE `role` = 'pharmacy_admin' AND `pharmacy_id` IS NULL;

