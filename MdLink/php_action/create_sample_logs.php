<?php
require_once 'db_connect.php';

// Create sample audit logs
$auditLogs = [
    [
        'user_id' => 1,
        'action' => 'CREATE',
        'entity_type' => 'medicine',
        'entity_id' => '1',
        'old_value' => null,
        'new_value' => json_encode(['name' => 'Paracetamol', 'category' => 'Analgesics', 'quantity' => 100]),
        'ip_address' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    [
        'user_id' => 1,
        'action' => 'UPDATE',
        'entity_type' => 'medicine',
        'entity_id' => '1',
        'old_value' => json_encode(['quantity' => 100]),
        'new_value' => json_encode(['quantity' => 80]),
        'ip_address' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    [
        'user_id' => 2,
        'action' => 'LOGIN',
        'entity_type' => 'user',
        'entity_id' => '2',
        'old_value' => null,
        'new_value' => json_encode(['login_time' => date('Y-m-d H:i:s')]),
        'ip_address' => '192.168.1.101',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    [
        'user_id' => 1,
        'action' => 'CREATE',
        'entity_type' => 'pharmacy',
        'entity_id' => '1',
        'old_value' => null,
        'new_value' => json_encode(['name' => 'City Pharmacy', 'license' => 'RL-2024-001']),
        'ip_address' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    [
        'user_id' => 2,
        'action' => 'DELETE',
        'entity_type' => 'category',
        'entity_id' => '5',
        'old_value' => json_encode(['name' => 'Deprecated Category']),
        'new_value' => null,
        'ip_address' => '192.168.1.101',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
];

$sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY))";

$stmt = $connect->prepare($sql);

foreach ($auditLogs as $log) {
    $stmt->bind_param('isssssss', 
        $log['user_id'], 
        $log['action'], 
        $log['entity_type'], 
        $log['entity_id'], 
        $log['old_value'], 
        $log['new_value'], 
        $log['ip_address'], 
        $log['user_agent']
    );
    $stmt->execute();
}

// Create sample security logs
$securityLogs = [
    [
        'user_id' => null,
        'event_type' => 'failed_login',
        'severity' => 'medium',
        'description' => 'Failed login attempt for username: admin',
        'ip_address' => '192.168.1.200',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'metadata' => json_encode(['username' => 'admin', 'attempts' => 3])
    ],
    [
        'user_id' => 1,
        'event_type' => 'suspicious_activity',
        'severity' => 'high',
        'description' => 'Multiple rapid requests detected from user',
        'ip_address' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'metadata' => json_encode(['requests_per_minute' => 150])
    ],
    [
        'user_id' => null,
        'event_type' => 'unauthorized_access',
        'severity' => 'critical',
        'description' => 'Attempt to access admin panel without proper authentication',
        'ip_address' => '10.0.0.50',
        'user_agent' => 'curl/7.68.0',
        'metadata' => json_encode(['endpoint' => '/admin/users', 'method' => 'GET'])
    ],
    [
        'user_id' => 2,
        'event_type' => 'failed_login',
        'severity' => 'low',
        'description' => 'Failed login attempt - incorrect password',
        'ip_address' => '192.168.1.101',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'metadata' => json_encode(['username' => 'pharmacist1'])
    ],
    [
        'user_id' => null,
        'event_type' => 'system_intrusion',
        'severity' => 'critical',
        'description' => 'SQL injection attempt detected',
        'ip_address' => '203.0.113.45',
        'user_agent' => 'sqlmap/1.4.7',
        'metadata' => json_encode(['payload' => "' OR 1=1 --", 'parameter' => 'id'])
    ]
];

$sql = "INSERT INTO security_logs (user_id, event_type, severity, description, ip_address, user_agent, metadata, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 7) DAY))";

$stmt = $connect->prepare($sql);

foreach ($securityLogs as $log) {
    $stmt->bind_param('issssss', 
        $log['user_id'], 
        $log['event_type'], 
        $log['severity'], 
        $log['description'], 
        $log['ip_address'], 
        $log['user_agent'], 
        $log['metadata']
    );
    $stmt->execute();
}

// Create sample regulatory submissions
$submissions = [
    [
        'submission_type' => 'license_renewal',
        'reference_number' => 'LIC-2024-0001',
        'title' => 'Annual Pharmacy License Renewal - City Pharmacy',
        'description' => 'Annual renewal application for pharmacy operating license',
        'submitted_by' => 1,
        'submission_date' => '2024-01-15',
        'due_date' => '2024-03-15',
        'status' => 'approved',
        'notes' => 'All documentation complete and verified'
    ],
    [
        'submission_type' => 'new_drug_application',
        'reference_number' => 'NDA-2024-0001',
        'title' => 'New Drug Application - Generic Amoxicillin',
        'description' => 'Application for approval to distribute generic amoxicillin tablets',
        'submitted_by' => 1,
        'submission_date' => '2024-02-01',
        'due_date' => '2024-05-01',
        'status' => 'under_review',
        'notes' => 'Awaiting clinical trial data review'
    ],
    [
        'submission_type' => 'adverse_event_report',
        'reference_number' => 'AER-2024-0001',
        'title' => 'Adverse Event Report - Patient Allergic Reaction',
        'description' => 'Report of allergic reaction to prescribed medication',
        'submitted_by' => 2,
        'submission_date' => '2024-02-10',
        'due_date' => null,
        'status' => 'submitted',
        'notes' => 'Urgent review required'
    ],
    [
        'submission_type' => 'inspection_response',
        'reference_number' => 'INS-2024-0001',
        'title' => 'Response to Regulatory Inspection Findings',
        'description' => 'Corrective action plan following routine inspection',
        'submitted_by' => 1,
        'submission_date' => '2024-01-20',
        'due_date' => '2024-02-20',
        'status' => 'approved',
        'notes' => 'All corrective actions implemented successfully'
    ],
    [
        'submission_type' => 'quality_report',
        'reference_number' => 'QUA-2024-0001',
        'title' => 'Quarterly Quality Assurance Report',
        'description' => 'Q1 2024 quality assurance and control report',
        'submitted_by' => 2,
        'submission_date' => '2024-02-28',
        'due_date' => '2024-04-30',
        'status' => 'draft',
        'notes' => 'Pending final review before submission'
    ]
];

$sql = "INSERT INTO regulatory_submissions (submission_type, reference_number, title, description, submitted_by, submission_date, due_date, status, notes, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 60) DAY))";

$stmt = $connect->prepare($sql);

foreach ($submissions as $submission) {
    $stmt->bind_param('ssssissss', 
        $submission['submission_type'], 
        $submission['reference_number'], 
        $submission['title'], 
        $submission['description'], 
        $submission['submitted_by'], 
        $submission['submission_date'], 
        $submission['due_date'], 
        $submission['status'], 
        $submission['notes']
    );
    $stmt->execute();
}

echo "Sample data created successfully!\n";
echo "- " . count($auditLogs) . " audit log entries\n";
echo "- " . count($securityLogs) . " security log entries\n";
echo "- " . count($submissions) . " regulatory submissions\n";
?>