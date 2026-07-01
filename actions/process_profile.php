<?php
session_start();
require '../includes/db_connect.php';

// Security: Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../student_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Sanitize inputs
    $email   = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $dob     = $_POST['date_of_birth'] ?? '';

    // ✨ UPDATED: Nilagyan ng strict type safety handlers para sa blank entries (MySQL conversion fixes)
    $clean_contact = !empty($contact) ? $contact : null;
    $clean_dob     = !empty($dob) ? $dob : null;

    // Set up the base update query
    $update_query = "UPDATE Users SET Email = :email, ContactNumber = :contact, DateOfBirth = :dob";
    $params = [
        'email'   => $email,
        'contact' => $clean_contact,
        'dob'     => $clean_dob,
        'uid'     => $user_id
    ];

    // Profile Picture Upload Logic
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB max
        
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Invalid image type. Please upload a JPG, PNG, or WEBP.";
            header("Location: ../student/profile.php");
            exit();
        }
        
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "Image is too large. Maximum size is 5MB.";
            header("Location: ../student/profile.php");
            exit();
        }

        $target_dir = "../uploads/profiles/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = "USER_" . $user_id . "_PROFILE_" . time() . "." . $file_ext;
        $target_path = $target_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $update_query .= ", ProfilePicture = :pic";
            $params['pic'] = $target_path;
        } else {
            $_SESSION['error'] = "Failed to save the image to the server.";
            header("Location: ../student/profile.php");
            exit();
        }
    }

    $update_query .= " WHERE UserID = :uid";

    try {
        $stmt = $pdo->prepare($update_query);
        $stmt->execute($params);
        $_SESSION['success'] = "Profile updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Update failed: " . $e->getMessage();
    }
    
    header("Location: ../student/profile.php");
    exit();
}