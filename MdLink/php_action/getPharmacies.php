<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    // Check if user is logged in
    if (!isset($_SESSION['userRole'])) {
        throw new Exception('Access denied. Please log in.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all active pharmacies
        $sql = "SELECT pharmacy_id, name, location FROM pharmacies ORDER BY name";
        $result = $connect->query($sql);
        
        if ($result) {
            $pharmacies = [];
            while ($row = $result->fetch_assoc()) {
                $pharmacies[] = array(
                    'pharmacy_id' => (int)$row['pharmacy_id'],
                    'name' => $row['name'],
                    'location' => $row['location']
                );
            }
            
            $response['success'] = true;
            $response['message'] = 'Pharmacies retrieved successfully';
            $response['data'] = $pharmacies;
        } else {
            throw new Exception('Failed to retrieve pharmacies: ' . $connect->error);
        }
    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    
    // Log the error
    error_log("Get pharmacies error: " . $e->getMessage());
}

// Close database connection
$connect->close();

// Return JSON response
echo json_encode($response);
?>