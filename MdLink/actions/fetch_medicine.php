<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Check if ID is provided
if ((!isset($_POST['id']) && !isset($_GET['id'])) || (empty($_POST['id']) && empty($_GET['id']))) {
    echo json_encode(['status' => 'error', 'message' => 'Medicine ID is required']);
    exit;
}

// Get ID from POST or GET
$medicineId = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

try {
    // Prepare and execute the query
    $query = "SELECT m.*, c.category_name, p.name as pharmacy_name 
              FROM medicines m
              LEFT JOIN category c ON m.category_id = c.category_id
              LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
              WHERE m.medicine_id = ?";

    $stmt = $connect->prepare($query);
    if (!$stmt) {
        throw new Exception("Database error: " . $connect->error);
    }
    
    $stmt->bind_param('i', $medicineId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Medicine not found');
    }

    $medicine = $result->fetch_assoc();
    
    // Format the response based on actual database structure
    $response = [
        'status' => 'success',
        'data' => [
            'medicine_id' => $medicine['medicine_id'],
            'name' => $medicine['name'] ?? '',
            'description' => $medicine['description'] ?? '',
            'price' => $medicine['price'] ?? 0,
            'stock_quantity' => $medicine['stock_quantity'] ?? 0,
            'expiry_date' => $medicine['expiry_date'] ?? '',
            'Restricted_Medicine' => $medicine['Restricted_Medicine'] ?? 0,
            'category_id' => $medicine['category_id'] ?? null,
            'pharmacy_id' => $medicine['pharmacy_id'] ?? null,
            'category_name' => $medicine['category_name'] ?? '',
            'pharmacy_name' => $medicine['pharmacy_name'] ?? ''
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
