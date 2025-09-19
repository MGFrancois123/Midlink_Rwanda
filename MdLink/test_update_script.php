<?php
// Test the update_pharmacy.php script directly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';

echo "<h2>Testing update_pharmacy.php script</h2>";

// First, let's see what pharmacies exist
echo "<h3>Available Pharmacies:</h3>";
$query = "SELECT pharmacy_id, name, license_number FROM pharmacies LIMIT 5";
$result = $connect->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>License</th><th>Test Update</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['pharmacy_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['license_number']) . "</td>";
        echo "<td><a href='test_update_script.php?test_id=" . $row['pharmacy_id'] . "'>Test Update</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No pharmacies found.";
}

// Test update if ID provided
if (isset($_GET['test_id'])) {
    $test_id = (int)$_GET['test_id'];
    echo "<h3>Testing Update for Pharmacy ID: $test_id</h3>";
    
    // Simulate POST data
    $_POST['pharmacy_id'] = $test_id;
    $_POST['name'] = 'Test Updated Name ' . time();
    $_POST['license_number'] = 'TEST-' . rand(10000, 99999);
    $_POST['contact_person'] = 'Test Contact';
    $_POST['contact_phone'] = '+250 788 123 456';
    $_POST['location'] = 'Test Location, Kigali, Rwanda';
    $_POST['address_details'] = 'Test Address Details';
    
    echo "<p>Simulating update with data:</p>";
    echo "<ul>";
    echo "<li>Name: " . $_POST['name'] . "</li>";
    echo "<li>License: " . $_POST['license_number'] . "</li>";
    echo "<li>Contact: " . $_POST['contact_person'] . "</li>";
    echo "<li>Phone: " . $_POST['contact_phone'] . "</li>";
    echo "<li>Location: " . $_POST['location'] . "</li>";
    echo "</ul>";
    
    // Capture output from update_pharmacy.php
    ob_start();
    include 'php_action/update_pharmacy.php';
    $output = ob_get_clean();
    
    echo "<h4>Update Script Response:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Decode JSON response
    $response = json_decode($output, true);
    if ($response) {
        if ($response['success']) {
            echo "<div style='color: green; font-weight: bold;'>✅ Update successful!</div>";
        } else {
            echo "<div style='color: red; font-weight: bold;'>❌ Update failed: " . $response['message'] . "</div>";
        }
    } else {
        echo "<div style='color: orange; font-weight: bold;'>⚠️ Invalid JSON response</div>";
    }
}

echo "<p><a href='manage_pharmacies.php'>Back to Manage Pharmacies</a></p>";
?>
