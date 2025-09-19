<?php
// Test version without authentication check
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session but don't include check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('./constant/connect.php');

// Get pharmacies for dropdown
$pharmacies = [];
$pharmacy_query = "SELECT pharmacy_id, name FROM pharmacies ORDER BY name";
$pharmacy_result = $connect->query($pharmacy_query);
if ($pharmacy_result) {
    while ($row = $pharmacy_result->fetch_assoc()) {
        $pharmacies[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - MdLink Rwanda (Test Version)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3><i class="fa fa-user-plus"></i> Add New User (Test Version)</h3>
                        <p class="mb-0">Create a new admin user account with appropriate role and permissions</p>
                        <small class="mt-2 d-block">⚠️ This is a test version without authentication</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fa fa-user"></i> User Information</h4>
                    </div>
                    <div class="card-body">
                        <!-- Alert Messages -->
                        <div id="alert-container"></div>

                        <form id="addUserForm" method="POST" action="php_action/createUser.php">
                            <div class="row">
                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Enter username" required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Enter email address" required>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           placeholder="Enter phone number">
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="super_admin">Super Admin</option>
                                        <option value="pharmacy_admin">Pharmacy Admin</option>
                                        <option value="finance_admin">Finance Admin</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Pharmacy Assignment (for pharmacy_admin) -->
                            <div class="row" id="pharmacy-row" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label for="pharmacy_id" class="form-label">Assigned Pharmacy <span class="text-danger">*</span></label>
                                    <select class="form-control" id="pharmacy_id" name="pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        <?php foreach ($pharmacies as $pharmacy): ?>
                                            <option value="<?php echo $pharmacy['pharmacy_id']; ?>">
                                                <?php echo htmlspecialchars($pharmacy['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Enter password" required>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Confirm password" required>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success me-3">
                                    <i class="fa fa-save"></i> Create User
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fa fa-refresh"></i> Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Info -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Debug Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Database Connection:</strong> <?php echo $connect ? 'Connected' : 'Failed'; ?></p>
                        <p><strong>Pharmacies Found:</strong> <?php echo count($pharmacies); ?></p>
                        <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive'; ?></p>
                        <p><strong>User Role:</strong> <?php echo $_SESSION['userRole'] ?? 'Not Set'; ?></p>
                        <p><strong>Admin ID:</strong> <?php echo $_SESSION['adminId'] ?? 'Not Set'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Role change handler
        $('#role').on('change', function() {
            const role = $(this).val();
            const pharmacyRow = $('#pharmacy-row');
            
            if (role === 'pharmacy_admin') {
                pharmacyRow.show();
                $('#pharmacy_id').prop('required', true);
            } else {
                pharmacyRow.hide();
                $('#pharmacy_id').prop('required', false);
                $('#pharmacy_id').val('');
            }
        });

        // Form submission
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const username = $('#username').val().trim();
            const email = $('#email').val().trim();
            const role = $('#role').val();
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            
            if (!username || !email || !role || !password || !confirmPassword) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }
            
            if (role === 'pharmacy_admin' && !$('#pharmacy_id').val()) {
                alert('Please select a pharmacy for pharmacy admin role');
                return;
            }
            
            // Submit form
            const formData = new FormData(this);
            
            $.ajax({
                url: 'php_action/createUser.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('User created successfully!');
                        resetForm();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                    console.log('Response:', xhr.responseText);
                }
            });
        });
    });

    function resetForm() {
        $('#addUserForm')[0].reset();
        $('#pharmacy-row').hide();
        $('#pharmacy_id').prop('required', false);
    }
    </script>
</body>
</html>

