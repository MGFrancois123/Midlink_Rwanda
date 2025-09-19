<?php
require_once './constant/connect.php';

echo "Setting up medical_staff table...\n";

// Check if table exists
$table_check = $connect->query("SHOW TABLES LIKE 'medical_staff'");
if ($table_check->num_rows > 0) {
    echo "✅ medical_staff table already exists\n";
} else {
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
        exit();
    }
}

// Add some sample data
$sample_data = [
    [
        'full_name' => 'Dr. Jean Baptiste',
        'role' => 'doctor',
        'license_number' => 'MD-2024-001',
        'specialty' => 'Cardiology',
        'phone' => '+250788123456',
        'email' => 'jean.baptiste@mdlink.rw',
        'pharmacy_id' => 1
    ],
    [
        'full_name' => 'Nurse Marie Claire',
        'role' => 'nurse',
        'license_number' => 'RN-2024-001',
        'specialty' => 'Emergency Care',
        'phone' => '+250788123457',
        'email' => 'marie.claire@mdlink.rw',
        'pharmacy_id' => 1
    ],
    [
        'full_name' => 'Dr. Paul Nkurunziza',
        'role' => 'doctor',
        'license_number' => 'MD-2024-002',
        'specialty' => 'Pediatrics',
        'phone' => '+250788123458',
        'email' => 'paul.nkurunziza@mdlink.rw',
        'pharmacy_id' => 2
    ],
    [
        'full_name' => 'Pharmacist Alice Mukamana',
        'role' => 'pharmacist',
        'license_number' => 'PH-2024-001',
        'specialty' => 'Clinical Pharmacy',
        'phone' => '+250788123459',
        'email' => 'alice.mukamana@mdlink.rw',
        'pharmacy_id' => 2
    ],
    [
        'full_name' => 'Lab Technician John Doe',
        'role' => 'technician',
        'license_number' => 'LT-2024-001',
        'specialty' => 'Laboratory Technology',
        'phone' => '+250788123460',
        'email' => 'john.doe@mdlink.rw',
        'pharmacy_id' => 3
    ]
];

// Check if table is empty
$count_query = "SELECT COUNT(*) as count FROM medical_staff";
$count_result = $connect->query($count_query);
$count = $count_result->fetch_assoc()['count'];

if ($count == 0) {
    echo "Adding sample data...\n";
    
    $insert_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $connect->prepare($insert_query);
    
    foreach ($sample_data as $staff) {
        $insert_stmt->bind_param("ssssssi", 
            $staff['full_name'], 
            $staff['role'], 
            $staff['license_number'], 
            $staff['specialty'], 
            $staff['phone'], 
            $staff['email'], 
            $staff['pharmacy_id']
        );
        
        if ($insert_stmt->execute()) {
            echo "✅ Added: " . $staff['full_name'] . "\n";
        } else {
            echo "❌ Failed to add: " . $staff['full_name'] . " - " . $insert_stmt->error . "\n";
        }
    }
} else {
    echo "✅ Table already has $count records\n";
}

echo "Setup complete!\n";
?>
