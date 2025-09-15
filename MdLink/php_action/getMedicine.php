<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

if (!isset($_GET['medicine_id']) || empty($_GET['medicine_id'])) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Medicine ID is required'
    ));
    exit;
}

$medicine_id = intval($_GET['medicine_id']);

try {
    $query = "SELECT m.medicine_id, m.pharmacy_id, m.name, m.description, m.price, m.stock_quantity, 
                     m.expiry_date, m.Restricted_Medicine as restricted_medicine, m.category_id, m.created_at,
                     p.name as pharmacy_name, c.category_name
              FROM medicines m
              LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
              LEFT JOIN categories c ON m.category_id = c.category_id
              WHERE m.medicine_id = ?";
    
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'i', $medicine_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($connect));
    }
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Medicine not found'
        ));
        exit;
    }
    
    $medicine = mysqli_fetch_assoc($result);
    
    echo json_encode(array(
        'success' => true,
        'message' => 'Medicine retrieved successfully',
        'data' => array(
            'medicine_id' => $medicine['medicine_id'],
            'pharmacy_id' => $medicine['pharmacy_id'],
            'name' => $medicine['name'],
            'description' => $medicine['description'],
            'price' => $medicine['price'],
            'stock_quantity' => $medicine['stock_quantity'],
            'expiry_date' => $medicine['expiry_date'],
            'Restricted_Medicine' => $medicine['restricted_medicine'],
            'category_id' => $medicine['category_id'],
            'created_at' => $medicine['created_at'],
            'pharmacy_name' => $medicine['pharmacy_name'],
            'category_name' => $medicine['category_name']
        )
    ));
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ));
}

mysqli_close($connect);
?>
