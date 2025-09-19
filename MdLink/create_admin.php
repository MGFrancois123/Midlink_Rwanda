<?php
require_once 'constant/connect.php';

echo "<h1>Creating Admin User</h1>";

try {
    // Check if admin_users table exists and show structure
    $result = $connect->query("SHOW TABLES LIKE 'admin_users'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ admin_users table doesn't exist. Creating it...</p>";
        
        $sql = "CREATE TABLE admin_users (
            admin_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($connect->query($sql)) {
            echo "<p style='color: green;'>✅ admin_users table created</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin_users table: " . $connect->error . "</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✅ admin_users table exists</p>";
        
        // Show table structure
        $structure = $connect->query("DESCRIBE admin_users");
        echo "<h3>Table Structure:</h3><table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Create test admin user with MD5 password (as expected by login.php)
    $username = 'admin';
    $password = 'admin123';
    $password_hash = md5($password); // Use MD5 as expected by login system
    $email = 'admin@test.com';
    $phone = '123456789';
    
    // Check if admin user already exists
    $check = $connect->prepare("SELECT admin_id FROM admin_users WHERE username = ? OR email = ?");
    $check->bind_param('ss', $username, $email);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Admin user already exists</p>";
        $existing = $result->fetch_assoc();
        echo "<p>Existing admin_id: {$existing['admin_id']}</p>";
    } else {
        $stmt = $connect->prepare("INSERT INTO admin_users (username, password_hash, email, phone, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->bind_param('ssss', $username, $password_hash, $email, $phone);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Email:</strong> admin@test.com</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
            echo "<p><strong>Password Hash (MD5):</strong> " . $password_hash . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user: " . $connect->error . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='login.php'>Go to Login Page</a></li>";
    echo "<li>Login with email: <strong>admin@test.com</strong> and password: <strong>admin123</strong></li>";
    echo "<li><a href='add_user.php'>Access Add User Page</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>