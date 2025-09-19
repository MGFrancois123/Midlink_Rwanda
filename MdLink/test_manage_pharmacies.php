<?php
require_once './constant/connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Manage Pharmacies - MdLink Rwanda</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { 
            padding: 8px 16px; 
            margin: 5px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <h1>Test Manage Pharmacies Functionality</h1>
    
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
        <h2>Pharmacies Data Test</h2>
        <?php
        try {
            // Test the same query used in manage_pharmacies.php
            $admin_columns = $connect->query("SHOW COLUMNS FROM admin_users LIKE 'pharmacy_id'");
            $has_pharmacy_id = $admin_columns && $admin_columns->num_rows > 0;
            
            if ($has_pharmacy_id) {
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
            } else {
                $query = "
                    SELECT p.*, 
                           0 as admin_count,
                           COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
                    FROM pharmacies p
                    LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
                    GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
                    ORDER BY p.created_at DESC
                ";
            }
            
            $result = $connect->query($query);
            if ($result && $result->num_rows > 0) {
                echo '<p class="success">✅ Found ' . $result->num_rows . ' pharmacies</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Name</th><th>License</th><th>Location</th><th>Contact Person</th><th>Phone</th><th>Admin Count</th><th>Medicine Count</th></tr>';
                
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
        <h2>Action Buttons Test</h2>
        <p class="info">Test the action buttons for each pharmacy:</p>
        <?php
        $result = $connect->query("SELECT pharmacy_id, name FROM pharmacies LIMIT 3");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">';
                echo '<strong>' . htmlspecialchars($row['name']) . '</strong> (ID: ' . $row['pharmacy_id'] . ')<br>';
                echo '<a href="manage_pharmacies.php" class="btn btn-primary">View in Manage Page</a> ';
                echo '<a href="create_pharmacy.php?edit=' . $row['pharmacy_id'] . '" class="btn btn-success">Edit</a> ';
                echo '<a href="javascript:void(0)" onclick="testDelete(' . $row['pharmacy_id'] . ', \'' . htmlspecialchars($row['name']) . '\')" class="btn btn-danger">Test Delete</a>';
                echo '</div>';
            }
        } else {
            echo '<p class="warning">⚠️ No pharmacies found to test actions</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Quick Links</h2>
        <p>
            <a href="manage_pharmacies.php" class="btn btn-primary">Go to Manage Pharmacies Page</a>
            <a href="create_pharmacy.php" class="btn btn-success">Create New Pharmacy</a>
            <a href="product.php" class="btn btn-info">View Products</a>
            <a href="diagnose_database.php" class="btn btn-danger">Database Diagnostic</a>
        </p>
    </div>
    
    <script>
    function testDelete(id, name) {
        if (confirm('Test delete for "' + name + '" (ID: ' + id + ')?\n\nThis will actually delete the pharmacy!')) {
            // Create a form to submit the delete request
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'php_action/delete_pharmacy.php';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'pharmacy_id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>
