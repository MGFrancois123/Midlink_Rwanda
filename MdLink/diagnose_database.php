<?php
// Comprehensive database diagnostic script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Diagnostic Report</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// Test 1: Basic PHP MySQLi Extension
echo "<div class='test-section'>";
echo "<h2>1. PHP MySQLi Extension Check</h2>";
if (extension_loaded('mysqli')) {
    echo "<p class='success'>✅ MySQLi extension is loaded</p>";
} else {
    echo "<p class='error'>❌ MySQLi extension is NOT loaded</p>";
    echo "<p>Please install the MySQLi extension for PHP</p>";
}
echo "</div>";

// Test 2: Database Connection
echo "<div class='test-section'>";
echo "<h2>2. Database Connection Test</h2>";

$localhost = "localhost";
$username = "root";
$password = "";
$dbname = "mdlink2";

echo "<p class='info'>Attempting to connect to database:</p>";
echo "<ul>";
echo "<li>Host: $localhost</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . (empty($password) ? "(empty)" : "(set)") . "</li>";
echo "<li>Database: $dbname</li>";
echo "</ul>";

$connect = new mysqli($localhost, $username, $password, $dbname);

if ($connect->connect_error) {
    echo "<p class='error'>❌ Connection failed: " . $connect->connect_error . "</p>";
    
    // Try connecting without database first
    echo "<p class='info'>Trying to connect to MySQL server without database...</p>";
    $connect_server = new mysqli($localhost, $username, $password);
    
    if ($connect_server->connect_error) {
        echo "<p class='error'>❌ Cannot connect to MySQL server: " . $connect_server->connect_error . "</p>";
        echo "<p class='warning'>Possible issues:</p>";
        echo "<ul>";
        echo "<li>MySQL server is not running</li>";
        echo "<li>Wrong host/username/password</li>";
        echo "<li>MySQL port is blocked</li>";
        echo "</ul>";
    } else {
        echo "<p class='success'>✅ Connected to MySQL server successfully</p>";
        
        // Check if database exists
        $result = $connect_server->query("SHOW DATABASES LIKE '$dbname'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✅ Database '$dbname' exists</p>";
        } else {
            echo "<p class='error'>❌ Database '$dbname' does not exist</p>";
            echo "<p class='info'>Available databases:</p>";
            $result = $connect_server->query("SHOW DATABASES");
            if ($result) {
                echo "<ul>";
                while ($row = $result->fetch_array()) {
                    echo "<li>" . $row[0] . "</li>";
                }
                echo "</ul>";
            }
        }
        $connect_server->close();
    }
} else {
    echo "<p class='success'>✅ Database connection successful</p>";
    
    // Test 3: Check tables
    echo "<div class='test-section'>";
    echo "<h2>3. Database Tables Check</h2>";
    
    $tables = ['pharmacies', 'admin_users', 'medicines', 'category'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        $result = $connect->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✅ Table '$table' exists</p>";
            $existing_tables[] = $table;
        } else {
            echo "<p class='error'>❌ Table '$table' does not exist</p>";
        }
    }
    
    // Show all tables in database
    echo "<p class='info'>All tables in database:</p>";
    $result = $connect->query("SHOW TABLES");
    if ($result) {
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
    
    // Test 4: Check pharmacies table structure
    if (in_array('pharmacies', $existing_tables)) {
        echo "<div class='test-section'>";
        echo "<h2>4. Pharmacies Table Structure</h2>";
        
        $result = $connect->query("DESCRIBE pharmacies");
        if ($result) {
            echo "<table>";
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
        }
        echo "</div>";
        
        // Test 5: Check pharmacies data
        echo "<div class='test-section'>";
        echo "<h2>5. Pharmacies Data Check</h2>";
        
        $result = $connect->query("SELECT COUNT(*) as count FROM pharmacies");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p class='info'>Number of pharmacies: " . $row['count'] . "</p>";
            
            if ($row['count'] > 0) {
                echo "<p class='success'>✅ Pharmacies data exists</p>";
                
                // Show sample data
                $result = $connect->query("SELECT * FROM pharmacies LIMIT 3");
                if ($result) {
                    echo "<p class='info'>Sample pharmacy data:</p>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>License</th><th>Location</th><th>Contact Person</th><th>Phone</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['pharmacy_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['license_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['contact_person']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['contact_phone']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<p class='warning'>⚠️ No pharmacies data found</p>";
            }
        }
        echo "</div>";
        
        // Test 6: Test the problematic query
        echo "<div class='test-section'>";
        echo "<h2>6. Test Problematic Query</h2>";
        
        $query = "
            SELECT p.*, 
                   COALESCE(COUNT(DISTINCT au.admin_id), 0) as admin_count,
                   COALESCE(COUNT(DISTINCT m.medicine_id), 0) as medicine_count
            FROM pharmacies p
            LEFT JOIN admin_users au ON p.pharmacy_id = au.pharmacy_id
            LEFT JOIN medicines m ON p.pharmacy_id = m.pharmacy_id
            GROUP BY p.pharmacy_id, p.name, p.license_number, p.contact_person, p.contact_phone, p.location, p.created_at
            ORDER BY p.created_at DESC
            LIMIT 10
        ";
        
        echo "<p class='info'>Testing query:</p>";
        echo "<pre>" . htmlspecialchars($query) . "</pre>";
        
        $result = $connect->query($query);
        if ($result) {
            echo "<p class='success'>✅ Query executed successfully</p>";
            echo "<p class='info'>Rows returned: " . $result->num_rows . "</p>";
        } else {
            echo "<p class='error'>❌ Query failed: " . $connect->error . "</p>";
        }
        echo "</div>";
    }
    
    $connect->close();
}
echo "</div>";

// Test 7: XAMPP Status Check
echo "<div class='test-section'>";
echo "<h2>7. XAMPP Status Check</h2>";
echo "<p class='info'>Please verify the following in XAMPP Control Panel:</p>";
echo "<ul>";
echo "<li>✅ Apache is running</li>";
echo "<li>✅ MySQL is running</li>";
echo "<li>✅ Check if MySQL port is 3306 (default)</li>";
echo "</ul>";
echo "</div>";

// Test 8: File Permissions
echo "<div class='test-section'>";
echo "<h2>8. File Permissions Check</h2>";
$files_to_check = [
    'constant/connect.php',
    'create_pharmacy.php',
    'php_action/create_pharmacy.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $file exists</p>";
    } else {
        echo "<p class='error'>❌ $file does not exist</p>";
    }
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Quick Fixes</h2>";
echo "<p class='info'>Try these solutions:</p>";
echo "<ol>";
echo "<li><strong>Start XAMPP Services:</strong> Open XAMPP Control Panel and start Apache and MySQL</li>";
echo "<li><strong>Check Database:</strong> Open phpMyAdmin and verify the 'mdlink2' database exists</li>";
echo "<li><strong>Import Database:</strong> If database doesn't exist, import the mdlink.sql file</li>";
echo "<li><strong>Check Credentials:</strong> Verify database credentials in constant/connect.php</li>";
echo "<li><strong>Check Port:</strong> Ensure MySQL is running on port 3306</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='create_pharmacy.php'>← Back to Create Pharmacy Page</a></p>";
?>
