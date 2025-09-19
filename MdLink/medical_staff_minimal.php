<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Medical Staff - Minimal Test</h1>";

try {
    require_once './constant/connect.php';
    echo "<p>✅ Database connected</p>";
    
    // Test basic query
    $query = "SELECT COUNT(*) as count FROM medical_staff";
    $result = $connect->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Medical staff count: $count</p>";
        
        if ($count == 0) {
            echo "<p>⚠️ No medical staff found. Adding sample data...</p>";
            
            // Add sample data
            $insert_query = "INSERT INTO medical_staff (full_name, role, license_number, specialty, phone, email, pharmacy_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $connect->prepare($insert_query);
            
            $staff_data = [
                'Dr. Jean Baptiste',
                'doctor',
                'MD-2024-001',
                'Cardiology',
                '+250788123456',
                'jean.baptiste@mdlink.rw',
                1
            ];
            
            $insert_stmt->bind_param("ssssssi", ...$staff_data);
            
            if ($insert_stmt->execute()) {
                echo "<p>✅ Sample staff added</p>";
            } else {
                echo "<p>❌ Failed to add sample staff: " . $insert_stmt->error . "</p>";
            }
        }
        
        // Show staff list
        $staff_query = "SELECT * FROM medical_staff ORDER BY created_at DESC";
        $staff_result = $connect->query($staff_query);
        
        if ($staff_result && $staff_result->num_rows > 0) {
            echo "<h3>Medical Staff List:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>Specialty</th><th>Phone</th></tr>";
            
            while ($row = $staff_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['staff_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                echo "<td>" . ucfirst($row['role']) . "</td>";
                echo "<td>" . htmlspecialchars($row['specialty'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['phone'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No staff found</p>";
        }
        
    } else {
        echo "<p>❌ Query failed: " . $connect->error . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<p><a href='medical_staff.php'>Try Full Medical Staff Page</a></p>";
?>
