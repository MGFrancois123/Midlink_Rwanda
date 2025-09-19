<?php
// Lightweight router to map old placeholder links to real pages (super-admin mode)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Always ensure connect/check are available if needed later
@include_once __DIR__ . '/constant/connect.php';
@include_once __DIR__ . '/constant/check.php';

function safe_redirect($url) {
    if (!headers_sent()) { header('Location: ' . $url); exit; }
    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    exit;
}

$title = isset($_GET['title']) ? trim((string)$_GET['title']) : '';

       // Map known placeholder titles to actual pages
       $routes = array(
           'Add / Update Medicines' => 'add-product.php',
           'All Medicines' => 'product.php',
           'Categories' => 'categories.php',
           'Stock Movements' => 'stock_movements.php',
           'Low Stock Alerts' => 'low_stock_alerts.php',
           'Expiry Alerts' => 'expiry_alerts.php',
           'Reports' => 'report.php',
           'Daily Sales Report' => 'report.php',
           'Security Logs' => 'audit_logs.php',
           'Audit Logs' => 'audit_logs.php',
           'Send SMS' => 'send_sms.php',
           'Pickup Reminders' => 'pickup_reminders.php',
           'Recall Alerts' => 'recall_alerts.php',
           
           // User Management Routes
           'Add User' => 'add_user.php',
           'Create Pharmacy' => 'create_pharmacy.php',
           'Manage Pharmacies' => 'manage_pharmacies.php',
           'Medical Staff' => 'medical_staff.php',
           'User Activity' => 'user_activity.php',
           'User Reports' => 'user_reports.php',
       );


if ($title !== '' && isset($routes[$title])) {
    safe_redirect($routes[$title]);
}

// Fallback: show a simple message with links to main pages
if (!headers_sent()) { header('Content-Type: text/html; charset=utf-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found</title>
    <link href="assets/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body style="padding:24px;font-family:Arial,Helvetica,sans-serif;">
    <h3>Page not found</h3>
    <p>The requested placeholder "<?php echo htmlspecialchars($title ?: ''); ?>" is not available.</p>
    <p>Try one of these pages:</p>
    <ul>
        <li><a href="add-product.php">Add / Update Medicines</a></li>
        <li><a href="product.php">All Medicines</a></li>
        <li><a href="categories.php">Categories</a></li>
        <li><a href="stock_movements.php">Stock Movements</a></li>
        <li><a href="low_stock_alerts.php">Low Stock Alerts</a></li>
        <li><a href="expiry_alerts.php">Expiry Alerts</a></li>
        <li><a href="report.php">Reports</a></li>
    </ul>
</body>
</html>


