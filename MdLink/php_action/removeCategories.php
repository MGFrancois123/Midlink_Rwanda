<?php 	
require_once 'core.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'messages' => []
];

try {
    // Check if it's a GET request with an ID parameter
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $categoryId = $connect->real_escape_string($_GET['id']);
        
        // First, check if there are any products associated with this category
        $checkSql = "SELECT COUNT(*) as product_count FROM product WHERE category_id = '$categoryId' AND active = 1";
        $result = $connect->query($checkSql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['product_count'] > 0) {
                throw new Exception('Cannot delete category because it has associated products');
            }
            
            // If no products are associated, proceed with deletion
            $sql = "UPDATE category SET status = 2 WHERE category_id = '$categoryId'";
            
            if ($connect->query($sql) === TRUE) {
                if ($connect->affected_rows > 0) {
                    $response['success'] = true;
                    $response['messages'] = 'Category deleted successfully';
                } else {
                    throw new Exception('Category not found or already deleted');
                }
            } else {
                throw new Exception('Error deleting category: ' . $connect->error);
            }
        } else {
            throw new Exception('Error checking for associated products: ' . $connect->error);
        }
    } else {
        throw new Exception('Invalid request or missing category ID');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['messages'] = $e->getMessage();
}

// Close the database connection
$connect->close();

// Return JSON response
echo json_encode($response);