<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Get search term from request
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Prepare response
$response = array(
    'results' => array(),
    'pagination' => array(
        'more' => false
    )
);

try {
    // Build base query
    $query = "SELECT DISTINCT m.medicine_id as id, m.name as text, m.barcode, m.stock_quantity 
              FROM medicines m 
              WHERE m.name LIKE ? 
              ORDER BY m.name ASC 
              LIMIT ? OFFSET ?";
    
    $countQuery = "SELECT COUNT(DISTINCT m.medicine_id) as total 
                   FROM medicines m 
                   WHERE m.name LIKE ?";
    
    // Add wildcards for partial matching
    $searchParam = "%$searchTerm%";
    
    // Get total count
    $stmt = $connect->prepare($countQuery);
    $stmt->bind_param('s', $searchParam);
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();
    $totalCount = (int)($totalResult['total'] ?? 0);
    
    // Get paginated results
    $stmt = $connect->prepare($query);
    $stmt->bind_param('sii', $searchParam, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Format results for Select2
    while ($row = $result->fetch_assoc()) {
        $response['results'][] = array(
            'id' => $row['id'],
            'text' => $row['text'],
            'barcode' => $row['barcode'],
            'stock' => (int)$row['stock_quantity']
        );
    }
    
    // Add pagination info
    $response['pagination']['more'] = ($page * $perPage) < $totalCount;
    $response['total_count'] = $totalCount;
    
} catch (Exception $e) {
    // Log error
    error_log('Error in searchMedicines.php: ' . $e->getMessage());
    
    // Return empty results on error
    $response['results'] = array();
    $response['pagination']['more'] = false;
}

// Return JSON response
echo json_encode($response);

// Close connection
if (isset($connect) && $connect) {
    $connect->close();
}
?>
