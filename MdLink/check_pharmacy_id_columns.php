<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';

echo "<h2>Check pharmacy_id Columns</h2>";

// Check database connection
if ($connect->connect_error) {
    echo "<div style='color: red;'>❌ Database connection failed: " . $connect->connect_error . "</div>";
    exit();
}

echo "<div style='color: green;'>✅ Database connected successfully</div>";

// Check each table for pharmacy_id column
$tables_to_check = ['medicines', 'admin_users', 'pharmacies'];

foreach ($tables_to_check as $table) {
    echo "<h3>Checking table: $table</h3>";
    
    // Check if table exists
    $table_exists_query = "SHOW TABLES LIKE '$table'";
    $table_exists_result = $connect->query($table_exists_query);
    
    if ($table_exists_result && $table_exists_result->num_rows > 0) {
        echo "<div style='color: green;'>✅ Table $table exists</div>";
        
        // Check if pharmacy_id column exists
        $column_check_query = "SHOW COLUMNS FROM $table LIKE 'pharmacy_id'";
        $column_check_result = $connect->query($column_check_query);
        
        if ($column_check_result && $column_check_result->num_rows > 0) {
            echo "<div style='color: green;'>✅ pharmacy_id column exists in $table</div>";
        } else {
            echo "<div style='color: red;'>❌ pharmacy_id column MISSING in $table</div>";
            
            // Add the missing column
            if ($table === 'medicines') {
                $add_column_query = "ALTER TABLE medicines ADD COLUMN pharmacy_id INT NULL AFTER medicine_id";
            } elseif ($table === 'admin_users') {
                $add_column_query = "ALTER TABLE admin_users ADD COLUMN pharmacy_id INT NULL AFTER admin_id";
            } else {
                continue; // pharmacies table should already have pharmacy_id as primary key
            }
            
            if ($connect->query($add_column_query)) {
                echo "<div style='color: green;'>✅ Successfully added pharmacy_id column to $table</div>";
            } else {
                echo "<div style='color: red;'>❌ Failed to add pharmacy_id column to $table: " . $connect->error . "</div>";
            }
        }
        
        // Show table structure
        $structure_query = "DESCRIBE $table";
        $structure_result = $connect->query($structure_query);
        
        if ($structure_result) {
            echo "<h4>Table Structure for $table:</h4>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = $structure_result->fetch_assoc()) {
                $highlight = ($row['Field'] === 'pharmacy_id') ? 'style="background-color: #90EE90;"' : '';
                echo "<tr $highlight>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "<td>" . $row['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<div style='color: red;'>❌ Table $table does not exist</div>";
    }
}

// Test the delete query that was failing
echo "<h3>Testing Delete Query</h3>";

$test_query = "SELECT pharmacy_id FROM pharmacies LIMIT 1";
$test_result = $connect->query($test_query);

if ($test_result && $test_result->num_rows > 0) {
    $pharmacy = $test_result->fetch_assoc();
    $pharmacy_id = $pharmacy['pharmacy_id'];
    
    echo "<p>Testing with Pharmacy ID: $pharmacy_id</p>";
    
    // Test the medicines delete query
    $medicines_query = "DELETE FROM medicines WHERE pharmacy_id = ?";
    $medicines_stmt = $connect->prepare($medicines_query);
    
    if ($medicines_stmt) {
        echo "<div style='color: green;'>✅ Medicines delete query prepared successfully</div>";
        $medicines_stmt->bind_param("i", $pharmacy_id);
        if ($medicines_stmt->execute()) {
            echo "<div style='color: green;'>✅ Medicines delete query executed successfully</div>";
        } else {
            echo "<div style='color: red;'>❌ Medicines delete query failed: " . $medicines_stmt->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Medicines delete query preparation failed: " . $connect->error . "</div>";
    }
    
    // Test the admin_users delete query
    $admins_query = "DELETE FROM admin_users WHERE pharmacy_id = ?";
    $admins_stmt = $connect->prepare($admins_query);
    
    if ($admins_stmt) {
        echo "<div style='color: green;'>✅ Admin users delete query prepared successfully</div>";
        $admins_stmt->bind_param("i", $pharmacy_id);
        if ($admins_stmt->execute()) {
            echo "<div style='color: green;'>✅ Admin users delete query executed successfully</div>";
        } else {
            echo "<div style='color: red;'>❌ Admin users delete query failed: " . $admins_stmt->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Admin users delete query preparation failed: " . $connect->error . "</div>";
    }
    
} else {
    echo "<p>No pharmacies found to test with.</p>";
}

echo "<p><a href='manage_pharmacies.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Delete Now</a></p>";
?>
