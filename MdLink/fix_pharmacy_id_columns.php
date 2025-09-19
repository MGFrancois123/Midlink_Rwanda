<?php
require_once './constant/connect.php';

echo "Fixing pharmacy_id columns...\n";

// Add pharmacy_id to medicines table if missing
$check_medicines = $connect->query("SHOW COLUMNS FROM medicines LIKE 'pharmacy_id'");
if ($check_medicines->num_rows == 0) {
    echo "Adding pharmacy_id to medicines table...\n";
    $connect->query("ALTER TABLE medicines ADD COLUMN pharmacy_id INT NULL AFTER medicine_id");
    echo "✅ Added pharmacy_id to medicines\n";
} else {
    echo "✅ medicines already has pharmacy_id\n";
}

// Add pharmacy_id to admin_users table if missing
$check_admins = $connect->query("SHOW COLUMNS FROM admin_users LIKE 'pharmacy_id'");
if ($check_admins->num_rows == 0) {
    echo "Adding pharmacy_id to admin_users table...\n";
    $connect->query("ALTER TABLE admin_users ADD COLUMN pharmacy_id INT NULL AFTER admin_id");
    echo "✅ Added pharmacy_id to admin_users\n";
} else {
    echo "✅ admin_users already has pharmacy_id\n";
}

echo "Fix complete!\n";
?>
