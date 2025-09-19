<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './constant/connect.php';
require_once './constant/check.php';

// Check if user is super admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') { 
    header('Location: dashboard.php'); 
    exit; 
}

// Get comprehensive statistics
$stats = [
    'total_users' => 0,
    'total_pharmacies' => 0,
    'total_medicines' => 0,
    'total_medical_staff' => 0,
    'audit_today' => 0,
    'security_today' => 0
];

$error_message = '';

try {
    // Get basic counts
    $user_count = $connect->query("SELECT COUNT(*) as count FROM admin_users");
    if ($user_count) $stats['total_users'] = (int)$user_count->fetch_assoc()['count'];
    
    $pharmacy_count = $connect->query("SELECT COUNT(*) as count FROM pharmacies");
    if ($pharmacy_count) $stats['total_pharmacies'] = (int)$pharmacy_count->fetch_assoc()['count'];
    
    $medicine_count = $connect->query("SELECT COUNT(*) as count FROM medicines");
    if ($medicine_count) $stats['total_medicines'] = (int)$medicine_count->fetch_assoc()['count'];
    
    $staff_count = $connect->query("SELECT COUNT(*) as count FROM medical_staff");
    if ($staff_count) $stats['total_medical_staff'] = (int)$staff_count->fetch_assoc()['count'];
    
    // Get today's audit events
    $audit_today = $connect->query("SELECT COUNT(*) as count FROM audit_logs WHERE DATE(action_time) = CURDATE()");
    if ($audit_today) $stats['audit_today'] = (int)$audit_today->fetch_assoc()['count'];
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - MdLink Rwanda</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2f855a;
            --secondary-color: #f8f9fa;
            --accent-color: #e6f4ea;
            --text-dark: #2d2d2d;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
        }

        .stat-card.users::before { background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%); }
        .stat-card.pharmacies::before { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
        .stat-card.medicines::before { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.staff::before { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.users { background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%); }
        .stat-icon.pharmacies { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
        .stat-icon.medicines { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-icon.staff { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            color: var(--primary-color);
            text-decoration: none;
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .action-text {
            font-weight: 600;
            text-align: center;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .dashboard-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fa fa-tachometer"></i> Super Admin Dashboard
            </h1>
            <p class="dashboard-subtitle">
                Welcome back! Here's an overview of your MdLink Rwanda system.
            </p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fa fa-exclamation-triangle"></i> 
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card users">
                <div class="stat-header">
                    <div class="stat-icon users">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_users']); ?></h3>
                <p class="stat-label">Total Admin Users</p>
            </div>

            <div class="stat-card pharmacies">
                <div class="stat-header">
                    <div class="stat-icon pharmacies">
                        <i class="fa fa-hospital-o"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_pharmacies']); ?></h3>
                <p class="stat-label">Registered Pharmacies</p>
            </div>

            <div class="stat-card medicines">
                <div class="stat-header">
                    <div class="stat-icon medicines">
                        <i class="fa fa-medkit"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_medicines']); ?></h3>
                <p class="stat-label">Medicine Records</p>
            </div>

            <div class="stat-card staff">
                <div class="stat-header">
                    <div class="stat-icon staff">
                        <i class="fa fa-user-md"></i>
                    </div>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_medical_staff']); ?></h3>
                <p class="stat-label">Medical Staff</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="add_user.php" class="action-btn">
                <div class="action-icon">
                    <i class="fa fa-user-plus"></i>
                </div>
                <div class="action-text">Add User</div>
            </a>

            <a href="create_pharmacy.php" class="action-btn">
                <div class="action-icon">
                    <i class="fa fa-hospital-o"></i>
                </div>
                <div class="action-text">Create Pharmacy</div>
            </a>

            <a href="add-product.php" class="action-btn">
                <div class="action-icon">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <div class="action-text">Add Medicine</div>
            </a>

            <a href="medical_staff.php" class="action-btn">
                <div class="action-icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <div class="action-text">Manage Staff</div>
            </a>

            <a href="manage_pharmacies.php" class="action-btn">
                <div class="action-icon">
                    <i class="fa fa-building"></i>
                </div>
                <div class="action-text">Manage Pharmacies</div>
            </a>

            <a href="product.php" class="action-btn">
                <div class="action-icon">
                    <i class="fa fa-list"></i>
                </div>
                <div class="action-text">View Medicines</div>
            </a>
        </div>
    </div>
</body>
</html>
