<?php
require_once './constant/connect.php';

echo "<h2>🔍 Debug Admin Count Issue</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if admin_users table exists
$check_table_sql = "SHOW TABLES LIKE 'admin_users'";
$table_result = $connect->query($check_table_sql);

if ($table_result && $table_result->num_rows > 0) {
    echo "<div style='color: green;'>✅ admin_users table exists</div>";
    
    // Check table structure
    echo "<h3>📋 admin_users Table Structure:</h3>";
    $structure_query = "DESCRIBE admin_users";
    $structure_result = $connect->query($structure_query);
    
    if ($structure_result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure_result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Check total count
    $count_query = "SELECT COUNT(*) as total FROM admin_users";
    $count_result = $connect->query($count_query);
    $total_count = $count_result ? $count_result->fetch_assoc()['total'] : 0;
    
    echo "<div style='color: blue; font-size: 18px;'>📊 Total admin_users records: <strong>$total_count</strong></div>";
    
    // Show sample data
    if ($total_count > 0) {
        echo "<h3>📋 Sample admin_users Data:</h3>";
        $sample_query = "SELECT * FROM admin_users LIMIT 5";
        $sample_result = $connect->query($sample_query);
        
        if ($sample_result && $sample_result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Pharmacy ID</th><th>Status</th></tr>";
            while ($row = $sample_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['admin_id']}</td>";
                echo "<td>{$row['username']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['role']}</td>";
                echo "<td>" . ($row['pharmacy_id'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['status'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<div style='color: orange;'>⚠️ No admin_users records found</div>";
    }
    
    // Test the exact query from manage_pharmacies.php
    echo "<h3>🧪 Test manage_pharmacies.php Query:</h3>";
    $test_query = "
        SELECT 
            (SELECT COUNT(*) FROM pharmacies) as total_pharmacies,
            (SELECT COUNT(*) FROM admin_users) as total_admins,
            (SELECT COUNT(*) FROM medicines) as total_medicines
    ";
    
    $test_result = $connect->query($test_query);
    if ($test_result && $test_result->num_rows > 0) {
        $test_row = $test_result->fetch_assoc();
        echo "<div style='color: green;'>✅ Query executed successfully</div>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>Metric</th><th>Count</th></tr>";
        echo "<tr><td>Total Pharmacies</td><td>{$test_row['total_pharmacies']}</td></tr>";
        echo "<tr><td>Total Admins</td><td>{$test_row['total_admins']}</td></tr>";
        echo "<tr><td>Total Medicines</td><td>{$test_row['total_medicines']}</td></tr>";
        echo "</table>";
    } else {
        echo "<div style='color: red;'>❌ Query failed: " . $connect->error . "</div>";
    }
    
} else {
    echo "<div style='color: red;'>❌ admin_users table does not exist</div>";
    echo "<div style='color: orange;'>💡 This is likely why the admin count shows 0</div>";
}

// Check if there are any users in other tables
echo "<h3>🔍 Check Other User Tables:</h3>";

$user_tables = ['users', 'user', 'pharmacy_users', 'staff'];
foreach ($user_tables as $table) {
    $check_sql = "SHOW TABLES LIKE '$table'";
    $check_result = $connect->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $count_sql = "SELECT COUNT(*) as count FROM $table";
        $count_result = $connect->query($count_sql);
        $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
        echo "<div style='color: blue;'>📊 $table table exists with $count records</div>";
    } else {
        echo "<div style='color: gray;'>❌ $table table does not exist</div>";
    }
}

echo "<h3>🎯 Possible Solutions:</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px;'>";
echo "<strong>If admin_users table is empty or doesn't exist:</strong><br>";
echo "1. Create admin_users table<br>";
echo "2. Add some admin users<br>";
echo "3. Update the query in manage_pharmacies.php<br>";
echo "</div>";

$connect->close();
?>
