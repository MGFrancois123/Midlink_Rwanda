<?php
// Debug test for stock_movements.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<h2>Debug Information</h2>";

// Check session
echo "<h3>Session Status:</h3>";
if (isset($_SESSION['userId']) && $_SESSION['userId']) {
    echo "<p style='color: green;'>✅ User is logged in - User ID: " . $_SESSION['userId'] . "</p>";
    if (isset($_SESSION['userRole'])) {
        echo "<p>User Role: " . $_SESSION['userRole'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ User is NOT logged in</p>";
    echo "<p><a href='login.php'>Click here to login</a></p>";
}

// Check database connection
echo "<h3>Database Connection:</h3>";
require_once './constant/connect.php';
if ($connect) {
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Test basic queries
    $userCount = 0;
    $medCount = 0;
    $pharmCount = 0;
    $auditCount = 0;
    $stockCount = 0;
    
    // Count admin users
    $userQ = $connect->query("SELECT COUNT(*) as count FROM admin_users");
    if($userQ) { 
        $userCount = $userQ->fetch_assoc()['count']; 
        echo "<p>✅ admin_users table - Count: $userCount</p>";
    } else {
        echo "<p style='color: red;'>❌ admin_users table error: " . $connect->error . "</p>";
    }
    
    // Count medicines
    $medQ = $connect->query("SELECT COUNT(*) as count FROM medicines");
    if($medQ) { 
        $medCount = $medQ->fetch_assoc()['count']; 
        echo "<p>✅ medicines table - Count: $medCount</p>";
    } else {
        echo "<p style='color: red;'>❌ medicines table error: " . $connect->error . "</p>";
    }
    
    // Count pharmacies
    $pharmQ = $connect->query("SELECT COUNT(*) as count FROM pharmacies");
    if($pharmQ) { 
        $pharmCount = $pharmQ->fetch_assoc()['count']; 
        echo "<p>✅ pharmacies table - Count: $pharmCount</p>";
    } else {
        echo "<p style='color: red;'>❌ pharmacies table error: " . $connect->error . "</p>";
    }
    
    // Count audit logs
    $auditQ = $connect->query("SELECT COUNT(*) as count FROM audit_logs");
    if($auditQ) { 
        $auditCount = $auditQ->fetch_assoc()['count']; 
        echo "<p>✅ audit_logs table - Count: $auditCount</p>";
    } else {
        echo "<p style='color: red;'>❌ audit_logs table error: " . $connect->error . "</p>";
    }
    
    // Count stock movements
    $stockQ = $connect->query("SELECT COUNT(*) as count FROM stock_movements");
    if($stockQ) { 
        $stockCount = $stockQ->fetch_assoc()['count']; 
        echo "<p>✅ stock_movements table - Count: $stockCount</p>";
    } else {
        echo "<p style='color: red;'>❌ stock_movements table error: " . $connect->error . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}

// Check if required files exist
echo "<h3>Required Files:</h3>";
$requiredFiles = [
    './constant/layout/head.php',
    './constant/layout/header.php', 
    './constant/layout/sidebar.php',
    './constant/check.php',
    './constant/connect.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}

echo "<h3>Next Steps:</h3>";
echo "<p><a href='stock_movements.php'>Try Stock Movements Page</a></p>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
?>
