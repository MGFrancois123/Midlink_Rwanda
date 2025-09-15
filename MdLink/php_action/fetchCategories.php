<?php 	

require_once 'core.php';

$sql = "SELECT category_id, category_name, description, status, created_at, updated_at FROM category ORDER BY category_name";
$result = $connect->query($sql);

$output = array('success' => true, 'data' => array());

if($result->num_rows > 0) { 
    while($row = $result->fetch_assoc()) {
        $status = isset($row['status']) ? ($row['status'] == '1' ? 'Available' : 'Not Available') : 'Available';
        
        $output['data'][] = array(
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name'],
            'description' => $row['description'] ?: 'N/A',
            'status' => $status,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        );
    }
} else {
    $output['data'] = array();
}

$connect->close();

echo json_encode($output);

?>

