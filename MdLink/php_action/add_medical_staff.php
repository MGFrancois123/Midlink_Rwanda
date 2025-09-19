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
error_log("Add medical staff request received: " . print_r($_POST, true));

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get form data
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';
$license_number = isset($_POST['license_number']) ? trim($_POST['license_number']) : '';
$specialty = isset($_POST['specialty']) ? trim($_POST['specialty']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$pharmacy_id = isset($_POST['pharmacy_id']) ? (int)$_POST['pharmacy_id'] : null;

// Validate required fields
if (empty($full_name) || empty($role)) {
    $missing_fields = [];
    if (empty($full_name)) $missing_fields[] = 'full_name';
    if (empty($role)) $missing_fields[] = 'role';
    
    error_log("Missing required fields: " . implode(', ', $missing_fields));
    echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing_fields)]);
    exit();
}

// Validate role
$valid_roles = ['doctor', 'nurse', 'pharmacist', 'technician'];
if (!in_array($role, $valid_roles)) {
    error_log("Invalid role: $role");
    echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
    exit();
}

// Validate email if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email: $email");
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate pharmacy_id if provided
if ($pharmacy_id !== null && $pharmacy_id > 0) {
    $pharmacy_check_query = "SELECT pharmacy_id FROM pharmacies WHERE pharmacy_id = ?";
    $pharmacy_check_stmt = $connect->prepare($pharmacy_check_query);
    $pharmacy_check_stmt->bind_param("i", $pharmacy_id);
    $pharmacy_check_stmt->execute();
    $pharmacy_check_result = $pharmacy_check_stmt->get_result();
    
    if ($pharmacy_check_result->num_rows === 0) {
        error_log("Invalid pharmacy_id: $pharmacy_id");
        echo json_encode(['success' => false, 'message' => 'Invalid pharmacy selected']);
        exit();
    }
} else {
    $pharmacy_id = null;
}

try {
    // Check if email already exists (if provided)
    if (!empty($email)) {
        $email_check_query = "SELECT staff_id FROM medical_staff WHERE email = ?";
        $email_check_stmt = $connect->prepare($email_check_query);
        $email_check_stmt->bind_param("s", $email);
        $email_check_stmt->execute();
        $email_check_result = $email_check_stmt->get_result();
        
        if ($email_check_result->num_rows > 0) {
            error_log("Email already exists: $email");
            echo json_encode(['success' => false, 'message' => 'Email address already exists']);
            exit();
        }
    }
    
    // Check if license number already exists (if provided)
    if (!empty($license_number)) {
        $license_check_query = "SELECT staff_id FROM medical_staff WHERE license_number = ?";
        $license_check_stmt = $connect->prepare($license_check_query);
        $license_check_stmt->bind_param("s", $license_number);
        $license_check_stmt->execute();
        $license_check_result = $license_check_stmt->get_result();
        
        if ($license_check_result->num_rows > 0) {
            error_log("License number already exists: $license_number");
            echo json_encode(['success' => false, 'message' => 'License number already exists']);
            exit();
        }
    }
    
    // Insert new medical staff
    $insert_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
    
    $insert_stmt = $connect->prepare($insert_query);
    
    if (!$insert_stmt) {
        error_log("Failed to prepare insert query: " . $connect->error);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare insert query']);
        exit();
    }
    
    $insert_stmt->bind_param("ssssssi", $full_name, $role, $license_number, $specialty, $phone, $email, $pharmacy_id);
    
    if ($insert_stmt->execute()) {
        $staff_id = $connect->insert_id;
        
        // Log the action
        error_log("Medical staff added successfully: ID $staff_id, Name: $full_name, Role: $role");
        
        // Log activity
        require_once '../activity_logger.php';
        logCreate($_SESSION['adminId'], 'medical_staff', $staff_id, "Added new medical staff: {$full_name} ({$role})");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Medical staff added successfully!',
            'data' => [
                'staff_id' => $staff_id,
                'full_name' => $full_name,
                'role' => $role,
                'license_number' => $license_number,
                'specialty' => $specialty,
                'phone' => $phone,
                'email' => $email,
                'pharmacy_id' => $pharmacy_id
            ]
        ]);
    } else {
        error_log("Failed to execute insert query: " . $insert_stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to add medical staff: ' . $insert_stmt->error]);
    }
    
} catch (Exception $e) {
    error_log("Error adding medical staff: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
