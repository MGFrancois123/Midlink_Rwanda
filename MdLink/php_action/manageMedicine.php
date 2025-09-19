<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

$response = array('success' => false, 'status' => 'error', 'message' => '', 'data' => null);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            // Add new medicine
            $name = $connect->real_escape_string($_POST['name']);
            $pharmacy_id = (int)$_POST['pharmacy_id'];
            $category_id = (int)$_POST['category_id'];
            $description = $connect->real_escape_string($_POST['description'] ?? '');
            $price = (float)$_POST['price'];
            $stock_quantity = (int)$_POST['stock_quantity'];
            // Format expiry date to YYYY-MM-DD format
            $expiry_date = !empty($_POST['expiry_date']) ? date('Y-m-d', strtotime($_POST['expiry_date'])) : null;
            $restricted_medicine = (int)$_POST['Restricted_Medicine'];
            
            // Validate required fields
            if (empty($name) || $pharmacy_id <= 0 || $category_id <= 0 || $price <= 0) {
                throw new Exception('Please fill all required fields');
            }
            
            // For pharmacy admins, ensure they can only add to their pharmacy
            if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'pharmacy_admin') {
                $pharmacy_id = 1; // Force Keza Pharma for pharmacy admins
            }
            
            // Check if medicine already exists in this pharmacy
            $check_sql = "SELECT medicine_id FROM medicines WHERE name = ? AND pharmacy_id = ?";
            $check_stmt = $connect->prepare($check_sql);
            $check_stmt->bind_param("si", $name, $pharmacy_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                throw new Exception('This medicine already exists in the selected pharmacy');
            }
            
            // Insert new medicine - using correct column name
            $sql = "INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, Restricted_Medicine, category_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("issdssii", $pharmacy_id, $name, $description, $price, $stock_quantity, $expiry_date, $restricted_medicine, $category_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['status'] = 'success';
                $response['message'] = 'Medicine added successfully';
                $response['data'] = [
                    'medicine_id' => $connect->insert_id,
                    'name' => $name
                ];
            } else {
                throw new Exception('Failed to add medicine: ' . $stmt->error);
            }
            
        } elseif ($action === 'update') {
            // Update existing medicine
            $medicine_id = (int)$_POST['medicine_id'];
            $name = $connect->real_escape_string($_POST['name']);
            $pharmacy_id = (int)$_POST['pharmacy_id'];
            $category_id = (int)$_POST['category_id'];
            $description = $connect->real_escape_string($_POST['description'] ?? '');
            $price = (float)$_POST['price'];
            $stock_quantity = (int)$_POST['stock_quantity'];
            // Format expiry date to YYYY-MM-DD format
            $expiry_date = !empty($_POST['expiry_date']) ? date('Y-m-d', strtotime($_POST['expiry_date'])) : null;
            $restricted_medicine = (int)$_POST['Restricted_Medicine'];
            
            // Validate required fields
            if ($medicine_id <= 0 || empty($name) || $pharmacy_id <= 0 || $category_id <= 0 || $price <= 0) {
                throw new Exception('Please fill all required fields');
            }
            
            // For pharmacy admins, ensure they can only update medicines from their pharmacy
            if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'pharmacy_admin') {
                $pharmacy_id = 1; // Force Keza Pharma for pharmacy admins
                
                // Verify the medicine belongs to their pharmacy
                $check_sql = "SELECT medicine_id FROM medicines WHERE medicine_id = ? AND pharmacy_id = 1";
                $check_stmt = $connect->prepare($check_sql);
                $check_stmt->bind_param("i", $medicine_id);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows === 0) {
                    throw new Exception('You can only update medicines from your pharmacy');
                }
            }
            
            // Check if medicine exists
            $check_sql = "SELECT medicine_id FROM medicines WHERE medicine_id = ?";
            $check_stmt = $connect->prepare($check_sql);
            $check_stmt->bind_param("i", $medicine_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows === 0) {
                throw new Exception('Medicine not found');
            }
            
            // Update medicine
            $sql = "UPDATE medicines SET 
                    pharmacy_id = ?, name = ?, description = ?, price = ?, 
                    stock_quantity = ?, expiry_date = ?, Restricted_Medicine = ?, category_id = ? 
                    WHERE medicine_id = ?";
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("issdssiii", $pharmacy_id, $name, $description, $price, $stock_quantity, $expiry_date, $restricted_medicine, $category_id, $medicine_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['status'] = 'success';
                $response['message'] = 'Medicine updated successfully';
                $response['data'] = [
                    'medicine_id' => $medicine_id,
                    'name' => $name
                ];
            } else {
                throw new Exception('Failed to update medicine: ' . $stmt->error);
            }
            
        } elseif ($action === 'delete') {
            // Delete medicine
            $medicine_id = (int)$_POST['medicine_id'];
            
            if ($medicine_id <= 0) {
                throw new Exception('Invalid medicine ID');
            }
            
            // For pharmacy admins, ensure they can only delete medicines from their pharmacy
            if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'pharmacy_admin') {
                // Verify the medicine belongs to their pharmacy
                $check_sql = "SELECT medicine_id FROM medicines WHERE medicine_id = ? AND pharmacy_id = 1";
                $check_stmt = $connect->prepare($check_sql);
                $check_stmt->bind_param("i", $medicine_id);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows === 0) {
                    throw new Exception('You can only delete medicines from your pharmacy');
                }
            }
            
            $sql = "DELETE FROM medicines WHERE medicine_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $medicine_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['status'] = 'success';
                $response['message'] = 'Medicine deleted successfully';
            } else {
                throw new Exception('Failed to delete medicine: ' . $stmt->error);
            }
            
        } else {
            throw new Exception('Invalid action');
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'list') {
            // Get all medicines with pharmacy and category details
            $sql = "SELECT m.*, p.name as pharmacy_name, c.category_name 
                    FROM medicines m 
                    LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id 
                    LEFT JOIN category c ON m.category_id = c.category_id";
            
            // Add pharmacy filter for pharmacy admins
            if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'pharmacy_admin') {
                $sql .= " WHERE m.pharmacy_id = 1";
            }
            
            $sql .= " ORDER BY m.name";
            
            $result = $connect->query($sql);
            $medicines = array();
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Get the restricted medicine value - try different possible column names
                    $restricted_db_value = null;
                    if (isset($row['Restricted_Medicine'])) {
                        $restricted_db_value = $row['Restricted_Medicine'];
                    } elseif (isset($row['Restricted Medicine'])) {
                        $restricted_db_value = $row['Restricted Medicine'];
                    } elseif (isset($row['restricted_medicine'])) {
                        $restricted_db_value = $row['restricted_medicine'];
                    }
                    
                    // Convert database value to Yes/No
                    $restricted_value = 'No'; // Default
                    if ($restricted_db_value == 1 || $restricted_db_value === '1' || $restricted_db_value === 'Yes') {
                        $restricted_value = 'Yes';
                    }
                    
                    $medicines[] = array(
                        'medicine_id' => $row['medicine_id'],
                        'name' => $row['name'],
                        'pharmacy_name' => $row['pharmacy_name'],
                        'category_name' => $row['category_name'],
                        'price' => number_format($row['price'], 2),
                        'stock_quantity' => $row['stock_quantity'],
                        'expiry_date' => $row['expiry_date'],
                        'restricted_medicine' => $restricted_value,
                        'description' => $row['description']
                    );
                }
            }
            
            $response['success'] = true;
            $response['status'] = 'success';
            $response['data'] = $medicines;
            
        } elseif ($action === 'get') {
            // Get single medicine
            $medicine_id = (int)$_GET['medicine_id'];
            
            if ($medicine_id <= 0) {
                throw new Exception('Invalid medicine ID');
            }
            
            $sql = "SELECT m.*, p.name as pharmacy_name, c.category_name 
                    FROM medicines m 
                    LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id 
                    LEFT JOIN category c ON m.category_id = c.category_id 
                    WHERE m.medicine_id = ?";
            
            // Add pharmacy filter for pharmacy admins
            if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'pharmacy_admin') {
                $sql .= " AND m.pharmacy_id = 1";
            }
            
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $medicine_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $response['success'] = true;
                $response['status'] = 'success';
                $response['data'] = $result->fetch_assoc();
            } else {
                throw new Exception('Medicine not found');
            }
            
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
