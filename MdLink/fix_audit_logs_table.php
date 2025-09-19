<?php
require_once './constant/connect.php';

try {
    // Check if audit_logs table has the required columns
    $result = $connect->query("DESCRIBE audit_logs");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    echo "Current audit_logs columns: " . implode(', ', $columns) . "\n";
    
    // Add missing columns if they don't exist
    $required_columns = [
        'description' => "ADD COLUMN description TEXT DEFAULT NULL",
        'ip_address' => "ADD COLUMN ip_address VARCHAR(45) DEFAULT NULL",
        'user_agent' => "ADD COLUMN user_agent TEXT DEFAULT NULL",
        'old_data' => "ADD COLUMN old_data TEXT DEFAULT NULL",
        'new_data' => "ADD COLUMN new_data TEXT DEFAULT NULL"
    ];
    
    foreach ($required_columns as $column => $sql) {
        if (!in_array($column, $columns)) {
            echo "Adding column: $column\n";
            $connect->query("ALTER TABLE audit_logs $sql");
        } else {
            echo "Column $column already exists\n";
        }
    }
    
    // Update action enum to include more values
    $connect->query("ALTER TABLE audit_logs MODIFY COLUMN action ENUM('CREATE','UPDATE','DELETE','LOGIN','LOGOUT','VIEW','SEARCH','EXPORT') NOT NULL");
    
    echo "Audit logs table structure updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
