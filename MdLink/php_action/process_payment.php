<?php
include '../constant/connect.php';
include '../constant/check.php';

// Require Composer autoload (install via: composer require flutterwave/flutterwave-v3)
require_once __DIR__ . '/../../vendor/autoload.php';

use Flutterwave\Flutterwave;
use Flutterwave\Payload;
use Flutterwave\Transaction;

// Initialize Flutterwave with your keys
Flutterwave::bootstrap(); // If needed, or directly use the Transaction class

// Your Flutterwave secret key (from dashboard; test mode)
$secret_key = "FLWPUBK_TEST-ab0db75066081fdc2501e5eb2cf42da1-X"; // Replace with your actual secret key

// Get POST data
$medicine_id = isset($_POST['medicine_id']) ? intval($_POST['medicine_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
$transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

// Validate input
if ($medicine_id <= 0 || $quantity <= 0 || $total_amount <= 0 || $transaction_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Verify transaction with Flutterwave
$transactionService = new Transaction();
$transactionService->setSecretKey($secret_key); // If SDK requires it; check exact SDK usage
$response = $transactionService->verify($transaction_id);

if ($response['status'] !== 'success' || 
    $response['data']['amount'] != $total_amount || 
    $response['data']['currency'] !== 'RWF') {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    exit;
}

// Check stock and update (assuming no orders table; adjust if you have one)
$stmt = $connect->prepare("SELECT stock_quantity FROM medicines WHERE medicine_id = ?");
$stmt->bind_param("i", $medicine_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || $row['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit;
}

// Update stock
$new_stock = $row['stock_quantity'] - $quantity;
$update_stmt = $connect->prepare("UPDATE medicines SET stock_quantity = ? WHERE medicine_id = ?");
$update_stmt->bind_param("ii", $new_stock, $medicine_id);
$update_stmt->execute();

// Optionally: Insert into an orders or payments table
// e.g., INSERT INTO orders (user_id, medicine_id, quantity, total, payment_method, transaction_id) VALUES (...)

echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
?>

