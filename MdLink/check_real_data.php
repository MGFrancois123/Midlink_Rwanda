<?php
require_once './constant/connect.php';

echo "=== CHECKING REAL DATA IN SYSTEM ===\n\n";

// Check if there are any real audit logs (not sample data)
echo "1. AUDIT LOGS ANALYSIS:\n";
$result = $connect->query("SELECT COUNT(*) as total FROM audit_logs");
$total = $result->fetch_assoc()['total'];
echo "Total audit logs: $total\n";

// Check for real user activities (not sample data)
$result = $connect->query("SELECT action, COUNT(*) as count FROM audit_logs GROUP BY action ORDER BY count DESC");
echo "\nActivity breakdown:\n";
while($row = $result->fetch_assoc()) {
    echo "- {$row['action']}: {$row['count']} times\n";
}

// Check recent real activities
echo "\n2. RECENT REAL ACTIVITIES:\n";
$result = $connect->query("SELECT al.*, au.username FROM audit_logs al LEFT JOIN admin_users au ON al.admin_id = au.admin_id ORDER BY al.action_time DESC LIMIT 10");
while($row = $result->fetch_assoc()) {
    echo "- {$row['username']} performed {$row['action']} on {$row['table_name']} at {$row['action_time']}\n";
}

// Check if there are any real user sessions or login activities
echo "\n3. REAL USER SESSIONS:\n";
$result = $connect->query("SELECT COUNT(*) as login_count FROM audit_logs WHERE action = 'LOGIN'");
$logins = $result->fetch_assoc()['login_count'];
echo "Total login activities: $logins\n";

// Check for real medicine/pharmacy activities
echo "\n4. REAL SYSTEM ACTIVITIES:\n";
$result = $connect->query("SELECT table_name, COUNT(*) as count FROM audit_logs WHERE table_name IN ('medicines', 'pharmacies', 'medical_staff', 'admin_users') GROUP BY table_name");
while($row = $result->fetch_assoc()) {
    echo "- {$row['table_name']}: {$row['count']} activities\n";
}

// Check if we should clear sample data and start fresh
echo "\n5. RECOMMENDATION:\n";
if ($total > 200) {
    echo "Large amount of data detected. This might include sample data.\n";
    echo "Would you like to clear sample data and keep only real activities?\n";
} else {
    echo "Data looks reasonable. This appears to be real user activity.\n";
}
?>
