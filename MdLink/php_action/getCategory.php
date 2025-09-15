<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Category ID is required'
    ));
    exit;
}

$category_id = intval($_GET['category_id']);

try {
    $query = "SELECT category_id, category_name, description, is_active, created_at FROM categories WHERE category_id = ?";
    
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($connect));
    }
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Category not found'
        ));
        exit;
    }
    
    $category = mysqli_fetch_assoc($result);
    
    echo json_encode(array(
        'success' => true,
        'message' => 'Category retrieved successfully',
        'data' => array(
            'category_id' => $category['category_id'],
            'category_name' => $category['category_name'],
            'description' => $category['description'],
            'is_active' => $category['is_active'],
            'created_at' => $category['created_at']
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
