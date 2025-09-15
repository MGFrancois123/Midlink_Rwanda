<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add_movement':
            $medicine_id = $_POST['medicine_id'] ?? '';
            $movement_type = $_POST['movement_type'] ?? '';
            $quantity = $_POST['quantity'] ?? 0;
            $notes = $_POST['notes'] ?? '';
            
            if (empty($medicine_id) || empty($movement_type) || empty($quantity)) {
                throw new Exception('Required fields are missing');
            }
            
            // Get current stock
            $stmt = $connect->prepare("SELECT stock_quantity FROM medicines WHERE medicine_id = ?");
            $stmt->bind_param("i", $medicine_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $medicine = $result->fetch_assoc();
            
            if (!$medicine) {
                throw new Exception('Medicine not found');
            }
            
            $previous_stock = $medicine['stock_quantity'];
            $new_stock = $previous_stock;
            
            // Calculate new stock based on movement type
            switch ($movement_type) {
                case 'IN':
                    $new_stock = $previous_stock + $quantity;
                    break;
                case 'OUT':
                    if ($quantity > $previous_stock) {
                        throw new Exception('Insufficient stock for this movement');
                    }
                    $new_stock = $previous_stock - $quantity;
                    break;
                case 'ADJUSTMENT':
                    $new_stock = $quantity; // Direct adjustment
                    break;
                default:
                    throw new Exception('Invalid movement type');
            }
            
            // Update medicine stock
            $stmt = $connect->prepare("UPDATE medicines SET stock_quantity = ? WHERE medicine_id = ?");
            $stmt->bind_param("ii", $new_stock, $medicine_id);
            $stmt->execute();
            
            // Record movement
            $stmt = $connect->prepare("INSERT INTO stock_movements (medicine_id, movement_type, quantity, previous_stock, new_stock, reference_number, notes, admin_id, movement_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $ref_number = 'REF-' . strtoupper(substr(md5(rand()), 0, 8));
            $admin_id = $_SESSION['admin_id'] ?? 1;
            $stmt->bind_param("isiiissi", $medicine_id, $movement_type, $quantity, $previous_stock, $new_stock, $ref_number, $notes, $admin_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Stock movement recorded successfully']);
            } else {
                throw new Exception('Failed to record stock movement');
            }
            break;
            
        case 'get_stock':
            $medicine_id = $_POST['medicine_id'] ?? $_GET['medicine_id'] ?? '';
            
            if (empty($medicine_id)) {
                throw new Exception('Medicine ID is required');
            }
            
            $stmt = $connect->prepare("SELECT m.*, p.name as pharmacy_name, c.category_name 
                                      FROM medicines m 
                                      LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id 
                                      LEFT JOIN category c ON m.category_id = c.category_id 
                                      WHERE m.medicine_id = ?");
            $stmt->bind_param("i", $medicine_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $medicine = $result->fetch_assoc();
            
            if (!$medicine) {
                throw new Exception('Medicine not found');
            }
            
            echo json_encode(['success' => true, 'medicine' => $medicine]);
            break;
            
        case 'list_movements':
            $medicine_id = $_POST['medicine_id'] ?? $_GET['medicine_id'] ?? '';
            $limit = $_POST['limit'] ?? $_GET['limit'] ?? 50;
            
            $sql = "SELECT sm.*, m.name as medicine_name, p.name as pharmacy_name 
                    FROM stock_movements sm 
                    LEFT JOIN medicines m ON sm.medicine_id = m.medicine_id 
                    LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id";
            
            if (!empty($medicine_id)) {
                $sql .= " WHERE sm.medicine_id = " . intval($medicine_id);
            }
            
            $sql .= " ORDER BY sm.movement_date DESC LIMIT " . intval($limit);
            
            $result = $connect->query($sql);
            $movements = [];
            
            while ($row = $result->fetch_assoc()) {
                $movements[] = $row;
            }
            
            echo json_encode(['success' => true, 'movements' => $movements]);
            break;
            
        case 'low_stock_report':
            $threshold = $_POST['threshold'] ?? $_GET['threshold'] ?? 10;
            
            $sql = "SELECT m.*, p.name as pharmacy_name, c.category_name 
                    FROM medicines m 
                    LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id 
                    LEFT JOIN category c ON m.category_id = c.category_id 
                    WHERE m.stock_quantity <= ? 
                    ORDER BY m.stock_quantity ASC";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $threshold);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $medicines = [];
            while ($row = $result->fetch_assoc()) {
                $medicines[] = $row;
            }
            
            echo json_encode(['success' => true, 'medicines' => $medicines]);
            break;
            
        case 'expiry_report':
            $days = $_POST['days'] ?? $_GET['days'] ?? 30;
            
            $sql = "SELECT m.*, p.name as pharmacy_name, c.category_name 
                    FROM medicines m 
                    LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id 
                    LEFT JOIN category c ON m.category_id = c.category_id 
                    WHERE m.expiry_date IS NOT NULL 
                    AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                    ORDER BY m.expiry_date ASC";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $days);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $medicines = [];
            while ($row = $result->fetch_assoc()) {
                $medicines[] = $row;
            }
            
            echo json_encode(['success' => true, 'medicines' => $medicines]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
