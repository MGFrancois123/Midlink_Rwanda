-- Create stock_movements table for tracking all stock changes
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `movement_id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `movement_type` enum('IN','OUT','ADJUSTMENT','EXPIRED') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movement_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`movement_id`),
  KEY `idx_medicine_id` (`medicine_id`),
  KEY `idx_movement_date` (`movement_date`),
  KEY `idx_movement_type` (`movement_type`),
  CONSTRAINT `fk_stock_movements_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_stock_movements_date_type` ON `stock_movements` (`movement_date`, `movement_type`);
CREATE INDEX IF NOT EXISTS `idx_stock_movements_medicine_date` ON `stock_movements` (`medicine_id`, `movement_date`);

-- Insert some sample stock movements (optional)
-- You can uncomment these if you want sample data
/*
INSERT INTO `stock_movements` (`medicine_id`, `movement_type`, `quantity`, `previous_stock`, `new_stock`, `reference_number`, `notes`, `user_id`, `movement_date`) VALUES
(1, 'IN', 100, 0, 100, 'PO-2024-001', 'Initial stock purchase', 1, '2024-01-15 10:00:00'),
(1, 'OUT', 25, 100, 75, 'SALE-001', 'Sold to customer', 1, '2024-01-16 14:30:00'),
(2, 'IN', 50, 0, 50, 'PO-2024-002', 'Stock replenishment', 1, '2024-01-17 09:15:00'),
(1, 'ADJUSTMENT', 5, 75, 70, 'ADJ-001', 'Stock count adjustment', 1, '2024-01-18 16:45:00');
*/