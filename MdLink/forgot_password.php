<?php include('./constant/layout/head.php');?>
<link rel="stylesheet" href="assets/css/popup_style.css">
<style>
/* Center and style the forgot form; keep it responsive */
.unix-login .container-fluid { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px; }
.forgot-card { border: 0; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,.08); }
.forgot-card .login-form { padding: 28px; }
.forgot-title { text-align: center; margin-bottom: 6px; }
.forgot-subtitle { text-align: center; color: #777; margin-bottom: 18px; font-size: 13px; }
.input-group-addon { background: #f5f6f8; }
.btn-full { width: 100%; }
/* Title bar inside the card */
.title-bar { background:#28a745; color:#fff; padding:10px 16px; border-top-left-radius:10px; border-top-right-radius:10px; text-align:center; }
.title-bar h3 { margin:0; font-size:18px; font-weight:600; color:#fff; }
/* Icon span sizing for email input */
.icon-addon { width: 44px; height: 44px; display:flex; align-items:center; justify-content:center; padding:0; }
.icon-addon i { font-size:16px; color:#fff; }
.input-group .form-control { height: 44px; }
</style>

<div id="main-wrapper">
    <div class="unix-login">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-20 col-md-19 col-sm-12">
                    <div class="login-content card forgot-card">
                        <div class="card-title title-bar">
                            <h3 class="forgot-title">Forgot Password</h3>
                        </div>
                        <div class="login-form">
                            <div class="forgot-subtitle">Enter your account email and we will send you a reset link</div>
                            <form method="POST" action="php_action/request_password_reset.php" novalidate>
                                <div class="form-group">
                                    <label>Email address</label>
                                    <div class="input-group ">
                                        <span class="input-group-addon btn-primary bg-dark icon-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email" id="fp_email" class="form-control" placeholder="you@example.com" required>
                                    </div>
                                    <small id="fp_msg" class="text-danger" style="display:none;">Please enter a valid email</small>
                                </div>
                                <button type="submit" id="fp_submit" class="btn btn-primary btn-flat btn-full" disabled><i class="fa fa-paper-plane"></i> Send reset link</button>
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
(function() {
  var email = document.getElementById('fp_email');
  var submit = document.getElementById('fp_submit');
  var msg = document.getElementById('fp_msg');
  var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  function valid() {
    var ok = emailRegex.test(email.value);
    email.classList.toggle('is-valid', ok);
    email.classList.toggle('is-invalid', !ok && email.value.length > 0);
    msg.style.display = ok || email.value.length === 0 ? 'none' : 'block';
    submit.disabled = !ok;
  }
  email.addEventListener('input', valid);
  valid();
})();
</script>
</body>
</html>

