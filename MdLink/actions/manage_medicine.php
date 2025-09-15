<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get the action (add or update)
$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = [];

try {
    if ($action === 'add' || $action === 'update') {
        // Validate required fields
        $required = ['name', 'pharmacy_id', 'category_id', 'price', 'expiry_date'];
        $errors = [];
        
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        if (!empty($errors)) {
            throw new Exception(implode('<br>', $errors));
        }
        
        // Sanitize input data
        $medicineId = isset($_POST['medicine_id']) ? intval($_POST['medicine_id']) : 0;
        $name = $connect->real_escape_string(trim($_POST['name']));
        $pharmacyId = intval($_POST['pharmacy_id']);
        $categoryId = intval($_POST['category_id']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $price = floatval($_POST['price']);
        $expiryDate = $connect->real_escape_string(trim($_POST['expiry_date']));
        $description = isset($_POST['description']) ? $connect->real_escape_string(trim($_POST['description'])) : '';
        $restricted = isset($_POST['restricted_medicine']) ? intval($_POST['restricted_medicine']) : 0;
        $status = 1; // Default status to active
        
        if ($action === 'add') {
            // Insert new medicine
            $query = "INSERT INTO medicines (name, description, price, stock_quantity, expiry_date, 
                     `Restricted Medicine`, category_id, pharmacy_id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $connect->prepare($query);
            $stmt->bind_param(
                'ssddsiii',
                $name,
                $description,
                $price,
                $quantity,
                $expiryDate,
                $restricted,
                $categoryId,
                $pharmacyId
            );
            
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Medicine added successfully',
                    'id' => $stmt->insert_id,
                    'success' => true
                ];
            } else {
                throw new Exception('Failed to add medicine: ' . $stmt->error);
            }
            
        } elseif ($action === 'update' && $medicineId > 0) {
            // Update existing medicine
            $query = "UPDATE medicines SET 
                     name = ?, 
                     description = ?, 
                     price = ?, 
                     stock_quantity = ?, 
                     expiry_date = ?, 
                     `Restricted Medicine` = ?, 
                     category_id = ?, 
                     pharmacy_id = ?
                     WHERE medicine_id = ?";
            
            $stmt = $connect->prepare($query);
            $stmt->bind_param(
                'ssddsiiii',
                $name,
                $description,
                $price,
                $quantity,
                $expiryDate,
                $restricted,
                $categoryId,
                $pharmacyId,
                $medicineId
            );
            
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Medicine updated successfully',
                    'id' => $medicineId,
                    'success' => true
                ];
            } else {
                throw new Exception('Failed to update medicine: ' . $stmt->error);
            }
        } else {
            throw new Exception('Invalid action or medicine ID');
        }
        
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['medicine_id'])) {
        // Handle delete action
        $medicineId = intval($_POST['medicine_id']);
        
        $query = "DELETE FROM medicines WHERE medicine_id = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param('i', $medicineId);
        
        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Medicine deleted successfully'
            ];
        } else {
            throw new Exception('Failed to delete medicine: ' . $stmt->error);
        }
        
    } else {
        throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
$connect->close();
?>
