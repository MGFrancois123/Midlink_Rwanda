<?php
session_start();
include('../constant/connect.php');

// Check if user is logged in
if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing cart ID']);
    exit;
}

$user_id = $_SESSION['adminId'];
$cart_id = (int)$_POST['cart_id'];

try {
    // Verify cart item belongs to user
    $verify_sql = "SELECT cart_id FROM cart WHERE cart_id = ? AND user_id = ?";
    $verify_stmt = $connect->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $cart_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        exit;
    }
    
    // Delete cart item
    $delete_sql = "DELETE FROM cart WHERE cart_id = ?";
    $delete_stmt = $connect->prepare($delete_sql);
    $delete_stmt->bind_param("i", $cart_id);
    
    if (!$delete_stmt->execute()) {
        throw new Exception('Failed to remove item from cart');
    }
    
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
        'message' => 'Item removed from cart successfully',
        'cart_total' => $cart_total
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$connect->close();
?>