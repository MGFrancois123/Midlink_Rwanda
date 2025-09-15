<?php
require_once 'core.php';
require_once 'db_connect.php';

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
        // For AJAX requests, we can use $_POST directly
        // No need to manually parse the input
        
        // Get form data
        $action = $_POST['action'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $categoryName = trim($_POST['categoriesName'] ?? '');
        $categoryStatus = $_POST['categoriesStatus'] ?? '';
        $description = trim($_POST['description'] ?? '');
        
        // Debug log - you can remove this after testing
        error_log('Category Form Data: ' . print_r($_POST, true));
        
        // Validate action
        if (!in_array($action, ['create', 'update'])) {
            throw new Exception('Invalid action');
        }
        
        // Common validations
        if (empty($categoryName)) {
            throw new Exception('Category name is required');
        }
        
        if (empty($categoryStatus) || !in_array($categoryStatus, ['1', '2'])) {
            throw new Exception('Invalid status');
        }
        
        // Sanitize inputs
        $categoryName = $connect->real_escape_string($categoryName);
        $description = $connect->real_escape_string($description);
        $categoryStatus = (int)$categoryStatus;
        
        if ($action === 'create') {
            // Create new category
            $sql = "INSERT INTO category (category_name, status, description, created_at) 
                    VALUES ('$categoryName', $categoryStatus, '$description', NOW())";
            
            if ($connect->query($sql) === TRUE) {
                $response['success'] = true;
                $response['id'] = $connect->insert_id;
                $response['messages'][] = 'Category created successfully';
            } else {
                throw new Exception('Error creating category: ' . $connect->error);
            }
        } else {
            // Update existing category
            if (empty($categoryId)) {
                throw new Exception('Category ID is required for update');
            }
            
            $categoryId = (int)$categoryId;
            $sql = "UPDATE category SET 
                    category_name = '$categoryName',
                    status = $categoryStatus,
                    description = '$description',
                    updated_at = NOW()
                    WHERE category_id = $categoryId";
            
            if ($connect->query($sql) === TRUE) {
                $response['success'] = true;
                $response['id'] = $categoryId;
                $response['messages'][] = 'Category updated successfully';
            } else {
                throw new Exception('Error updating category: ' . $connect->error);
            }
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['messages'][] = $e->getMessage();
}

// Close database connection
$connect->close();

// Return JSON response
echo json_encode($response);
