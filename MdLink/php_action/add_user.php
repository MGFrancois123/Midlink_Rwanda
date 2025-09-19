<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constant/connect.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

try {
    // Check if user is logged in and has super_admin role
    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'super_admin') {
        throw new Exception('Access denied. Only Super Administrators can add users.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input data
        $username = trim($connect->real_escape_string($_POST['username'] ?? ''));
        $email = trim($connect->real_escape_string($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = trim($connect->real_escape_string($_POST['phone'] ?? ''));
        $status = trim($connect->real_escape_string($_POST['status'] ?? 'active'));

        // Validation
        $errors = [];

        // Username validation
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters long';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }

        // Email validation
        if (empty($email)) {
            $errors[] = 'Email address is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }

        // Password validation
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        // Password confirmation validation
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }

        // Status validation
        if (!empty($status) && !in_array($status, ['active', 'disabled'])) {
            $errors[] = 'Invalid status selected';
        }

        // Phone validation (optional)
        if (!empty($phone) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $phone)) {
            $errors[] = 'Please enter a valid phone number';
        }


        // Check if username already exists
        if (empty($errors)) {
            $username_check = $connect->prepare("SELECT admin_id FROM admin_users WHERE username = ?");
            $username_check->bind_param("s", $username);
            $username_check->execute();
            $username_result = $username_check->get_result();
            
            if ($username_result->num_rows > 0) {
                $errors[] = 'Username already exists. Please choose a different username.';
            }
        }

        // Check if email already exists
        if (empty($errors)) {
            $email_check = $connect->prepare("SELECT admin_id FROM admin_users WHERE email = ?");
            $email_check->bind_param("s", $email);
            $email_check->execute();
            $email_result = $email_check->get_result();
            
            if ($email_result->num_rows > 0) {
                $errors[] = 'Email address already exists. Please use a different email.';
            }
        }


        // If there are validation errors, return them
        if (!empty($errors)) {
            throw new Exception(implode('. ', $errors));
        }

        // Start transaction
        $connect->autocommit(false);

        try {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $user_sql = "INSERT INTO admin_users (username, password_hash, email, phone, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $user_stmt = $connect->prepare($user_sql);
            
            if (!$user_stmt) {
                throw new Exception('Database error: ' . $connect->error);
            }

            $user_stmt->bind_param("sssss", $username, $password_hash, $email, $phone, $status);

            if (!$user_stmt->execute()) {
                throw new Exception('Failed to create user: ' . $user_stmt->error);
            }

            $user_id = $connect->insert_id;
            $user_stmt->close();

            // Log the user creation
            $log_sql = "INSERT INTO audit_logs (admin_id, table_name, record_id, action, new_data, action_time) VALUES (?, 'admin_users', ?, 'CREATE', ?, NOW())";
            $log_stmt = $connect->prepare($log_sql);
            
            if ($log_stmt) {
                $log_data = json_encode([
                    'username' => $username,
                    'email' => $email,
                    'status' => $status,
                    'phone' => $phone
                ]);
                $log_stmt->bind_param("iis", $_SESSION['userId'], $user_id, $log_data);
                $log_stmt->execute();
                $log_stmt->close();
            }

            // Commit transaction
            $connect->commit();
            $connect->autocommit(true);

            $response['success'] = true;
            $response['message'] = 'User created successfully!';
            $response['data'] = array(
                'user_id' => $user_id,
                'username' => $username,
                'email' => $email,
                'status' => $status
            );

        } catch (Exception $e) {
            // Rollback transaction
            $connect->rollback();
            $connect->autocommit(true);
            throw $e;
        }

    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Close database connection
$connect->close();

// Return JSON response
echo json_encode($response);
?>
