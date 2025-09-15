<?php ob_start(); ?>
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
   <?php
   
include('./constant/layout/head.php');
  include('./constant/connect.php');
  // Single connection (mdlink) loaded via connect.php
session_start();

// Already logged in: prefer adminId (new). Fallback to legacy userId.
if(isset($_SESSION['adminId']) || isset($_SESSION['userId'])) {
  if (!isset($_SESSION['adminId']) && isset($_SESSION['userId'])) {
    $_SESSION['adminId'] = (int)$_SESSION['userId'];
  }
  if (isset($_SESSION['userRole'])) {
    // All users go to super admin dashboard
    header('Location: dashboard_super.php');
  }
  exit;
}

$errors = array();

if($_POST) {    

  $email = $_POST['email'];
  $password = $_POST['password'];

  if(empty($email) || empty($password)) {
    if($email == "") {
      $errors[] = "email is required";
    } 

    if($password == "") {
      $errors[] = "Password is required";
    }
  } else {
    // 1) Try mdlink admin_users first (super_admin, pharmacy_admin, finance_admin)
    $isAdminAuthed = false;
    if (isset($connect) && $connect instanceof mysqli) {
      $given = trim($password);
      
      // Use prepared statement to prevent SQL injection
      $adminSql = "SELECT admin_id, username, password_hash, role 
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
          $md5Given = md5($given);
          
          // Primary: MD5 check. Compatibility: accept bcrypt verify or legacy plaintext if still present
          $passOk = ($stored === $md5Given) 
                    || (function_exists('password_verify') && password_verify($given, $stored))
                    || ($stored === $given);
          
          if ($passOk) {
            // Normalize new session key and keep legacy for compatibility
            $_SESSION['adminId'] = (int)$admin['admin_id'];
            $_SESSION['userId'] = (int)$admin['admin_id'];
            $_SESSION['userRole'] = 'super_admin'; // Force all users to super_admin
            $_SESSION['username'] = $admin['username'] ?? 'Admin';
            
            // Redirect to super admin dashboard
            header('Location: dashboard_super.php');
            exit;
          }
        }
        $stmt->close();
      }
    }

    // Only admin logins are allowed here; no fallback to general users

    ?>


    <div class="alert alert-danger" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px; text-align: center;">
      <strong>Login Failed!</strong><br>
      Incorrect email/password combination. Please try again.
      <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
       
    <?php 
  } // /else not empty email // password
  
} // /if $_POST

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
                                <h3 class="login-title">Welcome back</h3>
                                <div class="login-subtitle">Sign in to continue</div>
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="loginForm">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="input-group">
                                            <span class="input-group-addon "><i class="fa fa-lock"></i></span>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                                        </div>
                                    </div>
                                    <div class="row remember-forgot" style="margin-top:6px;">
                                        <div class="col-xs-6 form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="pl-3" id="rememberMe"> Remember me
                                            </label>
                                        </div>
                                          
                                        <div class="col-xs-9 text-right">
                                            <a href="forgot_password.php" class="f-w-600 text-gray forgot">Forgot Password?</a>
                                        </div>
                                    </div>
                                    <button style="background-color: #0d67cdff; margin-top:18px;" type="submit" name="login" id="loginBtn" class="btn btn-info btn-flat btn-full" disabled>
                                        <i class="fa fa-sign-in"></i> Login
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
    (function(){
        var email = document.getElementById('email');
        var password = document.getElementById('password');
        var btn = document.getElementById('loginBtn');
        var toggle = document.getElementById('togglePw');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        function validate(){
            var ok = emailRegex.test(email.value) && password.value.length > 0;
            email.classList.toggle('is-valid', emailRegex.test(email.value));
            email.classList.toggle('is-invalid', !emailRegex.test(email.value) && email.value.length>0);
            password.classList.toggle('is-valid', password.value.length>0);
            password.classList.toggle('is-invalid', password.value.length===0 && document.activeElement===password);
            btn.disabled = !ok;
        }
        email.addEventListener('input', validate);
        password.addEventListener('input', validate);
        validate();
        if (toggle) {
            toggle.addEventListener('click', function(){
                var type = password.type === 'password' ? 'text' : 'password';
                password.type = type;
                this.firstElementChild.className = type === 'password' ? 'fa fa-eye' : 'fa fa-eye-slash';
            });
        }
    })();
    </script>
</body>

</html>
