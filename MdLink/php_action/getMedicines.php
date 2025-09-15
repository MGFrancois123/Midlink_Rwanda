<?php
header('Content-Type: application/json');
include_once('../constant/connect.php');

try {
    // Query medicines with pharmacy and category names
    $sql = "SELECT 
                m.medicine_id,
                m.name,
                m.description,
                m.price,
                m.stock_quantity,
                m.expiry_date,
                m.Restricted_Medicine,
                m.category_id,
                c.category_name,
                p.name as pharmacy_name,
                p.pharmacy_id
            FROM medicines m
            LEFT JOIN category c ON m.category_id = c.category_id
            LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
            ORDER BY m.medicine_id DESC";
    
    $result = $connect->query($sql);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $connect->error);
    }
    
    $medicines = [];
    while ($row = $result->fetch_assoc()) {
        $medicines[] = [
            'medicine_id' => (int)$row['medicine_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => (float)$row['price'],
            'stock_quantity' => (int)$row['stock_quantity'],
            'expiry_date' => $row['expiry_date'],
            'Restricted_Medicine' => (int)$row['Restricted_Medicine'],
            'category_id' => (int)$row['category_id'],
            'category_name' => $row['category_name'],
            'pharmacy_name' => $row['pharmacy_name'],
            'pharmacy_id' => (int)$row['pharmacy_id']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $medicines,
        'total' => count($medicines)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}
?>