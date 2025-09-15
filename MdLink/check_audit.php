<?php
require_once 'constant/connect.php';

echo "Checking audit_logs table structure:\n";
$result = $connect->query("DESCRIBE audit_logs");
while($row = $result->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

echo "\nChecking security_logs table structure:\n";
$result = $connect->query("DESCRIBE security_logs");
while($row = $result->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}
?>
