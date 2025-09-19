<?php
require_once './constant/connect.php';

echo "<h2>Category Cleanup and Management</h2>";

// First, let's see what categories currently exist
echo "<h3>Current Categories in Database:</h3>";
$sql = "SELECT category_id, category_name, description, status, created_at FROM category ORDER BY category_name";
$result = $connect->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th><th>Created</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['category_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description'] ?: 'N/A') . "</td>";
        echo "<td>" . ($row['status'] == '1' ? 'Active' : 'Inactive') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No categories found in database.</p>";
}

// Check for duplicates
echo "<h3>Checking for Duplicate Categories:</h3>";
$duplicateSql = "SELECT category_name, COUNT(*) as count FROM category GROUP BY category_name HAVING COUNT(*) > 1";
$duplicateResult = $connect->query($duplicateSql);

if ($duplicateResult && $duplicateResult->num_rows > 0) {
    echo "<p style='color: red;'>Found duplicate categories:</p>";
    echo "<ul>";
    while($row = $duplicateResult->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['category_name']) . " (appears " . $row['count'] . " times)</li>";
    }
    echo "</ul>";
    
    // Offer to clean up duplicates
    echo "<h3>Clean Up Duplicates:</h3>";
    echo "<form method='POST'>";
    echo "<input type='submit' name='cleanup_duplicates' value='Remove Duplicate Categories' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "</form>";
    
    if (isset($_POST['cleanup_duplicates'])) {
        echo "<h4>Cleaning up duplicates...</h4>";
        
        // Get all duplicate category names
        $duplicateNames = [];
        $duplicateResult = $connect->query($duplicateSql);
        while($row = $duplicateResult->fetch_assoc()) {
            $duplicateNames[] = $row['category_name'];
        }
        
        foreach ($duplicateNames as $categoryName) {
            // Keep the first occurrence, delete the rest
            $keepSql = "SELECT MIN(category_id) as keep_id FROM category WHERE category_name = ?";
            $stmt = $connect->prepare($keepSql);
            $stmt->bind_param("s", $categoryName);
            $stmt->execute();
            $result = $stmt->get_result();
            $keepId = $result->fetch_assoc()['keep_id'];
            
            // Delete duplicates (keep the one with the smallest ID)
            $deleteSql = "DELETE FROM category WHERE category_name = ? AND category_id != ?";
            $stmt = $connect->prepare($deleteSql);
            $stmt->bind_param("si", $categoryName, $keepId);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Cleaned up duplicates for: " . htmlspecialchars($categoryName) . "</p>";
            } else {
                echo "<p style='color: red;'>✗ Error cleaning up: " . htmlspecialchars($categoryName) . " - " . $stmt->error . "</p>";
            }
        }
        
        echo "<p><strong>Cleanup completed!</strong> <a href='add-product.php'>Go back to Add Product form</a></p>";
    }
} else {
    echo "<p style='color: green;'>No duplicate categories found.</p>";
}

// Add sample categories if none exist
$countSql = "SELECT COUNT(*) as count FROM category";
$countResult = $connect->query($countSql);
$count = $countResult->fetch_assoc()['count'];

if ($count == 0) {
    echo "<h3>No categories found. Adding sample categories...</h3>";
    
    $sampleCategories = [
        ['name' => 'Anti-infectives', 'description' => 'Treat infections caused by bacteria, viruses, fungi, or parasites.'],
        ['name' => 'Antibiotics', 'description' => 'Medicines that fight bacterial infections'],
        ['name' => 'Pain Relief', 'description' => 'Medicines for pain and fever management'],
        ['name' => 'Cardiovascular', 'description' => 'Medicines for heart and blood pressure'],
        ['name' => 'Diabetes', 'description' => 'Medicines for diabetes management'],
        ['name' => 'Respiratory', 'description' => 'Medicines for breathing and lung conditions'],
        ['name' => 'Dermatology', 'description' => 'Medicines for skin conditions'],
        ['name' => 'Mental Health', 'description' => 'Medicines for psychiatric conditions'],
        ['name' => 'Pediatrics', 'description' => 'Medicines specifically for children'],
        ['name' => 'Emergency Medicine', 'description' => 'Critical care and emergency medicines']
    ];
    
    $insertSql = "INSERT INTO category (category_name, description, status) VALUES (?, ?, '1')";
    $stmt = $connect->prepare($insertSql);
    
    foreach ($sampleCategories as $category) {
        $stmt->bind_param("ss", $category['name'], $category['description']);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✓ Added: " . htmlspecialchars($category['name']) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding: " . htmlspecialchars($category['name']) . " - " . $stmt->error . "</p>";
        }
    }
    
    echo "<p><strong>Sample categories added!</strong> <a href='add-product.php'>Go back to Add Product form</a></p>";
}

$connect->close();
?>
