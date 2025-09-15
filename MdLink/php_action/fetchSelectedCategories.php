<?php 	

require_once 'core.php';

$categoriesId = $_POST['categoriesId'];

$sql = "SELECT 
    category_id, 
    category_name, 
    status as categories_status, 
    active as categories_active 
FROM category 
WHERE category_id = $categoriesId";
$result = $connect->query($sql);

if($result->num_rows > 0) { 
 $row = $result->fetch_array();
} // if num_rows

$connect->close();

echo json_encode($row);