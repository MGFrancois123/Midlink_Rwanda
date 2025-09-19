<?php
require_once './constant/connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Pharmacy Data - MdLink Rwanda</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Test Pharmacy Data from Database</h1>
    
    <div class="test-section">
        <h2>Database Connection Test</h2>
        <?php
        if ($connect->connect_error) {
            echo '<p class="error">❌ Database connection failed: ' . $connect->connect_error . '</p>';
        } else {
            echo '<p class="success">✅ Database connection successful</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Table Existence Check</h2>
        <?php
        $tables = ['pharmacies', 'admin_users', 'medicines', 'category'];
        foreach ($tables as $table) {
            $result = $connect->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo '<p class="success">✅ Table "' . $table . '" exists</p>';
            } else {
                echo '<p class="error">❌ Table "' . $table . '" does not exist</p>';
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Pharmacies Data</h2>
        <?php
        try {
            $query = "
                SELECT p.*, 
                       COALESCE(COUNT(DISTINCT au.admin_id), 0) as admin_count,
                       COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
                FROM pharmacies p
                LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
                LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
                GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
                ORDER BY p.created_at DESC
            ";
            
            $result = $connect->query($query);
            if ($result && $result->num_rows > 0) {
                echo '<p class="success">✅ Found ' . $result->num_rows . ' pharmacies in database</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Name</th><th>License</th><th>Location</th><th>Contact Person</th><th>Phone</th><th>Admin Count</th><th>Medicine Count</th><th>Created</th></tr>';
                
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['pharmacy_id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['license_number']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['location']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['contact_person']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['contact_phone']) . '</td>';
                    echo '<td>' . $row['admin_count'] . '</td>';
                    echo '<td>' . $row['medicine_count'] . '</td>';
                    echo '<td>' . $row['created_at'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">⚠️ No pharmacies found in database</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Error fetching pharmacies: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Statistics</h2>
        <?php
        try {
            $stats_query = "
                SELECT 
                    (SELECT COUNT(*) FROM pharmacies) as total_pharmacies,
                    (SELECT COUNT(*) FROM admin_users) as total_admins,
                    (SELECT COUNT(*) FROM medicines) as total_medicines,
                    (SELECT COUNT(*) FROM category) as total_categories
            ";
            $stats_result = $connect->query($stats_query);
            if ($stats_result && $stats_result->num_rows > 0) {
                $stats = $stats_result->fetch_assoc();
                echo '<table>';
                echo '<tr><th>Metric</th><th>Count</th></tr>';
                echo '<tr><td>Total Pharmacies</td><td>' . $stats['total_pharmacies'] . '</td></tr>';
                echo '<tr><td>Total Admin Users</td><td>' . $stats['total_admins'] . '</td></tr>';
                echo '<tr><td>Total Medicines</td><td>' . $stats['total_medicines'] . '</td></tr>';
                echo '<tr><td>Total Categories</td><td>' . $stats['total_categories'] . '</td></tr>';
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Error fetching statistics: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Quick Links</h2>
        <p>
            <a href="create_pharmacy.php">Go to Create Pharmacy Page</a> |
            <a href="product.php">Go to Product Management</a> |
            <a href="add-product.php">Add New Medicine</a>
        </p>
    </div>
</body>
</html>
