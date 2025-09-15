<?php
// Test script to verify category insertion
require_once 'php_action/core.php';

echo "<h2>Category Insertion Test</h2>";

// Test 1: Check database connection
echo "<h3>1. Database Connection Test</h3>";
if ($connect->ping()) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test 2: Check table structure
echo "<h3>2. Table Structure Test</h3>";
$result = $connect->query("DESCRIBE category");
if ($result) {
    echo "✅ Category table exists<br>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Category table does not exist<br>";
    exit;
}

// Test 3: Test insertion
echo "<h3>3. Insertion Test</h3>";
$testName = "Test Category " . date('Y-m-d H:i:s');
$testDescription = "Test description";
$testStatus = "1";

$sql = "INSERT INTO category (category_name, description, status) VALUES (?, ?, ?)";
$stmt = $connect->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sss", $testName, $testDescription, $testStatus);
    
    if ($stmt->execute()) {
        $insertId = $stmt->insert_id;
        echo "✅ Test insertion successful! Insert ID: $insertId<br>";
        
        // Verify the insertion
        $verifySql = "SELECT * FROM category WHERE category_id = ?";
        $verifyStmt = $connect->prepare($verifySql);
        $verifyStmt->bind_param("i", $insertId);
        $verifyStmt->execute();
        $result = $verifyStmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            echo "✅ Verification successful:<br>";
            echo "- ID: " . $row['category_id'] . "<br>";
            echo "- Name: " . $row['category_name'] . "<br>";
            echo "- Description: " . $row['description'] . "<br>";
            echo "- Status: " . $row['status'] . "<br>";
            echo "- Created: " . $row['created_at'] . "<br>";
        } else {
            echo "❌ Verification failed - record not found<br>";
        }
        
        // Clean up test data
        $deleteSql = "DELETE FROM category WHERE category_id = ?";
        $deleteStmt = $connect->prepare($deleteSql);
        $deleteStmt->bind_param("i", $insertId);
        $deleteStmt->execute();
        echo "🧹 Test data cleaned up<br>";
        
    } else {
        echo "❌ Test insertion failed: " . $stmt->error . "<br>";
    }
    $stmt->close();
} else {
    echo "❌ Prepare statement failed: " . $connect->error . "<br>";
}

// Test 4: Check current categories
echo "<h3>4. Current Categories</h3>";
$result = $connect->query("SELECT * FROM category ORDER BY category_id DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "Current categories in database:<br>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th><th>Created</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['category_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No categories found in database<br>";
}

$connect->close();
echo "<h3>✅ Test completed!</h3>";
?>
