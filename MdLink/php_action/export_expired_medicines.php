<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

// Get filter parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

// Detect categories schema dynamically
$hasCategory = false; $hasCategories = false; $categoryExpr = "'N/A'"; $joinCategory = '';
$r1 = $connect->query("SHOW TABLES LIKE 'category'"); if ($r1 && $r1->num_rows>0) $hasCategory = true;
$r2 = $connect->query("SHOW TABLES LIKE 'categories'"); if ($r2 && $r2->num_rows>0) $hasCategories = true;
if ($hasCategory && $hasCategories) {
    $categoryExpr = "COALESCE(c.category_name, c2.name)";
    $joinCategory = " LEFT JOIN category c ON m.category_id=c.category_id LEFT JOIN categories c2 ON m.category_id=c2.category_id ";
} elseif ($hasCategory) {
    $categoryExpr = "c.category_name";
    $joinCategory = " LEFT JOIN category c ON m.category_id=c.category_id ";
} elseif ($hasCategories) {
    $categoryExpr = "c2.name";
    $joinCategory = " LEFT JOIN categories c2 ON m.category_id=c2.category_id ";
}

// Build the main query
$query = "SELECT m.medicine_id, m.name, m.stock_quantity, m.expiry_date, m.price, ".$categoryExpr." as category_name,
                 DATEDIFF(m.expiry_date, CURDATE()) as days_delta,
                 (m.price * m.stock_quantity) as total_value
          FROM medicines m ".$joinCategory." WHERE 1=1";

// Add search filter
if (!empty($search)) {
    $safe = $connect->real_escape_string($search);
    if ($hasCategory && $hasCategories) {
        $query .= " AND (m.name LIKE '%".$safe."%' OR c.category_name LIKE '%".$safe."%' OR c2.name LIKE '%".$safe."%')";
    } elseif ($hasCategory) {
        $query .= " AND (m.name LIKE '%".$safe."%' OR c.category_name LIKE '%".$safe."%')";
    } elseif ($hasCategories) {
        $query .= " AND (m.name LIKE '%".$safe."%' OR c2.name LIKE '%".$safe."%')";
    } else {
        $query .= " AND m.name LIKE '%".$safe."%'";
    }
}

// Add category filter
if (!empty($category)) {
    $cat = $connect->real_escape_string($category);
    if ($hasCategory && $hasCategories) {
        $query .= " AND (c.category_name='".$cat."' OR c2.name='".$cat."')";
    } elseif ($hasCategory) {
        $query .= " AND c.category_name='".$cat."'";
    } elseif ($hasCategories) {
        $query .= " AND c2.name='".$cat."'";
    }
}

// Add status filter
if (!empty($status)) {
    switch ($status) {
        case 'expired':
            $query .= " AND m.expiry_date < CURDATE()";
            break;
        case 'expiring_soon':
            $query .= " AND m.expiry_date >= CURDATE() AND m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'expiring_later':
            $query .= " AND m.expiry_date > DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
            break;
    }
} else {
    // Default: show expired and expiring soon
    $query .= " AND (m.expiry_date < CURDATE() OR m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))";
}

$query .= " ORDER BY m.expiry_date ASC";

$result = $connect->query($query);

if (!$result) {
    die("Database query failed: " . $connect->error);
}

// Set headers for CSV download
$filename = 'expired_medicines_report_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add headers
fputcsv($output, [
    'Medicine ID',
    'Medicine Name',
    'Category',
    'Stock Quantity',
    'Expiry Date',
    'Days Expired',
    'Unit Price (Rwf)',
    'Total Value (Rwf)',
    'Status',
    'Export Date'
]);

// Add data rows
while ($row = $result->fetch_assoc()) {
    $days = (int)$row['days_delta'];
    $status = $days < 0 ? 'Expired' : ($days <= 7 ? 'Expiring Soon' : 'Expiring Later');
    
    fputcsv($output, [
        $row['medicine_id'],
        $row['name'],
        $row['category_name'] ?? 'N/A',
        $row['stock_quantity'],
        $row['expiry_date'],
        $days,
        number_format($row['price'], 0),
        number_format($row['total_value'], 0),
        $status,
        date('Y-m-d H:i:s')
    ]);
}

// Close the file pointer
fclose($output);
exit;
?>


