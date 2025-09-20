<?php
session_start();
include('../constant/connect.php');

// Check if user is logged in
if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['medicine_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$user_id = $_SESSION['adminId'];
$medicine_id = (int)$_POST['medicine_id'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// Check if medicine exists and has sufficient stock
$medicine_sql = "SELECT medicine_id, name, stock_quantity, price FROM medicines WHERE medicine_id = ?";
$medicine_stmt = $connect->prepare($medicine_sql);
$medicine_stmt->bind_param("i", $medicine_id);
$medicine_stmt->execute();
$medicine_result = $medicine_stmt->get_result();

if ($medicine_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Medicine not found']);
    exit;
}

$medicine = $medicine_result->fetch_assoc();

if ($medicine['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock available']);
    exit;
}

try {
    // Check if item already exists in cart
    $check_sql = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND medicine_id = ?";
    $check_stmt = $connect->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $medicine_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing cart item
        $existing_cart = $check_result->fetch_assoc();
        $new_quantity = $existing_cart['quantity'] + $quantity;
        
        // Check if new total quantity exceeds stock
        if ($new_quantity > $medicine['stock_quantity']) {
            echo json_encode(['success' => false, 'message' => 'Total quantity would exceed available stock']);
            exit;
        }
        
        $update_sql = "UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ?";
        $update_stmt = $connect->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $existing_cart['cart_id']);
        
        if ($update_stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Cart updated successfully',
                'medicine_name' => $medicine['name'],
                'total_quantity' => $new_quantity
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
    } else {
        // Add new item to cart
        $insert_sql = "INSERT INTO cart (user_id, medicine_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $connect->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $user_id, $medicine_id, $quantity);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Item added to cart successfully',
                'medicine_name' => $medicine['name'],
                'quantity' => $quantity
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
        }
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$connect->close();
?>