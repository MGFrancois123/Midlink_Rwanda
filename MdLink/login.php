<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Midilink Rwanda - Login</title>
    <!-- Login/Signup page with toggle functionality -->
    <link rel="stylesheet" href="assets/css/popup_style.css"> 
    <style>
.footer1 {
  position: fixed;
  bottom: 0;
  width: 100%;
  color: #5c4ac7;
  text-align: center;
}

/* Login card styling */
.unix-login .container-fluid { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 6px; }
.login-card { border:0; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,.08); background:#fff; }
.login-card .login-form { padding: 28px; }
.login-title { text-align:center; margin-bottom:4px; }
.login-subtitle { text-align:center; color:#777; margin-bottom:18px; font-size:13px; }
.input-group-addon { background:#f5f6f8; }
.btn-full { width:100%; }
/* Explicit centering helpers */
.login-center { margin: 0 auto; display:flex; justify-content:center; width:100%; }
.login-col { float:none; margin:0 auto; }
/* Shift left only on large screens; keep centered on smaller screens */
@media (min-width: 992px) {
  .login-col { margin-left: -100px; margin-right: auto; }
}
#togglePw{ display:none; }
/* Make inputs look like the first screenshot (icon inside field) */
.input-group { position: relative; }
.input-group .input-group-addon { 
  position: absolute; 
  left: 12px; 
  top: 50%; 
  transform: translateY(-50%); 
  border: 0; 
  background: transparent; 
  color: #6c757d; 
  width: auto; 
  z-index: 2;
}
.input-group .form-control { 
  padding-left: 40px; 
  height: 46px; 
  border-radius: 0 !important;
}

/* Password toggle styling */
.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #6c757d;
  z-index: 3;
  padding: 4px;
  transition: color 0.3s ease;
}

.password-toggle:hover {
  color: #0d67cdff;
}

.input-group .form-control.has-toggle {
  padding-right: 40px;
}

.form-group > label { display: none; }
/* space between remember and forgot link */
.remember-forgot .forgot { margin-left: 16px; }

/* Logo section styling */
.logo-section {
    margin-bottom: 30px;
}

.logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    flex-wrap: wrap;
}

.logo-image {
    width: 280px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.logo-text {
    text-align: left;
    flex: 1;
    min-width: 250px;
}

.company-name {
    font-size: 36px;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.2;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.company-name .midilink {
    color: #1e3a8a; /* Dark blue for MIDILINK */
}

.company-name .rwanda {
    color: #059669; /* Green for RWANDA */
}

.system-name {
    font-size: 22px;
    font-weight: 500;
    color: #666;
    margin: 0;
    line-height: 1.3;
    font-style: italic;
}

/* Toggle switch styling */
.form-toggle {
    text-align: center;
    margin-bottom: 20px;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 180px;
    background: #f0f0f0;
    border-radius: 25px;
    padding: 4px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.toggle-option {
    display: inline-block;
    width: 50%;
    padding: 8px 16px;
    text-align: center;
    cursor: pointer;
    border-radius: 20px;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 14px;
}

.toggle-option.active {
    background: #0d67cdff;
    color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-option:not(.active) {
    color: #666;
}

/* Signup specific fields (initially hidden) */
.signup-only {
    display: none;
}

/* Success/Error messages */
.alert {
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 12px;
    position: relative;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert .close {
    position: absolute;
    top: 8px;
    right: 15px;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
}

.alert .close:hover {
    opacity: 1;
}

/* Role dropdown styling */
.role-dropdown {
    position: relative;
}

.role-dropdown select {
    padding-left: 40px;
    height: 46px;
    border-radius: 0 !important;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: #fff url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iOCIgdmlld0JveD0iMCAwIDEyIDgiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0xIDFMNiA2TDExIDEiIHN0cm9rZT0iIzZjNzU3ZCIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+') no-repeat right 15px center;
    padding-right: 40px;
}

/* Form validation styles */
.form-control.is-valid {
    border-color: #28a745;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

/* Responsive design for logo section */
@media (max-width: 768px) {
    .logo-container {
        text-align: center;
        gap: 0;
    }
    
    .company-name {
        font-size: 30px;
    }
    
    .system-name {
        font-size: 18px;
    }
    
    .logo-image {
        width: 220px;
    }
}

@media (max-width: 480px) {
    .company-name {
        font-size: 26px;
    }
    
    .system-name {
        font-size: 16px;
    }
    
    .logo-image {
        width: 180px;
    }
}
</style>
</head>
<body>
   <?php
   
include('./constant/layout/head.php');
include('./constant/connect.php');
// Single connection (mdlink) loaded via connect.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Already logged in: redirect based on user role
if(isset($_SESSION['adminId']) || isset($_SESSION['userId'])) {
  if (!isset($_SESSION['adminId']) && isset($_SESSION['userId'])) {
    $_SESSION['adminId'] = (int)$_SESSION['userId'];
  }
  if (isset($_SESSION['userRole'])) {
    // Redirect based on role
    if ($_SESSION['userRole'] === 'user') {
        header('Location: store.php');
    } else {
        header('Location: dashboard_super.php');
    }
    exit;
  }
}

$errors = array();
$success_message = '';
$is_signup_mode = isset($_POST['mode']) && $_POST['mode'] === 'signup';

if($_POST) {    
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mode = $_POST['mode'] ?? 'login';

    if($mode === 'signup') {
        // Signup logic
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'] ?? '';

        // Validation for signup
        if(empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone) || empty($role)) {
            if(empty($username)) $errors[] = "Username is required";
            if(empty($email)) $errors[] = "Email is required";
            if(empty($password)) $errors[] = "Password is required";
            if(empty($confirm_password)) $errors[] = "Password confirmation is required";
            if(empty($phone)) $errors[] = "Phone number is required";
            if(empty($role)) $errors[] = "Role selection is required";
        } else if($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        } else if(strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long";
        } else {
            // Check if email already exists in both tables
            $email_exists = false;
            
            if (isset($connect) && $connect instanceof mysqli) {
                // Check admin_users table
                $checkAdminSql = "SELECT admin_id FROM admin_users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1";
                $stmt = $connect->prepare($checkAdminSql);
                if ($stmt) {
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows > 0) {
                        $email_exists = true;
                    }
                    $stmt->close();
                }
                
                // Check users table if not found in admin_users
                if (!$email_exists) {
                    $checkUserSql = "SELECT user_id FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1";
                    $stmt = $connect->prepare($checkUserSql);
                    if ($stmt) {
                        $stmt->bind_param('s', $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result && $result->num_rows > 0) {
                            $email_exists = true;
                        }
                        $stmt->close();
                    }
                }
                
                if ($email_exists) {
                    $errors[] = "Email already exists. Please use a different email.";
                } else {
                    // Create new user based on role selection
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    if ($role === 'Pharmacy') {
                        // Add to admin_users table
                        $insertSql = "INSERT INTO admin_users (username, email, password_hash, phone, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())";
                        $insertStmt = $connect->prepare($insertSql);
                        
                        if ($insertStmt) {
                            $insertStmt->bind_param('ssss', $username, $email, $password_hash, $phone);
                            
                            if ($insertStmt->execute()) {
                                $success_message = "Pharmacy account created successfully! You can now login.";
                                $is_signup_mode = false; // Switch back to login mode
                            } else {
                                $errors[] = "Failed to create pharmacy account. Please try again.";
                            }
                            $insertStmt->close();
                        }
                    } else if ($role === 'Patient') {
                        // Add to users table with 'user' role
                        $insertSql = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())";
                        $insertStmt = $connect->prepare($insertSql);
                        
                        if ($insertStmt) {
                            $insertStmt->bind_param('sss', $username, $email, $password_hash);
                            
                            if ($insertStmt->execute()) {
                                $success_message = "Patient account created successfully! You can now login.";
                                $is_signup_mode = false; // Switch back to login mode
                            } else {
                                $errors[] = "Failed to create patient account. Please try again.";
                            }
                            $insertStmt->close();
                        }
                    } else {
                        $errors[] = "Invalid role selection.";
                    }
                }
            }
        }
    } else {
        // Login logic - search both tables
        if(empty($email) || empty($password)) {
            if($email == "") {
                $errors[] = "Email is required";
            } 
            if($password == "") {
                $errors[] = "Password is required";
            }
        } else {
            $isAuthenticated = false;
            $userRole = '';
            $userId = 0;
            $username = '';
            
            if (isset($connect) && $connect instanceof mysqli) {
                $given = trim($password);
                
                // First, try admin_users table
                $adminSql = "SELECT admin_id, username, password_hash, email, phone, created_at 
                           FROM admin_users 
                           WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) 
                           LIMIT 1";
                
                $stmt = $connect->prepare($adminSql);
                if ($stmt) {
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $adminRes = $stmt->get_result();
                    
                    if ($adminRes && $adminRes->num_rows === 1) {
                        $admin = $adminRes->fetch_assoc();
                        $stored = trim((string)$admin['password_hash']);
                        $passOk = false;
                        
                        // Check if stored hash is bcrypt (starts with $2y$)
                        if (strpos($stored, '$2y$') === 0) {
                            // Use password_verify for bcrypt hashes
                            $passOk = password_verify($given, $stored);
                        } else {
                            // Use MD5 for legacy hashes
                            $md5Given = md5($given);
                            $passOk = ($stored === $md5Given);
                        }
                        
                        if ($passOk) {
                            $isAuthenticated = true;
                            $userRole = 'super_admin'; // Admin users get super_admin role
                            $userId = (int)$admin['admin_id'];
                            $username = $admin['username'];
                        }
                    }
                    $stmt->close();
                }
                
                // If not found in admin_users, try users table
                if (!$isAuthenticated) {
                    $userSql = "SELECT user_id, username, password, email, role, created_at 
                              FROM users 
                              WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) 
                              LIMIT 1";
                    
                    $stmt = $connect->prepare($userSql);
                    if ($stmt) {
                        $stmt->bind_param('s', $email);
                        $stmt->execute();
                        $userRes = $stmt->get_result();
                        
                        if ($userRes && $userRes->num_rows === 1) {
                            $user = $userRes->fetch_assoc();
                            $stored = trim((string)$user['password']);
                            $passOk = false;
                            
                            // Check if stored hash is bcrypt (starts with $2y$)
                            if (strpos($stored, '$2y$') === 0) {
                                // Use password_verify for bcrypt hashes
                                $passOk = password_verify($given, $stored);
                            } else {
                                // Use MD5 for legacy hashes
                                $md5Given = md5($given);
                                $passOk = ($stored === $md5Given);
                            }
                            
                            if ($passOk) {
                                $isAuthenticated = true;
                                $userRole = $user['role']; // Use role from database
                                $userId = (int)$user['user_id'];
                                $username = $user['username'];
                            }
                        }
                        $stmt->close();
                    }
                }
                
                if ($isAuthenticated) {
                    // Set session variables
                    $_SESSION['adminId'] = $userId;
                    $_SESSION['userId'] = $userId;
                    $_SESSION['userRole'] = $userRole;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    
                    // Log login activity
                    if (file_exists('activity_logger.php')) {
                        require_once 'activity_logger.php';
                        logLogin($userId, $username);
                    }
                    
                    // Redirect based on role
                    if ($userRole === 'user') {
                        header('Location: store.php');
                    } else {
                        header('Location: dashboard_super.php');
                    }
                    exit;
                } else {
                    $errors[] = "Incorrect email/password combination. Please try again.";
                }
            }
        }
    }
}

?>
    
    <div id="main-wrapper">
        <div class="unix-login">

            <div class="container-fluid" style="background-image: url('assets/uploadImage/Logo/banner3.jpg'); background-color:#ffffff; background-size:cover;">
                <div class="row w-80 justify-content-center login-center">
                    <div class="col-lg-5 col-md-7 col-sm-10 login-col">
                        <div class="login-content card login-card">
                            <div class="login-form">
                                <div class="logo-section">
                                    <div class="logo-container">
                                        <img src="./assets/uploadImage/Logo/log.jpg" alt="Midilink Rwanda - Pharmacy & Healthcare" class="logo-image">
                                        <!-- Text branding is now included in the logo image -->
                                        <!-- <div class="logo-text">
                                            <h1 class="company-name"><span class="midilink">MIDILINK</span> <span class="rwanda">RWANDA</span></h1>
                                            <h2 class="system-name">SMARTCARE SYSTEM</h2>
                                        </div> -->
                                    </div>
                                </div>

                                <!-- Toggle Switch -->
                                <div class="form-toggle">
                                    <div class="toggle-switch">
                                        <div class="toggle-option <?php echo !$is_signup_mode ? 'active' : ''; ?>" onclick="switchMode('login')">Login</div>
                                        <div class="toggle-option <?php echo $is_signup_mode ? 'active' : ''; ?>" onclick="switchMode('signup')">Sign Up</div>
                                    </div>
                                </div>

                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <strong>Error!</strong><br>
                                        <?php foreach($errors as $error): ?>
                                            • <?php echo htmlspecialchars($error); ?><br>
                                        <?php endforeach; ?>
                                        <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                                    </div>
                                <?php endif; ?>

                                <?php if ($success_message): ?>
                                    <div class="alert alert-success">
                                        <strong>Success!</strong><br>
                                        <?php echo htmlspecialchars($success_message); ?>
                                        <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                                    </div>
                                <?php endif; ?>

                                <h3 class="login-title" id="formTitle"><?php echo $is_signup_mode ? 'Create Account' : 'Welcome back'; ?></h3>
                                <div class="login-subtitle" id="formSubtitle"><?php echo $is_signup_mode ? 'Sign up to get started' : 'Sign in to continue'; ?></div>
                                
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="authForm">
                                    <input type="hidden" name="mode" id="formMode" value="<?php echo $is_signup_mode ? 'signup' : 'login'; ?>">
                                    
                                    <!-- Username field (signup only) -->
                                    <div class="form-group signup-only" id="usernameGroup" <?php echo $is_signup_mode ? 'style="display: block;"' : ''; ?>>
                                        <label>Username</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" <?php echo $is_signup_mode ? 'required' : ''; ?>>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                        </div>
                                    </div>

                                    <!-- Phone field (signup only) -->
                                    <div class="form-group signup-only" id="phoneGroup" <?php echo $is_signup_mode ? 'style="display: block;"' : ''; ?>>
                                        <label>Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="Phone Number" <?php echo $is_signup_mode ? 'required' : ''; ?>>
                                        </div>
                                    </div>

                                    <!-- Role selection dropdown (signup only) -->
                                    <div class="form-group signup-only role-dropdown" id="roleGroup" <?php echo $is_signup_mode ? 'style="display: block;"' : ''; ?>>
                                        <label>Select Role</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                            <select name="role" id="role" class="form-control" <?php echo $is_signup_mode ? 'required' : ''; ?>>
                                                <option value="">Select your role...</option>
                                                <option value="Pharmacy">Pharmacy (Admin Access)</option>
                                                <option value="Patient">Patient (User Access)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input type="password" id="password" name="password" class="form-control has-toggle" placeholder="Password" required>
                                            <span class="password-toggle" onclick="togglePassword('password')" id="passwordToggle">
                                                <i class="fa fa-eye" id="passwordToggleIcon"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Confirm Password field (signup only) -->
                                    <div class="form-group signup-only" id="confirmPasswordGroup" <?php echo $is_signup_mode ? 'style="display: block;"' : ''; ?>>
                                        <label>Confirm Password</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control has-toggle" placeholder="Confirm Password" <?php echo $is_signup_mode ? 'required' : ''; ?>>
                                            <span class="password-toggle" onclick="togglePassword('confirm_password')" id="confirmPasswordToggle">
                                                <i class="fa fa-eye" id="confirmPasswordToggleIcon"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Remember me and Forgot password (login only) -->
                                    <div class="row remember-forgot login-only" id="rememberForgotRow" style="margin-top:6px; <?php echo $is_signup_mode ? 'display: none;' : ''; ?>">
                                        <div class="col-xs-6 form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="pl-3" id="rememberMe"> Remember me
                                            </label>
                                        </div>
                                          
                                        <div class="col-xs-9 text-right">
                                            <a href="forgot_password.php" class="f-w-600 text-gray forgot">Forgot Password?</a>
                                        </div>
                                    </div>

                                    <button style="background-color: #0d67cdff; margin-top:18px;" type="submit" name="submit" id="submitBtn" class="btn btn-info btn-flat btn-full" disabled>
                                        <i class="fa fa-sign-in" id="submitIcon"></i> <span id="submitText"><?php echo $is_signup_mode ? 'Create Account' : 'Login'; ?></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="./assets/js/lib/jquery/jquery.min.js"></script>
    <script src="./assets/js/lib/bootstrap/js/popper.min.js"></script>
    <script src="./assets/js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slimscroll.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="./assets/js/custom.min.js"></script>
    
    <script>
    // Password visibility toggle functionality
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(fieldId + 'ToggleIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.className = 'fa fa-eye-slash';
        } else {
            passwordField.type = 'password';
            toggleIcon.className = 'fa fa-eye';
        }
    }

    // Mode switching functionality
    function switchMode(mode) {
        // Update toggle visual state
        document.querySelectorAll('.toggle-option').forEach(opt => opt.classList.remove('active'));
        document.querySelector(`[onclick="switchMode('${mode}')"]`).classList.add('active');
        
        // Update hidden form mode
        document.getElementById('formMode').value = mode;
        
        // Show/hide signup-only fields
        const signupFields = document.querySelectorAll('.signup-only');
        const loginFields = document.querySelectorAll('.login-only');
        
        if (mode === 'signup') {
            signupFields.forEach(field => field.style.display = 'block');
            loginFields.forEach(field => field.style.display = 'none');
            document.getElementById('formTitle').textContent = 'Create Account';
            document.getElementById('formSubtitle').textContent = 'Sign up to get started';
            document.getElementById('submitText').textContent = 'Create Account';
            document.getElementById('submitIcon').className = 'fa fa-user-plus';
            
            // Add required attribute to signup fields
            document.getElementById('username').required = true;
            document.getElementById('phone').required = true;
            document.getElementById('role').required = true;
            document.getElementById('confirm_password').required = true;
        } else {
            signupFields.forEach(field => field.style.display = 'none');
            loginFields.forEach(field => field.style.display = 'block');
            document.getElementById('formTitle').textContent = 'Welcome back';
            document.getElementById('formSubtitle').textContent = 'Sign in to continue';
            document.getElementById('submitText').textContent = 'Login';
            document.getElementById('submitIcon').className = 'fa fa-sign-in';
            
            // Remove required attribute from signup fields
            document.getElementById('username').required = false;
            document.getElementById('phone').required = false;
            document.getElementById('role').required = false;
            document.getElementById('confirm_password').required = false;
        }
        
        // Reset password visibility for all password fields
        const passwordFields = ['password', 'confirm_password'];
        passwordFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + 'ToggleIcon');
            if (field && toggleIcon) {
                field.type = 'password';
                toggleIcon.className = 'fa fa-eye';
            }
        });
        
        // Clear form and revalidate
        document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"], select').forEach(input => {
            input.value = '';
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        // Call validation if it's available
        if (typeof window.validateForm === 'function') {
            window.validateForm();
        }
    }

    // Form validation
    (function(){
        var email = document.getElementById('email');
        var password = document.getElementById('password');
        var username = document.getElementById('username');
        var phone = document.getElementById('phone');
        var role = document.getElementById('role');
        var confirmPassword = document.getElementById('confirm_password');
        var btn = document.getElementById('submitBtn');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        
        function validate(){
            var mode = document.getElementById('formMode').value;
            var isValid = false;
            
            if (mode === 'signup') {
                // Signup validation
                var usernameValid = username.value.length >= 3;
                var emailValid = emailRegex.test(email.value);
                var phoneValid = phoneRegex.test(phone.value);
                var roleValid = role.value !== '';
                var passwordValid = password.value.length >= 6;
                var confirmPasswordValid = confirmPassword.value === password.value && confirmPassword.value.length > 0;
                
                isValid = usernameValid && emailValid && phoneValid && roleValid && passwordValid && confirmPasswordValid;
                
                // Visual feedback
                username.classList.toggle('is-valid', usernameValid);
                username.classList.toggle('is-invalid', !usernameValid && username.value.length > 0);
                
                phone.classList.toggle('is-valid', phoneValid);
                phone.classList.toggle('is-invalid', !phoneValid && phone.value.length > 0);
                
                role.classList.toggle('is-valid', roleValid);
                role.classList.toggle('is-invalid', !roleValid && role.value !== '');
                
                confirmPassword.classList.toggle('is-valid', confirmPasswordValid);
                confirmPassword.classList.toggle('is-invalid', !confirmPasswordValid && confirmPassword.value.length > 0);
                
                password.classList.toggle('is-valid', passwordValid);
                password.classList.toggle('is-invalid', !passwordValid && password.value.length > 0);
            } else {
                // Login validation
                isValid = emailRegex.test(email.value) && password.value.length > 0;
                
                password.classList.toggle('is-valid', password.value.length > 0);
                password.classList.toggle('is-invalid', password.value.length === 0 && document.activeElement === password);
            }
            
            // Common email validation
            email.classList.toggle('is-valid', emailRegex.test(email.value));
            email.classList.toggle('is-invalid', !emailRegex.test(email.value) && email.value.length > 0);
            
            btn.disabled = !isValid;
        }
        
        // Add event listeners
        email.addEventListener('input', validate);
        password.addEventListener('input', validate);
        username.addEventListener('input', validate);
        phone.addEventListener('input', validate);
        role.addEventListener('change', validate);
        confirmPassword.addEventListener('input', validate);
        
        // Initial validation
        validate();
        
        // Make validate function globally accessible for switchMode
        window.validateForm = validate;
    })();
    </script>
</body>

</html>