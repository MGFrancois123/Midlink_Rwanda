<?php
require_once './constant/connect.php';

echo "<h2>Fix Medical Staff Table</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if medical_staff table exists
$check_table_sql = "SHOW TABLES LIKE 'medical_staff'";
$result = $connect->query($check_table_sql);

if ($result && $result->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ medical_staff table does not exist. Creating it...</div>";
    
    $create_table_sql = "
    CREATE TABLE medical_staff (
        staff_id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        role ENUM('doctor', 'nurse', 'pharmacist', 'technician') NOT NULL,
        license_number VARCHAR(100) NULL,
        specialty VARCHAR(255) NULL,
        phone VARCHAR(20) NULL,
        email VARCHAR(255) NULL,
        pharmacy_id INT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE SET NULL ON UPDATE CASCADE
    )";
    
    if ($connect->query($create_table_sql)) {
        echo "<div style='color: green;'>✅ medical_staff table created successfully</div>";
        
        // Add sample data
        $sample_data = [
            ['Dr. Jean Baptiste', 'doctor', 'MD-001', 'Cardiology', '+250788123456', 'jean@mdlink.rw', 1],
            ['Nurse Marie Claire', 'nurse', 'RN-001', 'Emergency', '+250788123457', 'marie@mdlink.rw', 1],
            ['Dr. Paul Nkurunziza', 'doctor', 'MD-002', 'Pediatrics', '+250788123458', 'paul@mdlink.rw', 2],
            ['Pharmacist Grace Mukamana', 'pharmacist', 'PH-001', 'Clinical Pharmacy', '+250788123459', 'grace@mdlink.rw', 1],
            ['Technician David Kwizera', 'technician', 'TC-001', 'Lab Technician', '+250788123460', 'david@mdlink.rw', NULL]
        ];
        
        $insert_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $connect->prepare($insert_query);
        
        foreach ($sample_data as $staff) {
            $insert_stmt->bind_param("ssssssi", ...$staff);
            if ($insert_stmt->execute()) {
                echo "<div style='color: green;'>✅ Added: " . $staff[0] . "</div>";
            }
        }
    } else {
        echo "<div style='color: red;'>❌ Failed to create table: " . $connect->error . "</div>";
    }
} else {
    echo "<div style='color: green;'>✅ medical_staff table exists</div>";
    
    // Check if pharmacy_id column exists
    $check_column_sql = "SHOW COLUMNS FROM medical_staff LIKE 'pharmacy_id'";
    $column_result = $connect->query($check_column_sql);
    
    if ($column_result && $column_result->num_rows == 0) {
        echo "<div style='color: orange;'>⚠️ pharmacy_id column missing, adding it...</div>";
        $add_column_sql = "ALTER TABLE medical_staff ADD COLUMN pharmacy_id INT NULL DEFAULT NULL";
        if ($connect->query($add_column_sql) === TRUE) {
            echo "<div style='color: green;'>✅ Successfully added pharmacy_id column</div>";
            
            // Add foreign key constraint
            $add_fk_sql = "ALTER TABLE medical_staff ADD CONSTRAINT fk_medical_staff_pharmacy FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE SET NULL ON UPDATE CASCADE";
            if ($connect->query($add_fk_sql) === TRUE) {
                echo "<div style='color: green;'>✅ Successfully added foreign key constraint</div>";
            } else {
                echo "<div style='color: orange;'>⚠️ Could not add foreign key constraint: " . $connect->error . "</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ Error adding pharmacy_id column: " . $connect->error . "</div>";
        }
    } else {
        echo "<div style='color: green;'>✅ pharmacy_id column already exists</div>";
    }
}

echo "<h3>Current Table Structure:</h3>";
$table_structure_sql = "DESCRIBE medical_staff";
$structure_result = $connect->query($table_structure_sql);
if ($structure_result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure_result->fetch_assoc()) {
        $style = ($row['Field'] == 'pharmacy_id') ? 'background-color: #90EE90;' : '';
        echo "<tr style=\"$style\"><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div style='color: red;'>❌ Could not retrieve table structure: " . $connect->error . "</div>";
}

echo "<h3>Testing Query:</h3>";
// Test the query that was failing
$test_query = "SELECT ms.*, p.name as pharmacy_name FROM medical_staff ms LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id ORDER BY ms.created_at DESC";
$test_result = $connect->query($test_query);

if ($test_result) {
    echo "<div style='color: green;'>✅ Query executed successfully</div>";
    echo "<p>Found " . $test_result->num_rows . " medical staff records</p>";
    
    if ($test_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>License</th><th>Pharmacy</th></tr>";
        while ($row = $test_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['staff_id']}</td>";
            echo "<td>{$row['full_name']}</td>";
            echo "<td>{$row['role']}</td>";
            echo "<td>{$row['license_number']}</td>";
            echo "<td>{$row['pharmacy_name']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<div style='color: red;'>❌ Query failed: " . $connect->error . "</div>";
}

echo "<p><a href='medical_staff.php'>Try Medical Staff Page</a></p>";

$connect->close();
?>
