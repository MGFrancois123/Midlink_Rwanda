<?php
// Get license numbers from database
include('../constant/check.php');
include('../constant/connect.php');

header('Content-Type: application/json');

try {
    // Query to get license numbers with pharmacy names
    $sql = "SELECT license_number, name as pharmacy_name 
            FROM pharmacies 
            WHERE license_number IS NOT NULL AND license_number != '' 
            ORDER BY name ASC";
    
    $result = $connect->query($sql);
    
    if ($result) {
        $licenses = [];
        while ($row = $result->fetch_assoc()) {
            $licenses[] = [
                'license_number' => $row['license_number'],
                'pharmacy_name' => $row['pharmacy_name']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'licenses' => $licenses
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database query failed: ' . $connect->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$connect->close();
?>
