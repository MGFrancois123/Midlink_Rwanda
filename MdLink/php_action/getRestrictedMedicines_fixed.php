<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Get all filter parameters with proper sanitization
 */
function getFilterParams($conn) {
    // Initialize filters array with default values
    $filters = array(
        'search' => isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '',
        'category' => isset($_GET['category']) ? (int)$_GET['category'] : 0,
        'pharmacy' => isset($_GET['pharmacy']) ? (int)$_GET['pharmacy'] : 0,
        'status' => isset($_GET['status']) ? (int)$_GET['status'] : null,
        'min_stock' => isset($_GET['min_stock']) ? (int)$_GET['min_stock'] : null,
        'max_stock' => isset($_GET['max_stock']) ? (int)$_GET['max_stock'] : null,
        'expiry_start' => !empty($_GET['expiry_start']) ? $conn->real_escape_string($_GET['expiry_start']) : null,
        'expiry_end' => !empty($_GET['expiry_end']) ? $conn->real_escape_string($_GET['expiry_end']) : null,
        'sort_by' => in_array($_GET['sort_by'] ?? '', array('name', 'stock_quantity', 'expiry_date', 'created_at')) 
                    ? $conn->real_escape_string($_GET['sort_by']) 
                    : 'name',
        'sort_order' => strtoupper($_GET['sort_order'] ?? '') === 'DESC' ? 'DESC' : 'ASC',
        'page' => max(1, (int)($_GET['page'] ?? 1)),
        'per_page' => min(100, max(1, (int)($_GET['per_page'] ?? 10)))
    );
    
    return $filters;
}

try {
    // Get and validate filters
    $filters = getFilterParams($connect);
    
    // Initialize variables
    $params = array();
    $types = '';
    $where = array();
    
    // Set pagination values from filters
    $page = $filters['page'];
    $perPage = $filters['per_page'];
    $offset = ($page - 1) * $perPage;
    
    // Build base query with all necessary joins
    $baseQuery = "FROM medicines m
                 LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                 LEFT JOIN categories c ON m.category_id = c.category_id";
    
    // Base condition for restricted medicines
    $where[] = "m.`Restricted_Medicine` = 1";
    
    // Search by medicine name (partial match)
    if (!empty($filters['search'])) {
        $where[] = "(m.name LIKE ? OR m.description LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ss';
    }
    
    // Filter by category
    if ($filters['category'] > 0) {
        $where[] = "m.category_id = ?";
        $params[] = $filters['category'];
        $types .= 'i';
    }
    
    // Filter by pharmacy
    if ($filters['pharmacy'] > 0) {
        $where[] = "m.pharmacy_id = ?";
        $params[] = $filters['pharmacy'];
        $types .= 'i';
    }
    
    // Filter by status
    if ($filters['status'] !== null) {
        $where[] = "m.status = ?";
        $params[] = $filters['status'];
        $types .= 'i';
    }
    
    // Filter by stock range
    if ($filters['min_stock'] !== null) {
        $where[] = "m.stock_quantity >= ?";
        $params[] = $filters['min_stock'];
        $types .= 'i';
    }
    
    if ($filters['max_stock'] !== null) {
        $where[] = "m.stock_quantity <= ?";
        $params[] = $filters['max_stock'];
        $types .= 'i';
    }
    
    // Filter by expiry date range
    if ($filters['expiry_start']) {
        $where[] = "m.expiry_date >= ?";
        $params[] = $filters['expiry_start'];
        $types .= 's';
    }
    
    if ($filters['expiry_end']) {
        $where[] = "m.expiry_date <= ?";
        $params[] = $filters['expiry_end'];
        $types .= 's';
    }
    
    // Add WHERE clause if we have conditions
    if (!empty($where)) {
        $baseQuery .= ' WHERE ' . implode(' AND ', $where);
    }
    
    // Get total count for pagination
    $countQuery = "SELECT COUNT(DISTINCT m.medicine_id) as total $baseQuery";
    $countStmt = $connect->prepare($countQuery);
    
    // Bind parameters if any
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $totalResult = $countStmt->get_result()->fetch_assoc();
    $totalItems = (int)($totalResult['total'] ?? 0);
    
    // Calculate pagination
    $totalPages = ceil($totalItems / $filters['per_page']);
    $offset = ($filters['page'] - 1) * $filters['per_page'];
    
    // Build main query with sorting and pagination
    $query = "SELECT 
                 m.medicine_id, 
                 m.pharmacy_id, 
                 m.name, 
                 m.description, 
                 m.price, 
                 m.stock_quantity, 
                 m.expiry_date, 
                 m.`Restricted_Medicine` as restricted_medicine,
                 m.status,
                 m.category_id, 
                 m.created_at,
                 m.barcode,
                 p.name as pharmacy_name,
                 p.phone as pharmacy_phone,
                 p.address as pharmacy_address,
                 c.category_name,
                 c.description as category_description,
                 (SELECT COUNT(*) FROM order_items oi WHERE oi.medicine_id = m.medicine_id) as total_orders
              $baseQuery
              GROUP BY m.medicine_id
              ORDER BY {$filters['sort_by']} {$filters['sort_order']}
              LIMIT ? OFFSET ?";
    
    // Prepare and execute the query
    $stmt = $connect->prepare($query);
    
    // Add pagination parameters to the parameters array
    $paginationTypes = $types . 'ii';
    $paginationParams = $params;
    $paginationParams[] = $filters['per_page'];
    $paginationParams[] = $offset;
    
    // Bind all parameters including pagination
    if (!empty($paginationParams)) {
        $stmt->bind_param($paginationTypes, ...$paginationParams);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Process results with additional data
    $medicines = array();
    while ($row = $result->fetch_assoc()) {
        $medicines[] = array(
            'id' => (int)$row['medicine_id'],
            'medicine_id' => (int)$row['medicine_id'],
            'pharmacy_id' => (int)$row['pharmacy_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => (float)$row['price'],
            'stock_quantity' => (int)$row['stock_quantity'],
            'expiry_date' => $row['expiry_date'],
            'restricted_medicine' => (int)$row['restricted_medicine'],
            'Restricted_Medicine' => (int)$row['restricted_medicine'],
            'status' => (int)$row['status'],
            'category_id' => (int)$row['category_id'],
            'created_at' => $row['created_at'],
            'pharmacy_name' => $row['pharmacy_name'],
            'pharmacy_phone' => $row['pharmacy_phone'] ?? '',
            'pharmacy_address' => $row['pharmacy_address'] ?? '',
            'category_name' => $row['category_name'],
            'category_description' => $row['category_description'] ?? '',
            'barcode' => $row['barcode'] ?? '',
            'total_orders' => (int)($row['total_orders'] ?? 0),
            'is_expired' => !empty($row['expiry_date']) && (strtotime($row['expiry_date']) < time()),
            'is_low_stock' => (int)$row['stock_quantity'] < 10,
            'stock_status' => (int)$row['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock',
            'formatted_price' => 'RWF ' . number_format((float)$row['price'], 2),
            'formatted_expiry' => !empty($row['expiry_date']) ? date('M d, Y', strtotime($row['expiry_date'])) : 'N/A',
            'formatted_stock' => (int)$row['stock_quantity'] . ' units',
            'status_text' => (int)$row['status'] === 1 ? 'Active' : 'Inactive',
            'status_class' => (int)$row['status'] === 1 ? 'success' : 'secondary'
        );
    }
    
    // Prepare response with metadata
    $response = array(
        'success' => true,
        'message' => 'Restricted medicines retrieved successfully',
        'data' => $medicines,
        'meta' => array(
            'total' => $totalItems,
            'per_page' => $filters['per_page'],
            'current_page' => $filters['page'],
            'last_page' => $totalPages,
            'from' => $totalItems > 0 ? (($filters['page'] - 1) * $filters['per_page'] + 1) : 0,
            'to' => min($filters['page'] * $filters['per_page'], $totalItems)
        )
    );
    
    // Add debug info in development
    if (isset($_GET['debug'])) {
        $response['debug'] = array(
            'query' => $query,
            'params' => $paginationParams,
            'types' => $paginationTypes
        );
    }
    
    // Return JSON response
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);

    // Log the error
    error_log('Error in getRestrictedMedicines.php: ' . $e->getMessage() . '\n' . $e->getTraceAsString());

    // Return error response
    $response = array(
        'success' => false,
        'message' => 'An error occurred while fetching restricted medicines',
        'error' => array(
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        )
    );

    // Only include full error details in debug mode
    if (!isset($_GET['debug'])) {
        unset($response['error']);
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
}

// Close database connection
if (isset($connect) && $connect) {
    $connect->close();
}
?>
