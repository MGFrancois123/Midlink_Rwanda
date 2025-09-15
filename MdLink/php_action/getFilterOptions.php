<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $response = [
        'success' => true,
        'data' => [
            'categories' => [],
            'pharmacies' => []
        ]
    ];

    // Fetch categories
    $categoryQuery = "SELECT category_id as id, category_name as text 
                     FROM categories 
                     WHERE status = 1 
                     ORDER BY category_name ASC";
    $categoryResult = $connect->query($categoryQuery);
    
    if ($categoryResult) {
        while ($row = $categoryResult->fetch_assoc()) {
            $response['data']['categories'][] = [
                'id' => (int)$row['id'],
                'text' => $row['text']
            ];
        }
    }

    // Fetch pharmacies
    $pharmacyQuery = "SELECT pharmacy_id as id, name as text 
                     FROM pharmacies 
                     WHERE active = 1 
                     ORDER BY name ASC";
    $pharmacyResult = $connect->query($pharmacyQuery);
    
    if ($pharmacyResult) {
        while ($row = $pharmacyResult->fetch_assoc()) {
            $response['data']['pharmacies'][] = [
                'id' => (int)$row['id'],
                'text' => $row['text']
            ];
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

mysqli_close($connect);
?>
