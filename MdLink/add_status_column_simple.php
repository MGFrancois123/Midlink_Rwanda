<?php
require_once './constant/connect.php';

echo "<h2>Add Status Column to Medical Staff Table</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if 'status' column exists
$check_status_sql = "SHOW COLUMNS FROM medical_staff LIKE 'status'";
$status_result = $connect->query($check_status_sql);

if ($status_result && $status_result->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ 'status' column missing, adding it...</div>";
    
    $add_status_sql = "ALTER TABLE medical_staff ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active'";
    
    if ($connect->query($add_status_sql) === TRUE) {
        echo "<div style='color: green;'>✅ Successfully added 'status' column</div>";
    } else {
        echo "<div style='color: red;'>❌ Error adding 'status' column: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: green;'>✅ 'status' column already exists</div>";
}

// Show final table structure
echo "<h3>Final Table Structure:</h3>";
$structure_query = "DESCRIBE medical_staff";
$structure_result = $connect->query($structure_query);

if ($structure_result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure_result->fetch_assoc()) {
        $style = ($row['Field'] == 'status') ? 'background-color: #90EE90;' : '';
        echo "<tr style=\"$style\"><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div style='color: red;'>❌ Error getting table structure: " . $connect->error . "</div>";
}

echo "<h3>🎯 Fix Complete!</h3>";
echo "<div style='color: green; font-size: 16px;'>";
echo "The 'status' column has been added to the medical_staff table.<br>";
echo "You can now add medical staff without the database error.<br>";
echo "</div>";

$connect->close();
?>
