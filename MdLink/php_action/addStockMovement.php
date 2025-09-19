<?php
require_once '../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $medicine_id = (int)$_POST['medicine_id'];
    $movement_type = $_POST['movement_type'];
    $quantity = (int)$_POST['quantity'];
    $reference_number = $_POST['reference_number'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $user_id = $_SESSION['userId'] ?? 1; // Default to user 1 if no session

    // Validate inputs
    if (!$medicine_id || !$movement_type || !$quantity) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Get current stock
    $sql = "SELECT stock_quantity FROM medicines WHERE medicine_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $medicine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Medicine not found']);
        exit;
    }
    
    $medicine = $result->fetch_assoc();
    $previous_stock = $medicine['stock_quantity'];
    
    // Calculate new stock based on movement type
    $new_stock = $previous_stock;
    switch ($movement_type) {
        case 'IN':
            $new_stock = $previous_stock + $quantity;
            break;
        case 'OUT':
            if ($quantity > $previous_stock) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock. Available: ' . $previous_stock]);
                exit;
            }
            $new_stock = $previous_stock - $quantity;
            break;
        case 'ADJUSTMENT':
            // For adjustments, quantity represents the new total stock
            $new_stock = $quantity;
            $quantity = $new_stock - $previous_stock; // Calculate the difference
            break;
        case 'EXPIRED':
            // For expired stock, remove all current stock if quantity is 0, otherwise remove specified quantity
            if ($quantity == 0) {
                $quantity = $previous_stock;
            }
            if ($quantity > $previous_stock) {
                echo json_encode(['success' => false, 'message' => 'Cannot remove more than available stock']);
                exit;
            }
            $new_stock = $previous_stock - $quantity;
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid movement type']);
            exit;
    }

    // Ensure stock doesn't go negative
    if ($new_stock < 0) {
        $new_stock = 0;
    }

    // Start transaction
    $connect->begin_transaction();

    try {
        // Create stock movement record
        $sql = "INSERT INTO stock_movements (medicine_id, movement_type, quantity, previous_stock, new_stock, reference_number, notes, user_id, movement_date, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param('isiisssi', $medicine_id, $movement_type, $quantity, $previous_stock, $new_stock, $reference_number, $notes, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create stock movement record');
        }

        // Update medicine stock
        $sql = "UPDATE medicines SET stock_quantity = ? WHERE medicine_id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param('ii', $new_stock, $medicine_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update medicine stock');
        }

        // Commit transaction
        $connect->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Stock movement recorded successfully',
            'previous_stock' => $previous_stock,
            'new_stock' => $new_stock
        ]);

    } catch (Exception $e) {
        $connect->rollback();
        echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>