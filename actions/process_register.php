<?php
session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $student_number = trim($_POST['student_number'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $program_id     = $_POST['program_id'] ?? null;
    $year_level     = $_POST['year_level'] ?? ''; 
    $password       = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $gender         = trim($_POST['gender'] ?? 'Not Specified');
    
    // ✨ NEW: Sinasalo na ang dalawang bagong fields mula sa registration modal
    $contact_number = trim($_POST['contact_number'] ?? '');
    $date_of_birth  = trim($_POST['date_of_birth'] ?? '');

    if (empty($first_name) || empty($last_name) || empty($student_number) || empty($email) || empty($program_id) || empty($year_level) || empty($password)) {
        $_SESSION['error'] = "All fields (including Year Level) are required to create a student account.";
        header("Location: ../student_login.php"); exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match. Please try again.";
        header("Location: ../student_login.php"); exit();
    }

    // ==========================================
    // ✨ THE GLOBAL STRICT PASSWORD RULE
    // ==========================================
    $strict_password_enabled = true; 
    
    if ($strict_password_enabled) {
        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*()\-_=+ {};:,<.>]/', $password)) {
            $_SESSION['error'] = "Security Policy: Password must be at least 8 characters long and include at least one number and one symbol.";
            header("Location: ../student_login.php"); 
            exit(); 
        }
    }

    try {
        $check_stmt = $pdo->prepare("SELECT UserID FROM Users WHERE StudentID_Num = :snum OR Email = :email");
        $check_stmt->execute(['snum' => $student_number, 'email' => $email]);
        
        if ($check_stmt->fetch()) {
            $_SESSION['error'] = "This student number or email is already linked to an account.";
            header("Location: ../student_login.php"); exit();
        }

        $prog_stmt = $pdo->prepare("SELECT ProgramName FROM Program WHERE ProgramID = :pid");
        $prog_stmt->execute(['pid' => $program_id]);
        $program_name = $prog_stmt->fetchColumn();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ✨ UPDATED: Dinagdagan ang ContactNumber at DateOfBirth sa data storage columns
        $insert_query = "
            INSERT INTO Users (Username, PasswordHash, Email, FirstName, LastName, StudentID_Num, Major, ProgramID, YearLevel, Role, GPA, Gender, ContactNumber, DateOfBirth) 
            VALUES (:username, :phash, :email, :fname, :lname, :snum, :major, :pid, :year_level, 'Student', 0.00, :gender, :contact_number, :date_of_birth)
        ";

        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->execute([
            'username'       => $student_number, 
            'phash'          => $hashed_password,
            'email'          => $email,
            'fname'          => $first_name,
            'lname'          => $last_name,
            'snum'           => $student_number,
            'major'          => $program_name,
            'pid'            => $program_id,
            'year_level'     => $year_level,
            'gender'         => $gender,
            'contact_number' => !empty($contact_number) ? $contact_number : null, // Ginagawang null kung blanko
            'date_of_birth'  => !empty($date_of_birth) ? $date_of_birth : null     // Ginagawang null kung blanko
        ]);

        $new_user_id = $pdo->lastInsertId(); 
        $log_stmt = $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, 'Account Created', NOW(), 'A new student account was registered via the portal.', ?)");
        $log_stmt->execute([$new_user_id, $_SERVER['REMOTE_ADDR']]);

        $_SESSION['success'] = "Registration successful! You can now log in.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "System Error: " . $e->getMessage();
    }
    header("Location: ../student_login.php");
    exit();
}