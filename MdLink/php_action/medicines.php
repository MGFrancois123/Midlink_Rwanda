<?php
require_once '../constant/connect.php';

header('Content-Type: application/json');

// Get pharmacy context
$pharmacyId = $_SESSION['pharmacy_id'] ?? 8; // Default to Ineza Pharmacy

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createMedicine($connect, $pharmacyId);
        break;
        
    case 'get':
        getMedicine($connect, $pharmacyId);
        break;
        
    case 'update':
        updateMedicine($connect, $pharmacyId);
        break;
        
    case 'update_stock':
        updateStock($connect, $pharmacyId);
        break;
        
    case 'delete':
        deleteMedicine($connect, $pharmacyId);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function createMedicine($connect, $pharmacyId) {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stockQuantity = $_POST['stock_quantity'] ?? 0;
    $expiryDate = $_POST['expiry_date'] ?? '';
    $batchNumber = $_POST['batch_number'] ?? '';
    $supplier = $_POST['supplier'] ?? '';
    $isRestricted = $_POST['is_restricted'] ?? 0;
    $description = $_POST['description'] ?? '';
    
    if (empty($name) || empty($category) || empty($price) || empty($expiryDate)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        return;
    }
    
    // Map category name to ID
    $categoryMap = [
        'Antibiotics' => 1,
        'Pain Relief' => 2,
        'Cardiovascular' => 3,
        'Diabetes' => 4,
        'Respiratory' => 5,
        'Gastrointestinal' => 6,
        'Vitamins' => 7,
        'Supplements' => 8,
        'Topical' => 9,
        'Injection' => 10,
        'Other' => 11
    ];
    
    $categoryId = $categoryMap[$category] ?? 11;
    
    // Combine description with batch and supplier info
    $fullDescription = $description;
    if (!empty($batchNumber)) {
        $fullDescription .= "\n\nBatch: " . $batchNumber;
    }
    if (!empty($supplier)) {
        $fullDescription .= "\nSupplier: " . $supplier;
    }
    
    $sql = "INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, `Restricted Medicine`, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("issdisii", $pharmacyId, $name, $fullDescription, $price, $stockQuantity, $expiryDate, $isRestricted, $categoryId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Medicine created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create medicine: ' . $connect->error]);
    }
}

function getMedicine($connect, $pharmacyId) {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Medicine ID required']);
        return;
    }
    
    $sql = "SELECT * FROM medicines WHERE medicine_id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ii", $id, $pharmacyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($medicine = $result->fetch_assoc()) {
        // Map category ID to name
        $categoryMap = [
            1 => 'Antibiotics',
            2 => 'Pain Relief',
            3 => 'Cardiovascular',
            4 => 'Diabetes',
            5 => 'Respiratory',
            6 => 'Gastrointestinal',
            7 => 'Vitamins',
            8 => 'Supplements',
            9 => 'Topical',
            10 => 'Injection',
            11 => 'Other'
        ];
        
        $medicine['category'] = $categoryMap[$medicine['category_id']] ?? 'Other';
        $medicine['is_restricted'] = $medicine['Restricted Medicine'];
        
        // Extract batch and supplier from description
        $batchNumber = '';
        $supplier = '';
        if (preg_match('/Batch:\s*([^\n]+)/', $medicine['description'], $matches)) {
            $batchNumber = trim($matches[1]);
        }
        if (preg_match('/Supplier:\s*([^\n]+)/', $medicine['description'], $matches)) {
            $supplier = trim($matches[1]);
        }
        
        $medicine['batch_number'] = $batchNumber;
        $medicine['supplier'] = $supplier;
        
        echo json_encode(['success' => true, 'data' => $medicine]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Medicine not found']);
    }
}

function updateMedicine($connect, $pharmacyId) {
    $id = $_POST['id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stockQuantity = $_POST['stock_quantity'] ?? 0;
    $expiryDate = $_POST['expiry_date'] ?? '';
    $batchNumber = $_POST['batch_number'] ?? '';
    $supplier = $_POST['supplier'] ?? '';
    $isRestricted = $_POST['is_restricted'] ?? 0;
    $description = $_POST['description'] ?? '';
    $minStock = $_POST['min_stock'] ?? 10;
    
    if (!$id || empty($name) || empty($category) || empty($price) || empty($expiryDate)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        return;
    }
    
    $sql = "UPDATE medicines SET name = ?, category = ?, price = ?, stock_quantity = ?, expiry_date = ?, batch_number = ?, supplier = ?, is_restricted = ?, description = ?, min_stock = ? WHERE id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssdisssisiii", $name, $category, $price, $stockQuantity, $expiryDate, $batchNumber, $supplier, $isRestricted, $description, $minStock, $id, $pharmacyId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Medicine updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update medicine: ' . $connect->error]);
    }
}

function updateStock($connect, $pharmacyId) {
    $medicineId = $_POST['medicine_id'] ?? 0;
    $movementType = $_POST['movement_type'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $notes = $_POST['notes'] ?? '';
    
    if (!$medicineId || !$movementType || !$quantity) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        return;
    }
    
    // Get current stock
    $sql = "SELECT stock_quantity FROM medicines WHERE id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ii", $medicineId, $pharmacyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$medicine = $result->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Medicine not found']);
        return;
    }
    
    $currentStock = $medicine['stock_quantity'];
    $newStock = $currentStock;
    
    // Calculate new stock based on movement type
    switch ($movementType) {
        case 'in':
            $newStock = $currentStock + $quantity;
            break;
        case 'out':
            $newStock = $currentStock - $quantity;
            if ($newStock < 0) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock. Current stock: ' . $currentStock]);
                return;
            }
            break;
        case 'adjustment':
            $newStock = $quantity;
            break;
    }
    
    // Update medicine stock
    $updateSql = "UPDATE medicines SET stock_quantity = ? WHERE id = ? AND pharmacy_id = ?";
    $updateStmt = $connect->prepare($updateSql);
    $updateStmt->bind_param("iii", $newStock, $medicineId, $pharmacyId);
    
    if ($updateStmt->execute()) {
        // Log stock movement
        $logSql = "INSERT INTO stock_movements (pharmacy_id, medicine_id, movement_type, quantity, previous_stock, new_stock, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $logStmt = $connect->prepare($logSql);
        $userId = $_SESSION['user_id'] ?? 1;
        $logStmt->bind_param("iisiiisi", $pharmacyId, $medicineId, $movementType, $quantity, $currentStock, $newStock, $notes, $userId);
        $logStmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update stock: ' . $connect->error]);
    }
}

function deleteMedicine($connect, $pharmacyId) {
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Medicine ID required']);
        return;
    }
    
    $sql = "DELETE FROM medicines WHERE id = ? AND pharmacy_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ii", $id, $pharmacyId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Medicine deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete medicine: ' . $connect->error]);
    }
}
?>
