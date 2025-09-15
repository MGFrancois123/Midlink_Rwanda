<?php
require_once './constant/connect.php';

echo "<h2>Adding Low Stock Test Data</h2>";

// Low stock medicines for testing alerts
$low_stock_medicines = [
    ['name' => 'Critical Stock Test 1', 'category_id' => 1, 'pharmacy_id' => 1, 'price' => 1500, 'stock' => 1, 'expiry' => '2025-12-31', 'restricted' => 0],
    ['name' => 'Critical Stock Test 2', 'category_id' => 2, 'pharmacy_id' => 2, 'price' => 2200, 'stock' => 0, 'expiry' => '2025-11-30', 'restricted' => 0],
    ['name' => 'Low Stock Test 1', 'category_id' => 3, 'pharmacy_id' => 3, 'price' => 1800, 'stock' => 3, 'expiry' => '2025-12-15', 'restricted' => 0],
    ['name' => 'Low Stock Test 2', 'category_id' => 4, 'pharmacy_id' => 4, 'price' => 3200, 'stock' => 2, 'expiry' => '2025-10-20', 'restricted' => 1],
    ['name' => 'Low Stock Test 3', 'category_id' => 5, 'pharmacy_id' => 5, 'price' => 1200, 'stock' => 4, 'expiry' => '2025-11-10', 'restricted' => 0],
    ['name' => 'Very Low Stock 1', 'category_id' => 1, 'pharmacy_id' => 1, 'price' => 2500, 'stock' => 1, 'expiry' => '2025-12-31', 'restricted' => 0],
    ['name' => 'Very Low Stock 2', 'category_id' => 2, 'pharmacy_id' => 2, 'price' => 1800, 'stock' => 2, 'expiry' => '2025-11-30', 'restricted' => 0],
    ['name' => 'Almost Out 1', 'category_id' => 3, 'pharmacy_id' => 3, 'price' => 3200, 'stock' => 1, 'expiry' => '2025-12-15', 'restricted' => 0],
    ['name' => 'Almost Out 2', 'category_id' => 4, 'pharmacy_id' => 4, 'price' => 4500, 'stock' => 0, 'expiry' => '2025-10-20', 'restricted' => 1],
    ['name' => 'Emergency Low 1', 'category_id' => 5, 'pharmacy_id' => 5, 'price' => 2800, 'stock' => 1, 'expiry' => '2025-11-10', 'restricted' => 0]
];

$success_count = 0;
$error_count = 0;

foreach ($low_stock_medicines as $medicine) {
    $name = $connect->real_escape_string($medicine['name']);
    $category_id = $medicine['category_id'];
    $pharmacy_id = $medicine['pharmacy_id'];
    $price = $medicine['price'];
    $stock = $medicine['stock'];
    $expiry = $medicine['expiry'];
    $restricted = $medicine['restricted'];
    
    // Check if medicine already exists
    $check_sql = "SELECT medicine_id FROM medicines WHERE name = ? AND pharmacy_id = ?";
    $check_stmt = $connect->prepare($check_sql);
    $check_stmt->bind_param("si", $name, $pharmacy_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Skipped: $name (already exists in pharmacy $pharmacy_id)</p>";
        continue;
    }
    
    // Insert medicine
    $sql = "INSERT INTO medicines (pharmacy_id, name, description, price, stock_quantity, expiry_date, `Restricted Medicine`, category_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $description = "Low stock test item for alert testing";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("issdssii", $pharmacy_id, $name, $description, $price, $stock, $expiry, $restricted, $category_id);
    
    if ($stmt->execute()) {
        $medicine_id = $connect->insert_id;
        $stock_status = $stock == 0 ? "OUT OF STOCK" : ($stock <= 2 ? "CRITICAL" : "LOW");
        echo "<p style='color: red;'>⚠️ Added Low Stock: $name (ID: $medicine_id) - Stock: $stock ($stock_status) - Price: RWF $price</p>";
        $success_count++;
        
        // Add stock movement record
        $movement_sql = "INSERT INTO stock_movements (medicine_id, movement_type, quantity, reason, created_at) 
                        VALUES (?, 'IN', ?, 'Initial low stock', NOW())";
        $movement_stmt = $connect->prepare($movement_sql);
        $movement_stmt->bind_param("ii", $medicine_id, $stock);
        $movement_stmt->execute();
        
    } else {
        echo "<p style='color: red;'>❌ Failed to add: $name - " . $stmt->error . "</p>";
        $error_count++;
    }
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p><strong>Successfully added:</strong> $success_count low stock medicines</p>";
echo "<p><strong>Errors:</strong> $error_count</p>";
echo "<p><strong>Total processed:</strong> " . count($low_stock_medicines) . " medicines</p>";

$connect->close();
echo "<br><a href='low_stock_alerts.php' class='btn btn-warning'>Check Low Stock Alerts</a>";
echo "<br><br><a href='product.php' class='btn btn-primary'>View All Medicines</a>";
?>
