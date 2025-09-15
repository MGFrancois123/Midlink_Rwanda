<?php 
include('./constant/check.php'); 
include('./constant/layout/head.php');
include('./constant/layout/header.php');
include('./constant/layout/sidebar.php');
include('./constant/connect.php');

// Get existing users for the list
$users_sql = "SELECT admin_id, username, role, email, phone, created_at 
              FROM admin_users 
              ORDER BY admin_id DESC";
$users_result = $connect->query($users_sql);

// Get statistics
$stats = [];
$stats['total'] = $users_result->num_rows;
$stats['super_admin'] = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE role = 'super_admin'")->fetch_assoc()['count'];
$stats['admin'] = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE role = 'admin'")->fetch_assoc()['count'];
$stats['staff'] = $connect->query("SELECT COUNT(*) as count FROM admin_users WHERE role = 'staff'")->fetch_assoc()['count'];
?>

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
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 8px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.stat-label {
    font-size: 1rem;
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
    padding: 40px;
}

.form-group {
    margin-bottom: 25px;
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
    padding: 15px;
    font-size: 0.9rem;
}

.table tbody td {
    border: none;
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
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
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['super_admin']; ?></div>
                        <div class="stat-label">Super Admins</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['admin']; ?></div>
                        <div class="stat-label">Administrators</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['staff']; ?></div>
                        <div class="stat-label">Staff Members</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Form -->
        <div class="row">
            <div class="col-lg-8">
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
                                        <label for="role"><i class="fa fa-shield"></i> User Role <span class="required">*</span></label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="" disabled selected hidden>Select Role</option>
                                            <option value="super_admin">Super Administrator</option>
                                            <option value="admin">Administrator</option>
                                            <option value="staff">Staff Member</option>
                                        </select>
                                        <div class="form-text">Choose the appropriate role for this user</div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes"><i class="fa fa-sticky-note"></i> Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about this user (optional)"></textarea>
                                        <div class="form-text">Any additional information about this user</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-secondary mr-2" id="btnReset">
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

            <!-- Quick Stats Sidebar -->
            <div class="col-lg-4">
                <div class="card form-card">
                    <div class="card-header">
                        <h5><i class="fa fa-info-circle"></i> Quick Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fa fa-lightbulb-o"></i> User Roles</h6>
                            <ul class="mb-0">
                                <li><strong>Super Administrator:</strong> Full system access</li>
                                <li><strong>Administrator:</strong> Management access</li>
                                <li><strong>Staff Member:</strong> Basic operational access</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-success">
                            <h6><i class="fa fa-check-circle"></i> Requirements</h6>
                            <ul class="mb-0">
                                <li>Username must be unique</li>
                                <li>Email must be valid and unique</li>
                                <li>Password minimum 8 characters</li>
                                <li>All required fields must be filled</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="row mt-4">
            <div class="col-12">
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
                                        <th>Role</th>
                                        <th>Phone</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                                        <?php while ($user = $users_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $user['admin_id']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <?php if ($user['role'] == 'super_admin'): ?>
                                                        <span class="badge badge-success">Super Admin</span>
                                                    <?php elseif ($user['role'] == 'admin'): ?>
                                                        <span class="badge badge-info">Administrator</span>
                                                    <?php elseif ($user['role'] == 'staff'): ?>
                                                        <span class="badge badge-warning">Staff Member</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary"><?php echo ucfirst($user['role']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'N/A'; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td><span class="badge badge-success">Active</span></td>
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
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        
        if (password !== confirmPassword) {
            showMessage('Passwords do not match!', 'danger');
            return;
        }
        
        if (password.length < 8) {
            showMessage('Password must be at least 8 characters long!', 'danger');
            return;
        }
        
        // Disable button and show loading
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding User...');
        
        // Submit form
        $.ajax({
            url: 'php_action/add_user.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage('User added successfully!', 'success');
                    form.reset();
                    // Reload page after 2 seconds to show updated list
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
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
});
</script>

</body>
</html>
