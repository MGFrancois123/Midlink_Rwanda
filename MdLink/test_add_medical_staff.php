<?php
require_once './constant/connect.php';

echo "<h2>Test Add Medical Staff Functionality</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Test the exact query from add_medical_staff.php
$test_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id, status) 
               VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";

$test_stmt = $connect->prepare($test_query);

if ($test_stmt) {
    echo "<div style='color: green;'>✅ Query prepared successfully</div>";
    
    // Test data
    $full_name = "Test Staff Member";
    $role = "doctor";
    $license_number = "TEST-001";
    $specialty = "General Medicine";
    $phone = "1234567890";
    $email = "test@example.com";
    $pharmacy_id = 1;
    
    $test_stmt->bind_param("ssssssi", $full_name, $role, $license_number, $specialty, $phone, $email, $pharmacy_id);
    
    if ($test_stmt->execute()) {
        echo "<div style='color: green;'>✅ Test insert executed successfully</div>";
        $test_id = $connect->insert_id;
        echo "<div style='color: blue;'>📝 Test record created with ID: $test_id</div>";
        
        // Clean up test record
        $cleanup_query = "DELETE FROM medical_staff WHERE staff_id = $test_id";
        if ($connect->query($cleanup_query)) {
            echo "<div style='color: green;'>✅ Test record cleaned up</div>";
        }
        
        echo "<h3>🎉 SUCCESS!</h3>";
        echo "<div style='color: green; font-size: 18px;'>";
        echo "The 'status' column error has been fixed!<br>";
        echo "You can now add medical staff without any database errors.<br>";
        echo "</div>";
        
    } else {
        echo "<div style='color: red;'>❌ Test insert failed: " . $test_stmt->error . "</div>";
    }
    
    $test_stmt->close();
} else {
    echo "<div style='color: red;'>❌ Failed to prepare query: " . $connect->error . "</div>";
}

$connect->close();
?>
