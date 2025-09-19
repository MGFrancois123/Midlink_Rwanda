<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fix Pharmacies Table</h2>";

// Check if database connection works
if ($connect->connect_error) {
    echo "<div style='color: red;'>❌ Database connection failed: " . $connect->connect_error . "</div>";
    exit();
}

echo "<div style='color: green;'>✅ Database connected successfully</div>";

// Check if updated_at column exists
$check_column_query = "SHOW COLUMNS FROM pharmacies LIKE 'updated_at'";
$check_result = $connect->query($check_column_query);

if ($check_result && $check_result->num_rows > 0) {
    echo "<div style='color: green;'>✅ updated_at column already exists</div>";
} else {
    echo "<div style='color: orange;'>⚠️ updated_at column missing, adding it...</div>";
    
    // Add the updated_at column
    $add_column_query = "ALTER TABLE pharmacies ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    
    if ($connect->query($add_column_query)) {
        echo "<div style='color: green;'>✅ Successfully added updated_at column</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to add updated_at column: " . $connect->error . "</div>";
        exit();
    }
}

// Verify the column was added
$verify_query = "SHOW COLUMNS FROM pharmacies";
$verify_result = $connect->query($verify_query);

if ($verify_result) {
    echo "<h3>Updated Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $verify_result->fetch_assoc()) {
        $highlight = ($row['Field'] === 'updated_at') ? 'style="background-color: #90EE90;"' : '';
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
} else {
    echo "<div style='color: red;'>❌ Could not verify table structure: " . $connect->error . "</div>";
}

// Test the update query now
echo "<h3>Testing Update Query</h3>";

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
                
                // Check if updated_at was set
                $check_updated_query = "SELECT updated_at FROM pharmacies WHERE pharmacy_id = ?";
                $check_updated_stmt = $connect->prepare($check_updated_query);
                $check_updated_stmt->bind_param("i", $pharmacy_id);
                $check_updated_stmt->execute();
                $check_updated_result = $check_updated_stmt->get_result();
                
                if ($check_updated_result && $check_updated_result->num_rows > 0) {
                    $updated_data = $check_updated_result->fetch_assoc();
                    echo "<div style='color: green;'>✅ updated_at timestamp set: " . $updated_data['updated_at'] . "</div>";
                }
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

echo "<h3>Fix Complete!</h3>";
echo "<p><a href='create_pharmacy.php?edit=9' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Update Now</a></p>";
echo "<p><a href='manage_pharmacies.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Manage Pharmacies</a></p>";
?>
