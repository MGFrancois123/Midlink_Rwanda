<?php
require_once './constant/connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Edit Functionality</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .medicine-item { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 10px; 
            border: 1px solid #ddd; 
            margin: 5px 0; 
            border-radius: 5px;
        }
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
    </style>
</head>
<body>
    <h1>Test Edit Functionality</h1>
    
    <div class="test-section">
        <h2>Available Medicines for Testing</h2>
        <p>Click "Edit" to test the edit functionality:</p>
        
        <?php
        // Fetch medicines for testing
        $sql = "SELECT 
                    m.medicine_id,
                    m.name,
                    m.description,
                    m.price,
                    m.stock_quantity,
                    m.expiry_date,
                    m.`Restricted Medicine`,
                    COALESCE(p.name, 'No Pharmacy') as pharmacy_name
                FROM medicines m
                LEFT JOIN pharmacies p ON m.pharmacy_id = p.pharmacy_id
                ORDER BY m.medicine_id DESC
                LIMIT 10";
        
        $result = $connect->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="medicine-item">';
                echo '<div>';
                echo '<strong>' . htmlspecialchars($row['name']) . '</strong><br>';
                echo '<small>ID: ' . $row['medicine_id'] . ' | Price: RWF ' . number_format($row['price']) . ' | Stock: ' . $row['stock_quantity'] . '</small><br>';
                echo '<small>Pharmacy: ' . htmlspecialchars($row['pharmacy_name']) . '</small>';
                echo '</div>';
                echo '<div>';
                echo '<a href="add-product.php?edit=' . $row['medicine_id'] . '" class="btn btn-primary">Edit</a>';
                echo '<a href="javascript:void(0)" onclick="deleteMedicine(' . $row['medicine_id'] . ')" class="btn btn-danger">Delete</a>';
                echo '<a href="product.php" class="btn btn-info">View All</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No medicines found. <a href="add-product.php">Add some medicines first</a>.</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test Instructions</h2>
        <ol>
            <li><strong>Click "Edit"</strong> on any medicine above</li>
            <li><strong>Verify the form loads</strong> with all fields populated</li>
            <li><strong>Check the display fields</strong> show the selected values in highlighted boxes</li>
            <li><strong>Make changes</strong> to any field</li>
            <li><strong>Click "Update Medicine"</strong> to save changes</li>
            <li><strong>Verify success message</strong> and return to product list</li>
        </ol>
    </div>
    
    <div class="test-section">
        <h2>Expected Behavior</h2>
        <ul>
            <li>✅ Form should load with all existing data pre-filled</li>
            <li>✅ Display boxes should show selected values in green</li>
            <li>✅ Form should submit to update_medicine.php</li>
            <li>✅ Success message should appear after update</li>
            <li>✅ Should redirect back to product.php</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h2>Quick Links</h2>
        <a href="add-product.php" class="btn btn-success">Add New Medicine</a>
        <a href="product.php" class="btn btn-info">View All Medicines</a>
        <a href="cleanup_categories.php" class="btn btn-primary">Clean Up Categories</a>
    </div>
    
    <script>
    // Add delete medicine function for test page
    function deleteMedicine(id) {
        if (confirm('Are you sure you want to delete this medicine? This action cannot be undone.')) {
            // Create a form to submit the delete request
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'php_action/delete_medicine.php';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'medicine_id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>
