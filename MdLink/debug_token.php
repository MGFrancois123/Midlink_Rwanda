<?php
require_once './constant/connect.php';

echo "<h2>Reset Token Debug</h2>";

// Check if reset_tokens table exists
$tableExists = $connect->query("SHOW TABLES LIKE 'reset_tokens'");
if ($tableExists && $tableExists->num_rows > 0) {
    echo "<p>✅ reset_tokens table exists</p>";
    
    // Show all tokens
    $tokens = $connect->query("SELECT * FROM reset_tokens ORDER BY created_at DESC");
    if ($tokens && $tokens->num_rows > 0) {
        echo "<h3>Existing Tokens:</h3>";
        echo "<table border='1'><tr><th>ID</th><th>User ID</th><th>User Type</th><th>Token Hash</th><th>Expires At</th><th>Created At</th></tr>";
        while ($row = $tokens->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['user_type'] . "</td>";
            echo "<td>" . substr($row['token_hash'], 0, 16) . "...</td>";
            echo "<td>" . $row['expires_at'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No tokens found in reset_tokens table</p>";
    }
} else {
    echo "<p>❌ reset_tokens table does not exist</p>";
}

// Check admin_users table
echo "<h3>Admin Users:</h3>";
$admins = $connect->query("SELECT admin_id, username, email FROM admin_users");
if ($admins && $admins->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Email</th></tr>";
    while ($row = $admins->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['admin_id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No admin users found</p>";
}

// Check users table
echo "<h3>Regular Users:</h3>";
$users = $connect->query("SELECT user_id, full_name, email FROM users LIMIT 5");
if ($users && $users->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th></tr>";
    while ($row = $users->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No regular users found</p>";
}

// Generate a test token for admin user
echo "<h3>Generate Test Token:</h3>";
$admin = $connect->query("SELECT admin_id FROM admin_users LIMIT 1");
if ($admin && $admin->num_rows > 0) {
    $adminRow = $admin->fetch_assoc();
    $userId = $adminRow['admin_id'];
    
    // Generate token
    $token = bin2hex(random_bytes(16));
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);
    
    // Ensure table exists
    $connect->query('CREATE TABLE IF NOT EXISTS reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type VARCHAR(10) NOT NULL DEFAULT "user",
        token_hash VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id), INDEX(token_hash)
    )');
    
    // Delete any existing tokens for this user
    $connect->query("DELETE FROM reset_tokens WHERE user_id = $userId");
    
    // Insert new token
    $stmt = $connect->prepare('INSERT INTO reset_tokens (user_id, user_type, token_hash, expires_at) VALUES (?, ?, ?, ?)');
    $userType = 'admin';
    $stmt->bind_param('isss', $userId, $userType, $tokenHash, $expiresAt);
    $stmt->execute();
    
    echo "<p>✅ Generated test token for admin user ID: $userId</p>";
    echo "<p><strong>Test URL:</strong> <a href='reset_password.php?token=$token' target='_blank'>reset_password.php?token=$token</a></p>";
    echo "<p><strong>Token:</strong> $token</p>";
    echo "<p><strong>Expires:</strong> $expiresAt</p>";
} else {
    echo "<p>❌ No admin users found to generate token for</p>";
}

echo "<hr>";
echo "<p><a href='forgot_password.php'>Go to Forgot Password</a> | <a href='login.php'>Go to Login</a></p>";
?>
