<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Fetch pharmacies
if(isset($_GET['fetch']) && $_GET['fetch'] === 'pharmacies') {
    $pharmacies = [];
    $sql = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
    $result = $connect->query($sql);
    
    if($result) {
        while($row = $result->fetch_assoc()) {
            $pharmacies[] = $row;
        }
    }
    
    echo json_encode(['status' => 'success', 'data' => $pharmacies]);
    exit;
}

// Fetch categories
if(isset($_GET['fetch']) && $_GET['fetch'] === 'categories') {
    $categories = [];
    $sql = "SELECT category_id, category_name FROM category ORDER BY category_name";
    $result = $connect->query($sql);
    
    if($result) {
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    echo json_encode(['status' => 'success', 'data' => $categories]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
