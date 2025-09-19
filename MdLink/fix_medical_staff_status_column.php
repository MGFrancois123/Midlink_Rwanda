<?php
require_once './constant/connect.php';

echo "<h2>Fix Medical Staff Status Column</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check current table structure
echo "<h3>Current Table Structure:</h3>";
$structure_query = "DESCRIBE medical_staff";
$structure_result = $connect->query($structure_query);

if ($structure_result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure_result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div style='color: red;'>❌ Error getting table structure: " . $connect->error . "</div>";
}

// Check if 'status' column exists
$check_status_sql = "SHOW COLUMNS FROM medical_staff LIKE 'status'";
$status_result = $connect->query($check_status_sql);

if ($status_result && $status_result->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ 'status' column missing, adding it...</div>";
    
    $add_status_sql = "ALTER TABLE medical_staff ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER email";
    
    if ($connect->query($add_status_sql) === TRUE) {
        echo "<div style='color: green;'>✅ Successfully added 'status' column</div>";
    } else {
        echo "<div style='color: red;'>❌ Error adding 'status' column: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: green;'>✅ 'status' column already exists</div>";
}

// Check if 'created_at' and 'updated_at' columns exist
$check_created_sql = "SHOW COLUMNS FROM medical_staff LIKE 'created_at'";
$created_result = $connect->query($check_created_sql);

if ($created_result && $created_result->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ 'created_at' column missing, adding it...</div>";
    
    $add_created_sql = "ALTER TABLE medical_staff ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status";
    
    if ($connect->query($add_created_sql) === TRUE) {
        echo "<div style='color: green;'>✅ Successfully added 'created_at' column</div>";
    } else {
        echo "<div style='color: red;'>❌ Error adding 'created_at' column: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: green;'>✅ 'created_at' column already exists</div>";
}

$check_updated_sql = "SHOW COLUMNS FROM medical_staff LIKE 'updated_at'";
$updated_result = $connect->query($check_updated_sql);

if ($updated_result && $updated_result->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ 'updated_at' column missing, adding it...</div>";
    
    $add_updated_sql = "ALTER TABLE medical_staff ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at";
    
    if ($connect->query($add_updated_sql) === TRUE) {
        echo "<div style='color: green;'>✅ Successfully added 'updated_at' column</div>";
    } else {
        echo "<div style='color: red;'>❌ Error adding 'updated_at' column: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: green;'>✅ 'updated_at' column already exists</div>";
}

// Show updated table structure
echo "<h3>Updated Table Structure:</h3>";
$updated_structure_query = "DESCRIBE medical_staff";
$updated_structure_result = $connect->query($updated_structure_query);

if ($updated_structure_result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $updated_structure_result->fetch_assoc()) {
        $style = ($row['Field'] == 'status' || $row['Field'] == 'created_at' || $row['Field'] == 'updated_at') ? 'background-color: #90EE90;' : '';
        echo "<tr style=\"$style\"><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div style='color: red;'>❌ Error getting updated table structure: " . $connect->error . "</div>";
}

// Test the add medical staff query
echo "<h3>Testing Add Medical Staff Query:</h3>";
$test_data = [
    'full_name' => 'Test Staff Member',
    'role' => 'doctor',
    'license_number' => 'TEST-001',
    'specialty' => 'General Medicine',
    'phone' => '1234567890',
    'email' => 'test@example.com',
    'pharmacy_id' => 1,
    'status' => 'active'
];

$test_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$test_stmt = $connect->prepare($test_query);

if ($test_stmt) {
    $test_stmt->bind_param("ssssssis", 
        $test_data['full_name'],
        $test_data['role'],
        $test_data['license_number'],
        $test_data['specialty'],
        $test_data['phone'],
        $test_data['email'],
        $test_data['pharmacy_id'],
        $test_data['status']
    );
    
    if ($test_stmt->execute()) {
        echo "<div style='color: green;'>✅ Test insert query executed successfully</div>";
        $test_id = $connect->insert_id;
        echo "<div style='color: blue;'>📝 Test record created with ID: $test_id</div>";
        
        // Clean up test record
        $cleanup_query = "DELETE FROM medical_staff WHERE staff_id = $test_id";
        if ($connect->query($cleanup_query)) {
            echo "<div style='color: green;'>✅ Test record cleaned up</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Test insert query failed: " . $test_stmt->error . "</div>";
    }
    $test_stmt->close();
} else {
    echo "<div style='color: red;'>❌ Failed to prepare test query: " . $connect->error . "</div>";
}

echo "<h3>🎯 Fix Complete!</h3>";
echo "<div style='color: green; font-size: 16px;'>";
echo "The 'status' column error should now be resolved.<br>";
echo "You can now add medical staff without the database error.<br>";
echo "</div>";

$connect->close();
?>
