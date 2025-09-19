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

// Get statistics
$stats = [
    'total_users' => 0,
    'active_users' => 0,
    'recent_users' => 0
];

try {
    $total_query = $connect->query("SELECT COUNT(*) as count FROM admin_users");
    if ($total_query) $stats['total_users'] = (int)$total_query->fetch_assoc()['count'];
    
    $active_query = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE role != 'super_admin'");
    if ($active_query) $stats['active_users'] = (int)$active_query->fetch_assoc()['count'];
    
    $recent_query = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    if ($recent_query) $stats['recent_users'] = (int)$recent_query->fetch_assoc()['count'];
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Get all users for display with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$users = [];
$total_pages = 1;

try {
    // Get total count
    $count_query = $connect->query("SELECT COUNT(*) as count FROM admin_users");
    $total_users = $count_query ? (int)$count_query->fetch_assoc()['count'] : 0;
    $total_pages = ceil($total_users / $limit);
    
    // Get users for current page
    $users_query = $connect->query("SELECT admin_id, username, email, phone, role, created_at FROM admin_users ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    if ($users_query) {
        while ($row = $users_query->fetch_assoc()) {
            $users[] = $row;
        }
    }
} catch (Exception $e) {
    // Handle error silently
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './constant/layout/head.php'; ?>
    <title>Add User - MdLink Rwanda</title>
    <style>
        .page-wrapper {
            background: #F9F9F9;
            padding-bottom: 60px;
        }
        
        .container-fluid {
            padding: 0 30px 25px;
        }
        
        .left-sidebar {
            position: fixed;
            width: 240px;
            height: 100%;
            top: 0;
            z-index: 20;
            background: #0f172a;
            -webkit-box-shadow: 0px 0px 10px rgb(120 130 140 / 13%);
            box-shadow: 0px 0px 10px rgb(120 130 140 / 13%);
        }
        
        @media (min-width: 1024px) {
            .page-wrapper {
                margin-left: 240px;
            }
        }
        
        @media (max-width: 1023px) {
            .page-wrapper {
                margin-left: 60px;
                -webkit-transition: 0.2s ease-in;
                -o-transition: 0.2s ease-in;
                transition: 0.2s ease-in;
            }
        }
        
        .user-management-header {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(47, 133, 90, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .user-management-header::before {
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
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
        }
        
        .stat-card.total::before { background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%); }
        .stat-card.active::before { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
        .stat-card.recent::before { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stat-icon.total { background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%); }
        .stat-icon.active { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
        .stat-icon.recent { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d2d2d;
            margin: 0;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d2d2d;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .required {
            color: #ef4444;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2f855a;
            box-shadow: 0 0 0 3px rgba(47, 133, 90, 0.1);
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .users-list {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .users-header {
            background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .users-body {
            padding: 1.5rem;
        }
        
        .user-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.3s ease;
        }
        
        .user-item:hover {
            background-color: #f0f9ff;
        }
        
        .user-item:last-child {
            border-bottom: none;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 1rem;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            color: #2d2d2d;
            margin-bottom: 0.25rem;
        }
        
        .user-email {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .user-role {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .role-pharmacy-admin {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .role-finance-admin {
            background: #fef3c7;
            color: #92400e;
        }
        
        .role-super-admin {
            background: #fce7f3;
            color: #be185d;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: none;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-decoration: none;
            color: #6b7280;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #2f855a;
            color: white;
            border-color: #2f855a;
        }
        
        .pagination .current {
            background: #2f855a;
            color: white;
            border-color: #2f855a;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div id="main-wrapper">
        <?php include './constant/layout/header.php'; ?>
        <?php include './constant/layout/sidebar.php'; ?>
        
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="user-management-header">
                    <h1 class="page-title">
                        <i class="fa fa-user-plus"></i> Add New User
                    </h1>
                    <p class="page-subtitle">
                        Create new admin users for the MdLink Rwanda system
                    </p>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="stat-icon total">
                            <i class="fa fa-users"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($stats['total_users']); ?></h3>
                        <p class="stat-label">Total Users</p>
                    </div>

                    <div class="stat-card active">
                        <div class="stat-icon active">
                            <i class="fa fa-user-check"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($stats['active_users']); ?></h3>
                        <p class="stat-label">Admin Users</p>
                    </div>

                    <div class="stat-card recent">
                        <div class="stat-icon recent">
                            <i class="fa fa-user-clock"></i>
                        </div>
                        <h3 class="stat-number"><?php echo number_format($stats['recent_users']); ?></h3>
                        <p class="stat-label">Recent Users (30 days)</p>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- Add User Form -->
                    <div class="form-card">
                        <div class="form-header">
                            <i class="fa fa-user-plus"></i>
                            <h3 class="form-title">User Information</h3>
                        </div>
                        <div class="form-body">
                            <form id="addUserForm">
                                <div id="userMessage"></div>
                                
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <i class="fa fa-user"></i> Username <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                                    <div class="form-text">Choose a unique username for login</div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fa fa-envelope"></i> Email Address <span class="required">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="user@example.com" required>
                                    <div class="form-text">Valid email address for notifications</div>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fa fa-lock"></i> Password <span class="required">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 8 characters" required>
                                    <div class="form-text">Password must be at least 8 characters long</div>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fa fa-lock"></i> Confirm Password <span class="required">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                                    <div class="form-text">Re-enter the password to confirm</div>
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fa fa-phone"></i> Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+250 788 XXX XXX">
                                    <div class="form-text">Optional: Contact phone number</div>
                                </div>

                                <div class="form-group">
                                    <label for="role" class="form-label">
                                        <i class="fa fa-user-tag"></i> User Role <span class="required">*</span>
                                    </label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="">Select user role</option>
                                        <option value="pharmacy_admin">Pharmacy Admin</option>
                                        <option value="finance_admin">Finance Admin</option>
                                    </select>
                                    <div class="form-text">Select the appropriate role for this user</div>
                                </div>

                                <button type="submit" class="btn-primary">
                                    <i class="fa fa-user-plus"></i> Add User
                                </button>
                                <button type="button" class="btn-secondary" onclick="clearForm()">
                                    <i class="fa fa-refresh"></i> Clear Form
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Users List -->
                    <div class="users-list">
                        <div class="users-header">
                            <i class="fa fa-users"></i>
                            <h3 class="form-title">All Users (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</h3>
                        </div>
                        <div class="users-body">
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <div class="user-item">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                            <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                        <div class="user-role role-<?php echo str_replace('_', '-', $user['role']); ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $user['role'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <!-- Pagination -->
                                <?php if ($total_pages > 1): ?>
                                    <div class="pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <?php if ($i == $page): ?>
                                                <span class="current"><?php echo $i; ?></span>
                                            <?php else: ?>
                                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fa fa-users"></i>
                                    <p>No users found</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById('userMessage');
            
            // Validate passwords match
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match!', 'error');
                return;
            }
            
            // Validate password length
            if (password.length < 8) {
                showMessage('Password must be at least 8 characters long!', 'error');
                return;
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding User...';
            
            // Submit form
            fetch('php_action/add_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('User added successfully!', 'success');
                    this.reset();
                    // Reload the page to show new user
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage(data.message || 'Error adding user', 'error');
                }
            })
            .catch(error => {
                showMessage('Network error occurred', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
        
        function showMessage(message, type) {
            const messageDiv = document.getElementById('userMessage');
            messageDiv.innerHTML = `
                <div class="alert alert-${type}">
                    <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    ${message}
                </div>
            `;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }
        
        function clearForm() {
            document.getElementById('addUserForm').reset();
            document.getElementById('userMessage').innerHTML = '';
        }
    </script>
</body>
</html>