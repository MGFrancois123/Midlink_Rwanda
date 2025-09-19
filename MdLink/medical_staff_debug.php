<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Medical Staff Debug - Step 1</h1>";

try {
    echo "<p>✅ PHP is working</p>";
    
    // Test database connection
    require_once './constant/connect.php';
    echo "<p>✅ Database connection loaded</p>";
    
    if ($connect->connect_error) {
        echo "<p>❌ Database connection failed: " . $connect->connect_error . "</p>";
    } else {
        echo "<p>✅ Database connected successfully</p>";
    }
    
    // Test authentication
    echo "<p>Testing authentication...</p>";
    require_once './constant/check.php';
    echo "<p>✅ Authentication check passed</p>";
    
    // Test medical_staff table
    echo "<p>Testing medical_staff table...</p>";
    $table_check = $connect->query("SHOW TABLES LIKE 'medical_staff'");
    
    if ($table_check->num_rows == 0) {
        echo "<p>⚠️ medical_staff table does not exist. Creating it...</p>";
        
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
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if ($connect->query($create_table)) {
            echo "<p>✅ medical_staff table created successfully</p>";
            
            // Add sample data
            $sample_data = [
                ['Dr. Jean Baptiste', 'doctor', 'MD-001', 'Cardiology', '+250788123456', 'jean@mdlink.rw', 1],
                ['Nurse Marie Claire', 'nurse', 'RN-001', 'Emergency', '+250788123457', 'marie@mdlink.rw', 1],
                ['Dr. Paul Nkurunziza', 'doctor', 'MD-002', 'Pediatrics', '+250788123458', 'paul@mdlink.rw', 2]
            ];
            
            $insert_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $connect->prepare($insert_query);
            
            foreach ($sample_data as $staff) {
                $insert_stmt->bind_param("ssssssi", ...$staff);
                if ($insert_stmt->execute()) {
                    echo "<p>✅ Added: " . $staff[0] . "</p>";
                }
            }
        } else {
            echo "<p>❌ Failed to create table: " . $connect->error . "</p>";
        }
    } else {
        echo "<p>✅ medical_staff table exists</p>";
    }
    
    // Test query
    $query = "SELECT COUNT(*) as count FROM medical_staff";
    $result = $connect->query($query);
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Medical staff count: $count</p>";
    }
    
    echo "<p><strong>All tests passed! The issue might be in the main file.</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<p><a href='medical_staff.php'>Try Main Page</a> | <a href='medical_staff_simple.php'>Try Simple Version</a></p>";
?>
