<?php
require_once './constant/connect.php';

echo "<h2>🔧 Add Sample Admin Users</h2>";

if ($connect->connect_error) {
    die("<div style='color: red;'>❌ Database Connection Failed: " . $connect->connect_error . "</div>");
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if admin_users table exists
$check_table_sql = "SHOW TABLES LIKE 'admin_users'";
$table_result = $connect->query($check_table_sql);

if ($table_result && $table_result->num_rows == 0) {
    echo "<div style='color: orange;'>⚠️ admin_users table does not exist. Creating it...</div>";
    
    $create_table_sql = "
    CREATE TABLE admin_users (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'admin', 'user') DEFAULT 'admin',
        pharmacy_id INT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE SET NULL
    )";
    
    if ($connect->query($create_table_sql) === TRUE) {
        echo "<div style='color: green;'>✅ admin_users table created successfully</div>";
    } else {
        echo "<div style='color: red;'>❌ Error creating table: " . $connect->error . "</div>";
        exit;
    }
} else {
    echo "<div style='color: green;'>✅ admin_users table exists</div>";
}

// Check current count
$count_query = "SELECT COUNT(*) as count FROM admin_users";
$count_result = $connect->query($count_query);
$current_count = $count_result ? $count_result->fetch_assoc()['count'] : 0;

echo "<div style='color: blue;'>📊 Current admin count: $current_count</div>";

if ($current_count == 0) {
    echo "<div style='color: orange;'>⚠️ No admin users found. Adding sample admins...</div>";
    
    // Get available pharmacies
    $pharmacy_query = "SELECT pharmacy_id, name FROM pharmacies LIMIT 3";
    $pharmacy_result = $connect->query($pharmacy_query);
    $pharmacies = [];
    if ($pharmacy_result && $pharmacy_result->num_rows > 0) {
        while ($row = $pharmacy_result->fetch_assoc()) {
            $pharmacies[] = $row;
        }
    }
    
    // Add sample admin users
    $sample_admins = [
        [
            'username' => 'superadmin',
            'email' => 'superadmin@mdlink.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'super_admin',
            'pharmacy_id' => null
        ],
        [
            'username' => 'pharmacy_admin1',
            'email' => 'admin1@mdlink.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'pharmacy_id' => isset($pharmacies[0]) ? $pharmacies[0]['pharmacy_id'] : null
        ],
        [
            'username' => 'pharmacy_admin2',
            'email' => 'admin2@mdlink.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'pharmacy_id' => isset($pharmacies[1]) ? $pharmacies[1]['pharmacy_id'] : null
        ]
    ];
    
    $insert_query = "INSERT INTO admin_users (username, email, password, role, pharmacy_id, status) VALUES (?, ?, ?, ?, ?, 'active')";
    $insert_stmt = $connect->prepare($insert_query);
    
    $added_count = 0;
    foreach ($sample_admins as $admin) {
        $insert_stmt->bind_param("ssssi", 
            $admin['username'],
            $admin['email'],
            $admin['password'],
            $admin['role'],
            $admin['pharmacy_id']
        );
        
        if ($insert_stmt->execute()) {
            $added_count++;
            echo "<div style='color: green;'>✅ Added admin: {$admin['username']} ({$admin['role']})</div>";
        } else {
            echo "<div style='color: red;'>❌ Failed to add admin: {$admin['username']} - " . $insert_stmt->error . "</div>";
        }
    }
    
    echo "<div style='color: green; font-size: 18px;'>🎉 Successfully added $added_count admin users!</div>";
    
} else {
    echo "<div style='color: green;'>✅ Admin users already exist</div>";
}

// Show final count
$final_count_query = "SELECT COUNT(*) as count FROM admin_users";
$final_count_result = $connect->query($final_count_query);
$final_count = $final_count_result ? $final_count_result->fetch_assoc()['count'] : 0;

echo "<h3>📊 Final Admin Count: $final_count</h3>";

if ($final_count > 0) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px;'>";
    echo "<h4 style='color: #155724;'>✅ PROBLEM SOLVED!</h4>";
    echo "<div style='color: #155724;'>";
    echo "The admin count should now show <strong>$final_count</strong> on the manage_pharmacies.php page.<br>";
    echo "Refresh the page to see the updated count.<br>";
    echo "</div>";
    echo "</div>";
}

$connect->close();
?>
