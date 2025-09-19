<?php
require_once './constant/connect.php';

echo "Checking admin count issue...\n";

if ($connect->connect_error) {
    die("Database Connection Failed: " . $connect->connect_error . "\n");
}

// Check if admin_users table exists
$check_table = $connect->query("SHOW TABLES LIKE 'admin_users'");
if ($check_table && $check_table->num_rows > 0) {
    echo "✅ admin_users table exists\n";
    
    // Count total admins
    $count_result = $connect->query("SELECT COUNT(*) as count FROM admin_users");
    $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
    echo "📊 Total admins: $count\n";
    
    if ($count > 0) {
        echo "📋 Sample admin data:\n";
        $sample_result = $connect->query("SELECT admin_id, username, email, role FROM admin_users LIMIT 3");
        while ($row = $sample_result->fetch_assoc()) {
            echo "  - ID: {$row['admin_id']}, Username: {$row['username']}, Role: {$row['role']}\n";
        }
    } else {
        echo "⚠️ No admin users found in database\n";
    }
} else {
    echo "❌ admin_users table does not exist\n";
}

$connect->close();
?>
