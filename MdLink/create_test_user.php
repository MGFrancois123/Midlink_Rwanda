<?php
require_once 'constant/connect.php';

echo "<h1>Creating Test User</h1>";

try {
    // Check if users table exists
    $result = $connect->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Users table doesn't exist. Creating it...</p>";
        
        $sql = "CREATE TABLE users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            role VARCHAR(20) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($connect->query($sql)) {
            echo "<p style='color: green;'>✅ Users table created</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create users table: " . $connect->error . "</p>";
            exit;
        }
    }
    
    // Create test user
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $email = 'admin@test.com';
    $role = 'super_admin';
    
    // Check if user already exists
    $check = $connect->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param('s', $username);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ User 'admin' already exists</p>";
    } else {
        $stmt = $connect->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $password, $email, $role);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Test user created successfully!</p>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create user: " . $connect->error . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='login.php'>Go to Login Page</a></li>";
    echo "<li>Login with username: <strong>admin</strong> and password: <strong>admin123</strong></li>";
    echo "<li><a href='stock_movements.php'>Access Stock Movements Page</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>