<?php
session_start();
include('../constant/connect.php');

// Check if user is logged in
if (!isset($_SESSION['adminId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

$user_id = $_SESSION['adminId'];
$order_id = (int)$_POST['order_id'];

try {
    // Get order details
    $order_sql = "SELECT 
                    order_id,
                    order_number,
                    total_amount,
                    payment_method,
                    payment_status,
                    order_status,
                    order_date,
                    shipping_address,
                    notes
                  FROM order_history 
                  WHERE order_id = ? AND user_id = ?";
    
    $order_stmt = $connect->prepare($order_sql);
    $order_stmt->bind_param("ii", $order_id, $user_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    
    if ($order_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    $order = $order_result->fetch_assoc();
    
    // Get order items
    $items_sql = "SELECT 
                    oi.item_id,
                    oi.quantity,
                    oi.unit_price,
                    oi.total_price,
                    m.name as medicine_name,
                    m.description,
                    p.name as pharmacy_name
                  FROM order_items oi
                  JOIN medicines m ON oi.medicine_id = m.medicine_id
                  LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                  WHERE oi.order_id = ?
                  ORDER BY oi.item_id";
    
    $items_stmt = $connect->prepare($items_sql);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'order' => $order,
            'items' => $items
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$connect->close();
?>