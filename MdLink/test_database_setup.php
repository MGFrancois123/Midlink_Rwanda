<?php
require_once 'php_action/db_connect.php';

// Check if tables exist and create them if they don't
$tables_to_check = [
    'audit_logs' => "CREATE TABLE IF NOT EXISTS `audit_logs` (
        `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `action` varchar(100) NOT NULL,
        `entity_type` varchar(50) NOT NULL,
        `entity_id` varchar(50) DEFAULT NULL,
        `old_value` text,
        `new_value` text,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`log_id`),
        KEY `user_id` (`user_id`),
        KEY `action` (`action`),
        KEY `entity` (`entity_type`,`entity_id`),
        KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'security_logs' => "CREATE TABLE IF NOT EXISTS `security_logs` (
        `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `event_type` varchar(50) NOT NULL,
        `severity` enum('low','medium','high','critical') NOT NULL,
        `description` text NOT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text,
        `metadata` text,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`log_id`),
        KEY `user_id` (`user_id`),
        KEY `event_type` (`event_type`),
        KEY `severity` (`severity`),
        KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'regulatory_submissions' => "CREATE TABLE IF NOT EXISTS `regulatory_submissions` (
        `submission_id` bigint(20) NOT NULL AUTO_INCREMENT,
        `submission_type` varchar(100) NOT NULL,
        `reference_number` varchar(100) DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `description` text,
        `submitted_by` int(11) NOT NULL,
        `submission_date` date NOT NULL,
        `due_date` date DEFAULT NULL,
        `status` enum('draft','submitted','under_review','approved','rejected','withdrawn') NOT NULL DEFAULT 'draft',
        `attachments` text,
        `notes` text,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`submission_id`),
        KEY `submission_type` (`submission_type`),
        KEY `reference_number` (`reference_number`),
        KEY `submitted_by` (`submitted_by`),
        KEY `status` (`status`),
        KEY `submission_date` (`submission_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

echo "<h2>Database Setup Verification</h2>\n";
echo "<table border='1'>\n";
echo "<tr><th>Table</th><th>Status</th><th>Action</th></tr>\n";

foreach ($tables_to_check as $table_name => $create_sql) {
    $exists = false;
    $action = "None";
    
    // Check if table exists
    $result = $connect->query("SHOW TABLES LIKE '$table_name'");
    if ($result && $result->num_rows > 0) {
        $exists = true;
        $status = "✅ Exists";
    } else {
        $status = "❌ Missing";
        // Try to create the table
        if ($connect->query($create_sql)) {
            $action = "✅ Created successfully";
        } else {
            $action = "❌ Failed to create: " . $connect->error;
        }
    }
    
    echo "<tr><td>$table_name</td><td>$status</td><td>$action</td></tr>\n";
}

echo "</table>\n";

// Insert some sample data if tables are empty
echo "<h3>Sample Data Insertion</h3>\n";

// Check and insert sample audit logs
$audit_count = $connect->query("SELECT COUNT(*) as count FROM audit_logs")->fetch_assoc()['count'];
if ($audit_count == 0) {
    $sample_audit = [
        [1, 'LOGIN', 'user', '1', null, '{"username":"admin","login_time":"' . date('Y-m-d H:i:s') . '"}', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Test Script'],
        [1, 'CREATE', 'medicine', '1', null, '{"name":"Paracetamol","category":"Pain Relief"}', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Test Script'],
        [1, 'UPDATE', 'pharmacy', '1', '{"status":"pending"}', '{"status":"active"}', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Test Script']
    ];
    
    $stmt = $connect->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_audit as $log) {
        $stmt->bind_param('isssssss', $log[0], $log[1], $log[2], $log[3], $log[4], $log[5], $log[6], $log[7]);
        $stmt->execute();
    }
    echo "✅ Inserted sample audit logs<br>\n";
}

// Check and insert sample security logs
$security_count = $connect->query("SELECT COUNT(*) as count FROM security_logs")->fetch_assoc()['count'];
if ($security_count == 0) {
    $sample_security = [
        [1, 'failed_login', 'high', 'Multiple failed login attempts from IP address', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Test Script', '{"attempts":5,"blocked":false}'],
        [null, 'unauthorized_access', 'critical', 'Attempt to access restricted area without proper authentication', '192.168.1.100', 'Suspicious Bot/1.0', '{"endpoint":"/admin","method":"GET"}'],
        [1, 'password_change', 'medium', 'User changed password successfully', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Test Script', '{"user_id":1,"success":true}']
    ];
    
    $stmt = $connect->prepare("INSERT INTO security_logs (user_id, event_type, severity, description, ip_address, user_agent, metadata) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_security as $log) {
        $stmt->bind_param('issssss', $log[0], $log[1], $log[2], $log[3], $log[4], $log[5], $log[6]);
        $stmt->execute();
    }
    echo "✅ Inserted sample security logs<br>\n";
}

// Check and insert sample regulatory submissions
$submission_count = $connect->query("SELECT COUNT(*) as count FROM regulatory_submissions")->fetch_assoc()['count'];
if ($submission_count == 0) {
    $sample_submissions = [
        ['drug_registration', 'DR-2025-001', 'New Drug Application for Generic Paracetamol', 'Application for registration of generic paracetamol tablets 500mg', 1, '2025-01-15', '2025-03-15', 'submitted', '[]', 'Initial submission for review'],
        ['pharmacy_license', 'PL-2025-002', 'Pharmacy License Renewal - City Pharmacy', 'Annual license renewal application for City Pharmacy', 1, '2025-01-10', '2025-02-28', 'under_review', '[]', 'All documentation provided'],
        ['annual_report', 'AR-2024-003', '2024 Annual Pharmacovigilance Report', 'Annual report on adverse drug reactions', 1, '2025-01-05', '2025-01-31', 'draft', '[]', 'Report being finalized']
    ];
    
    $stmt = $connect->prepare("INSERT INTO regulatory_submissions (submission_type, reference_number, title, description, submitted_by, submission_date, due_date, status, attachments, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_submissions as $submission) {
        $stmt->bind_param('ssssssssss', $submission[0], $submission[1], $submission[2], $submission[3], $submission[4], $submission[5], $submission[6], $submission[7], $submission[8], $submission[9]);
        $stmt->execute();
    }
    echo "✅ Inserted sample regulatory submissions<br>\n";
}

echo "<h3>Final Status</h3>\n";
echo "<p>Audit Logs: " . $connect->query("SELECT COUNT(*) as count FROM audit_logs")->fetch_assoc()['count'] . " records</p>\n";
echo "<p>Security Logs: " . $connect->query("SELECT COUNT(*) as count FROM security_logs")->fetch_assoc()['count'] . " records</p>\n";
echo "<p>Regulatory Submissions: " . $connect->query("SELECT COUNT(*) as count FROM regulatory_submissions")->fetch_assoc()['count'] . " records</p>\n";

echo "<h3>Test Links</h3>\n";
echo "<p><a href='placeholder.php?title=Audit%20Logs'>Test Audit Logs Page</a></p>\n";
echo "<p><a href='placeholder.php?title=Security%20Logs'>Test Security Logs Page</a></p>\n";
echo "<p><a href='placeholder.php?title=Regulatory%20Submissions'>Test Regulatory Submissions Page</a></p>\n";

?>