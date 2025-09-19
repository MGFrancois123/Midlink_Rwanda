<?php
require_once './constant/connect.php';

// Sample audit log data
$sample_logs = [
    // Login activities
    ['admin_id' => 1, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged into the system', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
    ['admin_id' => 2, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 2, 'description' => 'User logged into the system', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
    ['admin_id' => 1, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged into the system', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-30 minutes'))],
    
    // Medicine activities
    ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'medicines', 'record_id' => 1, 'description' => 'Added new medicine: Paracetamol 500mg', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-25 minutes'))],
    ['admin_id' => 2, 'action' => 'UPDATE', 'table_name' => 'medicines', 'record_id' => 1, 'description' => 'Updated medicine stock quantity from 100 to 150', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-20 minutes'))],
    ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'medicines', 'record_id' => null, 'description' => 'Viewed medicine catalog', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-15 minutes'))],
    
    // Pharmacy activities
    ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'pharmacies', 'record_id' => 1, 'description' => 'Created new pharmacy: Kigali Central Pharmacy', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-10 minutes'))],
    ['admin_id' => 2, 'action' => 'UPDATE', 'table_name' => 'pharmacies', 'record_id' => 1, 'description' => 'Updated pharmacy contact information', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-5 minutes'))],
    
    // Medical staff activities
    ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'medical_staff', 'record_id' => 1, 'description' => 'Added new medical staff: Dr. John Doe', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-3 minutes'))],
    ['admin_id' => 2, 'action' => 'VIEW', 'table_name' => 'medical_staff', 'record_id' => null, 'description' => 'Viewed medical staff list', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-2 minutes'))],
    
    // Logout activities
    ['admin_id' => 1, 'action' => 'LOGOUT', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged out of the system', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime('-1 minute'))],
];

// Add more historical data for the last 30 days
for ($i = 1; $i <= 30; $i++) {
    $date = date('Y-m-d H:i:s', strtotime("-{$i} days"));
    
    // Random activities for each day
    $daily_activities = [
        ['admin_id' => 1, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged into the system', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => $date],
        ['admin_id' => 2, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 2, 'description' => 'User logged into the system', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime($date . ' +1 hour'))],
        ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'medicines', 'record_id' => rand(1, 100), 'description' => 'Added new medicine', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime($date . ' +2 hours'))],
        ['admin_id' => 2, 'action' => 'UPDATE', 'table_name' => 'medicines', 'record_id' => rand(1, 100), 'description' => 'Updated medicine information', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime($date . ' +3 hours'))],
        ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'medicines', 'record_id' => null, 'description' => 'Viewed medicine catalog', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime($date . ' +4 hours'))],
        ['admin_id' => 2, 'action' => 'DELETE', 'table_name' => 'medicines', 'record_id' => rand(1, 100), 'description' => 'Deleted expired medicine', 'ip_address' => '192.168.1.101', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime($date . ' +5 hours'))],
        ['admin_id' => 1, 'action' => 'LOGOUT', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged out of the system', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'action_time' => date('Y-m-d H:i:s', strtotime($date . ' +6 hours'))],
    ];
    
    $sample_logs = array_merge($sample_logs, $daily_activities);
}

try {
    // Clear existing audit logs
    $connect->query("DELETE FROM audit_logs");
    
    // Get existing admin IDs
    $admin_result = $connect->query("SELECT admin_id FROM admin_users LIMIT 3");
    $admin_ids = [];
    while ($row = $admin_result->fetch_assoc()) {
        $admin_ids[] = $row['admin_id'];
    }
    
    if (empty($admin_ids)) {
        echo "No admin users found. Please create admin users first.\n";
        exit;
    }
    
    echo "Found admin IDs: " . implode(', ', $admin_ids) . "\n";
    
    // Insert sample data
    $stmt = $connect->prepare("INSERT INTO audit_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent, action_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $inserted = 0;
    foreach ($sample_logs as $log) {
        // Use existing admin IDs
        $admin_id = $admin_ids[array_rand($admin_ids)];
        
        $stmt->bind_param('ississss', 
            $admin_id, 
            $log['action'], 
            $log['table_name'], 
            $log['record_id'], 
            $log['description'], 
            $log['ip_address'], 
            $log['user_agent'], 
            $log['action_time']
        );
        
        if ($stmt->execute()) {
            $inserted++;
        }
    }
    
    echo "Successfully added {$inserted} sample audit log entries!<br>";
    echo "<a href='user_activity.php'>View User Activity Page</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
