<?php
require_once '../constant/connect.php';
header('Content-Type: application/json');

try {
    $start_date = $_POST['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_POST['end_date'] ?? date('Y-m-d');
    
    // Validate dates
    if (!strtotime($start_date) || !strtotime($end_date)) {
        throw new Exception('Invalid date format');
    }
    
    // Get payments data
    $sql = "SELECT p.*, au.username as admin_name 
            FROM payments p 
            LEFT JOIN admin_users au ON p.admin_id = au.admin_id 
            WHERE DATE(p.paid_at) BETWEEN ? AND ? 
            ORDER BY p.paid_at DESC";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $payments = [];
    $total_sales = 0;
    $total_transactions = 0;
    $completed_transactions = 0;
    $payment_methods = [];
    $daily_sales = [];
    
    while ($row = $result->fetch_assoc()) {
        $payment = [
            'date' => date('M j, Y', strtotime($row['paid_at'])),
            'time' => date('g:i A', strtotime($row['paid_at'])),
            'amount' => floatval($row['amount']),
            'method' => $row['method'],
            'status' => $row['status'],
            'admin_name' => $row['admin_name'] ?: 'System'
        ];
        
        $payments[] = $payment;
        
        // Calculate totals
        if ($row['status'] === 'completed') {
            $total_sales += $row['amount'];
            $completed_transactions++;
        }
        $total_transactions++;
        
        // Payment methods distribution
        $method = $row['method'];
        if (!isset($payment_methods[$method])) {
            $payment_methods[$method] = 0;
        }
        $payment_methods[$method]++;
        
        // Daily sales trend
        $date_key = date('Y-m-d', strtotime($row['paid_at']));
        if (!isset($daily_sales[$date_key])) {
            $daily_sales[$date_key] = 0;
        }
        if ($row['status'] === 'completed') {
            $daily_sales[$date_key] += $row['amount'];
        }
    }
    
    // Prepare chart data
    $chart_data = [
        'payment_methods' => [
            'labels' => array_keys($payment_methods),
            'data' => array_values($payment_methods)
        ],
        'daily_sales' => [
            'labels' => array_keys($daily_sales),
            'data' => array_values($daily_sales)
        ]
    ];
    
    // Calculate summary
    $summary = [
        'total_sales' => $total_sales,
        'total_transactions' => $total_transactions,
        'avg_transaction' => $total_transactions > 0 ? round($total_sales / $total_transactions, 2) : 0,
        'success_rate' => $total_transactions > 0 ? round(($completed_transactions / $total_transactions) * 100, 1) : 0
    ];
    
    // Sort daily sales by date
    ksort($daily_sales);
    $chart_data['daily_sales']['labels'] = array_keys($daily_sales);
    $chart_data['daily_sales']['data'] = array_values($daily_sales);
    
    echo json_encode([
        'success' => true,
        'payments' => $payments,
        'summary' => $summary,
        'charts' => $chart_data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$connect->close();
?>
