<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the request
error_log("DEBUG: Update pharmacy request received: " . print_r($_POST, true));

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("DEBUG: Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get form data
$pharmacy_id = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$license_number = isset($_POST['license_number']) ? trim($_POST['license_number']) : '';
$contact_person = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';
$contact_phone = isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$address_details = isset($_POST['address_details']) ? trim($_POST['address_details']) : '';

error_log("DEBUG: Parsed data - ID: $pharmacy_id, Name: $name, License: $license_number");

// Validate required fields
if (empty($pharmacy_id) || empty($name) || empty($license_number) || empty($contact_person) || empty($contact_phone) || empty($location)) {
    $missing_fields = [];
    if (empty($pharmacy_id)) $missing_fields[] = 'pharmacy_id';
    if (empty($name)) $missing_fields[] = 'name';
    if (empty($license_number)) $missing_fields[] = 'license_number';
    if (empty($contact_person)) $missing_fields[] = 'contact_person';
    if (empty($contact_phone)) $missing_fields[] = 'contact_phone';
    if (empty($location)) $missing_fields[] = 'location';
    
    error_log("DEBUG: Missing required fields: " . implode(', ', $missing_fields));
    echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing_fields)]);
    exit();
}

// Combine location with address details if provided
if (!empty($address_details)) {
    $location .= ', ' . $address_details;
}

// Test database connection first
if ($connect->connect_error) {
    error_log("DEBUG: Database connection error: " . $connect->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $connect->connect_error]);
    exit();
}

error_log("DEBUG: Database connection successful");

try {
    // Check if pharmacy exists
    error_log("DEBUG: Checking if pharmacy exists with ID: $pharmacy_id");
    $check_query = "SELECT pharmacy_id FROM pharmacies WHERE pharmacy_id = ?";
    $check_stmt = $connect->prepare($check_query);
    
    if (!$check_stmt) {
        error_log("DEBUG: Failed to prepare check query: " . $connect->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare check query - ' . $connect->error]);
        exit();
    }
    
    $check_stmt->bind_param("i", $pharmacy_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        error_log("DEBUG: Pharmacy not found with ID: $pharmacy_id");
        echo json_encode(['success' => false, 'message' => 'Pharmacy not found']);
        exit();
    }
    
    error_log("DEBUG: Pharmacy exists, checking license number");
    
    // Check if license number is already used by another pharmacy
    $license_check_query = "SELECT pharmacy_id FROM pharmacies WHERE license_number = ? AND pharmacy_id != ?";
    $license_check_stmt = $connect->prepare($license_check_query);
    
    if (!$license_check_stmt) {
        error_log("DEBUG: Failed to prepare license check query: " . $connect->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare license check query - ' . $connect->error]);
        exit();
    }
    
    $license_check_stmt->bind_param("si", $license_number, $pharmacy_id);
    $license_check_stmt->execute();
    $license_check_result = $license_check_stmt->get_result();
    
    if ($license_check_result->num_rows > 0) {
        error_log("DEBUG: License number already used: $license_number");
        echo json_encode(['success' => false, 'message' => 'License number is already used by another pharmacy']);
        exit();
    }
    
    error_log("DEBUG: License number is unique, proceeding with update");
    
    // Update pharmacy
    $update_query = "UPDATE pharmacies SET 
                     name = ?, 
                     license_number = ?, 
                     contact_person = ?, 
                     contact_phone = ?, 
                     location = ?,
                     updated_at = CURRENT_TIMESTAMP
                     WHERE pharmacy_id = ?";
    
    error_log("DEBUG: Preparing update query: $update_query");
    $update_stmt = $connect->prepare($update_query);
    
    if (!$update_stmt) {
        error_log("DEBUG: Failed to prepare update query: " . $connect->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare update query - ' . $connect->error]);
        exit();
    }
    
    error_log("DEBUG: Update query prepared successfully, binding parameters");
    $bind_result = $update_stmt->bind_param("sssssi", $name, $license_number, $contact_person, $contact_phone, $location, $pharmacy_id);
    
    if (!$bind_result) {
        error_log("DEBUG: Failed to bind parameters: " . $update_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to bind parameters - ' . $update_stmt->error]);
        exit();
    }
    
    error_log("DEBUG: Parameters bound successfully, executing update");
    $execute_result = $update_stmt->execute();
    
    if ($execute_result) {
        error_log("DEBUG: Update executed successfully");
        echo json_encode([
            'success' => true, 
            'message' => 'Pharmacy updated successfully!',
            'data' => [
                'pharmacy_id' => $pharmacy_id,
                'name' => $name,
                'license_number' => $license_number,
                'contact_person' => $contact_person,
                'contact_phone' => $contact_phone,
                'location' => $location
            ]
        ]);
    } else {
        error_log("DEBUG: Update execution failed: " . $update_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $update_stmt->error]);
    }
    
} catch (Exception $e) {
    error_log("DEBUG: Exception caught: " . $e->getMessage());
    error_log("DEBUG: Exception trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Error $e) {
    error_log("DEBUG: Error caught: " . $e->getMessage());
    error_log("DEBUG: Error trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'PHP error: ' . $e->getMessage()]);
}
?>
