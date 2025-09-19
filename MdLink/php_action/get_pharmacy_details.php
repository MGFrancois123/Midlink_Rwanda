<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    if (!isset($_GET['pharmacy_id']) || empty($_GET['pharmacy_id'])) {
        throw new Exception('Pharmacy ID is required');
    }
    
    $pharmacy_id = (int)$_GET['pharmacy_id'];
    
    if ($pharmacy_id <= 0) {
        throw new Exception('Invalid pharmacy ID');
    }
    
    // Check if admin_users table has pharmacy_id column
    $admin_columns = $connect->query("SHOW COLUMNS FROM admin_users LIKE 'pharmacy_id'");
    $has_pharmacy_id = $admin_columns && $admin_columns->num_rows > 0;
    
    if ($has_pharmacy_id) {
        // Use pharmacy_id column if it exists
        $query = "
            SELECT p.*, 
                   COALESCE(COUNT(DISTINCT au.admin_id), 0) as admin_count,
                   COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
            FROM pharmacies p
            LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
            LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
            WHERE p.pharmacy_id = ?
            GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
        ";
    } else {
        // Fallback query without pharmacy_id join
        $query = "
            SELECT p.*, 
                   0 as admin_count,
                   COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
            FROM pharmacies p
            LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
            WHERE p.pharmacy_id = ?
            GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
        ";
    }
    
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $pharmacy_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $pharmacy = $result->fetch_assoc();
        
        // Format the created_at date
        if ($pharmacy['created_at']) {
            $pharmacy['created_at'] = date('F j, Y \a\t g:i A', strtotime($pharmacy['created_at']));
        }
        
        $response['success'] = true;
        $response['data'] = $pharmacy;
    } else {
        throw new Exception('Pharmacy not found');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
