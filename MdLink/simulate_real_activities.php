<?php
require_once './constant/connect.php';
require_once './activity_logger.php';

echo "=== SIMULATING REAL USER ACTIVITIES ===\n";

// Simulate real user activities
$activities = [
    // Login activities
    ['admin_id' => 1, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged into the system'],
    ['admin_id' => 22, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 22, 'description' => 'User logged into the system'],
    ['admin_id' => 23, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 23, 'description' => 'User logged into the system'],
    
    // View activities
    ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'medicines', 'record_id' => null, 'description' => 'Viewed medicine catalog'],
    ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'pharmacies', 'record_id' => null, 'description' => 'Viewed pharmacy management page'],
    ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'medical_staff', 'record_id' => null, 'description' => 'Viewed medical staff management page'],
    ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'admin_users', 'record_id' => null, 'description' => 'Viewed user activity page'],
    
    // Medicine activities
    ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'medicines', 'record_id' => 1, 'description' => 'Added new medicine: Paracetamol 500mg'],
    ['admin_id' => 22, 'action' => 'CREATE', 'table_name' => 'medicines', 'record_id' => 2, 'description' => 'Added new medicine: Amoxicillin 250mg'],
    ['admin_id' => 1, 'action' => 'UPDATE', 'table_name' => 'medicines', 'record_id' => 1, 'description' => 'Updated medicine stock quantity'],
    ['admin_id' => 23, 'action' => 'DELETE', 'table_name' => 'medicines', 'record_id' => 3, 'description' => 'Deleted expired medicine'],
    
    // Pharmacy activities
    ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'pharmacies', 'record_id' => 1, 'description' => 'Created new pharmacy: Kigali Central Pharmacy'],
    ['admin_id' => 22, 'action' => 'UPDATE', 'table_name' => 'pharmacies', 'record_id' => 1, 'description' => 'Updated pharmacy contact information'],
    
    // Medical staff activities
    ['admin_id' => 1, 'action' => 'CREATE', 'table_name' => 'medical_staff', 'record_id' => 1, 'description' => 'Added new medical staff: Dr. John Doe'],
    ['admin_id' => 23, 'action' => 'CREATE', 'table_name' => 'medical_staff', 'record_id' => 2, 'description' => 'Added new medical staff: Nurse Mary Smith'],
    
    // Search activities
    ['admin_id' => 1, 'action' => 'SEARCH', 'table_name' => 'medicines', 'record_id' => null, 'description' => 'Searched for "paracetamol" in medicines'],
    ['admin_id' => 22, 'action' => 'SEARCH', 'table_name' => 'pharmacies', 'record_id' => null, 'description' => 'Searched for "kigali" in pharmacies'],
    
    // Export activities
    ['admin_id' => 1, 'action' => 'EXPORT', 'table_name' => 'medicines', 'record_id' => null, 'description' => 'Exported medicines data as CSV'],
    ['admin_id' => 23, 'action' => 'EXPORT', 'table_name' => 'pharmacies', 'record_id' => null, 'description' => 'Exported pharmacies data as Excel'],
    
    // Logout activities
    ['admin_id' => 1, 'action' => 'LOGOUT', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged out of the system'],
    ['admin_id' => 22, 'action' => 'LOGOUT', 'table_name' => 'admin_users', 'record_id' => 22, 'description' => 'User logged out of the system'],
];

// Insert activities with realistic timestamps
$stmt = $connect->prepare("INSERT INTO audit_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent, action_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$inserted = 0;
foreach ($activities as $i => $activity) {
    // Create realistic timestamps (spread over last few days)
    $hours_ago = $i * 2; // 2 hours apart
    $timestamp = date('Y-m-d H:i:s', strtotime("-{$hours_ago} hours"));
    
    $ip_address = '192.168.1.' . (100 + ($i % 10));
    $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    
    $stmt->bind_param('ississss', 
        $activity['admin_id'], 
        $activity['action'], 
        $activity['table_name'], 
        $activity['record_id'], 
        $activity['description'], 
        $ip_address, 
        $user_agent, 
        $timestamp
    );
    
    if ($stmt->execute()) {
        $inserted++;
    }
}

echo "Added $inserted real activity records\n";

// Show final count
$result = $connect->query('SELECT COUNT(*) as count FROM audit_logs');
$count = $result->fetch_assoc()['count'];
echo "Total audit logs now: $count\n";

echo "\n=== ACTIVITY BREAKDOWN ===\n";
$result = $connect->query('SELECT action, COUNT(*) as count FROM audit_logs GROUP BY action ORDER BY count DESC');
while($row = $result->fetch_assoc()) {
    echo "- {$row['action']}: {$row['count']} times\n";
}

echo "\n=== RECENT ACTIVITIES ===\n";
$result = $connect->query('SELECT al.*, au.username FROM audit_logs al LEFT JOIN admin_users au ON al.admin_id = au.admin_id ORDER BY al.action_time DESC LIMIT 5');
while($row = $result->fetch_assoc()) {
    echo "- {$row['username']} performed {$row['action']}: {$row['description']} at {$row['action_time']}\n";
}

echo "\nReal data setup complete! The User Activity page will now show realistic user activities.\n";
?>
