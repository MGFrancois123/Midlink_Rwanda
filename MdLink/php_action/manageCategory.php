<?php
require_once 'core.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            // Add new category
            $category_name = $connect->real_escape_string($_POST['category_name']);
            $description = $connect->real_escape_string($_POST['description'] ?? '');
            $is_active = (int)$_POST['is_active'];
            $status = $is_active == 1 ? '1' : '2'; // Convert to status format
            
            // Validate required fields
            if (empty($category_name)) {
                throw new Exception('Category name is required');
            }
            
            // Check if category already exists
            $check_sql = "SELECT category_id FROM category WHERE category_name = ?";
            $check_stmt = $connect->prepare($check_sql);
            $check_stmt->bind_param("s", $category_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                throw new Exception('Category already exists');
            }
            
            // Insert new category with status
            $sql = "INSERT INTO category (category_name, description, status) VALUES (?, ?, ?)";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("sss", $category_name, $description, $status);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Category added successfully';
                $response['data'] = array('category_id' => $connect->insert_id);
            } else {
                throw new Exception('Failed to add category: ' . $stmt->error);
            }
            
        } elseif ($action === 'update') {
            // Update existing category
            $category_id = (int)$_POST['category_id'];
            $category_name = $connect->real_escape_string($_POST['category_name']);
            $description = $connect->real_escape_string($_POST['description'] ?? '');
            $is_active = (int)$_POST['is_active'];
            $status = $is_active == 1 ? '1' : '2'; // Convert to status format
            
            // Validate required fields
            if ($category_id <= 0 || empty($category_name)) {
                throw new Exception('Please fill all required fields');
            }
            
            // Check if category exists
            $check_sql = "SELECT category_id FROM category WHERE category_id = ?";
            $check_stmt = $connect->prepare($check_sql);
            $check_stmt->bind_param("i", $category_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows === 0) {
                throw new Exception('Category not found');
            }
            
            // Check if name already exists for other categories
            $check_name_sql = "SELECT category_id FROM category WHERE category_name = ? AND category_id != ?";
            $check_name_stmt = $connect->prepare($check_name_sql);
            $check_name_stmt->bind_param("si", $category_name, $category_id);
            $check_name_stmt->execute();
            
            if ($check_name_stmt->get_result()->num_rows > 0) {
                throw new Exception('Category name already exists');
            }
            
            // Update category with status
            $sql = "UPDATE category SET category_name = ?, description = ?, status = ? WHERE category_id = ?";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("sssi", $category_name, $description, $status, $category_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Category updated successfully';
            } else {
                throw new Exception('Failed to update category: ' . $stmt->error);
            }
            
        } elseif ($action === 'delete') {
            // Delete category
            $category_id = (int)$_POST['category_id'];
            
            if ($category_id <= 0) {
                throw new Exception('Invalid category ID');
            }
            
            // Check if category is being used by medicines
            $check_usage_sql = "SELECT COUNT(*) as count FROM medicines WHERE category_id = ?";
            $check_usage_stmt = $connect->prepare($check_usage_sql);
            $check_usage_stmt->bind_param("i", $category_id);
            $check_usage_stmt->execute();
            $usage_result = $check_usage_stmt->get_result()->fetch_assoc();
            
            if ($usage_result['count'] > 0) {
                throw new Exception('Cannot delete category: It is being used by ' . $usage_result['count'] . ' medicine(s)');
            }
            
            $sql = "DELETE FROM category WHERE category_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $category_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Category deleted successfully';
            } else {
                throw new Exception('Failed to delete category: ' . $stmt->error);
            }
            
        } else {
            throw new Exception('Invalid action');
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'list') {
            // Get all categories
            $sql = "SELECT c.*, 
                           (SELECT COUNT(*) FROM medicines WHERE category_id = c.category_id) as medicine_count 
                    FROM category c 
                    ORDER BY c.category_name";
            
            $result = $connect->query($sql);
            $categories = array();
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $categories[] = array(
                        'category_id' => $row['category_id'],
                        'category_name' => $row['category_name'],
                        'description' => $row['description'],
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at'],
                        'medicine_count' => $row['medicine_count'],
                        'status' => isset($row['status']) ? ($row['status'] == '1' ? 'Available' : 'Not Available') : 'Available'
                    );
                }
            }
            
            $response['success'] = true;
            $response['data'] = $categories;
            
        } elseif ($action === 'get') {
            // Get single category
            $category_id = (int)$_GET['category_id'];
            
            if ($category_id <= 0) {
                throw new Exception('Invalid category ID');
            }
            
            $sql = "SELECT * FROM category WHERE category_id = ?";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $response['success'] = true;
                $response['data'] = $result->fetch_assoc();
            } else {
                throw new Exception('Category not found');
            }
            
        } elseif ($action === 'dropdown') {
            // Get categories for dropdown
            $sql = "SELECT category_id, category_name FROM category ORDER BY category_name";
            
            $result = $connect->query($sql);
            $categories = array();
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $categories[] = array(
                        'category_id' => $row['category_id'],
                        'category_name' => $row['category_name']
                    );
                }
            }
            
            $response['success'] = true;
            $response['data'] = $categories;
            
        } else {
            throw new Exception('Invalid action');
        }
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$connect->close();
echo json_encode($response);
?>
