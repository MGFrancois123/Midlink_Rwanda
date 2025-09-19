<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';

// Test the update functionality directly
echo "<h2>Testing Update Functionality</h2>";

// First, get a pharmacy to test with
$query = "SELECT * FROM pharmacies LIMIT 1";
$result = $connect->query($query);

if ($result && $result->num_rows > 0) {
    $pharmacy = $result->fetch_assoc();
    echo "<h3>Testing with Pharmacy: " . htmlspecialchars($pharmacy['name']) . " (ID: " . $pharmacy['pharmacy_id'] . ")</h3>";
    
    // Simulate the update
    $_POST['pharmacy_id'] = $pharmacy['pharmacy_id'];
    $_POST['name'] = $pharmacy['name'] . ' - Updated ' . date('H:i:s');
    $_POST['license_number'] = $pharmacy['license_number'];
    $_POST['contact_person'] = $pharmacy['contact_person'];
    $_POST['contact_phone'] = $pharmacy['contact_phone'];
    $_POST['location'] = $pharmacy['location'];
    $_POST['address_details'] = '';
    
    echo "<p>Updating with new name: " . htmlspecialchars($_POST['name']) . "</p>";
    
    // Capture the output
    ob_start();
    include 'php_action/update_pharmacy.php';
    $output = ob_get_clean();
    
    echo "<h4>Update Response:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Parse JSON response
    $response = json_decode($output, true);
    if ($response) {
        if ($response['success']) {
            echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>";
            echo "✅ SUCCESS: " . $response['message'];
            echo "</div>";
        } else {
            echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
            echo "❌ ERROR: " . $response['message'];
            echo "</div>";
        }
    } else {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;'>";
        echo "⚠️ WARNING: Invalid JSON response";
        echo "</div>";
    }
    
} else {
    echo "<p>No pharmacies found to test with.</p>";
}

echo "<p><a href='create_pharmacy.php?edit=9'>Test Edit Page</a> | <a href='manage_pharmacies.php'>Manage Pharmacies</a></p>";
?>
