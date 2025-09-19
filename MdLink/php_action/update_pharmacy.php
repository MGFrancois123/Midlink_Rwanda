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
error_log("Update pharmacy request received: " . print_r($_POST, true));

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
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

// Validate required fields
if (empty($pharmacy_id) || empty($name) || empty($license_number) || empty($contact_person) || empty($contact_phone) || empty($location)) {
    $missing_fields = [];
    if (empty($pharmacy_id)) $missing_fields[] = 'pharmacy_id';
    if (empty($name)) $missing_fields[] = 'name';
    if (empty($license_number)) $missing_fields[] = 'license_number';
    if (empty($contact_person)) $missing_fields[] = 'contact_person';
    if (empty($contact_phone)) $missing_fields[] = 'contact_phone';
    if (empty($location)) $missing_fields[] = 'location';
    
    error_log("Missing required fields: " . implode(', ', $missing_fields));
    echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing_fields)]);
    exit();
}

// Combine location with address details if provided
if (!empty($address_details)) {
    $location .= ', ' . $address_details;
}

try {
    // Check if pharmacy exists
    $check_query = "SELECT pharmacy_id FROM pharmacies WHERE pharmacy_id = ?";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bind_param("i", $pharmacy_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Pharmacy not found']);
        exit();
    }
    
    // Check if license number is already used by another pharmacy
    $license_check_query = "SELECT pharmacy_id FROM pharmacies WHERE license_number = ? AND pharmacy_id != ?";
    $license_check_stmt = $connect->prepare($license_check_query);
    $license_check_stmt->bind_param("si", $license_number, $pharmacy_id);
    $license_check_stmt->execute();
    $license_check_result = $license_check_stmt->get_result();
    
    if ($license_check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'License number is already used by another pharmacy']);
        exit();
    }
    
    // Update pharmacy
    $update_query = "UPDATE pharmacies SET 
                     name = ?, 
                     license_number = ?, 
                     contact_person = ?, 
                     contact_phone = ?, 
                     location = ?,
                     updated_at = CURRENT_TIMESTAMP
                     WHERE pharmacy_id = ?";
    
    $update_stmt = $connect->prepare($update_query);
    $update_stmt->bind_param("sssssi", $name, $license_number, $contact_person, $contact_phone, $location, $pharmacy_id);
    
    if ($update_stmt->execute()) {
        // Log the update action
        error_log("Pharmacy updated successfully: ID $pharmacy_id, Name: $name, License: $license_number");
        
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
        error_log("Failed to execute update query: " . $update_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to update pharmacy: ' . $update_stmt->error]);
    }
    
} catch (Exception $e) {
    error_log("Error updating pharmacy: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
