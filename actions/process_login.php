<?php
session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Check which login portal is being used
    $login_type = $_POST['login_type'] ?? 'student';
    $error_redirect = ($login_type === 'admin') ? '../admin_login.php' : '../student_login.php';

    try {
        // Query the Users table
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // 1. Verify user and check password
        if ($user && password_verify($password, $user['PasswordHash'])) {
            
            // --- THE GATEKEEPER LOGIC ---
            
            // Prevent students from using the Admin Login page
            if ($login_type === 'admin' && $user['Role'] === 'Student') {
                $_SESSION['error'] = "Access denied. Students must use the Student Portal.";
                header("Location: " . $error_redirect);
                exit();
            }

            // Prevent staff from using the Student Login page
            if ($login_type === 'student' && $user['Role'] !== 'Student') {
                $_SESSION['error'] = "Staff accounts must use the Administrative Portal.";
                header("Location: " . $error_redirect);
                exit();
            }

            // --- SESSION INITIALIZATION ---
            // We use the exact casing from your DB columns
            $_SESSION['logged_in']  = true;
            $_SESSION['user_id']    = $user['UserID'];
            $_SESSION['username']   = $user['Username'];
            $_SESSION['role']       = $user['Role']; // Ensure this is 'Student', 'Internal_Admin', etc.
            $_SESSION['first_name'] = $user['FirstName'];
            $_SESSION['last_name']  = $user['LastName'];
            $_SESSION['email']      = $user['Email'];

            // --- REDIRECTION ---
            switch ($user['Role']) {
                case 'Student':
                    header("Location: ../student/dashboard.php");
                    exit();
                case 'Internal_Admin':
                    header("Location: ../internal_admin/dashboard.php");
                    exit();
                case 'External_Admin':
                    header("Location: ../external_admin/dashboard.php");
                    exit();
                case 'Super_Admin':
                    header("Location: ../super_admin/dashboard.php");
                    exit();
                default:
                    $_SESSION['error'] = "Account role not recognized.";
                    header("Location: " . $error_redirect);
                    exit();
            }

        } else {
            $_SESSION['error'] = "Invalid student number or password.";
            header("Location: " . $error_redirect);
            exit();
        }

    } catch (PDOException $e) {
        die("Login Error: " . $e->getMessage());
    }
} else {
    header("Location: ../student_login.php");
    exit();
}