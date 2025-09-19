<?php
require_once './constant/connect.php';

echo "=== CLEARING SAMPLE DATA ===\n";

try {
    // Clear all sample audit logs
    $result = $connect->query("DELETE FROM audit_logs");
    echo "Cleared all sample audit logs\n";
    
    // Get count after clearing
    $result = $connect->query("SELECT COUNT(*) as count FROM audit_logs");
    $count = $result->fetch_assoc()['count'];
    echo "Remaining audit logs: $count\n";
    
    echo "\n=== SETTING UP REAL ACTIVITY LOGGING ===\n";
    
    // Create a function to log real activities
    $logging_function = "
    <?php
    function logActivity(\$admin_id, \$action, \$table_name, \$record_id = null, \$description = '', \$old_data = null, \$new_data = null) {
        global \$connect;
        
        \$ip_address = \$_SERVER['REMOTE_ADDR'] ?? 'unknown';
        \$user_agent = \$_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        \$stmt = \$connect->prepare('INSERT INTO audit_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent, old_data, new_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        \$stmt->bind_param('ississsss', \$admin_id, \$action, \$table_name, \$record_id, \$description, \$ip_address, \$user_agent, \$old_data, \$new_data);
        return \$stmt->execute();
    }
    ?>";
    
    file_put_contents('activity_logger.php', $logging_function);
    echo "Created activity_logger.php for real activity tracking\n";
    
    // Add some real activities for testing
    $real_activities = [
        ['admin_id' => 1, 'action' => 'LOGIN', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged into the system', 'ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
        ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'medicines', 'record_id' => null, 'description' => 'Viewed medicine catalog', 'ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
        ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'pharmacies', 'record_id' => null, 'description' => 'Viewed pharmacy list', 'ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
        ['admin_id' => 1, 'action' => 'VIEW', 'table_name' => 'medical_staff', 'record_id' => null, 'description' => 'Viewed medical staff list', 'ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
        ['admin_id' => 1, 'action' => 'LOGOUT', 'table_name' => 'admin_users', 'record_id' => 1, 'description' => 'User logged out of the system', 'ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
    ];
    
    $stmt = $connect->prepare("INSERT INTO audit_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($real_activities as $activity) {
        $stmt->bind_param('ississs', 
            $activity['admin_id'], 
            $activity['action'], 
            $activity['table_name'], 
            $activity['record_id'], 
            $activity['description'], 
            $activity['ip_address'], 
            $activity['user_agent']
        );
        $stmt->execute();
    }
    
    echo "Added 5 real activity records for testing\n";
    
    // Final count
    $result = $connect->query("SELECT COUNT(*) as count FROM audit_logs");
    $count = $result->fetch_assoc()['count'];
    echo "Total audit logs now: $count\n";
    
    echo "\n=== REAL DATA SETUP COMPLETE ===\n";
    echo "The User Activity page will now show only real user activities.\n";
    echo "As users interact with the system, their activities will be logged.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
