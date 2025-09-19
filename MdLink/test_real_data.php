<?php
require_once './constant/connect.php';

echo "=== CURRENT REAL DATA ===\n";

$result = $connect->query('SELECT COUNT(*) as count FROM audit_logs');
$count = $result->fetch_assoc()['count'];
echo "Total audit logs: $count\n\n";

echo "Recent activities:\n";
$result = $connect->query('SELECT al.*, au.username FROM audit_logs al LEFT JOIN admin_users au ON al.admin_id = au.admin_id ORDER BY al.action_time DESC LIMIT 10');
while($row = $result->fetch_assoc()) {
    echo "- {$row['username']} performed {$row['action']}: {$row['description']} at {$row['action_time']}\n";
}

echo "\n=== ACTIVITY BREAKDOWN ===\n";
$result = $connect->query('SELECT action, COUNT(*) as count FROM audit_logs GROUP BY action ORDER BY count DESC');
while($row = $result->fetch_assoc()) {
    echo "- {$row['action']}: {$row['count']} times\n";
}
?>
