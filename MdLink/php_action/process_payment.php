<?php
session_start();
include('../constant/connect.php');

// Check if user is logged in
if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['medicine_id']) || !isset($_POST['quantity']) || !isset($_POST['payment_method']) || !isset($_POST['total_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$user_id = $_SESSION['adminId'];
$medicine_id = (int)$_POST['medicine_id'];
$quantity = (int)$_POST['quantity'];
$payment_method = $_POST['payment_method'];
$total_amount = (float)$_POST['total_amount'];

// Validate inputs
if ($quantity <= 0 || $total_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity or amount']);
    exit;
}

if (!in_array($payment_method, ['card', 'mobile_money'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
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

// Verify the total amount
$expected_total = $medicine['price'] * $quantity;
if (abs($total_amount - $expected_total) > 0.01) {
    echo json_encode(['success' => false, 'message' => 'Invalid total amount']);
    exit;
}

try {
    // Start transaction
    $connect->autocommit(FALSE);
    
    // Generate unique order number
    $order_number = 'ORD' . date('Ymd') . sprintf('%06d', rand(1, 999999));
    
    // Insert order into order_history
    $order_sql = "INSERT INTO order_history (user_id, order_number, total_amount, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, 'completed', 'processing')";
    $order_stmt = $connect->prepare($order_sql);
    $order_stmt->bind_param("isds", $user_id, $order_number, $total_amount, $payment_method);
    
    if (!$order_stmt->execute()) {
        throw new Exception('Failed to create order');
    }
    
    $order_id = $connect->insert_id;
    
    // Insert order item
    $item_sql = "INSERT INTO order_items (order_id, medicine_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
    $item_stmt = $connect->prepare($item_sql);
    $unit_price = $medicine['price'];
    $item_stmt->bind_param("iiidd", $order_id, $medicine_id, $quantity, $unit_price, $total_amount);
    
    if (!$item_stmt->execute()) {
        throw new Exception('Failed to add order item');
    }
    
    // Update medicine stock
    $new_stock = $medicine['stock_quantity'] - $quantity;
    $stock_sql = "UPDATE medicines SET stock_quantity = ? WHERE medicine_id = ?";
    $stock_stmt = $connect->prepare($stock_sql);
    $stock_stmt->bind_param("ii", $new_stock, $medicine_id);
    
    if (!$stock_stmt->execute()) {
        throw new Exception('Failed to update stock');
    }
    
    // Commit transaction
    $connect->commit();
    
    // Log the purchase activity
    if (file_exists('../activity_logger.php')) {
        require_once '../activity_logger.php';
        logView($user_id, 'purchase', "Purchased {$quantity} units of {$medicine['name']} for RWF " . number_format($total_amount));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'order_number' => $order_number,
        'order_id' => $order_id,
        'medicine_name' => $medicine['name'],
        'quantity' => $quantity,
        'total_amount' => $total_amount
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    $connect->rollback();
    echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
}

// Restore autocommit
$connect->autocommit(TRUE);
$connect->close();
?>