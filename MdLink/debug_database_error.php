<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Error Debug</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
if ($connect->connect_error) {
    echo "<div style='color: red;'>❌ Connection Failed: " . $connect->connect_error . "</div>";
    exit();
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Test if pharmacies table exists
echo "<h3>2. Table Structure Test</h3>";
$tables_query = "SHOW TABLES LIKE 'pharmacies'";
$tables_result = $connect->query($tables_query);

if ($tables_result && $tables_result->num_rows > 0) {
    echo "<div style='color: green;'>✅ Pharmacies table exists</div>";
    
    // Check table structure
    $structure_query = "DESCRIBE pharmacies";
    $structure_result = $connect->query($structure_query);
    
    if ($structure_result) {
        echo "<h4>Table Structure:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='color: red;'>❌ Could not get table structure: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Pharmacies table does not exist</div>";
}

// Test if there are any pharmacies
echo "<h3>3. Data Test</h3>";
$count_query = "SELECT COUNT(*) as count FROM pharmacies";
$count_result = $connect->query($count_query);

if ($count_result) {
    $count = $count_result->fetch_assoc()['count'];
    echo "<div style='color: green;'>✅ Found $count pharmacies in database</div>";
    
    if ($count > 0) {
        // Get a sample pharmacy
        $sample_query = "SELECT * FROM pharmacies LIMIT 1";
        $sample_result = $connect->query($sample_query);
        
        if ($sample_result && $sample_result->num_rows > 0) {
            $pharmacy = $sample_result->fetch_assoc();
            echo "<h4>Sample Pharmacy:</h4>";
            echo "<ul>";
            foreach ($pharmacy as $key => $value) {
                echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
            }
            echo "</ul>";
        }
    }
} else {
    echo "<div style='color: red;'>❌ Could not count pharmacies: " . $connect->error . "</div>";
}

// Test the specific update query
echo "<h3>4. Update Query Test</h3>";

// Get a pharmacy ID to test with
$test_query = "SELECT pharmacy_id FROM pharmacies LIMIT 1";
$test_result = $connect->query($test_query);

if ($test_result && $test_result->num_rows > 0) {
    $pharmacy = $test_result->fetch_assoc();
    $pharmacy_id = $pharmacy['pharmacy_id'];
    
    echo "<p>Testing with Pharmacy ID: $pharmacy_id</p>";
    
    // Test the update query
    $update_query = "UPDATE pharmacies SET 
                     name = ?, 
                     license_number = ?, 
                     contact_person = ?, 
                     contact_phone = ?, 
                     location = ?,
                     updated_at = CURRENT_TIMESTAMP
                     WHERE pharmacy_id = ?";
    
    $update_stmt = $connect->prepare($update_query);
    
    if ($update_stmt) {
        echo "<div style='color: green;'>✅ Update query prepared successfully</div>";
        
        // Test with sample data
        $name = "Test Pharmacy " . date('H:i:s');
        $license_number = "TEST" . rand(1000, 9999);
        $contact_person = "Test Person";
        $contact_phone = "1234567890";
        $location = "Test Location";
        
        $bind_result = $update_stmt->bind_param("sssssi", $name, $license_number, $contact_person, $contact_phone, $location, $pharmacy_id);
        
        if ($bind_result) {
            echo "<div style='color: green;'>✅ Parameters bound successfully</div>";
            
            $execute_result = $update_stmt->execute();
            
            if ($execute_result) {
                echo "<div style='color: green;'>✅ Update query executed successfully</div>";
                echo "<p>Updated pharmacy with new name: $name</p>";
            } else {
                echo "<div style='color: red;'>❌ Update query execution failed: " . $update_stmt->error . "</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ Parameter binding failed: " . $update_stmt->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ Update query preparation failed: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ No pharmacies found to test with</div>";
}

// Test error logging
echo "<h3>5. Error Logging Test</h3>";
$log_message = "Test error log message at " . date('Y-m-d H:i:s');
error_log($log_message);
echo "<div style='color: green;'>✅ Error log test message sent: $log_message</div>";

echo "<h3>6. PHP Error Log Location</h3>";
echo "<p>Check your PHP error log at: " . ini_get('error_log') . "</p>";
echo "<p>Or check XAMPP error logs in: C:\\xampp\\apache\\logs\\error.log</p>";

echo "<p><a href='create_pharmacy.php?edit=9'>Test Edit Page</a> | <a href='manage_pharmacies.php'>Manage Pharmacies</a></p>";
?>
