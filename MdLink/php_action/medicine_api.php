<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $pharmacy_id = $_POST['pharmacy_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $stock_quantity = $_POST['stock_quantity'] ?? 0;
            $category_id = $_POST['category_id'] ?? '';
            $expiry_date = $_POST['expiry_date'] ?? '';
            $restricted = $_POST['restricted'] ?? '0';
            
            if (empty($name) || empty($pharmacy_id) || empty($category_id)) {
                throw new Exception('Required fields are missing');
            }
            
            $stmt = $connect->prepare("INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, Restricted_Medicine, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdsssi", $pharmacy_id, $name, $description, $price, $stock_quantity, $expiry_date, $restricted, $category_id);
            
            if ($stmt->execute()) {
                $medicine_id = $connect->insert_id;
                
                // Record initial stock movement
                $stmt2 = $connect->prepare("INSERT INTO stock_movements (medicine_id, movement_type, quantity, previous_stock, new_stock, reference_number, notes, admin_id, movement_date) VALUES (?, 'IN', ?, 0, ?, ?, ?, ?, NOW())");
                $ref_number = 'INIT-' . strtoupper(substr(md5(rand()), 0, 8));
                $notes = 'Initial stock entry';
                $admin_id = $_SESSION['admin_id'] ?? 1;
                $stmt2->bind_param("iiissi", $medicine_id, $stock_quantity, $stock_quantity, $ref_number, $notes, $admin_id);
                $stmt2->execute();
                
                echo json_encode(['success' => true, 'message' => 'Medicine added successfully', 'medicine_id' => $medicine_id]);
            } else {
                throw new Exception('Failed to add medicine');
            }
            break;
            
        case 'update':
            $medicine_id = $_POST['medicine_id'] ?? '';
            $name = $_POST['name'] ?? '';
            $pharmacy_id = $_POST['pharmacy_id'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $category_id = $_POST['category_id'] ?? '';
            $expiry_date = $_POST['expiry_date'] ?? '';
            $restricted = $_POST['restricted'] ?? '0';
            
            if (empty($medicine_id) || empty($name) || empty($pharmacy_id) || empty($category_id)) {
                throw new Exception('Required fields are missing');
            }
            
            $stmt = $connect->prepare("UPDATE medicines SET pharmacy_id = ?, name = ?, description = ?, price = ?, expiry_date = ?, Restricted_Medicine = ?, category_id = ? WHERE medicine_id = ?");
            $stmt->bind_param("issdsssi", $pharmacy_id, $name, $description, $price, $expiry_date, $restricted, $category_id, $medicine_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Medicine updated successfully']);
            } else {
                throw new Exception('Failed to update medicine');
            }
            break;
            
        case 'delete':
            $medicine_id = $_POST['medicine_id'] ?? '';
            
            if (empty($medicine_id)) {
                throw new Exception('Medicine ID is required');
            }
            
            $stmt = $connect->prepare("DELETE FROM medicines WHERE medicine_id = ?");
            $stmt->bind_param("i", $medicine_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Medicine deleted successfully']);
            } else {
                throw new Exception('Failed to delete medicine');
            }
            break;
            
        case 'list':
            $medicines = [];
            $sql = "SELECT m.*, p.name as pharmacy_name, c.category_name 
                    FROM medicines m 
                    LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id 
                    LEFT JOIN category c ON m.category_id = c.category_id 
                    ORDER BY m.medicine_id DESC";
            
            $result = $connect->query($sql);
            while ($row = $result->fetch_assoc()) {
                $medicines[] = $row;
            }
            
            echo json_encode(['success' => true, 'items' => $medicines]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
