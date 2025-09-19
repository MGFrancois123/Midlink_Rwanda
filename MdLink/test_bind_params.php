<?php
// Test script to verify bind_param parameter count
echo "<h2>Testing bind_param Parameter Count</h2>";

// Simulate the parameters we're using
$pharmacy_id = 3;
$name = 'Atenolol 50mg';
$description = 'Beta blocker for heart conditions';
$price = 4000.0;
$stock_quantity = 5666;
$expiry_date = '2025-05-06';
$restricted_medicine = 1;
$category_id = 6;
$medicine_id = 209;

// Count the parameters
$params = [$pharmacy_id, $name, $description, $price, $stock_quantity, $expiry_date, $restricted_medicine, $category_id, $medicine_id];
$param_count = count($params);

echo "<p>Number of parameters: " . $param_count . "</p>";

// Define the type string
$type_string = "issddsiii";
$type_count = strlen($type_string);

echo "<p>Number of type definitions: " . $type_count . "</p>";

if ($param_count === $type_count) {
    echo "<p style='color: green;'>✅ Parameter count matches type string!</p>";
} else {
    echo "<p style='color: red;'>❌ Mismatch! Parameters: $param_count, Types: $type_count</p>";
}

// Show the mapping
echo "<h3>Parameter Mapping:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Position</th><th>Type</th><th>Parameter</th><th>Value</th></tr>";

$types = str_split($type_string);
for ($i = 0; $i < count($params); $i++) {
    $type_name = $types[$i] === 'i' ? 'integer' : ($types[$i] === 's' ? 'string' : 'double');
    echo "<tr>";
    echo "<td>" . ($i + 1) . "</td>";
    echo "<td>" . $types[$i] . " ($type_name)</td>";
    echo "<td>\$" . ['pharmacy_id', 'name', 'description', 'price', 'stock_quantity', 'expiry_date', 'restricted_medicine', 'category_id', 'medicine_id'][$i] . "</td>";
    echo "<td>" . htmlspecialchars($params[$i]) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='product.php'>Back to Product List</a></p>";
?>
