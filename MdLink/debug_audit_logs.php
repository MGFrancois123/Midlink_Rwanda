<?php
require_once './constant/connect.php';

echo "=== AUDIT LOGS DEBUG ===\n";

// Check audit logs count
$result = $connect->query("SELECT COUNT(*) as count FROM audit_logs");
$count = $result->fetch_assoc()['count'];
echo "Total audit logs: $count\n\n";

// Check admin users
$result = $connect->query("SELECT admin_id, username, role FROM admin_users");
echo "Admin users:\n";
while($row = $result->fetch_assoc()) {
    echo "- ID: {$row['admin_id']}, Username: {$row['username']}, Role: {$row['role']}\n";
}

echo "\n";

// Check sample audit logs
$result = $connect->query("SELECT * FROM audit_logs ORDER BY action_time DESC LIMIT 5");
echo "Recent audit logs:\n";
while($row = $result->fetch_assoc()) {
    echo "- ID: {$row['log_id']}, Admin: {$row['admin_id']}, Action: {$row['action']}, Time: {$row['action_time']}\n";
}

echo "\n=== TESTING STATISTICS QUERY ===\n";

// Test statistics query
$stats_query = "SELECT COUNT(*) as total_activities FROM audit_logs";
$result = $connect->query($stats_query);
$total = $result->fetch_assoc()['total_activities'];
echo "Total activities: $total\n";

$active_query = "SELECT COUNT(DISTINCT admin_id) as active_users FROM audit_logs WHERE action = 'LOGIN' AND action_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result = $connect->query($active_query);
$active = $result->fetch_assoc()['active_users'];
echo "Active users (last 7 days): $active\n";

$today_query = "SELECT COUNT(*) as today_activities FROM audit_logs WHERE DATE(action_time) = CURDATE()";
$result = $connect->query($today_query);
$today = $result->fetch_assoc()['today_activities'];
echo "Today's activities: $today\n";

$logins_query = "SELECT COUNT(*) as system_logins FROM audit_logs WHERE action = 'LOGIN'";
$result = $connect->query($logins_query);
$logins = $result->fetch_assoc()['system_logins'];
echo "System logins: $logins\n";
?>
