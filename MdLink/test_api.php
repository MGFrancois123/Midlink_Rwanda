<?php
echo "=== TESTING API ENDPOINTS ===\n\n";

// Test statistics API
echo "1. Testing Statistics API:\n";
$url = "http://localhost/FYP/FYP/Final_year_project/MdLink%20Rwanda/MdLink/php_action/get_user_activity_statistics.php";
$response = file_get_contents($url);
echo "Response: " . $response . "\n\n";

// Test chart data API
echo "2. Testing Chart Data API:\n";
$url = "http://localhost/FYP/FYP/Final_year_project/MdLink%20Rwanda/MdLink/php_action/get_activity_chart_data.php";
$response = file_get_contents($url);
echo "Response: " . $response . "\n\n";

// Test user activity API
echo "3. Testing User Activity API:\n";
$url = "http://localhost/FYP/FYP/Final_year_project/MdLink%20Rwanda/MdLink/php_action/get_user_activity.php";
$response = file_get_contents($url);
echo "Response: " . substr($response, 0, 200) . "...\n\n";
?>
