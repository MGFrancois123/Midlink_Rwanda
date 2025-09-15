<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    $sql = "SELECT category_id, category_name FROM category ORDER BY category_name";
    $result = $connect->query($sql);
    
    if ($result) {
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        $response['success'] = true;
        $response['data'] = $categories;
        $response['message'] = 'Categories loaded successfully';
    } else {
        throw new Exception('Database query failed: ' . $connect->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$connect->close();
echo json_encode($response);
?>
