
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 06:10 PM
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
-- Database: `mdlink`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','pharmacy_admin','finance_admin') NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `username`, `password_hash`, `role`, `email`, `phone`, `created_at`) VALUES
(1, 'Frederic', 'b93c605ed4f5010c40b442dab5574ca5', 'super_admin', 'nzamurambahofrederic28@gmail.com', '0791905996', '2025-08-27 17:40:48'),
(2, 'Eric', 'b93c605ed4f5010c40b442dab5574ca5', 'pharmacy_admin', 'nzamfred3@gmail.com', '0792021423', '2025-08-27 17:42:49'),
(3, 'Francois', 'b93c605ed4f5010c40b442dab5574ca5', 'finance_admin', 'fredynation79@gmail.com', '0792021423', '2025-08-27 17:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `action` enum('CREATE','UPDATE','DELETE') NOT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Anti-infectives', 'Treat infections caused by bacteria, viruses, fungi, or parasites.', '1', '2025-08-28 03:33:46', '2025-08-28 03:33:46');

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
  `stock_quantity` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `Restricted_Medicine` varchar(30) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `pharmacy_id`, `name`, `description`, `price`, `stock_quantity`, `expiry_date`, `Restricted_Medicine`, `category_id`) VALUES
(1, 1, 'Amoxicillin', 'Treat infections caused by bacteria, viruses, fungi, or parasites.', 400.00, 46, '2025-08-28', '1', 1);

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('mobile_money','card','cash') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacies`
--

INSERT INTO `pharmacies` (`pharmacy_id`, `name`, `location`, `license_number`, `contact_person`, `contact_phone`, `created_at`) VALUES
(1, 'KEZA PHARMACY', 'Umujyi wa Kigali / Gasabo / Kacyiru / Kamutwa / Umuco', 'MDLink-5083-0166', 'Dr Frederic', '+250791905996', '2025-08-28 03:30:19');

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
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id_idx` (`admin_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

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
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `admin_id` (`admin_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacies`
--
ALTER TABLE `pharmacies`
  MODIFY `pharmacy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
