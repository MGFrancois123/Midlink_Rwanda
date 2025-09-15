-- MdLink Rwanda - Cleanup to keep ONLY User Management related tables
-- Safe plan:
-- 1) BACKUP your database BEFORE running this script!
--    Example (PowerShell):
--    & "C:/xampp/mysql/bin/mysqldump.exe" -u root mdlink2 > C:/backup/mdlink2_before_user_mgmt_cleanup.sql
-- 2) Run this script against database mdlink2
-- 3) Verify application

-- What we KEEP (required by User Management section only):
--   admin_users        -> Add User
--   pharmacies         -> Create/Manage Pharmacy
--   audit_logs         -> User Activity
--   reports            -> User Reports

-- What we DROP (not required for the above features):
--   category, medicines, ml_predictions, ml_training_data,
--   payments, prescriptions, sms_notifications,
--   special_pharmacy_requests, stock_movements

SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables only if they exist
DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `medicines`;
DROP TABLE IF EXISTS `ml_predictions`;
DROP TABLE IF EXISTS `ml_training_data`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `prescriptions`;
DROP TABLE IF EXISTS `sms_notifications`;
DROP TABLE IF EXISTS `special_pharmacy_requests`;
DROP TABLE IF EXISTS `stock_movements`;

SET FOREIGN_KEY_CHECKS = 1;

-- Optional: tighten indexes on kept tables (no-op if already present)
ALTER TABLE `admin_users`
  ADD INDEX `idx_admin_users_role` (`role`);

ALTER TABLE `pharmacies`
  ADD INDEX `idx_pharmacies_name` (`name`);

-- Note: We intentionally do NOT touch data in kept tables.
-- If you want to prune sample rows, do it explicitly here.

-- Verification queries (optional):
-- SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE();
-- SHOW TABLES;



