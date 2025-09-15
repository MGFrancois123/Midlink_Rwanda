<!DOCTYPE html>
<html>
<head>
    <title>Stock Movements Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>🔍 Stock Movements System Diagnostic</h1>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    echo "<div class='section'>";
    echo "<h2>1. Database Connection Test</h2>";
    try {
        require_once 'constant/connect.php';
        echo "<p class='success'>✅ Database connection successful</p>";
        echo "<p>Database: " . $connect->get_server_info() . "</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
        exit;
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>2. Required Tables Check</h2>";
    $required_tables = ['medicines', 'pharmacies', 'stock_movements', 'users'];
    foreach ($required_tables as $table) {
        $result = $connect->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p class='success'>✅ Table '$table' exists</p>";
            
            // Count records
            $count_result = $connect->query("SELECT COUNT(*) as count FROM $table");
            $count = $count_result->fetch_assoc()['count'];
            echo "<p>&nbsp;&nbsp;&nbsp;Records: $count</p>";
        } else {
            echo "<p class='error'>❌ Table '$table' missing</p>";
        }
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>3. Stock Movements Table Structure</h2>";
    $result = $connect->query("DESCRIBE stock_movements");
    if ($result) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Cannot describe stock_movements table</p>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>4. Sample Data Check</h2>";
    
    // Check medicines
    echo "<h3>Medicines (first 5):</h3>";
    $result = $connect->query("SELECT medicine_id, name, pharmacy_id, stock_quantity FROM medicines LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Pharmacy ID</th><th>Stock</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['medicine_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . $row['pharmacy_id'] . "</td>";
            echo "<td>" . $row['stock_quantity'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No medicines found</p>";
    }
    
    // Check stock movements
    echo "<h3>Stock Movements (first 5):</h3>";
    $result = $connect->query("SELECT sm.*, m.name as medicine_name FROM stock_movements sm LEFT JOIN medicines m ON sm.medicine_id = m.medicine_id ORDER BY sm.movement_date DESC LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Medicine</th><th>Type</th><th>Quantity</th><th>Previous</th><th>New</th><th>Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['movement_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['medicine_name'] ?? 'Unknown') . "</td>";
            echo "<td>" . $row['movement_type'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['previous_stock'] . "</td>";
            echo "<td>" . $row['new_stock'] . "</td>";
            echo "<td>" . $row['movement_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No stock movements found</p>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>5. File System Check</h2>";
    $required_files = [
        'placeholder.php',
        'php_action/addStockMovement.php',
        'constant/connect.php',
        'constant/layout/head.php',
        'constant/layout/header.php',
        'constant/layout/sidebar.php'
    ];
    
    foreach ($required_files as $file) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ File '$file' exists</p>";
        } else {
            echo "<p class='error'>❌ File '$file' missing</p>";
        }
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>6. Test Links</h2>";
    echo "<p><a href='stock_movements.php' target='_blank'>🔗 Open Stock Movements Page</a></p>";
    echo "<p><a href='low_stock_alerts.php' target='_blank'>🔗 Open Low Stock Alerts Page</a></p>";
    echo "<p><a href='placeholder.php?title=Expiry%20Alerts' target='_blank'>🔗 Open Expiry Alerts Page</a></p>";
    echo "</div>";
    ?>
    
    <div class='section'>
        <h2>7. Quick Fixes</h2>
        <p>If you're seeing issues, try these:</p>
        <ul>
            <li><strong>XAMPP not running:</strong> Start Apache and MySQL in XAMPP Control Panel</li>
            <li><strong>Database not found:</strong> Make sure 'mdlink' database exists in phpMyAdmin</li>
            <li><strong>Missing tables:</strong> Run the SQL files in the sql/ directory</li>
            <li><strong>Permission issues:</strong> Check file permissions in the project directory</li>
        </ul>
    </div>
</body>
</html>