-- System Logs and Analytics Database Schema
-- Created: 2025-08-24

-- System Sessions Table
CREATE TABLE IF NOT EXISTS `system_sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `last_activity` datetime NOT NULL,
  `user_data` text,
  `status` enum('active','expired','terminated') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Audit Logs Table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` varchar(50) DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `entity` (`entity_type`,`entity_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Security Logs Table
CREATE TABLE IF NOT EXISTS `security_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `metadata` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `event_type` (`event_type`),
  KEY `severity` (`severity`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usage Analytics Table
CREATE TABLE IF NOT EXISTS `usage_analytics` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `page_url` varchar(255) NOT NULL,
  `http_method` varchar(10) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `referrer` varchar(255) DEFAULT NULL,
  `query_string` text,
  `execution_time` float DEFAULT NULL,
  `memory_usage` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `page_url` (`page_url`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Regulatory Submissions Table
CREATE TABLE IF NOT EXISTS `regulatory_submissions` (
  `submission_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `submission_type` varchar(100) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `submitted_by` int(11) NOT NULL,
  `submission_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','rejected','withdrawn') NOT NULL DEFAULT 'draft',
  `attachments` text,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`),
  KEY `submission_type` (`submission_type`),
  KEY `reference_number` (`reference_number`),
  KEY `submitted_by` (`submitted_by`),
  KEY `status` (`status`),
  KEY `submission_date` (`submission_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- System Metrics Table (for statistics)
CREATE TABLE IF NOT EXISTS `system_metrics` (
  `metric_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `metric_date` date NOT NULL,
  `metric_hour` tinyint(2) DEFAULT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_value` decimal(20,4) NOT NULL,
  `metadata` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`metric_id`),
  UNIQUE KEY `metric_unique` (`metric_date`,`metric_hour`,`metric_name`),
  KEY `metric_name` (`metric_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraints
ALTER TABLE `system_sessions`
  ADD CONSTRAINT `system_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `security_logs`
  ADD CONSTRAINT `security_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `usage_analytics`
  ADD CONSTRAINT `usage_analytics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `regulatory_submissions`
  ADD CONSTRAINT `regulatory_submissions_ibfk_1` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
