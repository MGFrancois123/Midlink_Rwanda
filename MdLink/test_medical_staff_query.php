<?php
require_once './constant/connect.php';

echo "<h2>Test Medical Staff Query</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Test the exact query from medical_staff.php
$staffQuery = "SELECT ms.*, p.name as pharmacy_name 
               FROM medical_staff ms 
               LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
               ORDER BY ms.created_at DESC";

echo "<h3>Testing Query:</h3>";
echo "<pre>" . htmlspecialchars($staffQuery) . "</pre>";

$staffResult = $connect->query($staffQuery);

if ($staffResult) {
    echo "<div style='color: green;'>✅ Query executed successfully</div>";
    echo "<p>Found " . $staffResult->num_rows . " medical staff records</p>";
    
    if ($staffResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>License</th><th>Specialty</th><th>Phone</th><th>Email</th><th>Pharmacy</th><th>Status</th><th>Created</th></tr>";
        while ($row = $staffResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['staff_id']}</td>";
            echo "<td>{$row['full_name']}</td>";
            echo "<td>{$row['role']}</td>";
            echo "<td>{$row['license_number']}</td>";
            echo "<td>{$row['specialty']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['pharmacy_name']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='color: orange;'>⚠️ No medical staff records found</div>";
        echo "<p>This is normal if the table was just created. You can add staff using the medical staff page.</p>";
    }
} else {
    echo "<div style='color: red;'>❌ Query failed: " . $connect->error . "</div>";
}

echo "<h3>Statistics Test:</h3>";

// Test statistics queries
$stats = [
    'total_staff' => 0,
    'doctors' => 0,
    'nurses' => 0,
    'assigned_staff' => 0
];

$totalQuery = "SELECT COUNT(*) as count FROM medical_staff";
$totalResult = $connect->query($totalQuery);
if ($totalResult) {
    $stats['total_staff'] = $totalResult->fetch_assoc()['count'];
    echo "<div style='color: green;'>✅ Total staff count: {$stats['total_staff']}</div>";
}

$doctorsQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'doctor'";
$doctorsResult = $connect->query($doctorsQuery);
if ($doctorsResult) {
    $stats['doctors'] = $doctorsResult->fetch_assoc()['count'];
    echo "<div style='color: green;'>✅ Doctors count: {$stats['doctors']}</div>";
}

$nursesQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE role = 'nurse'";
$nursesResult = $connect->query($nursesQuery);
if ($nursesResult) {
    $stats['nurses'] = $nursesResult->fetch_assoc()['count'];
    echo "<div style='color: green;'>✅ Nurses count: {$stats['nurses']}</div>";
}

$assignedQuery = "SELECT COUNT(*) as count FROM medical_staff WHERE pharmacy_id IS NOT NULL";
$assignedResult = $connect->query($assignedQuery);
if ($assignedResult) {
    $stats['assigned_staff'] = $assignedResult->fetch_assoc()['count'];
    echo "<div style='color: green;'>✅ Assigned staff count: {$stats['assigned_staff']}</div>";
}

echo "<p><a href='medical_staff.php'>Try Medical Staff Page</a> | <a href='medical_staff_simple.php'>Try Simple Version</a></p>";

$connect->close();
?>
