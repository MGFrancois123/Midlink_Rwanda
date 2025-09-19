<?php
require_once '../constant/connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['userRole'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../add-product.php?error=Invalid request method');
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$description = isset($_POST['description']) ? trim((string)$_POST['description']) : null;
$price = isset($_POST['price']) ? (float)$_POST['price'] : null;
$stockQuantity = isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0;
$expiryDate = isset($_POST['expiry_date']) && $_POST['expiry_date'] !== '' ? $_POST['expiry_date'] : null;
$pharmacyId = isset($_POST['pharmacy_id']) && $_POST['pharmacy_id'] !== '' ? (int)$_POST['pharmacy_id'] : null;
$categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$restricted = isset($_POST['restricted_medicine']) ? (int)$_POST['restricted_medicine'] : 0;

if ($name === '' || $price === null || $categoryId === null) {
    header('Location: ../add-product.php?error=Name, price and category are required');
    exit;
}

if ($price <= 0) {
    header('Location: ../add-product.php?error=Price must be greater than 0');
    exit;
}

if ($stockQuantity < 0) {
    header('Location: ../add-product.php?error=Stock quantity cannot be negative');
    exit;
}

// Image upload removed from form

$sql = "INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, `Restricted Medicine`, category_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $connect->prepare($sql);
if (!$stmt) {
    header('Location: ../add-product.php?error=Database error: ' . urlencode($connect->error));
    exit;
}

// Bind params (i s s d i s i i)
$stmt->bind_param(
    'issdisii',
    $pharmacyId,
    $name,
    $description,
    $price,
    $stockQuantity,
    $expiryDate,
    $restricted,
    $categoryId
);

$ok = $stmt->execute();
if (!$ok) {
    header('Location: ../add-product.php?error=Failed to add medicine: ' . urlencode($stmt->error));
    exit;
}

// Log activity
require_once '../activity_logger.php';
logCreate($_SESSION['adminId'], 'medicines', $connect->insert_id, "Added new medicine: {$name}");

// Success - redirect back to product list with success message
header('Location: ../product.php?success=Medicine added successfully');
exit;
?>


