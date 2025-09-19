<?php
// Test script to verify passwords
include('./constant/connect.php');

echo "<h2>Password Verification Test</h2>";

// Test data from your admin_users table
$test_users = [
    [
        'username' => 'Frederic',
        'email' => 'nzamfred3@gmail.com',
        'password_hash' => 'b8285fcc71c6a669b825682417c81b2e',
        'type' => 'MD5'
    ],
    [
        'username' => 'NZAMURAMBAHO',
        'email' => 'nzamurambahofrederic28@gmail.com',
        'password_hash' => '$2y$10$TAGwgXi/CXpJEO17sqa1mu..1BsVyTnFNsnuSJyEdoU...',
        'type' => 'Bcrypt'
    ]
];

echo "<h3>Testing Password Verification Logic:</h3>";

foreach ($test_users as $user) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h4>User: " . htmlspecialchars($user['username']) . "</h4>";
    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
    echo "<p>Hash Type: " . $user['type'] . "</p>";
    echo "<p>Stored Hash: " . htmlspecialchars($user['password_hash']) . "</p>";
    
    // Test with common passwords
    $test_passwords = ['admin', 'password', '123456', 'frederic', 'nzamurambaho', 'test'];
    
    echo "<h5>Testing Common Passwords:</h5>";
    foreach ($test_passwords as $test_pass) {
        $passOk = false;
        
        if ($user['type'] === 'Bcrypt') {
            // For bcrypt, we need the full hash - let's try to get it from database
            $sql = "SELECT password_hash FROM admin_users WHERE email = ?";
            $stmt = $connect->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('s', $user['email']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $full_hash = $row['password_hash'];
                    $passOk = password_verify($test_pass, $full_hash);
                }
                $stmt->close();
            }
        } else {
            // MD5 test
            $md5_test = md5($test_pass);
            $passOk = ($user['password_hash'] === $md5_test);
        }
        
        $status = $passOk ? '<span style="color: green;">✓ MATCH</span>' : '<span style="color: red;">✗ No match</span>';
        echo "<p>Password '{$test_pass}': {$status}</p>";
    }
    
    echo "</div>";
}

echo "<h3>Instructions:</h3>";
echo "<ol>";
echo "<li>Try logging in with each email address</li>";
echo "<li>Use the passwords that show 'MATCH' above</li>";
echo "<li>Check the error logs for debugging information</li>";
echo "</ol>";

echo "<h3>To check error logs:</h3>";
echo "<p>Look in your XAMPP error logs or PHP error log for messages starting with 'Login attempt' or 'Bcrypt verification' or 'MD5 verification'</p>";

?>
