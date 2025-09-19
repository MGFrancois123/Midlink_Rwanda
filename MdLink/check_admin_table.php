<?php
require_once './constant/connect.php';

echo "=== ADMIN USERS TABLE STRUCTURE ===\n";

// Check admin_users table structure
$result = $connect->query("DESCRIBE admin_users");
echo "Admin users columns:\n";
while($row = $result->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

echo "\n=== ADMIN USERS DATA ===\n";

// Check admin_users data
$result = $connect->query("SELECT * FROM admin_users");
echo "Admin users data:\n";
while($row = $result->fetch_assoc()) {
    print_r($row);
}

echo "\n=== AUDIT LOGS STRUCTURE ===\n";

// Check audit_logs table structure
$result = $connect->query("DESCRIBE audit_logs");
echo "Audit logs columns:\n";
while($row = $result->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}
?>
