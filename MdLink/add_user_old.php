<?php 
include('./constant/check.php'); 
include('./constant/layout/head.php');
include('./constant/layout/header.php');
include('./constant/layout/sidebar.php');
include('./constant/connect.php');

// Pagination settings
$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get total count of users
$count_sql = "SELECT COUNT(*) as total FROM admin_users";
$count_result = $connect->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get existing users for the list with pagination
$users_sql = "SELECT admin_id, username, password_hash, email, phone, status, created_at
              FROM admin_users
              ORDER BY admin_id DESC
              LIMIT $records_per_page OFFSET $offset";
$users_result = $connect->query($users_sql);

// Get statistics
$stats = [];
$stats['total'] = $total_records;
$stats['active'] = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE status = 'active'")->fetch_assoc()['count'];
$stats['recent'] = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
?>

<!-- Ensure FontAwesome is loaded -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<style>
.page-wrapper { width: 100%; }
.page-wrapper .container-fluid { width: 100%; max-width: 100%; padding-left: 15px; padding-right: 15px; }

.user-management-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    color: white;
    text-align: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.user-management-header h3 {
    margin-bottom: 15px;
    font-weight: 700;
    font-size: 2.5rem;
}

.user-management-header p {
    opacity: 0.9;
    margin-bottom: 0;
    font-size: 1.1rem;
}

.stats-card {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    color: white;
    border-radius: 8px;
    padding: 8px;
    margin-bottom: 10px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(46, 125, 50, 0.2);
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.4rem;
    font-weight: bold;
    margin-bottom: 3px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.stat-label {
    font-size: 0.75rem;
    opacity: 0.9;
    font-weight: 500;
}

.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border: none;
    /* Allow native dropdowns to render outside card */
    overflow: visible;
}

.form-card .card-header {
    background: linear-gradient(135deg, #4caf50, #66bb6a);
    color: white;
    border: none;
    padding: 25px 30px;
    border-radius: 20px 20px 0 0;
}

.form-card .card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.4rem;
}

.form-card .card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 12px;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    height: auto;
    color: #333;
    background-color: #fff;
    position: relative;
    z-index: 1;
}

.form-control:focus {
    border-color: #4caf50;
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
    outline: none;
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Ensure select dropdowns render above surrounding elements */
select.form-control { position: relative; z-index: 2; background-color: #fff; color: #333; }
select.form-control option { color: #333; background-color: #fff; }

.btn-primary {
    background: linear-gradient(135deg, #4caf50, #66bb6a);
    border: none;
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    background: linear-gradient(135deg, #45a049, #5cb85c);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #868e96);
    border: none;
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #5a6268, #6c757d);
}

.table-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border: none;
    overflow: hidden;
    margin-top: 30px;
}

.table-card .card-header {
    background: linear-gradient(135deg, #2196f3, #42a5f5);
    color: white;
    border: none;
    padding: 25px 30px;
    border-radius: 20px 20px 0 0;
}

.table-card .card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.4rem;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #333;
    padding: 10px 12px;
    font-size: 0.85rem;
}

.table tbody td {
    border: none;
    padding: 8px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.9rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    padding: 8px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.8rem;
}

.badge-success {
    background: linear-gradient(135deg, #4caf50, #66bb6a);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #ff9800, #ffb74d);
    color: white;
}

.badge-info {
    background: linear-gradient(135deg, #2196f3, #42a5f5);
    color: white;
}

.alert {
    border: none;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border-left: 5px solid #4caf50;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border-left: 5px solid #dc3545;
}

.required {
    color: #dc3545;
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 5px;
}

/* Compact Quick Info Section Styles */
.info-section {
    border-left: 2px solid #4caf50;
    padding-left: 8px;
    margin-bottom: 10px;
}

.field-guideline-compact {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 4px 6px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    margin-bottom: 2px;
}

.field-guideline-compact:hover {
    background: #e3f2fd;
    border-color: #2196f3;
    transform: translateX(2px);
}

.field-name-compact {
    font-weight: 600;
    color: #333;
    font-size: 0.75rem;
    margin-bottom: 1px;
    line-height: 1.2;
}

.field-desc-compact {
    font-size: 0.7rem;
    color: #666;
    font-style: italic;
    line-height: 1.1;
}

.requirement-item-compact {
    display: flex;
    align-items: center;
    padding: 2px 0;
    font-size: 0.75rem;
    color: #555;
    line-height: 1.2;
}

.requirement-item-compact i {
    margin-right: 6px;
    font-size: 0.7rem;
}

/* Pagination Styles */
.pagination-info {
    font-size: 0.85rem;
}

.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
}

.pagination-sm .page-item:first-child .page-link {
    border-top-left-radius: 0.2rem;
    border-bottom-left-radius: 0.2rem;
}

.pagination-sm .page-item:last-child .page-link {
    border-top-right-radius: 0.2rem;
    border-bottom-right-radius: 0.2rem;
}

.page-link {
    color: #4caf50;
    border: 1px solid #dee2e6;
}

.page-link:hover {
    color: #45a049;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #4caf50;
    border-color: #4caf50;
}

/* Action Button Styles */
.btn-group .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    border-radius: 0.2rem;
    margin: 0 1px;
    min-width: 30px;
}

.btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
    background-color: transparent;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
    transform: scale(1.05);
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
    background-color: transparent;
}

.btn-outline-danger:hover {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
    transform: scale(1.05);
}

.btn-group .btn i {
    font-size: 0.65rem;
}

.btn-group .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-group .btn:disabled:hover {
    transform: none;
}

/* Responsive adjustments for Quick Info */
@media (max-width: 768px) {
    .user-management-header {
        padding: 25px 20px;
    }
    
    .user-management-header h3 {
        font-size: 2rem;
    }
    
    .form-card .card-body {
        padding: 25px;
    }
    
    .stats-card {
        margin-bottom: 15px;
    }
    
    .field-guideline {
        padding: 8px;
    }
    
    .field-name {
        font-size: 0.85rem;
    }
    
    .field-desc {
        font-size: 0.75rem;
    }
}
</style>

<div class="page-wrapper">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="user-management-header">
            <h3><i class="fa fa-user-plus"></i> Add New User</h3>
            <p>Create and manage user accounts for staff and administrators</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-3 justify-content-center">
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stat-item">
                        <i class="fa fa-users" style="font-size: 1.2rem; margin-bottom: 8px; opacity: 0.8;"></i>
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stat-item">
                        <i class="fa fa-check-circle" style="font-size: 1.2rem; margin-bottom: 8px; opacity: 0.8;"></i>
                        <div class="stat-number"><?php echo $stats['active']; ?></div>
                        <div class="stat-label">Active Users</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stat-item">
                        <i class="fa fa-user-plus" style="font-size: 1.2rem; margin-bottom: 8px; opacity: 0.8;"></i>
                        <div class="stat-number"><?php echo $stats['recent']; ?></div>
                        <div class="stat-label">New This Month</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Form -->
        <div class="row">
            <div class="col-lg-7">
                <div class="card form-card">
                    <div class="card-header">
                        <h5><i class="fa fa-user-plus"></i> User Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="addUserForm">
                            <div id="userMessage"></div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username"><i class="fa fa-user"></i> Username <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                                        <div class="form-text">Choose a unique username for login</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><i class="fa fa-envelope"></i> Email Address <span class="required">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="user@example.com" required>
                                        <div class="form-text">Valid email address for notifications</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password"><i class="fa fa-lock"></i> Password <span class="required">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 8 characters" required>
                                        <div class="form-text">Password must be at least 8 characters long</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password"><i class="fa fa-lock"></i> Confirm Password <span class="required">*</span></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                                        <div class="form-text">Re-enter the password to confirm</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone"><i class="fa fa-phone"></i> Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="+250 788 XXX XXX">
                                        <div class="form-text">Optional: Contact phone number</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status"><i class="fa fa-toggle-on"></i> Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="active" selected>Active</option>
                                            <option value="disabled">Disabled</option>
                                        </select>
                                        <div class="form-text">User account status</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-secondary mr-3" id="btnReset">
                                        <i class="fa fa-refresh"></i> Reset Form
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="btnAddUser">
                                        <i class="fa fa-user-plus"></i> Add User
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Info Sidebar -->
            <div class="col-lg-5">
                <div class="card form-card">
                    <div class="card-header">
                        <h5><i class="fa fa-info-circle"></i> Quick Info</h5>
                    </div>
                    <div class="card-body p-2">
                        <!-- Compact Field Guidelines -->
                        <div class="info-section mb-2">
                            <h6 class="text-primary mb-1" style="font-size: 0.9rem;"><i class="fa fa-lightbulb-o"></i> Field Guidelines</h6>
                            <div class="field-guideline-compact mb-1">
                                <div class="field-name-compact"><i class="fa fa-user text-info"></i> Username</div>
                                <div class="field-desc-compact">Unique, alphanumeric</div>
                            </div>
                            <div class="field-guideline-compact mb-1">
                                <div class="field-name-compact"><i class="fa fa-envelope text-info"></i> Email</div>
                                <div class="field-desc-compact">Valid email format</div>
                            </div>
                            <div class="field-guideline-compact mb-1">
                                <div class="field-name-compact"><i class="fa fa-lock text-info"></i> Password</div>
                                <div class="field-desc-compact">Min 8 characters</div>
                            </div>
                            <div class="field-guideline-compact mb-1">
                                <div class="field-name-compact"><i class="fa fa-phone text-info"></i> Phone</div>
                                <div class="field-desc-compact">Optional contact</div>
                            </div>
                            <div class="field-guideline-compact mb-1">
                                <div class="field-name-compact"><i class="fa fa-toggle-on text-info"></i> Status</div>
                                <div class="field-desc-compact">Active/Disabled</div>
                            </div>
                        </div>
                        
                        <!-- Compact Requirements -->
                        <div class="info-section">
                            <h6 class="text-success mb-1" style="font-size: 0.9rem;"><i class="fa fa-check-circle"></i> Requirements</h6>
                            <div class="requirement-item-compact">
                                <i class="fa fa-check text-success"></i> Unique username
                            </div>
                            <div class="requirement-item-compact">
                                <i class="fa fa-check text-success"></i> Valid email
                            </div>
                            <div class="requirement-item-compact">
                                <i class="fa fa-check text-success"></i> Strong password
                            </div>
                            <div class="requirement-item-compact">
                                <i class="fa fa-check text-success"></i> Select status
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="row mt-4">
            <div class="col-lg-10">
                <div class="card table-card">
                    <div class="card-header">
                        <h5><i class="fa fa-users"></i> Existing Users</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                                        <?php while ($user = $users_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $user['admin_id']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'N/A'; ?></td>
                                                <td>
                                                    <?php if ($user['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Disabled</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary edit-user" 
                                                                data-id="<?php echo $user['admin_id']; ?>"
                                                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                                data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                                                data-status="<?php echo $user['status']; ?>"
                                                                data-password="<?php echo htmlspecialchars($user['password_hash']); ?>"
                                                                title="Edit User">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-user" 
                                                                data-id="<?php echo $user['admin_id']; ?>"
                                                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                                title="Delete User">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No users found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="pagination-info">
                                <small class="text-muted">
                                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> 
                                    of <?php echo $total_records; ?> users
                                </small>
                            </div>
                            <nav aria-label="Users pagination">
                                <ul class="pagination pagination-sm mb-0">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);
                                    
                                    for ($i = $start_page; $i <= $end_page; $i++):
                                    ?>
                                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php'); ?>

<script>
$(document).ready(function() {
    // Form validation
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const btn = $('#btnAddUser');
        
        // Basic validation
        const username = $('#username').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const status = $('#status').val();
        
        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        
        let hasErrors = false;
        
        if (!username) {
            $('#username').addClass('is-invalid');
            showMessage('Username is required!', 'danger');
            hasErrors = true;
        } else if (username.length < 3) {
            $('#username').addClass('is-invalid');
            showMessage('Username must be at least 3 characters long!', 'danger');
            hasErrors = true;
        } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            $('#username').addClass('is-invalid');
            showMessage('Username can only contain letters, numbers, and underscores!', 'danger');
            hasErrors = true;
        }
        
        if (!email) {
            $('#email').addClass('is-invalid');
            showMessage('Email is required!', 'danger');
            hasErrors = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email').addClass('is-invalid');
            showMessage('Please enter a valid email address!', 'danger');
            hasErrors = true;
        }
        
        // Password validation
        const editMode = $('#addUserForm').data('edit-mode');
        
        if (!editMode) {
            // For new users, password is required
            if (!password) {
                $('#password').addClass('is-invalid');
                showMessage('Password is required!', 'danger');
                hasErrors = true;
            } else if (password.length < 8) {
                $('#password').addClass('is-invalid');
                showMessage('Password must be at least 8 characters long!', 'danger');
                hasErrors = true;
            }
        } else {
            // For edit mode, password is optional but if provided, must be valid
            if (password && password.length < 8) {
                $('#password').addClass('is-invalid');
                showMessage('Password must be at least 8 characters long!', 'danger');
                hasErrors = true;
            }
        }
        
        // Password confirmation validation
        if (password && confirmPassword && password !== confirmPassword) {
            $('#confirm_password').addClass('is-invalid');
            showMessage('Passwords do not match!', 'danger');
            hasErrors = true;
        } else if (password && !confirmPassword) {
            $('#confirm_password').addClass('is-invalid');
            showMessage('Please confirm the password!', 'danger');
            hasErrors = true;
        }
        
        if (!status) {
            $('#status').addClass('is-invalid');
            showMessage('Status is required!', 'danger');
            hasErrors = true;
        }
        
        if (hasErrors) {
            return;
        }
        
        // Disable button and show loading
        if (editMode) {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating User...');
        } else {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding User...');
        }
        
        // Submit form
        // Add edit mode data to FormData
        if (editMode) {
            formData.append('edit_mode', 'true');
            formData.append('edit_id', $('#addUserForm').data('edit-id'));
            formData.append('original_password', $('#addUserForm').data('original-password'));
        }
        
        $.ajax({
            url: 'php_action/add_user.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (editMode) {
                        showMessage('User updated successfully! The page will refresh to show the updated user list.', 'success');
                    } else {
                        showMessage('User added successfully! The page will refresh to show the updated user list.', 'success');
                    }
                    form.reset();
                    $('.form-control').removeClass('is-invalid');
                    // Reload page after 3 seconds to show updated list
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                } else {
                    showMessage(response.message || 'Failed to add user. Please try again.', 'danger');
                }
            },
            error: function() {
                showMessage('An error occurred. Please try again.', 'danger');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-user-plus"></i> Add User');
            }
        });
    });
    
    // Reset form
    $('#btnReset').on('click', function() {
        $('#addUserForm')[0].reset();
        $('#userMessage').html('');
    });
    
    // Clear validation errors on input
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Password confirmation validation
    $('#confirm_password').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (confirmPassword && password !== confirmPassword) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Show message function
    function showMessage(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        $('#userMessage').html(`
            <div class="alert ${alertClass}">
                <i class="fa ${icon}"></i> ${message}
            </div>
        `);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#userMessage').html('');
        }, 5000);
    }
    
    // Edit user functionality
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        const username = $(this).data('username');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const status = $(this).data('status');
        const passwordHash = $(this).data('password');
        
        // Clear any previous validation states
        $('#username, #email, #phone, #status, #password, #confirm_password').removeClass('is-invalid');
        
        // Populate form with user data
        $('#username').val(username);
        $('#email').val(email);
        $('#phone').val(phone);
        $('#status').val(status);
        
        // For password fields, we'll show placeholder text indicating current password
        $('#password').val('').attr('placeholder', 'Enter new password (leave blank to keep current)');
        $('#confirm_password').val('').attr('placeholder', 'Confirm new password');
        
        // Add a note about password
        $('#password').after('<small class="form-text text-info"><i class="fa fa-info-circle"></i> Leave password fields blank to keep current password</small>');
        
        // Change form title and button text
        $('.form-card .card-header h5').html('<i class="fa fa-edit"></i> Edit User');
        $('#addUserBtn').html('<i class="fa fa-save"></i> Update User');
        
        // Store edit mode and original password hash
        $('#addUserForm').data('edit-mode', true);
        $('#addUserForm').data('edit-id', userId);
        $('#addUserForm').data('original-password', passwordHash);
        
        // Scroll to form with smooth animation
        $('html, body').animate({
            scrollTop: $('#addUserForm').offset().top - 100
        }, 500);
        
        showMessage(`User "${username}" loaded for editing. Update the information and click "Update User" to save changes.`, 'success');
    });
    
    // Delete user functionality
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        const username = $(this).data('username');
        const $btn = $(this);
        
        // Create a more styled confirmation dialog
        const confirmMessage = `Are you sure you want to delete user "${username}"?\n\nThis action cannot be undone and will permanently remove the user from the system.`;
        
        if (confirm(confirmMessage)) {
            // Disable button and show loading
            $btn.prop('disabled', true);
            $btn.html('<i class="fa fa-spinner fa-spin"></i>');
            
            // Send delete request
            $.ajax({
                url: 'php_action/delete_user.php',
                type: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showMessage(`User "${username}" deleted successfully!`, 'success');
                        // Add fade out effect to the row
                        $btn.closest('tr').fadeOut(500, function() {
                            // Reload page after animation
                            setTimeout(function() {
                                location.reload();
                            }, 300);
                        });
                    } else {
                        showMessage(response.message || 'Failed to delete user.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', error);
                    showMessage('An error occurred while deleting the user. Please try again.', 'danger');
                },
                complete: function() {
                    // Re-enable button
                    $btn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
                }
            });
        }
    });
    
    // Reset form when clicking "Add User" button in edit mode
    $('#addUserBtn').on('click', function() {
        const editMode = $('#addUserForm').data('edit-mode');
        if (editMode) {
            // This is handled by the form submission
            return true;
        }
    });
    
    // Add escape key to cancel edit mode
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#addUserForm').data('edit-mode')) {
            resetForm();
        }
    });
    
    // Function to reset form to add mode
    function resetForm() {
        $('#addUserForm')[0].reset();
        $('#addUserForm').data('edit-mode', false);
        $('#addUserForm').data('edit-id', null);
        $('#addUserForm').data('original-password', null);
        
        // Reset password field placeholders
        $('#password').attr('placeholder', 'Minimum 8 characters');
        $('#confirm_password').attr('placeholder', 'Confirm password');
        
        // Remove password info note if it exists
        $('#password').next('.form-text.text-info').remove();
        
        $('.form-card .card-header h5').html('<i class="fa fa-user-plus"></i> Add New User');
        $('#addUserBtn').html('<i class="fa fa-plus"></i> Add User');
        $('#username, #email, #phone, #status, #password, #confirm_password').removeClass('is-invalid');
        showMessage('Form reset to add new user mode.', 'success');
    }
});
</script>

</body>
</html>
