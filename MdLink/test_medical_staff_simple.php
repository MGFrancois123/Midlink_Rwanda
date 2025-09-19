<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing Medical Staff Page</h1>";

try {
    require_once './constant/connect.php';
    echo "<p>✅ Database connected</p>";
    
    // Test basic query
    $query = "SELECT COUNT(*) as count FROM medical_staff";
    $result = $connect->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Medical staff count: $count</p>";
    } else {
        echo "<p>❌ Query failed: " . $connect->error . "</p>";
    }
    
    // Test if we can include the main file
    echo "<p>Testing include of medical_staff.php...</p>";
    
    // Check if file exists
    if (file_exists('medical_staff.php')) {
        echo "<p>✅ medical_staff.php file exists</p>";
        
        // Try to read first few lines
        $lines = file('medical_staff.php');
        echo "<p>✅ File has " . count($lines) . " lines</p>";
        echo "<p>First line: " . htmlspecialchars($lines[0]) . "</p>";
    } else {
        echo "<p>❌ medical_staff.php file not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='medical_staff.php'>Try Medical Staff Page</a></p>";
?>
