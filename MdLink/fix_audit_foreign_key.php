<?php
require_once './constant/connect.php';

try {
    // Remove foreign key constraint temporarily
    $connect->query("ALTER TABLE audit_logs DROP FOREIGN KEY IF EXISTS fk_audit_admin_id");
    
    // Also remove the constraint if it has a different name
    $result = $connect->query("SHOW CREATE TABLE audit_logs");
    $table_info = $result->fetch_assoc();
    
    if (strpos($table_info['Create Table'], 'CONSTRAINT') !== false) {
        // Find and drop any existing foreign key constraints
        $connect->query("ALTER TABLE audit_logs DROP FOREIGN KEY audit_logs_ibfk_1");
    }
    
    echo "Foreign key constraints removed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
