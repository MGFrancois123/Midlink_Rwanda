<?php
// Database setup script to fix common issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Setup Helper</h1>";
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

echo "<div class='test-section'>";
echo "<h2>Step 1: Connect to MySQL Server</h2>";

$connect = new mysqli($localhost, $username, $password);

if ($connect->connect_error) {
    echo "<p class='error'>❌ Cannot connect to MySQL server: " . $connect->connect_error . "</p>";
    echo "<p class='warning'>Please start MySQL in XAMPP Control Panel first!</p>";
    exit;
} else {
    echo "<p class='success'>✅ Connected to MySQL server successfully</p>";
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Step 2: Create Database</h2>";

// Check if database exists
$result = $connect->query("SHOW DATABASES LIKE '$dbname'");
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ Database '$dbname' already exists</p>";
} else {
    // Create database
    if ($connect->query("CREATE DATABASE $dbname")) {
        echo "<p class='success'>✅ Database '$dbname' created successfully</p>";
    } else {
        echo "<p class='error'>❌ Failed to create database: " . $connect->error . "</p>";
        exit;
    }
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Step 3: Select Database</h2>";

if ($connect->select_db($dbname)) {
    echo "<p class='success'>✅ Selected database '$dbname'</p>";
} else {
    echo "<p class='error'>❌ Failed to select database: " . $connect->error . "</p>";
    exit;
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Step 4: Create Tables</h2>";

// Check if tables exist
$tables_check = $connect->query("SHOW TABLES");
$existing_tables = [];
if ($tables_check) {
    while ($row = $tables_check->fetch_array()) {
        $existing_tables[] = $row[0];
    }
}

$required_tables = ['pharmacies', 'admin_users', 'medicines', 'category'];

foreach ($required_tables as $table) {
    if (in_array($table, $existing_tables)) {
        echo "<p class='success'>✅ Table '$table' already exists</p>";
    } else {
        echo "<p class='warning'>⚠️ Table '$table' does not exist - creating...</p>";
        
        // Create basic table structures
        switch ($table) {
            case 'pharmacies':
                $sql = "CREATE TABLE pharmacies (
                    pharmacy_id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    license_number VARCHAR(100) NOT NULL UNIQUE,
                    contact_person VARCHAR(255) NOT NULL,
                    contact_phone VARCHAR(20) NOT NULL,
                    location TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                break;
                
            case 'admin_users':
                $sql = "CREATE TABLE admin_users (
                    admin_id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    pharmacy_id INT,
                    role ENUM('admin', 'super_admin') DEFAULT 'admin',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id)
                )";
                break;
                
            case 'medicines':
                $sql = "CREATE TABLE medicines (
                    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    price DECIMAL(10,2) NOT NULL,
                    stock_quantity INT NOT NULL DEFAULT 0,
                    expiry_date DATE,
                    `Restricted Medicine` TINYINT(1) DEFAULT 0,
                    pharmacy_id INT,
                    category_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id)
                )";
                break;
                
            case 'category':
                $sql = "CREATE TABLE category (
                    category_id INT AUTO_INCREMENT PRIMARY KEY,
                    category_name VARCHAR(255) NOT NULL,
                    status TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                break;
        }
        
        if ($connect->query($sql)) {
            echo "<p class='success'>✅ Table '$table' created successfully</p>";
        } else {
            echo "<p class='error'>❌ Failed to create table '$table': " . $connect->error . "</p>";
        }
    }
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Step 5: Insert Sample Data</h2>";

// Check if pharmacies table has data
$result = $connect->query("SELECT COUNT(*) as count FROM pharmacies");
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    echo "<p class='info'>Inserting sample pharmacy data...</p>";
    
    $sample_pharmacies = [
        "INSERT INTO pharmacies (name, license_number, contact_person, contact_phone, location) VALUES 
         ('Ineza Pharmacy', 'RL-2024-001', 'Dr. Jean Bosco', '+250 788 123 456', 'Kigali, Gasabo District')",
        "INSERT INTO pharmacies (name, license_number, contact_person, contact_phone, location) VALUES 
         ('Keza Pharmacy', 'RL-2024-002', 'Dr. Marie Claire', '+250 789 987 654', 'Kigali, Kicukiro District')",
        "INSERT INTO pharmacies (name, license_number, contact_person, contact_phone, location) VALUES 
         ('KIMIRONKO PHARMACY', 'RL-2024-003', 'Dr. Paul Nkurunziza', '+250 788 555 123', 'Kigali, Nyarugenge District')"
    ];
    
    foreach ($sample_pharmacies as $sql) {
        if ($connect->query($sql)) {
            echo "<p class='success'>✅ Sample pharmacy data inserted</p>";
        } else {
            echo "<p class='error'>❌ Failed to insert sample data: " . $connect->error . "</p>";
        }
    }
} else {
    echo "<p class='success'>✅ Pharmacies table already has $count records</p>";
}

// Check if categories table has data
$result = $connect->query("SELECT COUNT(*) as count FROM category");
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    echo "<p class='info'>Inserting sample category data...</p>";
    
    $sample_categories = [
        "INSERT INTO category (category_name, status) VALUES ('Antibiotics', 1)",
        "INSERT INTO category (category_name, status) VALUES ('Pain Relief', 1)",
        "INSERT INTO category (category_name, status) VALUES ('Vitamins', 1)",
        "INSERT INTO category (category_name, status) VALUES ('Cardiovascular', 1)",
        "INSERT INTO category (category_name, status) VALUES ('Diabetes', 1)"
    ];
    
    foreach ($sample_categories as $sql) {
        if ($connect->query($sql)) {
            echo "<p class='success'>✅ Sample category data inserted</p>";
        } else {
            echo "<p class='error'>❌ Failed to insert sample data: " . $connect->error . "</p>";
        }
    }
} else {
    echo "<p class='success'>✅ Categories table already has $count records</p>";
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Step 6: Test Connection</h2>";

// Test the connection with the new database
$test_connect = new mysqli($localhost, $username, $password, $dbname);
if ($test_connect->connect_error) {
    echo "<p class='error'>❌ Test connection failed: " . $test_connect->connect_error . "</p>";
} else {
    echo "<p class='success'>✅ Test connection successful!</p>";
    
    // Test the problematic query
    $query = "SELECT COUNT(*) as count FROM pharmacies";
    $result = $test_connect->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p class='success'>✅ Can query pharmacies table: " . $row['count'] . " records found</p>";
    } else {
        echo "<p class='error'>❌ Cannot query pharmacies table: " . $test_connect->error . "</p>";
    }
    
    $test_connect->close();
}
echo "</div>";

$connect->close();

echo "<div class='test-section'>";
echo "<h2>Next Steps</h2>";
echo "<p class='info'>Database setup complete! Now try:</p>";
echo "<ol>";
echo "<li><a href='create_pharmacy.php'>Go to Create Pharmacy Page</a></li>";
echo "<li><a href='diagnose_database.php'>Run Database Diagnostic</a></li>";
echo "<li><a href='test_pharmacy_data.php'>Test Pharmacy Data</a></li>";
echo "</ol>";
echo "</div>";
?>
