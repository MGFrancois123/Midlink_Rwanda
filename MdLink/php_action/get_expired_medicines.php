<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';
header('Content-Type: application/json');

try {
    // Get user role and pharmacy_id for data scoping
    $userRole = $_SESSION['userRole'] ?? '';
    $pharmacyId = $_SESSION['pharmacy_id'] ?? null;
    
    // Build WHERE clause based on user role
    $where_clause = "";
    if ($userRole === 'pharmacy_admin' || $userRole === 'finance_admin') {
        $where_clause = "WHERE m.pharmacy_id = " . intval($pharmacyId);
    }
    
    // Get filter parameters
    $search = $_POST['search'] ?? '';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? '';
    
    // Build the main query
    $query = "SELECT m.medicine_id, m.name, m.stock_quantity, m.expiry_date, m.price, 
                     c.name as category_name,
                     DATEDIFF(m.expiry_date, CURDATE()) as days_expired,
                     (m.price * m.stock_quantity) as total_value
              FROM medicines m
              LEFT JOIN categories c ON m.category_id = c.category_id
              WHERE 1=1";
    
    // Add pharmacy filter
    if ($where_clause) {
        $query .= " AND m.pharmacy_id = " . intval($pharmacyId);
    }
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND (m.name LIKE '%" . $connect->real_escape_string($search) . "%' 
                   OR c.name LIKE '%" . $connect->real_escape_string($search) . "%')";
    }
    
    // Add category filter
    if (!empty($category)) {
        $query .= " AND c.name = '" . $connect->real_escape_string($category) . "'";
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
        throw new Exception("Database query failed: " . $connect->error);
    }
    
    $medicines = [];
    $totalExpired = 0;
    $expiringSoon = 0;
    $totalValue = 0;
    $disposedCount = 0;
    
    while ($row = $result->fetch_assoc()) {
        $medicines[] = [
            'medicine_id' => $row['medicine_id'],
            'name' => $row['name'],
            'category' => $row['category_name'],
            'stock_quantity' => $row['stock_quantity'],
            'expiry_date' => $row['expiry_date'],
            'days_expired' => $row['days_expired'],
            'value' => $row['total_value']
        ];
        
        // Update statistics
        if ($row['days_expired'] < 0) {
            $totalExpired++;
            $totalValue += $row['total_value'];
        } elseif ($row['days_expired'] <= 7) {
            $expiringSoon++;
        }
    }
    
    // Get statistics
    $stats = [
        'total_expired' => $totalExpired,
        'expiring_soon' => $expiringSoon,
        'total_value' => $totalValue,
        'disposed_count' => $disposedCount
    ];
    
    // Get chart data
    $charts = getChartData($connect, $where_clause);
    
    echo json_encode([
        'success' => true,
        'medicines' => $medicines,
        'stats' => $stats,
        'charts' => $charts
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'medicines' => [],
        'stats' => [
            'total_expired' => 0,
            'expiring_soon' => 0,
            'total_value' => 0,
            'disposed_count' => 0
        ],
        'charts' => [
            'expiry_status' => ['labels' => [], 'data' => []],
            'monthly_expiry' => ['labels' => [], 'data' => []]
        ]
    ]);
}

function getChartData($connect, $where_clause) {
    // Expiry Status Distribution
    $statusQuery = "SELECT 
        CASE 
            WHEN expiry_date < CURDATE() THEN 'Expired'
            WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'Expiring Soon'
            WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Expiring Later'
            ELSE 'Safe'
        END as status,
        COUNT(*) as count
        FROM medicines m
        WHERE (expiry_date < CURDATE() OR expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))
        $where_clause
        GROUP BY status
        ORDER BY 
        CASE status
            WHEN 'Expired' THEN 1
            WHEN 'Expiring Soon' THEN 2
            WHEN 'Expiring Later' THEN 3
            ELSE 4
        END";
    
    $statusResult = $connect->query($statusQuery);
    $statusLabels = [];
    $statusData = [];
    
    if ($statusResult) {
        while ($row = $statusResult->fetch_assoc()) {
            $statusLabels[] = $row['status'];
            $statusData[] = (int)$row['count'];
        }
    }
    
    // Monthly Expiry Trend (last 6 months)
    $monthlyQuery = "SELECT 
        DATE_FORMAT(expiry_date, '%Y-%m') as month,
        COUNT(*) as count
        FROM medicines m
        WHERE expiry_date < CURDATE()
        $where_clause
        AND expiry_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6";
    
    $monthlyResult = $connect->query($monthlyQuery);
    $monthlyLabels = [];
    $monthlyData = [];
    
    if ($monthlyResult) {
        while ($row = $monthlyResult->fetch_assoc()) {
            $monthlyLabels[] = date('M Y', strtotime($row['month'] . '-01'));
            $monthlyData[] = (int)$row['count'];
        }
    }
    
    // Reverse arrays to show chronological order
    $monthlyLabels = array_reverse($monthlyLabels);
    $monthlyData = array_reverse($monthlyData);
    
    return [
        'expiry_status' => [
            'labels' => $statusLabels,
            'data' => $statusData
        ],
        'monthly_expiry' => [
            'labels' => $monthlyLabels,
            'data' => $monthlyData
        ]
    ];
}
?>


