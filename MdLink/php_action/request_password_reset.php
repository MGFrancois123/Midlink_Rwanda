<?php
require_once '../constant/connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generateSecureToken($lengthBytes = 16) {
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($lengthBytes));
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($lengthBytes));
    }
    $token = '';
    for ($i = 0; $i < $lengthBytes; $i++) {
        $token .= chr(mt_rand(0, 255));
    }
    return bin2hex($token);
}

// Support password reset for both admin_users and users in mdlink
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($email === '') {
        header('Location: ../forgot_password.php');
        exit;
    }

    // Try admin_users first
    $userId = 0; $userType = '';
    if ($adm = $connect->prepare('SELECT admin_id FROM admin_users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1')) {
        $adm->bind_param('s', $email);
        $adm->execute();
        $adm->store_result();
        if ($adm->num_rows === 1) {
            $adm->bind_result($adminId);
            $adm->fetch();
            $userId = (int)$adminId; $userType = 'admin';
        }
    }
    // If not admin, try regular users
    if ($userId === 0) {
        if ($stmt = $connect->prepare('SELECT user_id FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1')) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($uid);
                $stmt->fetch();
                $userId = (int)$uid; $userType = 'user';
            }
        }
    }

    if ($userId > 0) {

        // Generate token and expiry (1 hour)
        $token = generateSecureToken(16);
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        // Ensure reset_tokens table exists (id, user_id, token_hash, expires_at)
        $connect->query('CREATE TABLE IF NOT EXISTS reset_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_type VARCHAR(10) NOT NULL DEFAULT "user",
            token_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(user_id), INDEX(token_hash)
        )');
        // Ensure user_type column exists on older installs
        $colExists = $connect->query("SHOW COLUMNS FROM reset_tokens LIKE 'user_type'");
        if ($colExists && $colExists->num_rows === 0) {
            $connect->query('ALTER TABLE reset_tokens ADD COLUMN user_type VARCHAR(10) NOT NULL DEFAULT "user"');
        }

        // Upsert token for this user (delete previous tokens)
        if ($del = $connect->prepare('DELETE FROM reset_tokens WHERE user_id = ?')) {
            $del->bind_param('i', $userId);
            $del->execute();
        }

        if ($ins = $connect->prepare('INSERT INTO reset_tokens (user_id, user_type, token_hash, expires_at) VALUES (?, ?, ?, ?)')) {
            $ins->bind_param('isss', $userId, $userType, $tokenHash, $expiresAt);
            $ins->execute();
        }

        // Build absolute reset URL
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $basePath = rtrim(dirname(dirname($_SERVER['REQUEST_URI'])), '/\\');
        $resetUrl = $scheme . '://' . $host . $basePath . '/reset_password.php?token=' . $token;

        // Try to send via configured mailer
        $sent = false;
        $sendError = '';

        // Default mail settings (can be overridden from DB)
        $mailSettings = [
            'host' => 'smtp.gmail.com',
            'name' => 'MIDILINK RWANDA',
            'username' => 'nzamurambahofrederic28@gmail.com',
            'password' => str_replace(' ', '', 'mcoq fvim sueu nzdk'),
            'port' => 587,
            'secure' => 'tls'
        ];
        // If email_config exists, override defaults (ignore if table missing)
        if (file_exists('../constant/connect1.php')) {
            include '../constant/connect1.php'; // defines $conn
            if (isset($conn)) {
                try {
                    $tbl = $conn->query("SHOW TABLES LIKE 'tbl_email_config'");
                    if ($tbl && $tbl->num_rows > 0) {
                        $cfg = $conn->query('SELECT * FROM tbl_email_config LIMIT 1');
                        if ($cfg && $cfg->num_rows) {
                            $rowCfg = $cfg->fetch_assoc();
                            $mailSettings['host'] = $rowCfg['mail_driver_host'] ?: $mailSettings['host'];
                            $mailSettings['name'] = $rowCfg['name'] ?: $mailSettings['name'];
                            $mailSettings['username'] = $rowCfg['mail_username'] ?: $mailSettings['username'];
                            $mailSettings['password'] = $rowCfg['mail_password'] ?: $mailSettings['password'];
                            $mailSettings['port'] = (int)($rowCfg['mail_port'] ?: $mailSettings['port']);
                            $mailSettings['secure'] = $rowCfg['mail_encrypt'] ?: $mailSettings['secure'];
                        }
                    }
                } catch (\Throwable $e) {
                    // Optional config: safely ignore if table/database is missing
                }
            }
        }

        // Prefer Composer-installed PHPMailer (vendor) if available
        if (file_exists('../../vendor/autoload.php')) {
            require_once '../../vendor/autoload.php';
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'error_log';
                $mail->Host = $mailSettings['host'];
                $mail->SMTPAuth = true;
                $mail->SMTPAutoTLS = true;
                $mail->Username = $mailSettings['username'];
                $mail->Password = $mailSettings['password'];
                $mail->SMTPSecure = $mailSettings['secure'];
                $mail->Port = $mailSettings['port'];
                $mail->setFrom($mailSettings['username'], $mailSettings['name']);
                $mail->addAddress($email);
                $mail->Subject = 'Password reset';
                $mail->Body = "Click the link to reset your password: $resetUrl";
                $mail->send();
                $sent = true;
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                $sendError = method_exists($mail, 'ErrorInfo') && $mail->ErrorInfo ? $mail->ErrorInfo : $e->getMessage();
            }
        } elseif (file_exists('../constant/PHPMailer/PHPMailerAutoload.php')) {
            // Legacy bundled PHPMailer (if present)
            require_once '../constant/PHPMailer/PHPMailerAutoload.php';
            $mail = new \PHPMailer\PHPMailer\PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'error_log';
            $mail->Host = $mailSettings['host'];
            $mail->SMTPAuth = true;
            $mail->SMTPAutoTLS = true;
            $mail->Username = $mailSettings['username'];
            $mail->Password = $mailSettings['password'];
            $mail->SMTPSecure = $mailSettings['secure'];
            $mail->Port = $mailSettings['port'];
            $mail->setFrom($mailSettings['username'], $mailSettings['name']);
            $mail->addAddress($email);
            $mail->Subject = 'Password reset';
            $mail->Body = "Click the link to reset your password: $resetUrl";
            $sent = $mail->send();
            if (!$sent) {
                $sendError = $mail->ErrorInfo;
            }
        } else {
            // Fallback to PHP mail()
            $subject = 'Password reset';
            $message = "Click the link to reset your password: $resetUrl";
            $headers = 'From: MIDILINK RWANDA <noreply@localhost>' . "\r\n" .
                       'Reply-To: noreply@localhost' . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();
            if (@mail($email, $subject, $message, $headers)) {
                $sent = true;
            } else {
                $sendError = 'PHPMailer not found and PHP mail() failed. Please install dependencies via Composer or configure SMTP for PHP mail.';
            }
        }

        // Show a user message (no link on screen)
        echo '<link rel="stylesheet" href="../assets/css/popup_style.css">';
        if ($sent) {
            echo '<div class="popup popup--icon -success js_success-popup popup--visible"><div class="popup__background"></div><div class="popup__content">';
            echo '<h3 class="popup__content__title">Success</h3>';
            echo '<p>Reset link sent to your email. Please check your inbox.</p>';
            echo '<p><a href="../login.php"><button class="button button--success">OK</button></a></p>';
            echo '</div></div>';
            exit;
        } else {
            echo '<div class="popup popup--icon -error js_error-popup popup--visible"><div class="popup__background"></div><div class="popup__content">';
            echo '<h3 class="popup__content__title">Email not sent</h3>';
            echo '<p>' . htmlspecialchars($sendError, ENT_QUOTES) . '</p>';
            echo '<p>Please verify Gmail App Password (no spaces), OpenSSL enabled, and SMTP (587/tls) accessible.</p>';
            echo '<p><a href="../forgot_password.php"><button class="button button--error">Back</button></a></p>';
            echo '</div></div>';
            exit;
        }
    }

    header('Location: ../login.php');
    exit;
}

header('Location: ../forgot_password.php');
exit;


