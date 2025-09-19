<?php
require_once './constant/connect.php';

echo "Checking medical_staff table...\n";

// Check if table exists
$table_check = $connect->query("SHOW TABLES LIKE 'medical_staff'");
if ($table_check->num_rows > 0) {
    echo "✅ medical_staff table exists\n";
    
    // Check table structure
    $structure = $connect->query("DESCRIBE medical_staff");
    echo "Table structure:\n";
    while ($row = $structure->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']}\n";
    }
} else {
    echo "❌ medical_staff table does not exist\n";
    echo "Creating medical_staff table...\n";
    
    $create_table = "CREATE TABLE medical_staff (
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
        FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE SET NULL
    )";
    
    if ($connect->query($create_table)) {
        echo "✅ medical_staff table created successfully\n";
    } else {
        echo "❌ Failed to create table: " . $connect->error . "\n";
    }
}
?>
