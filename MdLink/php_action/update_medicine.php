<?php
// Start session and check authentication
include('../constant/check.php');
include('../constant/connect.php');

// Include activity logger
require_once('../activity_logger.php');

// Ensure no output before redirects
ob_start();

// Process the update request
error_log("=== UPDATE MEDICINE DEBUG ===");
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("Database connection: " . ($connect ? "Connected" : "Failed"));

// Simplified condition - just check for POST and medicine_id
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['medicine_id']) && !empty($_POST['medicine_id'])) {
    // Validate required fields
    if (!isset($_POST['category_id']) || empty($_POST['category_id']) ||
        !isset($_POST['name']) || empty($_POST['name']) ||
        !isset($_POST['pharmacy_id']) || empty($_POST['pharmacy_id'])) {
        error_log("Missing required fields for update");
        ob_end_clean();
        header("Location: ../product.php?error=validation_failed");
        exit();
    }
    
    $medicine_id = intval($_POST['medicine_id']);
    $category_id = intval($_POST['category_id']);
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $pharmacy_id = intval($_POST['pharmacy_id']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $expiry_date = $_POST['expiry_date'] ?: null;
    $restricted_medicine = intval($_POST['restricted_medicine']);
    
    // Validate required fields
    if (empty($name) || empty($category_id) || $price <= 0 || $stock_quantity < 0) {
        header("Location: ../product.php?error=validation_failed");
        exit;
    }
    
    // Update medicine in database
    $sql = "UPDATE medicines SET 
            category_id = ?, 
            name = ?, 
            description = ?, 
            pharmacy_id = ?, 
            price = ?, 
            stock_quantity = ?, 
            expiry_date = ?, 
            Restricted_Medicine = ?
            WHERE medicine_id = ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("issisdsii", 
        $category_id, 
        $name, 
        $description, 
        $pharmacy_id, 
        $price, 
        $stock_quantity, 
        $expiry_date, 
        $restricted_medicine,
        $medicine_id
    );
    
    if ($stmt->execute()) {
        error_log("✅ Update successful for medicine ID: $medicine_id");
        
        // Log the update activity
        logUpdate($connect, $_SESSION['adminId'], 'medicines', $medicine_id, 
                 'Updated medicine: ' . $name, 
                 json_encode(['category_id' => $category_id, 'pharmacy_id' => $pharmacy_id, 'price' => $price, 'stock' => $stock_quantity]));
        
        // Redirect back to product.php with success message
        ob_end_clean(); // End and clean output buffer
        header("Location: ../product.php?success=updated&medicine=" . urlencode($name));
        exit();
    } else {
        error_log("❌ Update failed: " . $stmt->error);
        error_log("❌ SQL Error: " . $connect->error);
        
        // Redirect back to product.php with error message
        ob_end_clean(); // End and clean output buffer
        header("Location: ../product.php?error=update_failed");
        exit();
    }
    
    $stmt->close();
} else {
    // Prevent direct access to this script
    ob_end_clean(); // End and clean output buffer
    header("Location: ../product.php?error=invalid_request");
    exit();
}

$connect->close();
?>