-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2025 at 06:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mdlink2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `pharmacy_id` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `email` varchar(190) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `pharmacy_id`, `username`, `password_hash`, `status`, `email`, `phone`, `created_at`) VALUES
(1, NULL, 'Frederic', '91cbb841d1b583aa18432ca3a49df41c', 'active', 'nzamfred3@gmail.com', '0786980814', '2025-08-27 17:40:48'),
(22, NULL, 'NZAMURAMBAHO', '$2y$10$TAGwgXi/CXpJEO17sqa1mu..1BsVyTnFNsnuSJyEdoUtqXMCigHfG', 'active', 'nzamurambahofrederic28@gmail.com', '0791905996', '2025-09-13 23:19:56'),
(23, NULL, 'Francois', '$2y$10$JdK1sKsUxMqsDQsnklp.wOMY.2fwEalehgs9QLU5rtrUXLOpxaR/W', 'active', 'harerimanafrancois3030@gmail.com', '+250796678252', '2025-09-14 07:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `action` enum('CREATE','UPDATE','DELETE','LOGIN','LOGOUT','VIEW','SEARCH','EXPORT') NOT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `admin_id`, `table_name`, `record_id`, `action`, `old_data`, `new_data`, `action_time`, `description`, `ip_address`, `user_agent`) VALUES
(244, 1, 'admin_users', 1, 'LOGIN', NULL, NULL, '2025-09-14 09:47:28', 'User logged into the system', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(245, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 09:47:28', 'Viewed medicine catalog', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(246, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 09:47:28', 'Viewed pharmacy list', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(247, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 09:47:28', 'Viewed medical staff list', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(248, 1, 'admin_users', 1, 'LOGOUT', NULL, NULL, '2025-09-14 09:47:28', 'User logged out of the system', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(249, 1, 'admin_users', 1, 'LOGIN', NULL, NULL, '2025-09-14 09:59:33', 'User logged into the system', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(250, 22, 'admin_users', 22, 'LOGIN', NULL, NULL, '2025-09-14 07:59:33', 'User logged into the system', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(251, 23, 'admin_users', 23, 'LOGIN', NULL, NULL, '2025-09-14 05:59:33', 'User logged into the system', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(252, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 03:59:33', 'Viewed medicine catalog', '192.168.1.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(253, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 01:59:33', 'Viewed pharmacy management page', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(254, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-13 23:59:33', 'Viewed medical staff management page', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(255, 1, 'admin_users', NULL, 'VIEW', NULL, NULL, '2025-09-13 21:59:33', 'Viewed user activity page', '192.168.1.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(256, 1, 'medicines', 1, 'CREATE', NULL, NULL, '2025-09-13 19:59:33', 'Added new medicine: Paracetamol 500mg', '192.168.1.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(257, 22, 'medicines', 2, 'CREATE', NULL, NULL, '2025-09-13 17:59:33', 'Added new medicine: Amoxicillin 250mg', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(258, 1, 'medicines', 1, 'UPDATE', NULL, NULL, '2025-09-13 15:59:33', 'Updated medicine stock quantity', '192.168.1.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(259, 23, 'medicines', 3, 'DELETE', NULL, NULL, '2025-09-13 13:59:33', 'Deleted expired medicine', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(260, 1, 'pharmacies', 1, 'CREATE', NULL, NULL, '2025-09-13 11:59:33', 'Created new pharmacy: Kigali Central Pharmacy', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(261, 22, 'pharmacies', 1, 'UPDATE', NULL, NULL, '2025-09-13 09:59:33', 'Updated pharmacy contact information', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(262, 1, 'medical_staff', 1, 'CREATE', NULL, NULL, '2025-09-13 07:59:33', 'Added new medical staff: Dr. John Doe', '192.168.1.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(263, 23, 'medical_staff', 2, 'CREATE', NULL, NULL, '2025-09-13 05:59:33', 'Added new medical staff: Nurse Mary Smith', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(264, 1, 'medicines', NULL, 'SEARCH', NULL, NULL, '2025-09-13 03:59:33', 'Searched for \"paracetamol\" in medicines', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(265, 22, 'pharmacies', NULL, 'SEARCH', NULL, NULL, '2025-09-13 01:59:33', 'Searched for \"kigali\" in pharmacies', '192.168.1.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(266, 1, 'medicines', NULL, 'EXPORT', NULL, NULL, '2025-09-12 23:59:33', 'Exported medicines data as CSV', '192.168.1.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(267, 23, 'pharmacies', NULL, 'EXPORT', NULL, NULL, '2025-09-12 21:59:33', 'Exported pharmacies data as Excel', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(268, 1, 'admin_users', 1, 'LOGOUT', NULL, NULL, '2025-09-12 19:59:33', 'User logged out of the system', '192.168.1.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(269, 22, 'admin_users', 22, 'LOGOUT', NULL, NULL, '2025-09-12 17:59:33', 'User logged out of the system', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(270, 1, 'admin_users', 1, 'LOGIN', NULL, NULL, '2025-09-14 10:00:12', 'User logged into the system', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(271, 22, 'admin_users', 22, 'LOGIN', NULL, NULL, '2025-09-14 08:00:12', 'User logged into the system', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(272, 23, 'admin_users', 23, 'LOGIN', NULL, NULL, '2025-09-14 06:00:12', 'User logged into the system', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(273, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 04:00:12', 'Viewed medicine catalog', '192.168.1.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(274, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 02:00:12', 'Viewed pharmacy management page', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(275, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 00:00:12', 'Viewed medical staff management page', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(276, 1, 'admin_users', NULL, 'VIEW', NULL, NULL, '2025-09-13 22:00:12', 'Viewed user activity page', '192.168.1.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(277, 1, 'medicines', 1, 'CREATE', NULL, NULL, '2025-09-13 20:00:12', 'Added new medicine: Paracetamol 500mg', '192.168.1.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(278, 22, 'medicines', 2, 'CREATE', NULL, NULL, '2025-09-13 18:00:12', 'Added new medicine: Amoxicillin 250mg', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(279, 1, 'medicines', 1, 'UPDATE', NULL, NULL, '2025-09-13 16:00:12', 'Updated medicine stock quantity', '192.168.1.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(280, 23, 'medicines', 3, 'DELETE', NULL, NULL, '2025-09-13 14:00:12', 'Deleted expired medicine', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(281, 1, 'pharmacies', 1, 'CREATE', NULL, NULL, '2025-09-13 12:00:12', 'Created new pharmacy: Kigali Central Pharmacy', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(282, 22, 'pharmacies', 1, 'UPDATE', NULL, NULL, '2025-09-13 10:00:12', 'Updated pharmacy contact information', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(283, 1, 'medical_staff', 1, 'CREATE', NULL, NULL, '2025-09-13 08:00:12', 'Added new medical staff: Dr. John Doe', '192.168.1.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(284, 23, 'medical_staff', 2, 'CREATE', NULL, NULL, '2025-09-13 06:00:12', 'Added new medical staff: Nurse Mary Smith', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(285, 1, 'medicines', NULL, 'SEARCH', NULL, NULL, '2025-09-13 04:00:12', 'Searched for \"paracetamol\" in medicines', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(286, 22, 'pharmacies', NULL, 'SEARCH', NULL, NULL, '2025-09-13 02:00:12', 'Searched for \"kigali\" in pharmacies', '192.168.1.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(287, 1, 'medicines', NULL, 'EXPORT', NULL, NULL, '2025-09-13 00:00:12', 'Exported medicines data as CSV', '192.168.1.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(288, 23, 'pharmacies', NULL, 'EXPORT', NULL, NULL, '2025-09-12 22:00:12', 'Exported pharmacies data as Excel', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(289, 1, 'admin_users', 1, 'LOGOUT', NULL, NULL, '2025-09-12 20:00:12', 'User logged out of the system', '192.168.1.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(290, 22, 'admin_users', 22, 'LOGOUT', NULL, NULL, '2025-09-12 18:00:12', 'User logged out of the system', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(291, 1, 'admin_users', 1, 'LOGIN', NULL, NULL, '2025-09-14 08:00:00', 'User logged into the system', '192.168.1.100', 'Mozilla/5.0'),
(292, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:05:00', 'Viewed medicine catalog', '192.168.1.100', 'Mozilla/5.0'),
(293, 1, 'medicines', 1, 'CREATE', NULL, NULL, '2025-09-14 08:10:00', 'Added new medicine: Paracetamol 500mg', '192.168.1.100', 'Mozilla/5.0'),
(294, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:15:00', 'Viewed pharmacy management page', '192.168.1.100', 'Mozilla/5.0'),
(295, 1, 'pharmacies', 1, 'CREATE', NULL, NULL, '2025-09-14 08:20:00', 'Created new pharmacy: Kigali Central Pharmacy', '192.168.1.100', 'Mozilla/5.0'),
(296, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:25:00', 'Viewed medical staff management page', '192.168.1.100', 'Mozilla/5.0'),
(297, 1, 'medical_staff', 1, 'CREATE', NULL, NULL, '2025-09-14 08:30:00', 'Added new medical staff: Dr. John Doe', '192.168.1.100', 'Mozilla/5.0'),
(298, 1, 'admin_users', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:35:00', 'Viewed user activity page', '192.168.1.100', 'Mozilla/5.0'),
(299, 1, 'admin_users', 1, 'LOGOUT', NULL, NULL, '2025-09-14 08:40:00', 'User logged out of the system', '192.168.1.100', 'Mozilla/5.0'),
(300, 22, 'admin_users', 22, 'LOGIN', NULL, NULL, '2025-09-14 09:00:00', 'User logged into the system', '192.168.1.101', 'Mozilla/5.0'),
(301, 22, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 09:05:00', 'Viewed medicine catalog', '192.168.1.101', 'Mozilla/5.0'),
(302, 22, 'medicines', 1, 'UPDATE', NULL, NULL, '2025-09-14 09:10:00', 'Updated medicine stock quantity', '192.168.1.101', 'Mozilla/5.0'),
(303, 22, 'medicines', NULL, 'SEARCH', NULL, NULL, '2025-09-14 09:15:00', 'Searched for \"paracetamol\" in medicines', '192.168.1.101', 'Mozilla/5.0'),
(304, 22, 'medicines', NULL, 'EXPORT', NULL, NULL, '2025-09-14 09:20:00', 'Exported medicines data as CSV', '192.168.1.101', 'Mozilla/5.0'),
(305, 22, 'admin_users', 22, 'LOGOUT', NULL, NULL, '2025-09-14 09:25:00', 'User logged out of the system', '192.168.1.101', 'Mozilla/5.0'),
(306, 23, 'admin_users', 23, 'LOGIN', NULL, NULL, '2025-09-14 10:00:00', 'User logged into the system', '192.168.1.102', 'Mozilla/5.0'),
(307, 23, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 10:05:00', 'Viewed pharmacy management page', '192.168.1.102', 'Mozilla/5.0'),
(308, 23, 'pharmacies', 1, 'UPDATE', NULL, NULL, '2025-09-14 10:10:00', 'Updated pharmacy contact information', '192.168.1.102', 'Mozilla/5.0'),
(309, 23, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 10:15:00', 'Viewed medical staff management page', '192.168.1.102', 'Mozilla/5.0'),
(310, 23, 'medical_staff', 2, 'CREATE', NULL, NULL, '2025-09-14 10:20:00', 'Added new medical staff: Nurse Mary Smith', '192.168.1.102', 'Mozilla/5.0'),
(311, 23, 'admin_users', 23, 'LOGOUT', NULL, NULL, '2025-09-14 10:25:00', 'User logged out of the system', '192.168.1.102', 'Mozilla/5.0'),
(312, 1, 'admin_users', 1, 'LOGIN', NULL, NULL, '2025-09-14 08:00:00', 'User logged into the system', '192.168.1.100', 'Mozilla/5.0'),
(313, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:05:00', 'Viewed medicine catalog', '192.168.1.100', 'Mozilla/5.0'),
(314, 1, 'medicines', 1, 'CREATE', NULL, NULL, '2025-09-14 08:10:00', 'Added new medicine: Paracetamol 500mg', '192.168.1.100', 'Mozilla/5.0'),
(315, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:15:00', 'Viewed pharmacy management page', '192.168.1.100', 'Mozilla/5.0'),
(316, 1, 'pharmacies', 1, 'CREATE', NULL, NULL, '2025-09-14 08:20:00', 'Created new pharmacy: Kigali Central Pharmacy', '192.168.1.100', 'Mozilla/5.0'),
(317, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:25:00', 'Viewed medical staff management page', '192.168.1.100', 'Mozilla/5.0'),
(318, 1, 'medical_staff', 1, 'CREATE', NULL, NULL, '2025-09-14 08:30:00', 'Added new medical staff: Dr. John Doe', '192.168.1.100', 'Mozilla/5.0'),
(319, 1, 'admin_users', NULL, 'VIEW', NULL, NULL, '2025-09-14 08:35:00', 'Viewed user activity page', '192.168.1.100', 'Mozilla/5.0'),
(320, 1, 'admin_users', 1, 'LOGOUT', NULL, NULL, '2025-09-14 08:40:00', 'User logged out of the system', '192.168.1.100', 'Mozilla/5.0'),
(321, 22, 'admin_users', 22, 'LOGIN', NULL, NULL, '2025-09-14 09:00:00', 'User logged into the system', '192.168.1.101', 'Mozilla/5.0'),
(322, 22, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 09:05:00', 'Viewed medicine catalog', '192.168.1.101', 'Mozilla/5.0'),
(323, 22, 'medicines', 1, 'UPDATE', NULL, NULL, '2025-09-14 09:10:00', 'Updated medicine stock quantity', '192.168.1.101', 'Mozilla/5.0'),
(324, 22, 'medicines', NULL, 'SEARCH', NULL, NULL, '2025-09-14 09:15:00', 'Searched for \"paracetamol\" in medicines', '192.168.1.101', 'Mozilla/5.0'),
(325, 22, 'medicines', NULL, 'EXPORT', NULL, NULL, '2025-09-14 09:20:00', 'Exported medicines data as CSV', '192.168.1.101', 'Mozilla/5.0'),
(326, 22, 'admin_users', 22, 'LOGOUT', NULL, NULL, '2025-09-14 09:25:00', 'User logged out of the system', '192.168.1.101', 'Mozilla/5.0'),
(327, 23, 'admin_users', 23, 'LOGIN', NULL, NULL, '2025-09-14 10:00:00', 'User logged into the system', '192.168.1.102', 'Mozilla/5.0'),
(328, 23, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 10:05:00', 'Viewed pharmacy management page', '192.168.1.102', 'Mozilla/5.0'),
(329, 23, 'pharmacies', 1, 'UPDATE', NULL, NULL, '2025-09-14 10:10:00', 'Updated pharmacy contact information', '192.168.1.102', 'Mozilla/5.0'),
(330, 23, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 10:15:00', 'Viewed medical staff management page', '192.168.1.102', 'Mozilla/5.0'),
(331, 23, 'medical_staff', 2, 'CREATE', NULL, NULL, '2025-09-14 10:20:00', 'Added new medical staff: Nurse Mary Smith', '192.168.1.102', 'Mozilla/5.0'),
(332, 23, 'admin_users', 23, 'LOGOUT', NULL, NULL, '2025-09-14 10:25:00', 'User logged out of the system', '192.168.1.102', 'Mozilla/5.0'),
(333, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 11:43:54', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(334, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 11:43:57', 'Viewed medical staff management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(335, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 11:44:58', 'Viewed medical staff management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(336, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 11:59:44', 'Viewed medical staff management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(337, 1, 'medical_staff', NULL, 'VIEW', NULL, NULL, '2025-09-14 11:59:53', 'Viewed medical staff management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(338, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 12:03:30', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(339, 1, 'medicines', 197, 'DELETE', NULL, '{\"deleted_medicine_name\":\"Omeprazole 20mg Capsules\",\"deleted_medicine_id\":197}', '2025-09-14 12:04:10', NULL, NULL, NULL),
(340, 1, 'medicines', 197, 'DELETE', NULL, NULL, '2025-09-14 12:04:10', 'Deleted medicine with ID: 197', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(341, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 12:04:10', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(342, 1, 'medicines', 210, 'CREATE', NULL, NULL, '2025-09-14 15:26:15', 'Added new medicine: Dexamethasone 4mg/ml', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(343, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 15:26:15', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(344, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 15:37:35', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(345, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 15:42:33', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(346, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 15:42:56', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(347, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 15:46:18', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(348, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:00:27', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(349, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:05:24', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(350, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:08:42', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(351, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:09:22', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(352, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:11:59', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(353, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:14:44', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(354, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:14:48', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(355, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 16:16:37', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(356, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:03:40', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(357, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:08:25', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(358, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:14:51', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(359, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:18:03', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(360, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:22:40', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(361, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:24:17', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(362, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:36:54', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(363, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:48:45', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(364, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 17:56:06', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(365, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 18:50:58', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(366, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 19:35:34', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(367, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 19:36:10', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(368, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 19:36:41', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(369, 1, 'medical_staff', 6, 'CREATE', NULL, NULL, '2025-09-14 19:38:56', 'Added new medical staff: betty Uwase (nurse)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(370, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 19:41:01', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(371, 1, 'medicines', 210, 'DELETE', NULL, '{\"deleted_medicine_name\":\"Dexamethasone 4mg\\/ml\",\"deleted_medicine_id\":210}', '2025-09-14 19:41:13', NULL, NULL, NULL),
(372, 1, 'medicines', 210, 'DELETE', NULL, NULL, '2025-09-14 19:41:13', 'Deleted medicine with ID: 210', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(373, 1, 'medicines', NULL, 'VIEW', NULL, NULL, '2025-09-14 19:41:13', 'Viewed medicine catalog', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(374, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 19:42:07', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(375, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 20:02:48', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(376, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 20:16:12', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(377, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-14 20:34:16', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(378, 1, 'pharmacies', NULL, 'VIEW', NULL, NULL, '2025-09-15 16:06:21', 'Viewed pharmacy management page', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `branch_reconciliation`
--

CREATE TABLE `branch_reconciliation` (
  `reconciliation_id` int(11) NOT NULL,
  `reconciliation_date` date NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `expected_amount` decimal(10,2) NOT NULL,
  `actual_amount` decimal(10,2) NOT NULL,
  `variance` decimal(10,2) NOT NULL,
  `status` enum('PENDING','RECONCILED','DISCREPANCY') DEFAULT 'PENDING',
  `notes` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Antibiotics', 'Antibiotic medications'),
(2, 'Pain Relief', 'Pain management medications'),
(3, 'Surgical Supplies', 'Surgical and medical supplies'),
(4, 'Emergency Kit', 'Emergency medical supplies');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('1','2') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Anti-infectives', 'Treat infections caused by bacteria, viruses, fungi, or parasites.', '1', '2025-08-28 03:33:46', '2025-08-28 03:33:46'),
(2, 'Antibiotics', 'Medicines that fight bacterial infections', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(3, 'Pain Relief', 'Medicines for pain and fever management', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(4, 'Cardiovascular', 'Medicines for heart and blood pressure', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(5, 'Diabetes', 'Medicines for diabetes management', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(6, 'Respiratory', 'Medicines for breathing and lung conditions', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(7, 'Vitamins & Supplements', 'Nutritional supplements and vitamins', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(8, 'Dermatology', 'Medicines for skin conditions', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(9, 'Mental Health', 'Medicines for psychiatric conditions', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(10, 'Pediatrics', 'Medicines specifically for children', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(11, 'Emergency Medicine', 'Critical care and emergency medicines', '1', '2025-08-28 17:53:22', '2025-08-28 17:53:22'),
(12, 'Antibiotics', 'Medicines for antibiotics', '1', '2025-08-28 18:02:49', '2025-08-28 18:02:49'),
(14, 'Cardiovascular', 'Medicines for cardiovascular', '1', '2025-08-28 18:02:49', '2025-08-28 18:02:49'),
(15, 'Diabetes', 'Medicines for diabetes', '1', '2025-08-28 18:02:49', '2025-08-28 18:02:49'),
(21, 'Emergency Medicine', 'Medicines for emergency medicine', '1', '2025-08-28 18:02:49', '2025-08-28 18:02:49');

-- --------------------------------------------------------

--
-- Table structure for table `daily_revenue`
--

CREATE TABLE `daily_revenue` (
  `revenue_id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expiry_alerts`
--

CREATE TABLE `expiry_alerts` (
  `alert_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `pharmacy_id` int(11) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `category_name` varchar(150) DEFAULT NULL,
  `expires_on` date NOT NULL,
  `days_left` int(11) DEFAULT NULL,
  `severity` enum('normal','urgent','expired') NOT NULL,
  `notified_super` tinyint(1) NOT NULL DEFAULT 0,
  `notified_pharma` tinyint(1) NOT NULL DEFAULT 0,
  `notified_finance` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expiry_alerts`
--

INSERT INTO `expiry_alerts` (`alert_id`, `medicine_id`, `pharmacy_id`, `name`, `category_name`, `expires_on`, `days_left`, `severity`, `notified_super`, `notified_pharma`, `notified_finance`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Amoxicillin', 'Anti-infectives', '2025-08-28', -1, 'expired', 0, 0, 0, '2025-08-28 14:39:26', '2025-08-28 14:39:26'),
(3, 1, 1, 'Amoxicillin', 'Anti-infectives', '2025-08-31', 1, 'urgent', 0, 0, 0, '2025-08-28 14:53:03', '2025-08-29 14:21:06'),
(8, 11, 3, 'Adrenaline 1mg/ml', 'Pediatrics', '2025-08-15', -15, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(9, 19, 5, 'Insulin Regular', 'Cardiovascular', '2025-09-15', 16, 'urgent', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(10, 20, 3, 'Insulin NPH', 'Cardiovascular', '2025-08-20', -10, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(11, 31, 3, 'Amitriptyline 25mg', 'Dermatology', '2025-09-20', 21, 'urgent', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(12, 32, 5, 'Diazepam 5mg', 'Dermatology', '2025-08-15', -15, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(13, 38, 3, 'Adrenaline 1mg/ml', 'Pediatrics', '2025-06-15', -76, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(14, 39, 1, 'Morphine 10mg/ml', 'Pediatrics', '2025-05-20', -102, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(15, 40, 1, 'Dexamethasone 4mg/ml', 'Pediatrics', '2025-07-10', -51, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(16, 41, 3, 'Atropine 1mg/ml', 'Pediatrics', '2025-06-25', -66, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06'),
(17, 42, 3, 'Naloxone 0.4mg/ml', 'Pediatrics', '2025-05-30', -92, 'expired', 0, 0, 0, '2025-08-29 14:21:06', '2025-08-29 14:21:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_payments`
--

CREATE TABLE `failed_payments` (
  `payment_id` int(11) NOT NULL,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `failed_payments`
--

INSERT INTO `failed_payments` (`payment_id`, `transaction_id`, `patient_name`, `patient_phone`, `amount`, `payment_method`, `status`, `failure_reason`, `retry_count`, `last_retry`, `admin_id`, `created_at`, `resolved_at`, `pharmacy_id`) VALUES
(17, 'PAY-001', 'Jean Bosco Nkurunziza', '+250 788 111 222', 15.50, 'MOBILE_MONEY', 'RETRY_PENDING', '0', 2, NULL, NULL, '2025-09-03 07:28:51', NULL, 7),
(18, 'PAY-002', 'Marie Uwimana', '+250 789 333 444', 8.75, 'BANK_TRANSFER', 'PERMANENTLY_FAILED', '0', 5, NULL, NULL, '2025-08-30 07:28:51', NULL, 7),
(19, 'PAY-003', 'Paul Nkurunziza', '+250 787 555 666', 22.30, 'CREDIT_CARD', 'RETRY_PENDING', '0', 1, NULL, 1, '2025-08-26 07:28:51', NULL, 7),
(20, 'PAY-004', 'Grace Mukamana', '+250 786 777 888', 5.25, 'MOBILE_MONEY', 'RESOLVED', '0', 3, NULL, NULL, '2025-09-03 07:28:51', NULL, 7),
(21, 'PAY-005', 'John Niyonsaba', '+250 785 999 000', 12.80, 'BANK_TRANSFER', 'RETRY_PENDING', '0', 2, NULL, NULL, '2025-08-25 07:28:51', NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `medical_staff`
--

CREATE TABLE `medical_staff` (
  `staff_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` enum('doctor','nurse') NOT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `specialty` varchar(150) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `assigned_pharmacy_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pharmacy_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_staff`
--

INSERT INTO `medical_staff` (`staff_id`, `full_name`, `role`, `license_number`, `specialty`, `phone`, `email`, `status`, `assigned_pharmacy_id`, `created_at`, `updated_at`, `pharmacy_id`) VALUES
(5, 'Clarisse ISHIMWE', 'doctor', 'Mdlink-2025-W', 'Cardiology', '0786678252', 'info@mdilink.rw', 'active', NULL, '2025-09-13 22:19:50', '2025-09-13 22:19:50', 7),
(6, 'betty Uwase', 'nurse', 'MDLink-INEZA', 'Cardiosity', '0786980814', 'info@globalinone.rw', 'active', NULL, '2025-09-14 19:38:56', '2025-09-14 19:38:56', 7);

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `pharmacy_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `Restricted Medicine` tinyint(1) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `pharmacy_id`, `name`, `description`, `price`, `stock_quantity`, `expiry_date`, `Restricted Medicine`, `category_id`) VALUES
(4, 4, 'Amlodipine 5mg', 'Blood pressure medication', 1800.00, 120, '2025-12-25', 0, 3),
(6, 5, 'Salbutamol Inhaler', 'Asthma medication', 3500.00, 80, '2025-11-30', 0, 5),
(10, 3, 'Amoxicillin Syrup', 'Pediatric antibiotic', 1800.00, 100, '2025-11-30', 0, 9),
(11, 3, 'Adrenaline 1mg/ml', 'Emergency medication', 8500.00, 25, '2025-08-15', 1, 10),
(15, 5, 'Lisinopril 10mg', 'ACE inhibitor for blood pressure', 2200.00, 6, '2025-11-25', 0, 3),
(16, 5, 'Atenolol 50mg', 'Beta blocker for heart conditions', 1600.00, 7, '2025-12-05', 0, 3),
(19, 5, 'Insulin Regular', 'Short-acting insulin', 4500.00, 2, '2025-09-15', 1, 4),
(21, 3, 'Beclomethasone Inhaler', 'Corticosteroid for asthma', 4200.00, 4, '2025-12-15', 0, 5),
(23, 4, 'Montelukast 10mg', 'Leukotriene receptor antagonist', 3200.00, 5, '2025-10-25', 0, 5),
(24, 4, 'Vitamin D3 1000IU', 'Bone health and immunity', 1800.00, 7, '2026-05-20', 0, 6),
(29, 4, 'Benzoyl Peroxide 5%', 'Acne treatment gel', 1600.00, 6, '2026-01-15', 0, 7),
(31, 3, 'Amitriptyline 25mg', 'Tricyclic antidepressant', 2800.00, 3, '2025-09-20', 1, 8),
(32, 5, 'Diazepam 5mg', 'Benzodiazepine for anxiety', 1800.00, 4, '2025-08-15', 1, 8),
(34, 4, 'Amoxicillin Syrup 125mg/5ml', 'Pediatric antibiotic suspension', 1800.00, 5, '2025-11-30', 0, 9),
(35, 3, 'Paracetamol Syrup 120mg/5ml', 'Pediatric pain and fever relief', 1200.00, 7, '2026-02-15', 0, 9),
(36, 3, 'ORS Powder', 'Oral rehydration solution', 500.00, 8, '2026-04-20', 0, 9),
(37, 3, 'Zinc Sulfate 20mg', 'Zinc supplement for children', 800.00, 4, '2026-03-10', 0, 9),
(38, 3, 'Adrenaline 1mg/ml', 'Emergency treatment for severe allergic reactions', 8500.00, 1, '2025-06-15', 1, 10),
(41, 3, 'Atropine 1mg/ml', 'Emergency treatment for bradycardia', 7500.00, 2, '2025-06-25', 1, 10),
(42, 3, 'Naloxone 0.4mg/ml', 'Emergency treatment for opioid overdose', 9500.00, 1, '2025-05-30', 1, 10),
(43, 4, 'Amoxicillin 500mg', 'Broad-spectrum antibiotic for bacterial infections', 1200.00, 150, '2026-12-31', 0, 1),
(44, 5, 'Ciprofloxacin 500mg', 'Antibiotic for urinary tract and respiratory infections', 1800.00, 120, '2026-10-15', 0, 1),
(46, 4, 'Doxycycline 100mg', 'Broad-spectrum antibiotic for various infections', 1400.00, 180, '2026-09-30', 0, 1),
(47, 4, 'Ceftriaxone 1g', 'Injectable antibiotic for severe infections', 3500.00, 50, '2026-08-15', 1, 1),
(48, 4, 'Artemether-lumefantrine', 'First-line treatment for uncomplicated malaria', 2500.00, 300, '2026-12-31', 0, 2),
(68, 3, 'Insulin Regular', 'Short-acting insulin for diabetes', 8500.00, 50, '2026-06-30', 1, 7),
(71, 5, 'Metformin 500mg', 'Biguanide for diabetes', 350.00, 300, '2026-12-31', 0, 7),
(73, 3, 'Ranitidine 150mg', 'H2 blocker for acid reflux', 800.00, 200, '2026-11-30', 0, 8),
(74, 5, 'Oral Rehydration Salts', 'ORS for dehydration', 150.00, 1000, '2026-12-31', 0, 8),
(75, 4, 'Oxytocin 10IU', 'Hormone for labor induction', 2500.00, 30, '2026-05-31', 1, 9),
(76, 4, 'Misoprostol 200mcg', 'For postpartum hemorrhage prevention', 1800.00, 40, '2026-06-30', 1, 9),
(78, 4, 'Ferrous sulfate 200mg', 'Iron supplement for anemia', 400.00, 250, '2026-12-31', 0, 9),
(79, 5, 'Folic acid 5mg', 'Vitamin supplement for pregnancy', 200.00, 300, '2026-12-31', 0, 9),
(81, 5, 'Phenobarbital 30mg', 'Anticonvulsant for epilepsy', 1800.00, 60, '2026-04-30', 1, 10),
(85, 4, 'Morphine 10mg/ml', 'Strong pain relief for severe pain', 12000.00, 15, '2026-01-31', 1, 10),
(86, 3, 'Dexamethasone 4mg/ml', 'Emergency steroid for severe inflammation', 6500.00, 25, '2026-03-31', 1, 10),
(88, 4, 'Naloxone 0.4mg/ml', 'Emergency treatment for opioid overdose', 9500.00, 12, '2026-01-31', 1, 10),
(93, 4, 'Ceftriaxone 1g Injection', 'Injectable antibiotic for severe infections', 3500.00, 50, '2026-08-15', 1, 1),
(94, 4, 'Azithromycin 500mg Tablet', 'Macrolide antibiotic for respiratory infections', 2200.00, 100, '2026-11-30', 0, 1),
(192, 8, 'Amoxicillin 500mg Capsules', 'Broad-spectrum antibiotic for bacterial infections. Take 1 capsule 3 times daily with meals.\n\nBatch: AMX-2025-001\nSupplier: Rwanda Pharma Ltd', 2500.00, 150, '2026-03-15', 0, 1),
(193, 8, 'Paracetamol 500mg Tablets', 'Pain reliever and fever reducer. Take 1-2 tablets every 6 hours as needed.\n\nBatch: PAR-2025-002\nSupplier: Kigali Medical Supplies', 500.00, 500, '2026-08-20', 0, 2),
(194, 8, 'Metformin 500mg Tablets', 'Oral diabetes medication. Take 1 tablet twice daily with meals.\n\nBatch: MET-2025-003\nSupplier: Diabetes Care Rwanda', 1800.00, 200, '2026-05-10', 0, 4),
(195, 8, 'Amlodipine 5mg Tablets', 'Calcium channel blocker for hypertension. Take 1 tablet daily.\n\nBatch: AML-2025-004\nSupplier: Cardio Health Ltd', 3200.00, 120, '2026-07-25', 0, 3),
(196, 8, 'Salbutamol Inhaler 100mcg', 'Bronchodilator for asthma and COPD. Use 1-2 puffs as needed.\n\nBatch: SAL-2025-005\nSupplier: Respiratory Solutions', 4500.00, 80, '2026-02-28', 0, 5),
(199, 8, 'Morphine 10mg Injection', 'Strong opioid pain medication. For severe pain only. Requires prescription.\n\nBatch: MOR-2025-008\nSupplier: Pain Management Ltd', 8500.00, 25, '2026-01-15', 1, 2),
(200, 8, 'Insulin Glargine 100IU/ml', 'Long-acting insulin for diabetes management. Requires prescription and proper storage.\n\nBatch: INS-2025-009\nSupplier: Diabetes Care Rwanda', 12000.00, 45, '2026-04-20', 1, 4),
(201, 8, 'Hydrocortisone Cream 1%', 'Topical corticosteroid for skin inflammation. Apply thin layer 2-3 times daily.\n\nBatch: HYD-2025-010\nSupplier: Dermatology Solutions', 1800.00, 60, '2026-06-18', 0, 9),
(202, 8, 'Ceftriaxone 1g Injection', 'Third-generation cephalosporin antibiotic. For serious bacterial infections.\n\nBatch: CEF-2025-011\nSupplier: Rwanda Pharma Ltd', 5500.00, 35, '2026-03-08', 0, 1),
(203, 8, 'Multivitamin Tablets', 'Daily multivitamin supplement. Take 1 tablet daily with breakfast.\n\nBatch: MUL-2025-012\nSupplier: Nutri Health Ltd', 1200.00, 400, '2026-10-05', 0, 8),
(204, 8, 'Lisinopril 10mg Tablets', 'ACE inhibitor for hypertension and heart failure. Take 1 tablet daily.\n\nBatch: LIS-2025-013\nSupplier: Cardio Health Ltd', 2800.00, 100, '2026-08-14', 0, 3),
(205, 8, 'Ibuprofen 400mg Tablets', 'NSAID for pain and inflammation. Take 1-2 tablets every 8 hours with food.\n\nBatch: IBU-2025-014\nSupplier: Kigali Medical Supplies', 800.00, 350, '2026-12-22', 0, 2),
(206, 8, 'Diazepam 5mg Tablets', 'Benzodiazepine for anxiety and muscle spasms. Controlled substance - requires prescription.\n\nBatch: DIA-2025-015\nSupplier: Mental Health Rwanda', 3500.00, 15, '2026-01-30', 1, 11),
(209, 3, 'Atenolol 50mg', 'Beta blocker for heart conditions', 5000.00, 5666, '2025-05-06', 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `ml_predictions`
--

CREATE TABLE `ml_predictions` (
  `prediction_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `pharmacy_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `predicted_disease` varchar(150) DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `predicted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ml_training_data`
--

CREATE TABLE `ml_training_data` (
  `data_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `pharmacy_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `symptom` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `outcome` enum('positive','negative','neutral') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `outstanding_balances`
--

CREATE TABLE `outstanding_balances` (
  `balance_id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `outstanding_amount` decimal(10,2) NOT NULL,
  `status` enum('CURRENT','OVERDUE','PAID') DEFAULT 'CURRENT',
  `due_date` date NOT NULL,
  `last_payment_date` date DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('mobile_money','card','cash') NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `admin_id`, `amount`, `method`, `status`, `paid_at`) VALUES
(1, 1, 15000.00, 'mobile_money', 'completed', '2025-08-29 05:46:45'),
(2, 1, 25000.00, 'cash', 'completed', '2025-08-29 04:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacies`
--

CREATE TABLE `pharmacies` (
  `pharmacy_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacies`
--

INSERT INTO `pharmacies` (`pharmacy_id`, `name`, `location`, `license_number`, `contact_person`, `contact_phone`, `created_at`, `updated_at`) VALUES
(3, 'Test Pharmacy 22:10:13', 'Test Location', 'TEST7691', 'Test Person', '1234567890', '2025-08-28 18:02:49', '2025-09-13 20:10:13'),
(4, 'REMEZO PHARMACY', 'Umujyi wa Kigali / Kicukiro / Remezo', 'MDLink-5083-0168', 'Dr Francois', '+250792021423', '2025-08-28 18:02:49', '2025-09-13 20:10:12'),
(5, 'NYARUGENGE PHARMACY', 'Umujyi wa Kigali / Nyarugenge / Nyamirambo', 'MDLink-5083-0169', 'Dr Jean', '+250788765432', '2025-08-28 18:02:49', '2025-09-13 20:10:12'),
(7, 'Ineza Pharmacy', 'Kigali', 'MDLink-INEZA', 'Ineza Manager', '0786111111', '2025-08-29 12:16:51', '2025-09-13 20:10:12'),
(8, 'Ineza Pharmacy', 'Nyarugenge, Kigali', 'RL-INEZA-2025', 'Dr. Jean Bosco, Pharmacist-in-Charge', '+250788123456', '2025-09-02 04:52:39', '2025-09-13 20:10:12'),
(9, 'Isano pharma', 'kimironko', 'Mdlink-2025-W', 'Dr MG', '+250786980814', '2025-09-07 20:22:28', '2025-09-13 20:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_reminders`
--

CREATE TABLE `pickup_reminders` (
  `id` int(11) NOT NULL,
  `pharmacy_id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `dosage_instructions` text DEFAULT NULL,
  `pickup_date` date NOT NULL,
  `reminder_date` datetime NOT NULL,
  `status` enum('pending','sent','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pickup_reminders`
--

INSERT INTO `pickup_reminders` (`id`, `pharmacy_id`, `patient_name`, `patient_phone`, `medicine_name`, `dosage_instructions`, `pickup_date`, `reminder_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Jean Uwimana', '+250786123456', 'Amoxicillin 500mg', 'Take 1 capsule 3 times daily with meals', '2025-09-02', '2025-08-31 09:00:00', 'pending', NULL, '2025-09-01 19:56:05', '2025-09-01 19:56:05'),
(2, 1, 'Marie Mukamana', '+250789654321', 'Paracetamol 500mg', 'Take 2 tablets every 6 hours as needed', '2025-09-03', '2025-09-01 09:00:00', 'pending', NULL, '2025-09-01 19:56:05', '2025-09-01 19:56:05'),
(3, 1, 'Pierre Ndayisaba', '+250785123456', 'Omeprazole 20mg', 'Take 1 capsule daily before breakfast', '2025-09-01', '2025-08-30 09:00:00', 'sent', NULL, '2025-09-01 19:56:05', '2025-09-01 19:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(150) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recall_alerts`
--

CREATE TABLE `recall_alerts` (
  `recall_id` int(11) NOT NULL,
  `medicine_name` varchar(191) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `category_name` varchar(150) DEFAULT NULL,
  `pharmacy_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `severity` enum('info','warning','critical') NOT NULL DEFAULT 'warning',
  `announced_on` date NOT NULL,
  `status` enum('open','in_progress','resolved') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refund_requests`
--

CREATE TABLE `refund_requests` (
  `refund_id` int(11) NOT NULL,
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
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `report_type` enum('sales','inventory','user_activity') DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reset_tokens`
--

CREATE TABLE `reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` varchar(10) NOT NULL DEFAULT 'user',
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reset_tokens`
--

INSERT INTO `reset_tokens` (`id`, `user_id`, `user_type`, `token_hash`, `expires_at`, `created_at`) VALUES
(2, 1, 'admin', '7ee79080cc10aa445b3073b2372e38560d48d3efcf055006c16d81a7e5f02aec', '2025-09-06 07:09:51', '2025-09-06 04:09:51'),
(4, 22, 'admin', 'eb306ed02c86fb0addb4077b2df31c84f6a5f109b76dc306260d53608327f571', '2025-09-14 02:36:29', '2025-09-13 23:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(100) NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `description` text DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(11) NOT NULL,
  `pharmacy_id` int(11) DEFAULT NULL,
  `sender_id` varchar(11) DEFAULT NULL,
  `recipient_phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `message_type` varchar(50) DEFAULT NULL,
  `api_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sms_logs`
--

INSERT INTO `sms_logs` (`id`, `pharmacy_id`, `sender_id`, `recipient_phone`, `message`, `message_type`, `api_response`, `created_at`) VALUES
(1, 7, 'Pharmacy', '+250786980814', 'Hello Francois', 'general', '{\"status\":\"error\",\"message\":\"Sender ID Not Valid with this user account\",\"link\":\"\"}', '2025-08-31 22:08:31'),
(2, 7, 'Inezapharma', '+250786980814', 'Hello Francois', 'general', '{\"status\":\"error\",\"message\":\"Sender ID Not Valid with this user account\",\"link\":\"\"}', '2025-08-31 22:10:07'),
(3, 7, 'INEZA', '+250786980814', 'Hello', 'general', '{\"status\":\"error\",\"message\":\"Sender ID Not Valid with this user account\",\"link\":\"\"}', '2025-09-01 07:10:06'),
(4, 7, 'PHARMACY', '+250786980814', 'Hello', 'general', '{\"status\":\"error\",\"message\":\"Sender ID Not Valid with this user account\",\"link\":\"\"}', '2025-09-01 07:15:39'),
(5, 7, 'PHARMACY', '+250786980814', 'Hello', 'general', '{\"status\":\"error\",\"message\":\"Sender ID Not Valid with this user account\",\"link\":\"\"}', '2025-09-01 07:25:22'),
(6, 7, 'PHARMACY', '+250786980814', 'Hello', 'general', '{\"status\":\"error\",\"message\":\"Sender ID Not Valid with this user account\",\"link\":\"\"}', '2025-09-01 07:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `sms_notifications`
--

CREATE TABLE `sms_notifications` (
  `sms_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('sent','failed') DEFAULT 'sent',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `special_pharmacy_requests`
--

CREATE TABLE `special_pharmacy_requests` (
  `request_id` int(11) NOT NULL,
  `pharmacy_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `movement_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `movement_type` enum('IN','OUT','ADJUSTMENT','EXPIRED') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `movement_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`movement_id`, `medicine_id`, `movement_type`, `quantity`, `previous_stock`, `new_stock`, `reference_number`, `notes`, `admin_id`, `movement_date`, `created_at`) VALUES
(7, 4, 'IN', 3, 52, 55, 'REF-EFDE9817', 'Return from Customer', NULL, '2025-08-25 20:02:49', '2025-08-28 18:02:49'),
(8, 4, 'OUT', 13, 41, 28, 'REF-8794C552', 'Sale to Customer', 1, '2025-08-25 20:02:49', '2025-08-28 18:02:49'),
(9, 4, 'OUT', 22, 30, 8, 'REF-E408A185', 'Expired Stock Removal', 1, '2025-08-03 20:02:49', '2025-08-28 18:02:49'),
(13, 6, 'ADJUSTMENT', 35, 102, 67, 'REF-DF7E7279', 'Stock Count Correction', 1, '2025-08-20 20:02:49', '2025-08-28 18:02:49'),
(14, 6, 'IN', 46, 93, 139, 'REF-587106D2', 'New Stock Arrival', NULL, '2025-08-18 20:02:49', '2025-08-28 18:02:49'),
(15, 6, 'IN', 33, 131, 164, 'REF-8B625687', 'New Stock Arrival', NULL, '2025-08-28 20:02:49', '2025-08-28 18:02:49'),
(16, 19, 'OUT', 14, 13, 0, 'REF-E8E1C7DF', 'Transfer to Other Branch', 1, '2025-07-20 20:37:36', '2025-08-28 18:37:36'),
(17, 19, 'OUT', 17, 17, 0, 'REF-5A33B6A4', 'Expired Stock Removal', NULL, '2025-07-27 20:37:36', '2025-08-28 18:37:36'),
(18, 19, 'ADJUSTMENT', 14, 48, 34, 'REF-41965D0B', 'Stock Count Correction', 1, '2025-07-02 20:37:36', '2025-08-28 18:37:36'),
(19, 21, 'OUT', 5, 47, 42, 'REF-AC2993B2', 'Transfer to Other Branch', NULL, '2025-07-25 20:37:36', '2025-08-28 18:37:36'),
(20, 21, 'OUT', 1, 10, 9, 'REF-6C25ECAA', 'Sale to Customer', NULL, '2025-08-16 20:37:36', '2025-08-28 18:37:36'),
(21, 21, 'ADJUSTMENT', 5, 21, 16, 'REF-66934359', 'Stock Count Correction', NULL, '2025-07-24 20:37:36', '2025-08-28 18:37:36'),
(25, 16, 'OUT', 8, 11, 3, 'REF-F69A724F', 'Emergency Use', NULL, '2025-08-11 20:37:36', '2025-08-28 18:37:36'),
(26, 16, 'OUT', 15, 47, 32, 'REF-D0C3FF1F', 'Expired Stock Removal', NULL, '2025-07-01 20:37:36', '2025-08-28 18:37:36'),
(27, 16, 'OUT', 1, 16, 15, 'REF-1B2D7E1E', 'Transfer to Other Branch', NULL, '2025-07-07 20:37:36', '2025-08-28 18:37:36'),
(28, 42, 'OUT', 10, 30, 20, 'REF-CCF8FAB8', 'Sale to Customer', NULL, '2025-07-09 20:37:36', '2025-08-28 18:37:36'),
(29, 42, 'OUT', 11, 21, 10, 'REF-2011DE1A', 'Emergency Use', NULL, '2025-07-08 20:37:36', '2025-08-28 18:37:36'),
(30, 42, 'OUT', 4, 22, 18, 'REF-1F06DAE8', 'Transfer to Other Branch', NULL, '2025-07-03 20:37:36', '2025-08-28 18:37:36'),
(31, 42, 'OUT', 18, 44, 26, 'REF-C75E6716', 'Emergency Use', NULL, '2025-08-14 20:37:36', '2025-08-28 18:37:36'),
(32, 35, 'OUT', 20, 41, 21, 'REF-26194146', 'Emergency Use', NULL, '2025-07-09 20:37:36', '2025-08-28 18:37:36'),
(33, 35, 'ADJUSTMENT', 17, 28, 11, 'REF-4263E25A', 'Stock Count Correction', NULL, '2025-07-27 20:37:36', '2025-08-28 18:37:36'),
(34, 24, 'OUT', 20, 38, 18, 'REF-A26C48E8', 'Expired Stock Removal', NULL, '2025-08-14 20:37:36', '2025-08-28 18:37:36'),
(35, 24, 'ADJUSTMENT', 12, 37, 25, 'REF-1D44C7AC', 'Stock Count Correction', 1, '2025-07-19 20:37:36', '2025-08-28 18:37:36'),
(36, 24, 'OUT', 12, 29, 17, 'REF-54FAB0CB', 'Emergency Use', NULL, '2025-07-31 20:37:36', '2025-08-28 18:37:36'),
(37, 24, 'OUT', 19, 50, 31, 'REF-1E994B66', 'Emergency Use', 1, '2025-07-26 20:37:36', '2025-08-28 18:37:36'),
(40, 23, 'OUT', 11, 47, 36, 'REF-19C490AF', 'Transfer to Other Branch', NULL, '2025-07-19 20:37:36', '2025-08-28 18:37:36'),
(41, 23, 'OUT', 12, 39, 27, 'REF-42FBF49D', 'Emergency Use', NULL, '2025-08-15 20:37:36', '2025-08-28 18:37:36'),
(42, 23, 'OUT', 19, 15, 0, 'REF-1C9EEBAF', 'Expired Stock Removal', NULL, '2025-07-04 20:37:36', '2025-08-28 18:37:36'),
(43, 23, 'ADJUSTMENT', 7, 11, 4, 'REF-03F045A8', 'Stock Count Correction', 1, '2025-07-10 20:37:36', '2025-08-28 18:37:36'),
(46, 36, 'OUT', 4, 24, 20, 'REF-68DB0A63', 'Sale to Customer', NULL, '2025-07-15 20:37:36', '2025-08-28 18:37:36'),
(47, 36, 'OUT', 20, 41, 21, 'REF-88D0AA34', 'Transfer to Other Branch', NULL, '2025-07-20 20:37:36', '2025-08-28 18:37:36'),
(48, 36, 'OUT', 19, 44, 25, 'REF-CABFB65E', 'Transfer to Other Branch', NULL, '2025-07-04 20:37:36', '2025-08-28 18:37:36'),
(49, 36, 'OUT', 7, 18, 11, 'REF-E5EE7E7E', 'Emergency Use', NULL, '2025-07-22 20:37:36', '2025-08-28 18:37:36'),
(50, 29, 'ADJUSTMENT', 19, 41, 22, 'REF-BC80159D', 'Stock Count Correction', 1, '2025-07-06 20:37:36', '2025-08-28 18:37:36'),
(51, 29, 'OUT', 13, 12, 0, 'REF-1D2C2A85', 'Transfer to Other Branch', 1, '2025-08-18 20:37:36', '2025-08-28 18:37:36'),
(52, 29, 'ADJUSTMENT', 6, 34, 28, 'REF-6C94AFCA', 'Stock Count Correction', NULL, '2025-07-14 20:37:36', '2025-08-28 18:37:36'),
(57, 31, 'ADJUSTMENT', 16, 14, 0, 'REF-94112ED9', 'Stock Count Correction', 1, '2025-07-19 20:37:36', '2025-08-28 18:37:36'),
(58, 31, 'OUT', 9, 40, 31, 'REF-2474DD3B', 'Sale to Customer', NULL, '2025-07-30 20:37:36', '2025-08-28 18:37:36'),
(59, 31, 'OUT', 17, 16, 0, 'REF-F0233C82', 'Expired Stock Removal', NULL, '2025-07-18 20:37:36', '2025-08-28 18:37:36'),
(60, 31, 'OUT', 15, 29, 14, 'REF-A81A33F8', 'Transfer to Other Branch', NULL, '2025-08-03 20:37:36', '2025-08-28 18:37:36'),
(61, 41, 'OUT', 3, 24, 21, 'REF-4AC065F8', 'Emergency Use', NULL, '2025-08-12 20:37:36', '2025-08-28 18:37:36'),
(62, 41, 'OUT', 14, 38, 24, 'REF-08C75B0C', 'Transfer to Other Branch', 1, '2025-08-11 20:37:36', '2025-08-28 18:37:36'),
(63, 41, 'ADJUSTMENT', 19, 41, 22, 'REF-97C0ABD0', 'Stock Count Correction', 1, '2025-08-10 20:37:36', '2025-08-28 18:37:36'),
(64, 41, 'OUT', 10, 22, 12, 'REF-8B5600CE', 'Transfer to Other Branch', NULL, '2025-07-31 20:37:36', '2025-08-28 18:37:36'),
(65, 41, 'OUT', 6, 28, 22, 'REF-0D89A11B', 'Transfer to Other Branch', NULL, '2025-07-17 20:37:36', '2025-08-28 18:37:36'),
(69, 44, 'IN', 31, 41, 72, 'REF-55E11C35', 'Restock from Supplier', 1, '2025-08-24 21:11:11', '2025-08-28 19:11:11'),
(70, 44, 'OUT', 16, 77, 61, 'REF-72D04B84', 'Transfer to Other Branch', 1, '2025-07-21 21:11:11', '2025-08-28 19:11:11'),
(71, 44, 'IN', 19, 60, 79, 'REF-429F1F39', 'Restock from Supplier', NULL, '2025-07-18 21:11:11', '2025-08-28 19:11:11'),
(72, 44, 'ADJUSTMENT', 41, 75, 34, 'REF-366C232C', 'Stock Count Correction', 1, '2025-08-26 21:11:11', '2025-08-28 19:11:11'),
(73, 71, 'ADJUSTMENT', 20, 100, 80, 'REF-6DB9B76F', 'Stock Count Correction', 1, '2025-06-23 21:11:11', '2025-08-28 19:11:11'),
(74, 71, 'IN', 22, 28, 50, 'REF-837F9674', 'Restock from Supplier', 1, '2025-06-26 21:11:11', '2025-08-28 19:11:11'),
(75, 71, 'OUT', 31, 72, 41, 'REF-E565E8DE', 'Transfer to Other Branch', NULL, '2025-07-02 21:11:11', '2025-08-28 19:11:11'),
(80, 11, 'IN', 22, 83, 105, 'REF-AECC8EF9', 'Restock from Supplier', NULL, '2025-06-22 21:11:11', '2025-08-28 19:11:11'),
(81, 11, 'IN', 44, 58, 102, 'REF-8142F164', 'Restock from Supplier', 1, '2025-06-17 21:11:11', '2025-08-28 19:11:11'),
(82, 10, 'IN', 28, 59, 87, 'REF-71F11FAB', 'Restock from Supplier', NULL, '2025-07-02 21:11:11', '2025-08-28 19:11:11'),
(83, 10, 'OUT', 38, 61, 23, 'REF-169E9C89', 'Transfer to Other Branch', 1, '2025-07-25 21:11:11', '2025-08-28 19:11:11'),
(84, 10, 'IN', 9, 48, 57, 'REF-4CE25BE0', 'Initial Stock', 1, '2025-08-12 21:11:11', '2025-08-28 19:11:11'),
(85, 43, 'OUT', 47, 23, 0, 'REF-CBD48029', 'Transfer to Other Branch', NULL, '2025-06-16 21:11:11', '2025-08-28 19:11:11'),
(86, 43, 'IN', 40, 95, 135, 'REF-E4B4477A', 'Initial Stock', NULL, '2025-08-03 21:11:11', '2025-08-28 19:11:11'),
(87, 43, 'OUT', 37, 55, 18, 'REF-18CC87C6', 'Sale to Customer', NULL, '2025-08-25 21:11:11', '2025-08-28 19:11:11'),
(91, 48, 'OUT', 24, 77, 53, 'REF-F6C7FD6D', 'Emergency Use', NULL, '2025-07-14 21:11:11', '2025-08-28 19:11:11'),
(92, 48, 'OUT', 14, 28, 14, 'REF-A2084D5D', 'Transfer to Other Branch', NULL, '2025-06-12 21:11:11', '2025-08-28 19:11:11'),
(109, 4, 'OUT', 20, 75, 55, 'REF-A2DC0225', 'Emergency Use', NULL, '2025-08-10 21:11:11', '2025-08-28 19:11:11'),
(110, 4, 'OUT', 23, 41, 18, 'REF-6BDA0EE0', 'Emergency Use', NULL, '2025-08-11 21:11:11', '2025-08-28 19:11:11'),
(113, 88, 'ADJUSTMENT', 36, 58, 22, 'REF-767AC996', 'Stock Count Correction', NULL, '2025-08-26 21:11:11', '2025-08-28 19:11:11'),
(114, 88, 'OUT', 21, 92, 71, 'REF-327691C0', 'Sale to Customer', 1, '2025-07-13 21:11:11', '2025-08-28 19:11:11'),
(115, 88, 'IN', 32, 61, 93, 'REF-3CDD138C', 'Restock from Supplier', 1, '2025-07-13 21:11:11', '2025-08-28 19:11:11'),
(116, 16, 'OUT', 11, 75, 64, 'REF-09F113FB', 'Emergency Use', NULL, '2025-07-11 21:11:11', '2025-08-28 19:11:11'),
(117, 16, 'IN', 23, 78, 101, 'REF-5FAFE8E2', 'Initial Stock', NULL, '2025-08-19 21:11:11', '2025-08-28 19:11:11'),
(118, 16, 'IN', 8, 70, 78, 'REF-3933C63F', 'Initial Stock', NULL, '2025-06-11 21:11:11', '2025-08-28 19:11:11'),
(119, 16, 'OUT', 18, 47, 29, 'REF-ABCA2388', 'Transfer to Other Branch', NULL, '2025-07-29 21:11:11', '2025-08-28 19:11:11'),
(139, 68, 'OUT', 92, 72, 0, 'REF-46572B79', 'Emergency Use', NULL, '2025-04-03 21:21:49', '2025-08-28 19:21:49'),
(140, 68, 'OUT', 64, 82, 18, 'REF-449553AF', 'Emergency Use', NULL, '2025-06-23 21:21:49', '2025-08-28 19:21:49'),
(141, 68, 'OUT', 21, 32, 11, 'REF-AC10A868', 'Donation to Health Center', NULL, '2025-03-14 21:21:49', '2025-08-28 19:21:49'),
(142, 68, 'IN', 26, 157, 183, 'REF-07DF174E', 'Restock from Manufacturer', NULL, '2025-07-09 21:21:49', '2025-08-28 19:21:49'),
(143, 68, 'OUT', 80, 171, 91, 'REF-2DEE5B86', 'Transfer to Other Branch', NULL, '2025-08-24 21:21:49', '2025-08-28 19:21:49'),
(144, 68, 'ADJUSTMENT', 22, 68, 46, 'REF-C4F19F36', 'Stock Count Correction', NULL, '2025-08-25 21:21:49', '2025-08-28 19:21:49'),
(171, 88, 'OUT', 96, 102, 6, 'REF-E6C193D0', 'Sale to Customer', NULL, '2025-06-21 21:21:49', '2025-08-28 19:21:49'),
(172, 88, 'IN', 43, 78, 121, 'REF-01868E97', 'Initial Stock from Supplier', NULL, '2025-03-16 21:21:49', '2025-08-28 19:21:49'),
(173, 88, 'OUT', 27, 43, 16, 'REF-3AC5FC70', 'Donation to Health Center', NULL, '2025-04-29 21:21:49', '2025-08-28 19:21:49'),
(178, 44, 'OUT', 66, 130, 64, 'REF-8EEAB649', 'Transfer to Other Branch', 1, '2025-07-27 21:21:49', '2025-08-28 19:21:49'),
(179, 44, 'OUT', 14, 130, 116, 'REF-2E441AFF', 'Donation to Health Center', NULL, '2025-06-18 21:21:49', '2025-08-28 19:21:49'),
(180, 44, 'OUT', 85, 81, 0, 'REF-EB90EE5C', 'Transfer to Other Branch', 1, '2025-07-01 21:21:49', '2025-08-28 19:21:49'),
(184, 85, 'ADJUSTMENT', 37, 186, 149, 'REF-1E5AD615', 'Stock Count Correction', NULL, '2025-08-15 21:21:49', '2025-08-28 19:21:49'),
(185, 85, 'OUT', 29, 64, 35, 'REF-601C799C', 'Sale to Customer', NULL, '2025-03-08 21:21:49', '2025-08-28 19:21:49'),
(186, 85, 'IN', 96, 42, 138, 'REF-4B20EFF3', 'Restock from Manufacturer', 1, '2025-07-21 21:21:49', '2025-08-28 19:21:49'),
(215, 10, 'OUT', 12, 83, 71, 'REF-4A83586C', 'Sale to Customer', NULL, '2025-05-09 21:21:49', '2025-08-28 19:21:49'),
(216, 10, 'OUT', 37, 80, 43, 'REF-2FA32718', 'Donation to Health Center', NULL, '2025-05-31 21:21:49', '2025-08-28 19:21:49'),
(217, 10, 'ADJUSTMENT', 81, 139, 58, 'REF-47EF60E3', 'Stock Count Correction', 1, '2025-05-03 21:21:49', '2025-08-28 19:21:49'),
(218, 10, 'OUT', 42, 69, 27, 'REF-ACA96A31', 'Expired Stock Removal', NULL, '2025-03-20 21:21:49', '2025-08-28 19:21:49'),
(219, 10, 'OUT', 30, 51, 21, 'REF-DFC80866', 'Emergency Use', NULL, '2025-04-14 21:21:49', '2025-08-28 19:21:49'),
(220, 94, 'OUT', 13, 95, 82, 'REF-87D26BA1', 'Expired Stock Removal', NULL, '2025-06-19 21:21:49', '2025-08-28 19:21:49'),
(221, 94, 'OUT', 90, 131, 41, 'REF-83609095', 'Sale to Customer', NULL, '2025-07-24 21:21:49', '2025-08-28 19:21:49'),
(222, 94, 'IN', 85, 180, 265, 'REF-D510A26C', 'Initial Stock from Supplier', 1, '2025-05-25 21:21:49', '2025-08-28 19:21:49'),
(223, 94, 'OUT', 44, 168, 124, 'REF-E5B3432C', 'Expired Stock Removal', 1, '2025-04-21 21:21:49', '2025-08-28 19:21:49'),
(224, 94, 'IN', 88, 95, 183, 'REF-3F85B746', 'Initial Stock from Supplier', NULL, '2025-04-08 21:21:49', '2025-08-28 19:21:49'),
(229, 73, 'OUT', 74, 116, 42, 'REF-6EBEDB06', 'Donation to Health Center', NULL, '2025-04-14 21:21:49', '2025-08-28 19:21:49'),
(230, 73, 'OUT', 9, 67, 58, 'REF-2066C51E', 'Expired Stock Removal', NULL, '2025-03-01 21:21:49', '2025-08-28 19:21:49'),
(231, 73, 'OUT', 97, 55, 0, 'REF-999FFDE4', 'Sale to Customer', NULL, '2025-03-08 21:21:49', '2025-08-28 19:21:49'),
(232, 73, 'OUT', 87, 117, 30, 'REF-E1EAC133', 'Donation to Health Center', NULL, '2025-03-15 21:21:49', '2025-08-28 19:21:49'),
(242, 74, 'ADJUSTMENT', 29, 178, 149, 'REF-10274C70', 'Stock Count Correction', NULL, '2025-04-26 21:21:49', '2025-08-28 19:21:49'),
(243, 74, 'OUT', 100, 183, 83, 'REF-C3D5D45E', 'Donation to Health Center', NULL, '2025-08-25 21:21:49', '2025-08-28 19:21:49'),
(244, 74, 'OUT', 47, 81, 34, 'REF-EB406BA7', 'Expired Stock Removal', 1, '2025-07-02 21:21:49', '2025-08-28 19:21:49');

-- --------------------------------------------------------

--
-- Table structure for table `suspicious_transactions`
--

CREATE TABLE `suspicious_transactions` (
  `suspicious_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `pattern_type` enum('UNUSUAL_AMOUNT','FREQUENT_PURCHASES','OFF_HOURS','SUSPICIOUS_MEDICINE','MULTIPLE_FAILURES') NOT NULL,
  `risk_score` int(11) NOT NULL DEFAULT 0,
  `status` enum('OPEN','INVESTIGATING','RESOLVED','ESCALATED') DEFAULT 'OPEN',
  `description` text NOT NULL,
  `exposure_amount` decimal(10,2) DEFAULT 0.00,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_exceptions`
--

CREATE TABLE `transaction_exceptions` (
  `exception_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `exception_type` enum('AMOUNT_MISMATCH','DUPLICATE_TRANSACTION','INVALID_MEDICINE','SYSTEM_ERROR','MANUAL_REVIEW') NOT NULL,
  `severity` enum('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL,
  `status` enum('OPEN','INVESTIGATING','RESOLVED','ESCALATED') DEFAULT 'OPEN',
  `description` text NOT NULL,
  `exposure_amount` decimal(10,2) DEFAULT 0.00,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$0r1ZSwTHkr2CDvAulfu1p.jV5HYSnC.x9zPYFveP/sgZart90QeAm', 'admin@test.com', 'super_admin', '2025-09-13 19:14:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `uk_admin_email` (`email`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id_idx` (`admin_id`);

--
-- Indexes for table `branch_reconciliation`
--
ALTER TABLE `branch_reconciliation`
  ADD PRIMARY KEY (`reconciliation_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `daily_revenue`
--
ALTER TABLE `daily_revenue`
  ADD PRIMARY KEY (`revenue_id`),
  ADD UNIQUE KEY `unique_daily_revenue` (`revenue_date`,`pharmacy_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `expiry_alerts`
--
ALTER TABLE `expiry_alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD UNIQUE KEY `uniq_medicine_expiry` (`medicine_id`,`expires_on`);

--
-- Indexes for table `failed_payments`
--
ALTER TABLE `failed_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `medical_staff`
--
ALTER TABLE `medical_staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `fk_medical_staff_pharmacy` (`pharmacy_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `fk_medicine_category` (`category_id`);

--
-- Indexes for table `ml_predictions`
--
ALTER TABLE `ml_predictions`
  ADD PRIMARY KEY (`prediction_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `ml_training_data`
--
ALTER TABLE `ml_training_data`
  ADD PRIMARY KEY (`data_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `outstanding_balances`
--
ALTER TABLE `outstanding_balances`
  ADD PRIMARY KEY (`balance_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `pharmacies`
--
ALTER TABLE `pharmacies`
  ADD PRIMARY KEY (`pharmacy_id`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `pickup_reminders`
--
ALTER TABLE `pickup_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_reminder_date` (`reminder_date`),
  ADD KEY `idx_pickup_date` (`pickup_date`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `recall_alerts`
--
ALTER TABLE `recall_alerts`
  ADD PRIMARY KEY (`recall_id`);

--
-- Indexes for table `refund_requests`
--
ALTER TABLE `refund_requests`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token_hash` (`token_hash`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pharmacy_id` (`pharmacy_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `sms_notifications`
--
ALTER TABLE `sms_notifications`
  ADD PRIMARY KEY (`sms_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `special_pharmacy_requests`
--
ALTER TABLE `special_pharmacy_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `requested_by` (`requested_by`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`movement_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `suspicious_transactions`
--
ALTER TABLE `suspicious_transactions`
  ADD PRIMARY KEY (`suspicious_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `transaction_exceptions`
--
ALTER TABLE `transaction_exceptions`
  ADD PRIMARY KEY (`exception_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=379;

--
-- AUTO_INCREMENT for table `branch_reconciliation`
--
ALTER TABLE `branch_reconciliation`
  MODIFY `reconciliation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `daily_revenue`
--
ALTER TABLE `daily_revenue`
  MODIFY `revenue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expiry_alerts`
--
ALTER TABLE `expiry_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `failed_payments`
--
ALTER TABLE `failed_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `medical_staff`
--
ALTER TABLE `medical_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `ml_predictions`
--
ALTER TABLE `ml_predictions`
  MODIFY `prediction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ml_training_data`
--
ALTER TABLE `ml_training_data`
  MODIFY `data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outstanding_balances`
--
ALTER TABLE `outstanding_balances`
  MODIFY `balance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pharmacies`
--
ALTER TABLE `pharmacies`
  MODIFY `pharmacy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pickup_reminders`
--
ALTER TABLE `pickup_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recall_alerts`
--
ALTER TABLE `recall_alerts`
  MODIFY `recall_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refund_requests`
--
ALTER TABLE `refund_requests`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sms_notifications`
--
ALTER TABLE `sms_notifications`
  MODIFY `sms_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `special_pharmacy_requests`
--
ALTER TABLE `special_pharmacy_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;

--
-- AUTO_INCREMENT for table `suspicious_transactions`
--
ALTER TABLE `suspicious_transactions`
  MODIFY `suspicious_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transaction_exceptions`
--
ALTER TABLE `transaction_exceptions`
  MODIFY `exception_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `branch_reconciliation`
--
ALTER TABLE `branch_reconciliation`
  ADD CONSTRAINT `fk_reconciliation_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reconciliation_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_revenue`
--
ALTER TABLE `daily_revenue`
  ADD CONSTRAINT `fk_daily_revenue_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;

--
-- Constraints for table `failed_payments`
--
ALTER TABLE `failed_payments`
  ADD CONSTRAINT `fk_failed_payment_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_failed_payment_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_staff`
--
ALTER TABLE `medical_staff`
  ADD CONSTRAINT `fk_medical_staff_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `fk_medicine_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`);

--
-- Constraints for table `ml_predictions`
--
ALTER TABLE `ml_predictions`
  ADD CONSTRAINT `ml_predictions_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`),
  ADD CONSTRAINT `ml_predictions_ibfk_2` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`),
  ADD CONSTRAINT `ml_predictions_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `ml_training_data`
--
ALTER TABLE `ml_training_data`
  ADD CONSTRAINT `ml_training_data_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`),
  ADD CONSTRAINT `ml_training_data_ibfk_2` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`),
  ADD CONSTRAINT `ml_training_data_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `outstanding_balances`
--
ALTER TABLE `outstanding_balances`
  ADD CONSTRAINT `fk_outstanding_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_outstanding_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `refund_requests`
--
ALTER TABLE `refund_requests`
  ADD CONSTRAINT `fk_refund_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_refund_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_refund_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `sms_notifications`
--
ALTER TABLE `sms_notifications`
  ADD CONSTRAINT `sms_notifications_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `special_pharmacy_requests`
--
ALTER TABLE `special_pharmacy_requests`
  ADD CONSTRAINT `special_pharmacy_requests_ibfk_1` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`),
  ADD CONSTRAINT `special_pharmacy_requests_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`),
  ADD CONSTRAINT `special_pharmacy_requests_ibfk_3` FOREIGN KEY (`requested_by`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_stockmovement_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stockmovement_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `suspicious_transactions`
--
ALTER TABLE `suspicious_transactions`
  ADD CONSTRAINT `fk_suspicious_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_suspicious_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_exceptions`
--
ALTER TABLE `transaction_exceptions`
  ADD CONSTRAINT `fk_exception_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exception_pharmacy` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacies` (`pharmacy_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
