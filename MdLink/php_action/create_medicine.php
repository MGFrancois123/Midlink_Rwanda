<?php
require_once '../constant/connect.php';
session_start();

function json_out($arr, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'POST required'], 405);
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
    json_out(['success' => false, 'message' => 'Name, price and category are required'], 400);
}

// Handle optional image upload
$imageName = null;
if (isset($_FILES['medicine_image']) && is_uploaded_file($_FILES['medicine_image']['tmp_name'])) {
    $uploadDir = dirname(__DIR__) . '/assets/myimages/';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
    $ext = pathinfo($_FILES['medicine_image']['name'], PATHINFO_EXTENSION);
    $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name));
    $imageName = $safeBase . '_' . time() . '.' . $ext;
    $target = $uploadDir . $imageName;
    if (!move_uploaded_file($_FILES['medicine_image']['tmp_name'], $target)) {
        json_out(['success' => false, 'message' => 'Failed to upload image'], 500);
    }
}

$sql = "INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, `Restricted Medicine`, category_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $connect->prepare($sql);
if (!$stmt) {
    json_out(['success' => false, 'message' => $connect->error], 500);
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
    json_out(['success' => false, 'message' => $stmt->error], 500);
}

// Redirect back to product list
header('Location: ../product.php');
exit;
?>


