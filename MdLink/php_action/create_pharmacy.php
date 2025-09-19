<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    // Check if user is logged in and has super_admin role
    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') {
        throw new Exception('Access denied. Only Super Administrators can create pharmacies.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input data
        $name = trim($connect->real_escape_string($_POST['name'] ?? ''));
        $license_number = trim($connect->real_escape_string($_POST['license_number'] ?? ''));
        $contact_person = trim($connect->real_escape_string($_POST['contact_person'] ?? ''));
        $contact_phone = trim($connect->real_escape_string($_POST['contact_phone'] ?? ''));
        $location = trim($connect->real_escape_string($_POST['location'] ?? ''));
        // Manager account is no longer auto-created; fields ignored if present

        // Validation
        $errors = [];

        // Pharmacy name validation
        if (empty($name)) {
            $errors[] = 'Pharmacy name is required';
        } elseif (strlen($name) < 3) {
            $errors[] = 'Pharmacy name must be at least 3 characters long';
        }

        // License number validation
        if (empty($license_number)) {
            $errors[] = 'License number is required';
        } elseif (strlen($license_number) < 5) {
            $errors[] = 'License number must be at least 5 characters long';
        }

        // Contact person validation
        if (empty($contact_person)) {
            $errors[] = 'Contact person is required';
        } elseif (strlen($contact_person) < 3) {
            $errors[] = 'Contact person name must be at least 3 characters long';
        }

        // Contact phone validation
        if (empty($contact_phone)) {
            $errors[] = 'Contact phone is required';
        } elseif (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $contact_phone)) {
            $errors[] = 'Please enter a valid phone number';
        }

        // Location validation
        if (empty($location)) {
            $errors[] = 'Location/address is required';
        } elseif (strlen($location) < 10) {
            $errors[] = 'Please provide a more detailed location/address';
        }

        // Manager account creation removed – no validation


        // Check if pharmacy name already exists
        if (empty($errors)) {
            $pharmacy_check = $connect->prepare("SELECT pharmacy_id FROM pharmacies WHERE name = ? OR license_number = ?");
            $pharmacy_check->bind_param("ss", $name, $license_number);
            $pharmacy_check->execute();
            $pharmacy_result = $pharmacy_check->get_result();
            
            if ($pharmacy_result->num_rows > 0) {
                $errors[] = 'Pharmacy name or license number already exists. Please choose different values.';
            }
        }

        // No manager email checks needed


        // If there are validation errors, return them
        if (!empty($errors)) {
            throw new Exception(implode('. ', $errors));
        }

        // Start transaction
        $connect->autocommit(false);

        try {
            // Insert pharmacy (single location column)
            $pharmacy_sql = "INSERT INTO pharmacies (name, license_number, contact_person, contact_phone, location, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $pharmacy_stmt = $connect->prepare($pharmacy_sql);
            
            if (!$pharmacy_stmt) {
                throw new Exception('Database error: ' . $connect->error);
            }

            $pharmacy_stmt->bind_param("sssss", $name, $license_number, $contact_person, $contact_phone, $location);

            if (!$pharmacy_stmt->execute()) {
                throw new Exception('Failed to create pharmacy: ' . $pharmacy_stmt->error);
            }

            $pharmacy_id = $connect->insert_id;
            $pharmacy_stmt->close();

            // Manager account creation removed


            // Commit transaction
            $connect->commit();
            $connect->autocommit(true);

            // Log activity
            require_once '../activity_logger.php';
            logCreate($_SESSION['adminId'], 'pharmacies', $pharmacy_id, "Created new pharmacy: {$name}");
            
            $response['success'] = true;
            $response['message'] = 'Pharmacy created successfully!';
            $response['data'] = array(
                'pharmacy_id' => $pharmacy_id,
                'pharmacy_name' => $name
            );

        } catch (Exception $e) {
            // Rollback transaction
            $connect->rollback();
            $connect->autocommit(true);
            throw $e;
        }

    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Close database connection
$connect->close();

// Return JSON response
echo json_encode($response);
?>

