<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Stock Movements Debug</h1>";

// Start session and set test user
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['userId'] = 1;
$_SESSION['userRole'] = 'super_admin';

try {
    require_once 'constant/connect.php';
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    // Test if required tables exist
    $tables = ['medicines', 'pharmacies', 'stock_movements', 'users'];
    foreach ($tables as $table) {
        $result = $connect->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' missing</p>";
        }
    }
    
    // Test the main query
    echo "<h3>Testing Stock Movements Query:</h3>";
    $sql = "SELECT sm.*, m.name as medicine_name, p.name as pharmacy_name, u.username
            FROM stock_movements sm
            LEFT JOIN medicines m ON sm.medicine_id = m.medicine_id
            LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
            LEFT JOIN users u ON sm.user_id = u.user_id
            ORDER BY sm.movement_date DESC, sm.created_at DESC
            LIMIT 5";
    
    $result = $connect->query($sql);
    if ($result) {
        echo "<p style='color: green;'>✅ Query executed successfully</p>";
        echo "<p>Found " . $result->num_rows . " records</p>";
        
        if ($result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Medicine</th><th>Pharmacy</th><th>Type</th><th>Quantity</th><th>Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['movement_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['medicine_name'] ?? 'Unknown') . "</td>";
                echo "<td>" . htmlspecialchars($row['pharmacy_name'] ?? 'Unknown') . "</td>";
                echo "<td>" . $row['movement_type'] . "</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>" . $row['movement_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Query failed: " . $connect->error . "</p>";
    }
    
    // Test medicines for dropdown
    echo "<h3>Testing Medicines Query:</h3>";
    $sql = "SELECT m.medicine_id, m.name, p.name as pharmacy_name, m.stock_quantity
            FROM medicines m
            LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
            ORDER BY m.name LIMIT 5";
    $result = $connect->query($sql);
    if ($result) {
        echo "<p style='color: green;'>✅ Medicines query successful</p>";
        echo "<p>Found " . $result->num_rows . " medicines</p>";
        
        if ($result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Pharmacy</th><th>Stock</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['medicine_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pharmacy_name'] ?? 'Unknown') . "</td>";
                echo "<td>" . $row['stock_quantity'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Medicines query failed: " . $connect->error . "</p>";
    }
    
    // Test if PHP action file exists
    echo "<h3>Testing PHP Action Files:</h3>";
    if (file_exists('php_action/addStockMovement.php')) {
        echo "<p style='color: green;'>✅ addStockMovement.php exists</p>";
    } else {
        echo "<p style='color: red;'>❌ addStockMovement.php missing</p>";
    }
    
    // Test if layout files exist
    echo "<h3>Testing Layout Files:</h3>";
    $layout_files = [
        'constant/layout/head.php',
        'constant/layout/header.php',
        'constant/layout/sidebar.php',
        'constant/layout/footer.php'
    ];
    
    foreach ($layout_files as $file) {
        if (file_exists($file)) {
            echo "<p style='color: green;'>✅ $file exists</p>";
        } else {
            echo "<p style='color: red;'>❌ $file missing</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🚀 Test Links:</h3>";
echo "<p><a href='test_stock_movements_system.php' target='_blank'>🔗 Test Stock Movements with Full System</a></p>";
echo "<p><a href='stock_movements.php' target='_blank'>🔗 Stock Movements Page</a></p>";
echo "<p><a href='login.php' target='_blank'>🔗 Login Page</a></p>";
echo "<p><a href='create_test_user.php' target='_blank'>🔗 Create Test User</a></p>";

echo "<hr>";
echo "<h3>📋 Instructions:</h3>";
echo "<ol>";
echo "<li><strong>If you see database/table errors:</strong> Run the SQL setup files first</li>";
echo "<li><strong>If you see 'no records' messages:</strong> Add some test data</li>";
echo "<li><strong>If layout files are missing:</strong> Check the file structure</li>";
echo "<li><strong>If authentication fails:</strong> Use the 'Create Test User' link and login</li>";
echo "</ol>";
?>