-- Additional tables for MdLink Rwanda Pharmacy Management System
-- Run this after your existing schema

-- Refund Requests Table
CREATE TABLE `refund_requests` (
  `refund_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `admin_id` int(11) DEFAULT NULL,
  `request_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL,
  PRIMARY KEY (`refund_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_refund_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_refund_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_refund_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Failed Payments Table
CREATE TABLE `failed_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('MOBILE_MONEY','BANK_TRANSFER','CREDIT_CARD','CASH') NOT NULL,
  `status` enum('RETRY_PENDING','PERMANENTLY_FAILED','RESOLVED') DEFAULT 'RETRY_PENDING',
  `failure_reason` text NOT NULL,
  `retry_count` int(11) DEFAULT 0,
  `last_retry` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` datetime DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_failed_payment_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_failed_payment_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Daily Revenue Table
CREATE TABLE `daily_revenue` (
  `revenue_id` int(11) NOT NULL AUTO_INCREMENT,
  `revenue_date` date NOT NULL,
  `total_sales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_refunds` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mobile_money` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank_transfer` decimal(10,2) NOT NULL DEFAULT 0.00,
  `credit_card` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transaction_count` int(11) NOT NULL DEFAULT 0,
  `pharmacy_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`revenue_id`),
  UNIQUE KEY `unique_daily_revenue` (`revenue_date`, `pharmacy_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_daily_revenue_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Outstanding Balances Table
CREATE TABLE `outstanding_balances` (
  `balance_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `outstanding_amount` decimal(10,2) NOT NULL,
  `status` enum('CURRENT','OVERDUE','PAID') DEFAULT 'CURRENT',
  `due_date` date NOT NULL,
  `last_payment_date` date DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pharmacy_id` int(11) NOT NULL,
  PRIMARY KEY (`balance_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_outstanding_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_outstanding_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Branch Reconciliation Table
CREATE TABLE `branch_reconciliation` (
  `reconciliation_id` int(11) NOT NULL AUTO_INCREMENT,
  `reconciliation_date` date NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `expected_amount` decimal(10,2) NOT NULL,
  `actual_amount` decimal(10,2) NOT NULL,
  `variance` decimal(10,2) NOT NULL,
  `status` enum('PENDING','RECONCILED','DISCREPANCY') DEFAULT 'PENDING',
  `notes` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pharmacy_id` int(11) NOT NULL,
  PRIMARY KEY (`reconciliation_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_reconciliation_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reconciliation_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Transaction Exceptions Table
CREATE TABLE `transaction_exceptions` (
  `exception_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) NOT NULL,
  `exception_type` enum('AMOUNT_MISMATCH','DUPLICATE_TRANSACTION','INVALID_MEDICINE','SYSTEM_ERROR','MANUAL_REVIEW') NOT NULL,
  `severity` enum('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL,
  `status` enum('OPEN','INVESTIGATING','RESOLVED','ESCALATED') DEFAULT 'OPEN',
  `description` text NOT NULL,
  `exposure_amount` decimal(10,2) DEFAULT 0.00,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` datetime DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL,
  PRIMARY KEY (`exception_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_exception_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_exception_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Suspicious Transactions Table
CREATE TABLE `suspicious_transactions` (
  `suspicious_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) NOT NULL,
  `pattern_type` enum('UNUSUAL_AMOUNT','FREQUENT_PURCHASES','OFF_HOURS','SUSPICIOUS_MEDICINE','MULTIPLE_FAILURES') NOT NULL,
  `risk_score` int(11) NOT NULL DEFAULT 0,
  `status` enum('OPEN','INVESTIGATING','RESOLVED','ESCALATED') DEFAULT 'OPEN',
  `description` text NOT NULL,
  `exposure_amount` decimal(10,2) DEFAULT 0.00,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` datetime DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL,
  PRIMARY KEY (`suspicious_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_suspicious_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_suspicious_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Audit Logs Table
CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `object_type` varchar(100) NOT NULL,
  `object_id` int(11) DEFAULT NULL,
  `status` enum('SUCCESS','FAILED') NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pharmacy_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_audit_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_audit_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Security Logs Table
CREATE TABLE `security_logs` (
  `security_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `severity` enum('INFO','WARNING','MEDIUM','HIGH','CRITICAL') NOT NULL,
  `description` text NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pharmacy_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`security_id`),
  KEY `admin_id` (`admin_id`),
  KEY `pharmacy_id` (`pharmacy_id`),
  CONSTRAINT `fk_security_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_security_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for Ineza Pharmacy (pharmacy_id = 1)
INSERT INTO `refund_requests` (`patient_name`, `patient_phone`, `medicine_id`, `quantity`, `unit_price`, `total_amount`, `refund_amount`, `reason`, `status`, `admin_id`, `request_date`, `notes`, `pharmacy_id`) VALUES
('Jean Pierre Uwimana', '+250 788 123 456', 181, 2, 0.15, 0.30, 0.30, 'Patient allergic reaction', 'PENDING', 1, '2024-01-15 14:30:00', 'Patient experienced mild rash after taking medication', 1),
('Marie Claire Niyonsaba', '+250 789 987 654', 182, 1, 0.25, 0.25, 0.25, 'Wrong medication dispensed', 'APPROVED', 2, '2024-01-15 16:45:00', 'Staff error - dispensed wrong strength', 1),
('Emmanuel Ndayisaba', '+250 787 456 789', 183, 3, 0.20, 0.60, 0.60, 'Expired medication', 'REJECTED', 1, '2024-01-14 11:20:00', 'Medication was within expiry date when dispensed', 1),
('Ange Uwase', '+250 786 321 654', 184, 1, 0.45, 0.45, 0.45, 'Side effects', 'PENDING', 1, '2024-01-14 09:15:00', 'Patient reported severe stomach upset', 1),
('David Kwizera', '+250 785 147 258', 185, 2, 0.30, 0.60, 0.30, 'Partial refund - unused portion', 'APPROVED', 2, '2024-01-13 15:30:00', 'Patient only used 1 tablet, returning unused portion', 1);

INSERT INTO `failed_payments` (`transaction_id`, `patient_name`, `patient_phone`, `amount`, `payment_method`, `status`, `failure_reason`, `retry_count`, `admin_id`, `pharmacy_id`) VALUES
('PAY-001', 'Jean Bosco Nkurunziza', '+250 788 111 222', 15.50, 'MOBILE_MONEY', 'RETRY_PENDING', 'Insufficient funds', 2, 1, 1),
('PAY-002', 'Marie Uwimana', '+250 789 333 444', 8.75, 'BANK_TRANSFER', 'PERMANENTLY_FAILED', 'Invalid account number', 5, 2, 1),
('PAY-003', 'Paul Nkurunziza', '+250 787 555 666', 22.30, 'CREDIT_CARD', 'RETRY_PENDING', 'Card declined', 1, 1, 1),
('PAY-004', 'Grace Mukamana', '+250 786 777 888', 5.25, 'MOBILE_MONEY', 'RESOLVED', 'Network timeout', 3, 2, 1),
('PAY-005', 'John Niyonsaba', '+250 785 999 000', 12.80, 'BANK_TRANSFER', 'RETRY_PENDING', 'Bank server error', 2, 1, 1),
('PAY-006', 'Alice Uwase', '+250 784 111 333', 18.90, 'CREDIT_CARD', 'PERMANENTLY_FAILED', 'Card expired', 4, 2, 1);

INSERT INTO `daily_revenue` (`revenue_date`, `total_sales`, `total_refunds`, `net_revenue`, `mobile_money`, `bank_transfer`, `credit_card`, `cash`, `transaction_count`, `pharmacy_id`) VALUES
('2024-01-15', 1250.50, 25.30, 1225.20, 800.30, 200.40, 150.60, 99.20, 45, 1),
('2024-01-14', 1180.75, 15.50, 1165.25, 750.25, 180.30, 120.20, 130.00, 38, 1),
('2024-01-13', 1350.80, 30.75, 1320.05, 900.50, 250.30, 100.00, 100.00, 52, 1),
('2024-01-12', 980.25, 10.20, 970.05, 650.15, 150.10, 80.00, 100.00, 35, 1),
('2024-01-11', 1420.60, 45.80, 1374.80, 950.40, 300.20, 120.00, 50.00, 48, 1),
('2024-01-10', 1100.30, 20.10, 1080.20, 700.20, 200.10, 100.00, 100.00, 42, 1),
('2024-01-09', 1280.90, 35.60, 1245.30, 850.30, 250.60, 100.00, 80.00, 46, 1);

INSERT INTO `outstanding_balances` (`patient_name`, `patient_phone`, `total_amount`, `paid_amount`, `outstanding_amount`, `status`, `due_date`, `last_payment_date`, `admin_id`, `pharmacy_id`) VALUES
('Jean Bosco Nkurunziza', '+250 788 111 222', 150.00, 50.00, 100.00, 'CURRENT', '2024-02-15', '2024-01-10', 1, 1),
('Marie Uwimana', '+250 789 333 444', 75.50, 0.00, 75.50, 'OVERDUE', '2024-01-10', NULL, 2, 1),
('Paul Nkurunziza', '+250 787 555 666', 200.25, 200.25, 0.00, 'PAID', '2024-01-20', '2024-01-18', 1, 1),
('Grace Mukamana', '+250 786 777 888', 120.75, 60.00, 60.75, 'CURRENT', '2024-02-01', '2024-01-12', 2, 1),
('John Niyonsaba', '+250 785 999 000', 300.00, 100.00, 200.00, 'OVERDUE', '2024-01-05', '2024-01-02', 1, 1),
('Alice Uwase', '+250 784 111 333', 85.30, 0.00, 85.30, 'CURRENT', '2024-02-20', NULL, 2, 1),
('David Kwizera', '+250 783 444 555', 180.60, 90.30, 90.30, 'CURRENT', '2024-02-10', '2024-01-14', 1, 1),
('Ange Uwimana', '+250 782 666 777', 95.40, 0.00, 95.40, 'OVERDUE', '2024-01-08', NULL, 2, 1);

INSERT INTO `branch_reconciliation` (`reconciliation_date`, `branch_name`, `expected_amount`, `actual_amount`, `variance`, `status`, `notes`, `admin_id`, `pharmacy_id`) VALUES
('2024-01-15', 'Main Branch - Kigali', 1250.50, 1248.30, -2.20, 'RECONCILED', 'Minor variance due to rounding', 1, 1),
('2024-01-15', 'Branch - Nyarugenge', 850.75, 845.20, -5.55, 'DISCREPANCY', 'Investigate missing cash', 2, 1),
('2024-01-15', 'Branch - Kimisagara', 650.30, 650.30, 0.00, 'RECONCILED', 'Perfect match', 1, 1);

INSERT INTO `transaction_exceptions` (`transaction_id`, `exception_type`, `severity`, `status`, `description`, `exposure_amount`, `admin_id`, `pharmacy_id`) VALUES
('TXN-001', 'AMOUNT_MISMATCH', 'HIGH', 'INVESTIGATING', 'Transaction amount differs from medicine price', 15.50, 1, 1),
('TXN-002', 'DUPLICATE_TRANSACTION', 'MEDIUM', 'OPEN', 'Same transaction processed twice', 8.75, 2, 1),
('TXN-003', 'INVALID_MEDICINE', 'CRITICAL', 'ESCALATED', 'Medicine ID does not exist in system', 22.30, 1, 1),
('TXN-004', 'SYSTEM_ERROR', 'LOW', 'RESOLVED', 'Temporary system glitch during processing', 5.25, 2, 1),
('TXN-005', 'MANUAL_REVIEW', 'MEDIUM', 'OPEN', 'Unusual purchase pattern detected', 12.80, 1, 1);

INSERT INTO `suspicious_transactions` (`transaction_id`, `pattern_type`, `risk_score`, `status`, `description`, `exposure_amount`, `admin_id`, `pharmacy_id`) VALUES
('SUS-001', 'UNUSUAL_AMOUNT', 85, 'INVESTIGATING', 'Transaction amount significantly higher than average', 150.00, 1, 1),
('SUS-002', 'FREQUENT_PURCHASES', 70, 'OPEN', 'Multiple purchases within short time frame', 75.50, 2, 1),
('SUS-003', 'OFF_HOURS', 60, 'RESOLVED', 'Transaction made outside business hours', 200.25, 1, 1),
('SUS-004', 'SUSPICIOUS_MEDICINE', 90, 'ESCALATED', 'Purchase of controlled substances in large quantities', 120.75, 2, 1),
('SUS-005', 'MULTIPLE_FAILURES', 80, 'INVESTIGATING', 'Multiple failed payment attempts', 300.00, 1, 1);

INSERT INTO `audit_logs` (`admin_id`, `action`, `object_type`, `object_id`, `status`, `message`, `ip_address`, `pharmacy_id`) VALUES
(1, 'LOGIN', 'admin_users', 1, 'SUCCESS', 'User logged in', '127.0.0.1', 1),
(2, 'CREATE', 'medicines', 102, 'SUCCESS', 'Added new medicine Paracetamol 500mg', '127.0.0.1', 1),
(1, 'UPDATE', 'stock_movements', 554, 'SUCCESS', 'Adjusted stock count', '127.0.0.1', 1),
(2, 'DELETE', 'categories', 14, 'FAILED', 'Delete category failed - constraint', '127.0.0.1', 1),
(1, 'ASSIGN_ROLE', 'admin_users', 7, 'SUCCESS', 'Assigned finance_admin role', '127.0.0.1', 1),
(2, 'EXPORT', 'reports', NULL, 'SUCCESS', 'Exported daily revenue', '127.0.0.1', 1);

INSERT INTO `security_logs` (`event_type`, `user`, `ip_address`, `location`, `severity`, `description`, `admin_id`, `pharmacy_id`) VALUES
('LOGIN_SUCCESS', 'pharmacy_admin', '192.168.1.100', 'Kigali, Rwanda', 'INFO', 'Successful login from trusted IP', 1, 1),
('FAILED_LOGIN', 'unknown_user', '203.45.67.89', 'Unknown', 'WARNING', 'Multiple failed login attempts from suspicious IP', NULL, 1),
('PASSWORD_CHANGE', 'finance_admin', '192.168.1.100', 'Kigali, Rwanda', 'INFO', 'Password changed successfully', 2, 1),
('SUSPICIOUS_ACTIVITY', 'pharmacy_admin', '45.78.123.45', 'Unknown', 'HIGH', 'Unusual data access pattern detected', 1, 1),
('ROLE_ASSIGNMENT', 'super_admin', '192.168.1.50', 'Kigali, Rwanda', 'INFO', 'Role assigned to new user', 1, 1),
('DATA_EXPORT', 'finance_admin', '192.168.1.100', 'Kigali, Rwanda', 'MEDIUM', 'Sensitive data exported to CSV', 2, 1),
('SESSION_TIMEOUT', 'pharmacy_admin', '192.168.1.100', 'Kigali, Rwanda', 'INFO', 'Session expired due to inactivity', 1, 1),
('UNAUTHORIZED_ACCESS', 'hacker_attempt', '185.220.101.42', 'Unknown', 'CRITICAL', 'Attempted access to restricted admin functions', NULL, 1);
