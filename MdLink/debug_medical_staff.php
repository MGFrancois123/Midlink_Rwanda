<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Medical Staff Debug</h2>";

// Test database connection
require_once './constant/connect.php';

if ($connect->connect_error) {
    echo "<div style='color: red;'>❌ Database connection failed: " . $connect->connect_error . "</div>";
    exit();
}

echo "<div style='color: green;'>✅ Database connected successfully</div>";

// Check if medical_staff table exists
$table_check = $connect->query("SHOW TABLES LIKE 'medical_staff'");
if ($table_check->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ medical_staff table does not exist. Creating it...</div>";
    
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
        echo "<div style='color: green;'>✅ medical_staff table created successfully</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to create table: " . $connect->error . "</div>";
        exit();
    }
} else {
    echo "<div style='color: green;'>✅ medical_staff table exists</div>";
}

// Check if table has data
$count_query = "SELECT COUNT(*) as count FROM medical_staff";
$count_result = $connect->query($count_query);
$count = $count_result->fetch_assoc()['count'];

echo "<div>📊 Medical staff count: $count</div>";

if ($count == 0) {
    echo "<div style='color: orange;'>⚠️ No medical staff found. Adding sample data...</div>";
    
    // Add sample data
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
        ]
    ];
    
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
            echo "<div style='color: green;'>✅ Added: " . $staff['full_name'] . "</div>";
        } else {
            echo "<div style='color: red;'>❌ Failed to add: " . $staff['full_name'] . " - " . $insert_stmt->error . "</div>";
        }
    }
}

// Test the query that medical_staff.php uses
echo "<h3>Testing Medical Staff Query</h3>";

$staffQuery = "SELECT ms.*, p.name as pharmacy_name 
               FROM medical_staff ms 
               LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
               ORDER BY ms.created_at DESC";

$staffResult = $connect->query($staffQuery);

if ($staffResult) {
    echo "<div style='color: green;'>✅ Query executed successfully</div>";
    echo "<div>📊 Found " . $staffResult->num_rows . " staff members</div>";
    
    if ($staffResult->num_rows > 0) {
        echo "<h4>Staff Data:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>Pharmacy</th></tr>";
        while ($row = $staffResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['staff_id'] . "</td>";
            echo "<td>" . $row['full_name'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "<td>" . ($row['pharmacy_name'] ?? 'Not assigned') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<div style='color: red;'>❌ Query failed: " . $connect->error . "</div>";
}

// Test statistics queries
echo "<h3>Testing Statistics Queries</h3>";

$totalQuery = "SELECT COUNT(*) as count FROM medical_staff";
$totalResult = $connect->query($totalQuery);
if ($totalResult) {
    $total = $totalResult->fetch_assoc()['count'];
    echo "<div>📊 Total staff: $total</div>";
} else {
    echo "<div style='color: red;'>❌ Total query failed: " . $connect->error . "</div>";
}

echo "<p><a href='medical_staff.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Medical Staff Page</a></p>";
?>
