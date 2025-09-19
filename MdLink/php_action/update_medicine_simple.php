<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $medicine_id = (int)$_POST['medicine_id'];
    $pharmacy_id = !empty($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : null;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock_quantity = (int)$_POST['stock_quantity'];
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    $restricted_medicine = (int)$_POST['restricted_medicine'];
    $category_id = (int)$_POST['category_id'];
    
    // Basic validation
    if (empty($name) || $price <= 0 || $stock_quantity < 0 || empty($category_id)) {
        header('Location: ../add-product.php?edit=' . $medicine_id . '&error=Please check all required fields');
        exit;
    }
    
    // Simple update query
    $update_sql = "UPDATE medicines SET 
                    pharmacy_id = ?, 
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    stock_quantity = ?, 
                    expiry_date = ?, 
                    `Restricted Medicine` = ?, 
                    category_id = ?
                   WHERE medicine_id = ?";
    
    $stmt = $connect->prepare($update_sql);
    
    if ($stmt) {
        // Bind parameters - all 9 parameters with correct types
        // i=integer, s=string, d=double
        // pharmacy_id, name, description, price, stock_quantity, expiry_date, restricted_medicine, category_id, medicine_id
        $stmt->bind_param("issddsiii", 
            $pharmacy_id, 
            $name, 
            $description, 
            $price, 
            $stock_quantity, 
            $expiry_date, 
            $restricted_medicine, 
            $category_id, 
            $medicine_id
        );
        
        if ($stmt->execute()) {
            // Success - redirect to product list
            header('Location: ../product.php?success=Updated successfully');
            exit;
        } else {
            // Update failed
            header('Location: ../add-product.php?edit=' . $medicine_id . '&error=Update failed: ' . $stmt->error);
            exit;
        }
    } else {
        // Prepare failed
        header('Location: ../add-product.php?edit=' . $medicine_id . '&error=Database error: ' . $connect->error);
        exit;
    }
} else {
    // Not a POST request or missing update parameter
    header('Location: ../product.php?error=Invalid request');
    exit;
}
?>