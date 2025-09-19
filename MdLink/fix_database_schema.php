<?php
// Fix database schema issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Schema Fix</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

$localhost = "localhost";
$username = "root";
$password = "";
$dbname = "mdlink2";

$connect = new mysqli($localhost, $username, $password, $dbname);

if ($connect->connect_error) {
    echo "<div class='test-section'>";
    echo "<p class='error'>❌ Cannot connect to database: " . $connect->connect_error . "</p>";
    echo "<p>Please run <a href='setup_database.php'>setup_database.php</a> first.</p>";
    echo "</div>";
    exit;
}

echo "<div class='test-section'>";
echo "<h2>1. Check Current Schema</h2>";

// Check admin_users table structure
$result = $connect->query("DESCRIBE admin_users");
if ($result) {
    echo "<p class='info'>Current admin_users table structure:</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Cannot describe admin_users table: " . $connect->error . "</p>";
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>2. Fix Missing Columns</h2>";

// Check if pharmacy_id column exists
$result = $connect->query("SHOW COLUMNS FROM admin_users LIKE 'pharmacy_id'");
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ pharmacy_id column already exists in admin_users table</p>";
} else {
    echo "<p class='warning'>⚠️ pharmacy_id column missing in admin_users table - adding it...</p>";
    
    // Add pharmacy_id column
    $sql = "ALTER TABLE admin_users ADD COLUMN pharmacy_id INT AFTER admin_id";
    if ($connect->query($sql)) {
        echo "<p class='success'>✅ Added pharmacy_id column to admin_users table</p>";
        
        // Add foreign key constraint if pharmacies table exists
        $check_pharmacies = $connect->query("SHOW TABLES LIKE 'pharmacies'");
        if ($check_pharmacies && $check_pharmacies->num_rows > 0) {
            $fk_sql = "ALTER TABLE admin_users ADD CONSTRAINT fk_admin_pharmacy FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE SET NULL";
            if ($connect->query($fk_sql)) {
                echo "<p class='success'>✅ Added foreign key constraint for pharmacy_id</p>";
            } else {
                echo "<p class='warning'>⚠️ Could not add foreign key constraint: " . $connect->error . "</p>";
            }
        }
    } else {
        echo "<p class='error'>❌ Failed to add pharmacy_id column: " . $connect->error . "</p>";
    }
}

// Check if medicines table has pharmacy_id column
$result = $connect->query("SHOW COLUMNS FROM medicines LIKE 'pharmacy_id'");
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ pharmacy_id column exists in medicines table</p>";
} else {
    echo "<p class='warning'>⚠️ pharmacy_id column missing in medicines table - adding it...</p>";
    
    // Add pharmacy_id column
    $sql = "ALTER TABLE medicines ADD COLUMN pharmacy_id INT AFTER medicine_id";
    if ($connect->query($sql)) {
        echo "<p class='success'>✅ Added pharmacy_id column to medicines table</p>";
        
        // Add foreign key constraint
        $fk_sql = "ALTER TABLE medicines ADD CONSTRAINT fk_medicine_pharmacy FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE SET NULL";
        if ($connect->query($fk_sql)) {
            echo "<p class='success'>✅ Added foreign key constraint for medicines pharmacy_id</p>";
        } else {
            echo "<p class='warning'>⚠️ Could not add foreign key constraint: " . $connect->error . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Failed to add pharmacy_id column to medicines: " . $connect->error . "</p>";
    }
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>3. Test Fixed Schema</h2>";

// Test the problematic query
$query = "
    SELECT p.*, 
           COALESCE(COUNT(DISTINCT au.admin_id), 0) as admin_count,
           COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
    FROM pharmacies p
    LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
    LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
    GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
    ORDER BY p.created_at DESC
    LIMIT 5
";

echo "<p class='info'>Testing the fixed query:</p>";
echo "<pre>" . htmlspecialchars($query) . "</pre>";

$result = $connect->query($query);
if ($result) {
    echo "<p class='success'>✅ Query executed successfully!</p>";
    echo "<p class='info'>Rows returned: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Pharmacy ID</th><th>Name</th><th>Admin Count</th><th>Medicine Count</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['pharmacy_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . $row['admin_count'] . "</td>";
            echo "<td>" . $row['medicine_count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p class='error'>❌ Query still failed: " . $connect->error . "</p>";
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>4. Update Sample Data</h2>";

// Update existing medicines to have pharmacy_id
$result = $connect->query("SELECT pharmacy_id FROM pharmacies LIMIT 1");
if ($result && $result->num_rows > 0) {
    $pharmacy_row = $result->fetch_assoc();
    $sample_pharmacy_id = $pharmacy_row['pharmacy_id'];
    
    // Update medicines without pharmacy_id
    $update_sql = "UPDATE medicines SET pharmacy_id = ? WHERE pharmacy_id IS NULL";
    $stmt = $connect->prepare($update_sql);
    $stmt->bind_param("i", $sample_pharmacy_id);
    
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        echo "<p class='success'>✅ Updated $affected medicines with pharmacy_id</p>";
    } else {
        echo "<p class='warning'>⚠️ Could not update medicines: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "<p class='warning'>⚠️ No pharmacies found to assign to medicines</p>";
}

// Update existing admin_users to have pharmacy_id
$result = $connect->query("SELECT pharmacy_id FROM pharmacies LIMIT 1");
if ($result && $result->num_rows > 0) {
    $pharmacy_row = $result->fetch_assoc();
    $sample_pharmacy_id = $pharmacy_row['pharmacy_id'];
    
    // Update admin_users without pharmacy_id
    $update_sql = "UPDATE admin_users SET pharmacy_id = ? WHERE pharmacy_id IS NULL";
    $stmt = $connect->prepare($update_sql);
    $stmt->bind_param("i", $sample_pharmacy_id);
    
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        echo "<p class='success'>✅ Updated $affected admin users with pharmacy_id</p>";
    } else {
        echo "<p class='warning'>⚠️ Could not update admin users: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "<p class='warning'>⚠️ No pharmacies found to assign to admin users</p>";
}
echo "</div>";

$connect->close();

echo "<div class='test-section'>";
echo "<h2>5. Next Steps</h2>";
echo "<p class='info'>Database schema has been fixed! Now try:</p>";
echo "<ol>";
echo "<li><a href='create_pharmacy.php'>Go to Create Pharmacy Page</a> - Should work now!</li>";
echo "<li><a href='test_pharmacy_data.php'>Test Pharmacy Data</a> - Verify everything works</li>";
echo "<li><a href='diagnose_database.php'>Run Full Diagnostic</a> - Check for any remaining issues</li>";
echo "</ol>";
echo "</div>";
?>
