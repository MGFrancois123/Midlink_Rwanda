<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	// Server-side validation
	$categoriesName = trim($_POST['categoriesName'] ?? '');
	$categoriesStatus = $_POST['categoriesStatus'] ?? ''; 
	$description = isset($_POST['description']) ? trim($_POST['description']) : 'Category created via admin panel';
	
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

	$stmt = $connect->prepare($sql);
	if (!$stmt) {
		$valid['success'] = false;
		$valid['messages'] = "Database prepare error: " . $connect->error;
	} else {
		$stmt->bind_param("sss", $categoriesName, $description, $categoriesStatus);
		
		if($stmt->execute()) {
			$valid['success'] = true;
			$valid['messages'] = "Category added successfully";
		} else {
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