<?php
include '../constant/connect.php';
include '../constant/check.php';

// Require Composer autoload (install via: composer require flutterwave/flutterwave-v3)
require_once __DIR__ . '/../../vendor/autoload.php';

use Flutterwave\Flutterwave;
use Flutterwave\Payload;
use Flutterwave\Transaction;

// Initialize Flutterwave
Flutterwave::bootstrap(); // If needed, or directly use the Transaction class

// Your Flutterwave secret key (from dashboard; test mode)
$secret_key = "FLWPUBK_TEST-ab0db75066081fdc2501e5eb2cf42da1-X"; // Replace with your actual secret key

// Get POST data
$total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
$transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

// Get user ID from session
$user_id = $_SESSION['adminId'];

// Validate input
if ($total_amount <= 0 || empty($transaction_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Verify transaction with Flutterwave
try {
    $transactionService = new Transaction();
    $transactionService->setSecretKey($secret_key); // If SDK requires it; check exact SDK usage
    $response = $transactionService->verify($transaction_id);

    if ($response['status'] !== 'success' || 
        $response['data']['amount'] != $total_amount || 
        $response['data']['currency'] !== 'RWF') {
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Payment verification error: ' . $e->getMessage()]);
    exit;
}

// Fetch cart items
$cart_sql = "SELECT 
                c.cart_id,
                c.medicine_id,
                c.quantity,
                m.name,
                m.price,
                m.stock_quantity,
                (c.quantity * m.price) as item_total
            FROM cart c
            JOIN medicines m ON c.medicine_id = m.medicine_id
            WHERE c.user_id = ?";

$cart_stmt = $connect->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

$cart_items = [];
$calculated_total = 0;

while ($row = $cart_result->fetch_assoc()) {
    $cart_items[] = $row;
    $calculated_total += $row['item_total'];
}

// Verify the total amount matches
if (empty($cart_items) || abs($calculated_total - $total_amount) > 0.01) {
    echo json_encode(['success' => false, 'message' => 'Cart total mismatch']);
    exit;
}

// Begin transaction
$connect->begin_transaction();

try {
    // Check stock availability and update stock for each item
    foreach ($cart_items as $item) {
        // Check current stock
        $stock_check_sql = "SELECT stock_quantity FROM medicines WHERE medicine_id = ? FOR UPDATE";
        $stock_stmt = $connect->prepare($stock_check_sql);
        $stock_stmt->bind_param("i", $item['medicine_id']);
        $stock_stmt->execute();
        $stock_result = $stock_stmt->get_result();
        $stock_row = $stock_result->fetch_assoc();

        if (!$stock_row || $stock_row['stock_quantity'] < $item['quantity']) {
            throw new Exception("Insufficient stock for " . $item['name']);
        }

        // Update stock
        $new_stock = $stock_row['stock_quantity'] - $item['quantity'];
        $update_stock_sql = "UPDATE medicines SET stock_quantity = ? WHERE medicine_id = ?";
        $update_stmt = $connect->prepare($update_stock_sql);
        $update_stmt->bind_param("ii", $new_stock, $item['medicine_id']);
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update stock for " . $item['name']);
        }
    }

    // Optional: Insert order record(s) into an orders table if you have one
    // This is a basic example - adjust based on your actual orders table structure
    /*
    $order_sql = "INSERT INTO orders (user_id, total_amount, payment_method, transaction_id, order_date, status) 
                  VALUES (?, ?, ?, ?, NOW(), 'completed')";
    $order_stmt = $connect->prepare($order_sql);
    $order_stmt->bind_param("idss", $user_id, $total_amount, $payment_method, $transaction_id);
    $order_stmt->execute();
    $order_id = $connect->insert_id;

    // Insert order items
    foreach ($cart_items as $item) {
        $order_item_sql = "INSERT INTO order_items (order_id, medicine_id, quantity, price, item_total) 
                           VALUES (?, ?, ?, ?, ?)";
        $order_item_stmt = $connect->prepare($order_item_sql);
        $order_item_stmt->bind_param("iiidd", $order_id, $item['medicine_id'], $item['quantity'], 
                                    $item['price'], $item['item_total']);
        $order_item_stmt->execute();
    }
    */

    // Clear the cart
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_stmt = $connect->prepare($clear_cart_sql);
    $clear_stmt->bind_param("i", $user_id);
    if (!$clear_stmt->execute()) {
        throw new Exception("Failed to clear cart");
    }

    // Commit the transaction
    $connect->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully',
        'transaction_id' => $transaction_id,
        'total_amount' => $total_amount
    ]);

} catch (Exception $e) {
    // Rollback the transaction
    $connect->rollback();
    
    echo json_encode([
        'success' => false, 
        'message' => 'Order processing failed: ' . $e->getMessage()
    ]);
}
?>