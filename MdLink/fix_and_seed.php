<?php
require_once 'constant/connect.php';

echo "Checking existing data...\n";

// Check pharmacies
$result = $connect->query("SELECT pharmacy_id, name FROM pharmacies");
echo "Existing pharmacies:\n";
while($row = $result->fetch_assoc()) {
    echo "- ID: {$row['pharmacy_id']}, Name: {$row['name']}\n";
}

// Check medicines
$result = $connect->query("SELECT medicine_id, medicine_name FROM medicines LIMIT 5");
echo "\nSample medicines:\n";
while($row = $result->fetch_assoc()) {
    echo "- ID: {$row['medicine_id']}, Name: {$row['medicine_name']}\n";
}

// Get the first pharmacy ID
$result = $connect->query("SELECT pharmacy_id FROM pharmacies LIMIT 1");
$pharmacy_id = $result->fetch_assoc()['pharmacy_id'];
echo "\nUsing pharmacy_id: $pharmacy_id\n";

// Get some medicine IDs
$result = $connect->query("SELECT medicine_id FROM medicines LIMIT 10");
$medicine_ids = [];
while($row = $result->fetch_assoc()) {
    $medicine_ids[] = $row['medicine_id'];
}
echo "Using medicine IDs: " . implode(', ', $medicine_ids) . "\n";

// Clear existing data first
echo "\nClearing existing data...\n";
$tables = ['refund_requests', 'failed_payments', 'daily_revenue', 'outstanding_balances', 'branch_reconciliation', 'transaction_exceptions', 'suspicious_transactions', 'audit_logs', 'security_logs'];
foreach($tables as $table) {
    $connect->query("DELETE FROM $table WHERE pharmacy_id = $pharmacy_id");
    echo "Cleared $table\n";
}

// Seed refund requests
echo "\nSeeding refund requests...\n";
$refundData = [
    ['Jean Pierre Uwimana', '+250 788 123 456', $medicine_ids[0], 2, 0.15, 0.30, 0.30, 'Patient allergic reaction', 'PENDING', 'Patient experienced mild rash after taking medication'],
    ['Marie Claire Niyonsaba', '+250 789 987 654', $medicine_ids[1], 1, 0.25, 0.25, 0.25, 'Wrong medication dispensed', 'APPROVED', 'Staff error - dispensed wrong strength'],
    ['Emmanuel Ndayisaba', '+250 787 456 789', $medicine_ids[2], 3, 0.20, 0.60, 0.60, 'Expired medication', 'REJECTED', 'Medication was within expiry date when dispensed'],
    ['Ange Uwase', '+250 786 321 654', $medicine_ids[3], 1, 0.45, 0.45, 0.45, 'Side effects', 'PENDING', 'Patient reported severe stomach upset'],
    ['David Kwizera', '+250 785 147 258', $medicine_ids[4], 2, 0.30, 0.60, 0.30, 'Partial refund - unused portion', 'APPROVED', 'Patient only used 1 tablet, returning unused portion']
];

foreach ($refundData as $refund) {
    $stmt = $connect->prepare("
        INSERT INTO refund_requests 
        (patient_name, patient_phone, medicine_id, quantity, unit_price, total_amount, refund_amount, reason, status, notes, pharmacy_id, admin_id, request_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW() - INTERVAL FLOOR(RAND() * 30) DAY)
    ");
    $stmt->bind_param("ssiiddsssiii", $refund[0], $refund[1], $refund[2], $refund[3], $refund[4], $refund[5], $refund[6], $refund[7], $refund[8], $refund[9], $pharmacy_id, rand(1,3));
    if ($stmt->execute()) {
        echo "✓ Refund request created\n";
    } else {
        echo "✗ Error: " . $stmt->error . "\n";
    }
    $stmt->close();
}

// Seed failed payments
echo "\nSeeding failed payments...\n";
$failedPaymentData = [
    ['PAY-001', 'Jean Bosco Nkurunziza', '+250 788 111 222', 15.50, 'MOBILE_MONEY', 'RETRY_PENDING', 'Insufficient funds', 2],
    ['PAY-002', 'Marie Uwimana', '+250 789 333 444', 8.75, 'BANK_TRANSFER', 'PERMANENTLY_FAILED', 'Invalid account number', 5],
    ['PAY-003', 'Paul Nkurunziza', '+250 787 555 666', 22.30, 'CREDIT_CARD', 'RETRY_PENDING', 'Card declined', 1],
    ['PAY-004', 'Grace Mukamana', '+250 786 777 888', 5.25, 'MOBILE_MONEY', 'RESOLVED', 'Network timeout', 3],
    ['PAY-005', 'John Niyonsaba', '+250 785 999 000', 12.80, 'BANK_TRANSFER', 'RETRY_PENDING', 'Bank server error', 2]
];

foreach ($failedPaymentData as $payment) {
    $stmt = $connect->prepare("
        INSERT INTO failed_payments 
        (transaction_id, patient_name, patient_phone, amount, payment_method, status, failure_reason, retry_count, pharmacy_id, admin_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW() - INTERVAL FLOOR(RAND() * 15) DAY)
    ");
    $stmt->bind_param("sssdssiii", $payment[0], $payment[1], $payment[2], $payment[3], $payment[4], $payment[5], $payment[6], $payment[7], $pharmacy_id, rand(1,3));
    if ($stmt->execute()) {
        echo "✓ Failed payment created\n";
    } else {
        echo "✗ Error: " . $stmt->error . "\n";
    }
    $stmt->close();
}

// Seed daily revenue (last 7 days)
echo "\nSeeding daily revenue...\n";
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $totalSales = rand(800, 2000);
    $totalRefunds = rand(10, 50);
    $netRevenue = $totalSales - $totalRefunds;
    $mobileMoney = $totalSales * 0.6;
    $bankTransfer = $totalSales * 0.2;
    $creditCard = $totalSales * 0.1;
    $cash = $totalSales * 0.1;
    $transactionCount = rand(20, 80);

    $stmt = $connect->prepare("
        INSERT INTO daily_revenue 
        (revenue_date, total_sales, total_refunds, net_revenue, mobile_money, bank_transfer, credit_card, cash, transaction_count, pharmacy_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        total_sales = VALUES(total_sales),
        total_refunds = VALUES(total_refunds),
        net_revenue = VALUES(net_revenue),
        mobile_money = VALUES(mobile_money),
        bank_transfer = VALUES(bank_transfer),
        credit_card = VALUES(credit_card),
        cash = VALUES(cash),
        transaction_count = VALUES(transaction_count)
    ");
    $stmt->bind_param("sdddddddi", $date, $totalSales, $totalRefunds, $netRevenue, $mobileMoney, $bankTransfer, $creditCard, $cash, $transactionCount, $pharmacy_id);
    if ($stmt->execute()) {
        echo "✓ Daily revenue for $date created\n";
    } else {
        echo "✗ Error: " . $stmt->error . "\n";
    }
    $stmt->close();
}

// Seed outstanding balances
echo "\nSeeding outstanding balances...\n";
$balanceData = [
    ['Jean Bosco Nkurunziza', '+250 788 111 222', 150.00, 50.00, 100.00, 'CURRENT', 7],
    ['Marie Uwimana', '+250 789 333 444', 75.50, 0.00, 75.50, 'OVERDUE', -5],
    ['Paul Nkurunziza', '+250 787 555 666', 200.25, 200.25, 0.00, 'PAID', 0],
    ['Grace Mukamana', '+250 786 777 888', 120.75, 60.00, 60.75, 'CURRENT', 10],
    ['John Niyonsaba', '+250 785 999 000', 300.00, 100.00, 200.00, 'OVERDUE', -10]
];

foreach ($balanceData as $balance) {
    $dueDate = date('Y-m-d', strtotime("+{$balance[6]} days"));
    $lastPaymentDate = $balance[3] > 0 ? date('Y-m-d', strtotime('-' . rand(1, 30) . ' days')) : null;
    
    $stmt = $connect->prepare("
        INSERT INTO outstanding_balances 
        (patient_name, patient_phone, total_amount, paid_amount, outstanding_amount, status, due_date, last_payment_date, pharmacy_id, admin_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssddddssii", $balance[0], $balance[1], $balance[2], $balance[3], $balance[4], $balance[5], $dueDate, $lastPaymentDate, $pharmacy_id, rand(1,3));
    if ($stmt->execute()) {
        echo "✓ Outstanding balance created\n";
    } else {
        echo "✗ Error: " . $stmt->error . "\n";
    }
    $stmt->close();
}

// Seed audit logs
echo "\nSeeding audit logs...\n";
$auditData = [
    ['LOGIN', 'admin_users', 1, 'SUCCESS', 'User logged in successfully', '127.0.0.1'],
    ['CREATE', 'medicines', 102, 'SUCCESS', 'Added new medicine Paracetamol 500mg', '127.0.0.1'],
    ['UPDATE', 'stock_movements', 554, 'SUCCESS', 'Adjusted stock count', '127.0.0.1'],
    ['DELETE', 'categories', 14, 'FAILED', 'Delete category failed - constraint violation', '127.0.0.1'],
    ['EXPORT', 'reports', NULL, 'SUCCESS', 'Exported daily revenue report', '127.0.0.1']
];

foreach ($auditData as $audit) {
    $stmt = $connect->prepare("
        INSERT INTO audit_logs 
        (admin_id, action, object_type, object_id, status, message, ip_address, pharmacy_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW() - INTERVAL FLOOR(RAND() * 5) DAY)
    ");
    $stmt->bind_param("isssssi", rand(1,3), $audit[0], $audit[1], $audit[2], $audit[3], $audit[4], $audit[5], $pharmacy_id);
    if ($stmt->execute()) {
        echo "✓ Audit log created\n";
    } else {
        echo "✗ Error: " . $stmt->error . "\n";
    }
    $stmt->close();
}

// Seed security logs
echo "\nSeeding security logs...\n";
$securityData = [
    ['LOGIN_SUCCESS', 'pharmacy_admin', '192.168.1.100', 'Kigali, Rwanda', 'INFO', 'Successful login from trusted IP'],
    ['FAILED_LOGIN', 'unknown_user', '203.45.67.89', 'Unknown', 'WARNING', 'Multiple failed login attempts from suspicious IP'],
    ['PASSWORD_CHANGE', 'finance_admin', '192.168.1.100', 'Kigali, Rwanda', 'INFO', 'Password changed successfully'],
    ['SUSPICIOUS_ACTIVITY', 'pharmacy_admin', '45.78.123.45', 'Unknown', 'HIGH', 'Unusual data access pattern detected'],
    ['UNAUTHORIZED_ACCESS', 'hacker_attempt', '185.220.101.42', 'Unknown', 'CRITICAL', 'Attempted access to restricted admin functions']
];

foreach ($securityData as $security) {
    $stmt = $connect->prepare("
        INSERT INTO security_logs 
        (event_type, user, ip_address, location, severity, description, pharmacy_id, admin_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW() - INTERVAL FLOOR(RAND() * 3) DAY)
    ");
    $stmt->bind_param("sssssi", $security[0], $security[1], $security[2], $security[3], $security[4], $security[5], $pharmacy_id, rand(1,3));
    if ($stmt->execute()) {
        echo "✓ Security log created\n";
    } else {
        echo "✗ Error: " . $stmt->error . "\n";
    }
    $stmt->close();
}

echo "\nData seeding completed!\n";
?>
