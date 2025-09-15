<?php
// Test version of createCategories.php without session requirements
require_once 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	// Server-side validation
	$categoriesName = trim($_POST['categoriesName'] ?? '');
	$categoriesStatus = $_POST['categoriesStatus'] ?? ''; 
	$description = isset($_POST['description']) ? trim($_POST['description']) : 'Category created via admin panel';
	
	// Log the received data for debugging
	error_log("TEST - Received POST data: " . print_r($_POST, true));
	error_log("TEST - Processed data - Name: '$categoriesName', Status: '$categoriesStatus', Description: '$description'");
	
	// Validation checks
	$errors = array();
	
	// Validate category name
	if (empty($categoriesName)) {
		$errors[] = "Category name is required";
	} elseif (strlen($categoriesName) < 2) {
		$errors[] = "Category name must be at least 2 characters";
	} elseif (strlen($categoriesName) > 100) {
		$errors[] = "Category name cannot exceed 100 characters";
	}
	
	// Validate status
	if (empty($categoriesStatus)) {
		$errors[] = "Status is required";
	} elseif (!in_array($categoriesStatus, ['1', '2'])) {
		$errors[] = "Invalid status value";
	}
	
	// If there are validation errors, return them
	if (!empty($errors)) {
		$valid['success'] = false;
		$valid['messages'] = implode(", ", $errors);
		echo json_encode($valid);
		exit;
	}
	
	// Check if category name already exists
	$checkSql = "SELECT COUNT(*) as count FROM category WHERE category_name = ?";
	$checkStmt = $connect->prepare($checkSql);
	if ($checkStmt) {
		$checkStmt->bind_param("s", $categoriesName);
		$checkStmt->execute();
		$result = $checkStmt->get_result();
		$row = $result->fetch_assoc();
		$checkStmt->close();
		
		if ($row['count'] > 0) {
			$valid['success'] = false;
			$valid['messages'] = "Category name already exists";
			echo json_encode($valid);
			exit;
		}
	}

	// Insert the category
	$sql = "INSERT INTO category (category_name, description, status) 
	VALUES (?, ?, ?)";

	error_log("TEST - SQL Query: " . $sql);
	error_log("TEST - Parameters: Name='$categoriesName', Description='$description', Status='$categoriesStatus'");

	$stmt = $connect->prepare($sql);
	if (!$stmt) {
		error_log("TEST - Prepare failed: " . $connect->error);
		$valid['success'] = false;
		$valid['messages'] = "Database prepare error: " . $connect->error;
	} else {
		$stmt->bind_param("sss", $categoriesName, $description, $categoriesStatus);
		
		if($stmt->execute()) {
			error_log("TEST - Insert successful. Insert ID: " . $stmt->insert_id);
			$valid['success'] = true;
			$valid['messages'] = "Category added successfully";
		} else {
			error_log("TEST - Execute failed: " . $stmt->error);
			$valid['success'] = false;
			$valid['messages'] = "Error while adding the category: " . $stmt->error;
		}
		
		$stmt->close();
	}

	$connect->close();

	echo json_encode($valid);
 
} else {
	$valid['success'] = false;
	$valid['messages'] = "No POST data received";
	echo json_encode($valid);
}
?>
