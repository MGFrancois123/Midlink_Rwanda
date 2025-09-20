<?php
session_start();
include('../constant/connect.php');

// Check if user is logged in
if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$user_id = $_SESSION['adminId'];
$cart_id = (int)$_POST['cart_id'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

try {
    // Verify cart item belongs to user and get medicine details
    $verify_sql = "SELECT c.cart_id, c.medicine_id, m.price, m.stock_quantity 
                   FROM cart c 
                   JOIN medicines m ON c.medicine_id = m.medicine_id 
                   WHERE c.cart_id = ? AND c.user_id = ?";
    $verify_stmt = $connect->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $cart_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        exit;
    }
    
    $cart_item = $verify_result->fetch_assoc();
    
    // Check if quantity exceeds stock
    if ($quantity > $cart_item['stock_quantity']) {
        echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock']);
        exit;
    }
    
    // Update cart quantity
    $update_sql = "UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ?";
    $update_stmt = $connect->prepare($update_sql);
    $update_stmt->bind_param("ii", $quantity, $cart_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update cart quantity');
    }
    
    // Calculate new item total and cart total
    $item_total = $quantity * $cart_item['price'];
    
    // Get updated cart total
    $total_sql = "SELECT SUM(c.quantity * m.price) as cart_total 
                  FROM cart c 
                  JOIN medicines m ON c.medicine_id = m.medicine_id 
                  WHERE c.user_id = ?";
    $total_stmt = $connect->prepare($total_sql);
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $cart_total = $total_result->fetch_assoc()['cart_total'] ?: 0;
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully',
        'item_total' => $item_total,
        'cart_total' => $cart_total,
        'quantity' => $quantity
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$connect->close();
?>