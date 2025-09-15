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
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the raw POST data
        $postData = file_get_contents('php://input');
        
        // If it's JSON, decode it
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $_POST = json_decode($postData, true);
        } else {
            // Otherwise, use the regular POST data
            parse_str($postData, $_POST);
        }
        
        // Get category ID from POST data
        $categoryId = $_POST['category_id'] ?? null;
        $categoryName = trim($_POST['categoriesName'] ?? '');
        $categoryStatus = $_POST['categoriesStatus'] ?? '';
        $description = trim($_POST['description'] ?? '');
        
        // Validate inputs
        if (empty($categoryId)) {
            throw new Exception('Category ID is required');
        }
        
        if (empty($categoryName)) {
            throw new Exception('Category name is required');
        }
        
        if (empty($categoryStatus) || !in_array($categoryStatus, ['1', '2'])) {
            throw new Exception('Invalid status');
        }
        
        // Sanitize inputs
        $categoryName = $connect->real_escape_string($categoryName);
        $description = $connect->real_escape_string($description);
        $categoryId = $connect->real_escape_string($categoryId);
        $categoryStatus = (int)$categoryStatus;
        
        // Check if category name already exists (excluding current category)
        $checkSql = "SELECT category_id FROM category WHERE category_name = '$categoryName' AND category_id != '$categoryId' AND active = 1";
        $result = $connect->query($checkSql);
        
        if ($result->num_rows > 0) {
            throw new Exception('Category name already exists');
        }
        
        // Update the category
        $sql = "UPDATE category SET 
                category_name = '$categoryName', 
                description = " . ($description ? "'$description'" : "NULL") . ",
                status = $categoryStatus,
                updated_at = NOW()
                WHERE category_id = '$categoryId'";
        
        if ($connect->query($sql) === TRUE) {
            $response['success'] = true;
            $response['messages'] = 'Category updated successfully';
            $response['category'] = [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'description' => $description,
                'status' => $categoryStatus
            ];
        } else {
            throw new Exception('Error updating category: ' . $connect->error);
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['messages'] = $e->getMessage();
}

// Close the database connection
$connect->close();

// Return JSON response
echo json_encode($response);