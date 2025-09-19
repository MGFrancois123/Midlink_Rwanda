<?php
// Simple debug page to see what's being sent
echo "<h1>Debug Form Submission</h1>";
echo "<h2>Request Method: " . $_SERVER['REQUEST_METHOD'] . "</h2>";

echo "<h3>POST Data:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h3>GET Data:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h3>All Headers:</h3>";
echo "<pre>";
print_r(getallheaders());
echo "</pre>";

echo "<h3>Raw Input:</h3>";
echo "<pre>";
echo file_get_contents('php://input');
echo "</pre>";
?>
