<?php
require_once '../constant/connect.php';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="daily_sales_report_' . date('Y-m-d') . '.csv"');

try {
    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    
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
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write CSV header
    fputcsv($output, [
        'Date',
        'Time',
        'Amount (RWF)',
        'Payment Method',
        'Status',
        'Admin User',
        'Transaction ID'
    ]);
    
    // Write data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            date('Y-m-d', strtotime($row['paid_at'])),
            date('H:i:s', strtotime($row['paid_at'])),
            number_format($row['amount'], 2),
            ucfirst(str_replace('_', ' ', $row['method'])),
            ucfirst($row['status']),
            $row['admin_name'] ?: 'System',
            $row['payment_id']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    // If error occurs, output error message
    header('Content-Type: text/plain');
    echo "Error: " . $e->getMessage();
}

$connect->close();
?>
