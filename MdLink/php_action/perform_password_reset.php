<?php
require_once '../constant/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Basic validation
    $lengthOk = strlen($password) >= 8;
    $upperOk = preg_match('/[A-Z]/', $password);
    $lowerOk = preg_match('/[a-z]/', $password);
    $numberOk = preg_match('/[0-9]/', $password);
    // Require at least one non-alphanumeric character to avoid complex escaping issues
    $specialOk = preg_match('/[^A-Za-z0-9]/', $password);

    if (
        $token === '' ||
        $password === '' ||
        $password !== $confirm ||
        !($lengthOk && $upperOk && $lowerOk && $numberOk && $specialOk)
    ) {
        header('Location: ../reset_password.php?token=' . urlencode($token));
        exit;
    }

    $tokenHash = hash('sha256', $token);
    $now = date('Y-m-d H:i:s');

    // Find matching token
    $stmt = $connect->prepare('SELECT user_id, user_type FROM reset_tokens WHERE token_hash = ? AND expires_at > ? LIMIT 1');
    $stmt->bind_param('ss', $tokenHash, $now);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows !== 1) {
        header('Location: ../login.php');
        exit;
    }
    $row = $res->fetch_assoc();
    $userId = (int)$row['user_id'];
    $userType = isset($row['user_type']) ? trim($row['user_type']) : 'user';

    // Hash new password with md5 to match existing login checks
    $newHash = md5($password);

    if ($userType === 'admin') {
        $upd = $connect->prepare('UPDATE admin_users SET password_hash = ? WHERE admin_id = ?');
        $upd->bind_param('si', $newHash, $userId);
    } else {
        $upd = $connect->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?');
        $upd->bind_param('si', $newHash, $userId);
    }
    $upd->execute();

    // Invalidate token
    $del = $connect->prepare('DELETE FROM reset_tokens WHERE user_id = ?');
    $del->bind_param('i', $userId);
    $del->execute();

    // Show success then redirect to login
    echo '<link rel="stylesheet" href="../assets/css/popup_style.css">';
    echo '<div class="popup popup--icon -success js_success-popup popup--visible">'
        .'<div class="popup__background"></div><div class="popup__content">'
        .'<h3 class="popup__content__title">Success</h3>'
        .'<p>Password has been reset successfully. Redirecting to login...</p>'
        .'<p><a href="../login.php"><button class="button button--success">Go to login</button></a></p>'
        .'</div></div>';
    echo '<script>setTimeout(function(){ window.location.href = "../login.php"; }, 2500);</script>';
    exit;
}

header('Location: ../login.php');
exit;