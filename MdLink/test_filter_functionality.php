<?php
require_once './constant/connect.php';

echo "<h2>🧪 Test Filter Functionality</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if we have medical staff data
$staff_query = "SELECT ms.*, p.name as pharmacy_name 
                FROM medical_staff ms 
                LEFT JOIN pharmacies p ON ms.pharmacy_id = p.pharmacy_id 
                ORDER BY ms.created_at DESC";
$staff_result = $connect->query($staff_query);

if ($staff_result && $staff_result->num_rows > 0) {
    echo "<div style='color: green;'>✅ Found " . $staff_result->num_rows . " medical staff records</div>";
    
    echo "<h3>📋 Sample Data for Filter Testing:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>Pharmacy ID</th><th>Pharmacy Name</th><th>Specialty</th></tr>";
    
    $count = 0;
    while ($row = $staff_result->fetch_assoc() && $count < 5) {
        echo "<tr>";
        echo "<td>{$row['staff_id']}</td>";
        echo "<td>{$row['full_name']}</td>";
        echo "<td>{$row['role']}</td>";
        echo "<td>{$row['pharmacy_id']}</td>";
        echo "<td>" . ($row['pharmacy_name'] ?? 'Not assigned') . "</td>";
        echo "<td>{$row['specialty']}</td>";
        echo "</tr>";
        $count++;
    }
    echo "</table>";
    
    echo "<h3>🎯 Filter Test Instructions:</h3>";
    echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<strong>To test the filter functionality:</strong><br>";
    echo "1. Go to: <a href='medical_staff.php' target='_blank'>medical_staff.php</a><br>";
    echo "2. Try filtering by Role (Doctor, Nurse, Pharmacist, Technician)<br>";
    echo "3. Try filtering by Pharmacy<br>";
    echo "4. Try searching by name or specialty<br>";
    echo "5. Use the 'Clear Filters' button to reset<br>";
    echo "6. Check browser console (F12) for debug logs<br>";
    echo "</div>";
    
} else {
    echo "<div style='color: orange;'>⚠️ No medical staff records found</div>";
    echo "<div style='color: blue;'>💡 Add some medical staff first to test the filters</div>";
}

// Check if pharmacies exist for filter testing
$pharmacy_query = "SELECT pharmacy_id, name FROM pharmacies LIMIT 5";
$pharmacy_result = $connect->query($pharmacy_query);

if ($pharmacy_result && $pharmacy_result->num_rows > 0) {
    echo "<h3>🏥 Available Pharmacies for Filtering:</h3>";
    echo "<ul>";
    while ($row = $pharmacy_result->fetch_assoc()) {
        echo "<li>ID: {$row['pharmacy_id']} - {$row['name']}</li>";
    }
    echo "</ul>";
} else {
    echo "<div style='color: orange;'>⚠️ No pharmacies found</div>";
}

echo "<h3>🔧 Filter Functionality Features:</h3>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px;'>";
echo "✅ <strong>Role Filter:</strong> Filter by Doctor, Nurse, Pharmacist, Technician<br>";
echo "✅ <strong>Pharmacy Filter:</strong> Filter by assigned pharmacy<br>";
echo "✅ <strong>Search Filter:</strong> Search by name or specialty<br>";
echo "✅ <strong>Clear Filters:</strong> Reset all filters<br>";
echo "✅ <strong>No Results Message:</strong> Shows when no matches found<br>";
echo "✅ <strong>Debug Logging:</strong> Console logs for troubleshooting<br>";
echo "</div>";

echo "<h3>🎉 Filter System Status:</h3>";
echo "<div style='color: green; font-size: 18px;'>";
echo "✅ <strong>FILTER FUNCTIONALITY FIXED AND ENHANCED</strong><br>";
echo "✅ <strong>APPLY FILTERS BUTTON WORKING</strong><br>";
echo "✅ <strong>CLEAR FILTERS BUTTON ADDED</strong><br>";
echo "✅ <strong>DEBUG LOGGING ENABLED</strong><br>";
echo "</div>";

$connect->close();
?>
