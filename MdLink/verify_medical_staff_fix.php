<?php
require_once './constant/connect.php';

echo "<h2>✅ Medical Staff Fix Verification</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if medical_staff table exists and has correct structure
$check_table_sql = "SHOW TABLES LIKE 'medical_staff'";
$result = $connect->query($check_table_sql);

if ($result && $result->num_rows > 0) {
    echo "<div style='color: green;'>✅ medical_staff table exists</div>";
    
    // Check if pharmacy_id column exists
    $check_column_sql = "SHOW COLUMNS FROM medical_staff LIKE 'pharmacy_id'";
    $column_result = $connect->query($check_column_sql);
    
    if ($column_result && $column_result->num_rows > 0) {
        echo "<div style='color: green;'>✅ pharmacy_id column exists</div>";
    } else {
        echo "<div style='color: red;'>❌ pharmacy_id column missing</div>";
    }
    
    // Test the query that was failing
    $test_query = "SELECT ms.*, p.name as pharmacy_name 
                   FROM medical_staff ms 
                   LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
                   ORDER BY ms.created_at DESC";
    
    $query_result = $connect->query($test_query);
    
    if ($query_result) {
        echo "<div style='color: green;'>✅ JOIN query works successfully</div>";
        echo "<div style='color: blue;'>📊 Found " . $query_result->num_rows . " medical staff records</div>";
    } else {
        echo "<div style='color: red;'>❌ JOIN query failed: " . $connect->error . "</div>";
    }
    
} else {
    echo "<div style='color: red;'>❌ medical_staff table does not exist</div>";
}

echo "<h3>🎯 Next Steps:</h3>";
echo "<div style='color: green;'>1. Visit: <a href='medical_staff.php'>medical_staff.php</a></div>";
echo "<div style='color: green;'>2. The page should now load without errors</div>";
echo "<div style='color: green;'>3. You should see the medical staff management interface</div>";

$connect->close();
?>
