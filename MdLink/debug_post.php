<?php
// Debug script to see what's being sent
echo "<h2>Debug Information</h2>";
echo "<p><strong>REQUEST_METHOD:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p><strong>POST Data:</strong></p>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<p><strong>GET Data:</strong></p>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<p><strong>All Headers:</strong></p>";
echo "<pre>";
print_r(getallheaders());
echo "</pre>";
?>
