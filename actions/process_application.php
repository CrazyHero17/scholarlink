<?php
session_start(); // ✨ ETO ANG SUSI NA NAKALIMUTAN NATIN!

// ✨ Hard Error Reporter
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/session_manager.php';
require '../includes/db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    die("Hindi ka naka-login as student.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scholarship_id'])) {
    $user_id = $_SESSION['user_id'];
    $scholarship_id = (int)$_POST['scholarship_id'];

    try {
        // 2. FETCH STUDENT GPA
        $user_stmt = $pdo->prepare("SELECT GPA FROM users WHERE UserID = ?");
        $user_stmt->execute([$user_id]);
        $student = $user_stmt->fetch(PDO::FETCH_ASSOC);
        $student_gwa = $student['GPA'] ?? 5.00;

        // 3. CREATE APPLICATION
        $app_stmt = $pdo->prepare("
            INSERT INTO application (ScholarshipID, UserID, Status, GPA, DateSubmitted) 
            VALUES (?, ?, 'Submitted', ?, NOW())
        ");
        $app_stmt->execute([$scholarship_id, $user_id, $student_gwa]);
        $application_id = $pdo->lastInsertId();

        // 4. FETCH SCHOLARSHIP REQUIREMENTS
        $req_stmt = $pdo->prepare("SELECT RequirementID, DocumentName FROM document_requirement WHERE ScholarshipID = ?");
        $req_stmt->execute([$scholarship_id]);
        $requirements = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. FETCH STUDENT'S VAULT DOCUMENTS
        $vault_stmt = $pdo->prepare("SELECT DocumentType, FilePath FROM user_vault WHERE UserID = ?");
        $vault_stmt->execute([$user_id]);
        $vault_docs = [];
        while ($row = $vault_stmt->fetch(PDO::FETCH_ASSOC)) {
            $vault_docs[$row['DocumentType']] = $row['FilePath'];
        }

        // 6. THE 1-CLICK COPY-PASTE MAGIC
        $target_dir = "../uploads/documents/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $doc_insert_stmt = $pdo->prepare("INSERT INTO submitted_document (ApplicationID, RequirementID, FilePath, VerificationStatus) VALUES (?, ?, ?, 'Pending')");

        foreach ($requirements as $req) {
            $doc_name = $req['DocumentName'];
            
            if (isset($vault_docs[$doc_name]) && file_exists($vault_docs[$doc_name])) {
                $vault_file_path = $vault_docs[$doc_name];
                $file_ext = pathinfo($vault_file_path, PATHINFO_EXTENSION);
                
                // Gawa ng bagong filename at i-copy
                $new_file_name = "STU-" . $user_id . "-APP-" . $application_id . "-REQ-" . $req['RequirementID'] . "-" . uniqid() . "." . $file_ext;
                $final_file_path = $target_dir . $new_file_name;

                if (copy($vault_file_path, $final_file_path)) {
                    $doc_insert_stmt->execute([$application_id, $req['RequirementID'], $final_file_path]);
                }
            }
        }

        // 7. SUCCESS REDIRECT
        $_SESSION['success'] = "⚡ 1-Click Application submitted successfully!";
        header("Location: ../student/applications.php");
        exit();

    } catch (PDOException $e) {
        die("<div style='padding: 30px; font-family: sans-serif; background-color: #fee2e2; color: #991b1b;'>
                <h2>🚨 Database Error sa 1-Click Apply!</h2>
                <p><strong>Message:</strong> " . $e->getMessage() . "</p>
             </div>");
    }
} else {
    die("Invalid Request.");
}
?>