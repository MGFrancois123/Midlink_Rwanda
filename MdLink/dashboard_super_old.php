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
    'security_today' => 0,
    'recent_pharmacies' => [],
    'recent_medicines' => [],
    'recent_staff' => []
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
    
    // Get recent pharmacies
    $recent_pharmacies = $connect->query("SELECT pharmacy_id, name, license_number, created_at FROM pharmacies ORDER BY created_at DESC LIMIT 5");
    if ($recent_pharmacies) {
        while ($row = $recent_pharmacies->fetch_assoc()) {
            $stats['recent_pharmacies'][] = $row;
        }
    }
    
    // Get recent medicines
    $recent_medicines = $connect->query("SELECT medicine_id, medicine_name, category, created_at FROM medicines ORDER BY created_at DESC LIMIT 5");
    if ($recent_medicines) {
        while ($row = $recent_medicines->fetch_assoc()) {
            $stats['recent_medicines'][] = $row;
        }
    }
    
    // Get recent medical staff
    $recent_staff = $connect->query("SELECT staff_id, full_name, role, created_at FROM medical_staff ORDER BY created_at DESC LIMIT 5");
    if ($recent_staff) {
        while ($row = $recent_staff->fetch_assoc()) {
            $stats['recent_staff'][] = $row;
        }
    }
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './constant/layout/head.php'; ?>
    <title>Super Admin Dashboard - MdLink Rwanda</title>
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
            --gradient-primary: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            --gradient-secondary: linear-gradient(135deg, #4c51bf 0%, #667eea 100%);
            --gradient-success: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
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
            background: var(--gradient-primary);
        }

        .stat-card.users::before { background: var(--gradient-secondary); }
        .stat-card.pharmacies::before { background: var(--gradient-success); }
        .stat-card.medicines::before { background: var(--gradient-warning); }
        .stat-card.staff::before { background: var(--gradient-info); }

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

        .stat-icon.users { background: var(--gradient-secondary); }
        .stat-icon.pharmacies { background: var(--gradient-success); }
        .stat-icon.medicines { background: var(--gradient-warning); }
        .stat-icon.staff { background: var(--gradient-info); }

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

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .content-header {
            background: var(--gradient-primary);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .content-body {
            padding: 1.5rem;
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
            background: var(--gradient-primary);
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

        .recent-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .recent-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
        }

        .recent-content {
            flex: 1;
        }

        .recent-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .recent-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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
            
            .content-grid {
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

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Pharmacies -->
            <div class="content-card">
                <div class="content-header">
                    <h3 class="content-title">
                        <i class="fa fa-hospital-o"></i> Recent Pharmacies
                    </h3>
                    <a href="manage_pharmacies.php" class="btn btn-sm btn-light">
                        <i class="fa fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="content-body">
                    <?php if (!empty($stats['recent_pharmacies'])): ?>
                        <?php foreach ($stats['recent_pharmacies'] as $pharmacy): ?>
                            <div class="recent-item">
                                <div class="recent-icon">
                                    <i class="fa fa-hospital-o"></i>
                                </div>
                                <div class="recent-content">
                                    <div class="recent-title"><?php echo htmlspecialchars($pharmacy['name']); ?></div>
                                    <div class="recent-meta">
                                        License: <?php echo htmlspecialchars($pharmacy['license_number']); ?> • 
                                        <?php echo date('M j, Y', strtotime($pharmacy['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-hospital-o"></i>
                            <p>No pharmacies registered yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Medicines -->
            <div class="content-card">
                <div class="content-header">
                    <h3 class="content-title">
                        <i class="fa fa-medkit"></i> Recent Medicines
                    </h3>
                    <a href="product.php" class="btn btn-sm btn-light">
                        <i class="fa fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="content-body">
                    <?php if (!empty($stats['recent_medicines'])): ?>
                        <?php foreach ($stats['recent_medicines'] as $medicine): ?>
                            <div class="recent-item">
                                <div class="recent-icon">
                                    <i class="fa fa-medkit"></i>
                                </div>
                                <div class="recent-content">
                                    <div class="recent-title"><?php echo htmlspecialchars($medicine['medicine_name']); ?></div>
                                    <div class="recent-meta">
                                        Category: <?php echo htmlspecialchars($medicine['category']); ?> • 
                                        <?php echo date('M j, Y', strtotime($medicine['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-medkit"></i>
                            <p>No medicines added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Medical Staff -->
            <div class="content-card">
                <div class="content-header">
                    <h3 class="content-title">
                        <i class="fa fa-user-md"></i> Recent Medical Staff
                    </h3>
                    <a href="medical_staff.php" class="btn btn-sm btn-light">
                        <i class="fa fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="content-body">
                    <?php if (!empty($stats['recent_staff'])): ?>
                        <?php foreach ($stats['recent_staff'] as $staff): ?>
                            <div class="recent-item">
                                <div class="recent-icon">
                                    <i class="fa fa-user-md"></i>
                                </div>
                                <div class="recent-content">
                                    <div class="recent-title"><?php echo htmlspecialchars($staff['full_name']); ?></div>
                                    <div class="recent-meta">
                                        Role: <?php echo ucfirst($staff['role']); ?> • 
                                        <?php echo date('M j, Y', strtotime($staff['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-user-md"></i>
                            <p>No medical staff added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System Activity -->
            <div class="content-card">
                <div class="content-header">
                    <h3 class="content-title">
                        <i class="fa fa-activity"></i> System Activity
                    </h3>
                    <span class="badge badge-light">Today</span>
                </div>
                <div class="content-body">
                    <div class="recent-item">
                        <div class="recent-icon">
                            <i class="fa fa-clipboard-list"></i>
                        </div>
                        <div class="recent-content">
                            <div class="recent-title">Audit Events</div>
                            <div class="recent-meta"><?php echo $stats['audit_today']; ?> events recorded today</div>
                        </div>
                    </div>
                    <div class="recent-item">
                        <div class="recent-icon">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <div class="recent-content">
                            <div class="recent-title">Security Events</div>
                            <div class="recent-meta"><?php echo $stats['security_today']; ?> security events today</div>
                        </div>
                    </div>
                </div>
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

    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
    </script>
</body>
</html>
