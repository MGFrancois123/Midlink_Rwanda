<?php
// Simple test to verify redirect is working
echo "Testing redirect...<br>";

// Test 1: Basic redirect
echo "Test 1: Basic redirect to product.php<br>";
header('Location: product.php?success=Test redirect successful');
exit;
?>
