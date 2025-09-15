<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

// Get request parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $connect->real_escape_string($_POST['search']['value']) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDir = isset($_POST['order'][0]['dir']) ? $connect->real_escape_string($_POST['order'][0]['dir']) : 'asc';

// Get additional filters
$pharmacyId = isset($_POST['pharmacy_id']) ? intval($_POST['pharmacy_id']) : 0;
$categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

// Column mapping for ordering
$columns = [
    0 => 'm.medicine_id',
    1 => 'm.name',
    2 => 'c.category_name',
    3 => 'p.name',
    4 => 'm.price',
    5 => 'm.stock_quantity',
    6 => 'm.expiry_date',
    7 => 'm.Restricted_Medicine',
    8 => 'm.status'
];

$orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'm.name';

// Build the query
$query = "SELECT SQL_CALC_FOUND_ROWS 
            m.medicine_id, m.name, m.description, m.price, 
            m.stock_quantity, m.expiry_date, m.Restricted_Medicine, m.status,
            c.category_id, c.category_name,
            p.pharmacy_id, p.name as pharmacy_name
          FROM medicines m
          LEFT JOIN category c ON m.category_id = c.category_id
          LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
          WHERE 1=1";

$params = [];
$types = '';

// Apply search filter
if (!empty($search)) {
    $query .= " AND (m.name LIKE ? OR m.description LIKE ? OR c.category_name LIKE ? OR p.name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ssss';
}

// Apply pharmacy filter
if ($pharmacyId > 0) {
    $query .= " AND m.pharmacy_id = ?";
    $params[] = $pharmacyId;
    $types .= 'i';
}

// Apply category filter
if ($categoryId > 0) {
    $query .= " AND m.category_id = ?";
    $params[] = $categoryId;
    $types .= 'i';
}

// Add ordering and pagination
$query .= " ORDER BY $orderBy $orderDir";
$query .= " LIMIT ?, ?";
$params[] = $start;
$params[] = $length;
$types .= 'ii';

// Prepare and execute the query
$stmt = $connect->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get total records
$totalRecords = $connect->query('SELECT FOUND_ROWS()')->fetch_row()[0];

// Fetch all records
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'DT_RowId' => 'row_' . $row['medicine_id'],
        'medicine_id' => $row['medicine_id'],
        'name' => $row['name'],
        'description' => $row['description'],
        'price' => $row['price'],
        'stock_quantity' => $row['stock_quantity'],
        'expiry_date' => $row['expiry_date'],
        'Restricted_Medicine' => $row['Restricted_Medicine'],
        'status' => $row['status'],
        'category_id' => $row['category_id'],
        'category_name' => $row['category_name'],
        'pharmacy_id' => $row['pharmacy_id'],
        'pharmacy_name' => $row['pharmacy_name']
    ];
}

// Get total records in the database
$totalFiltered = $totalRecords;

// If there's a search term, get the filtered count
if (!empty($search)) {
    $countQuery = "SELECT COUNT(*) as count 
                  FROM medicines m
                  LEFT JOIN category c ON m.category_id = c.category_id
                  LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                  WHERE 1=1";
    
    if ($pharmacyId > 0) {
        $countQuery .= " AND m.pharmacy_id = $pharmacyId";
    }
    if ($categoryId > 0) {
        $countQuery .= " AND m.category_id = $categoryId";
    }
    
    $countResult = $connect->query($countQuery);
    $totalFiltered = $countResult->fetch_assoc()['count'];
}

// Prepare response
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data' => $data
];

echo json_encode($response);
?>
