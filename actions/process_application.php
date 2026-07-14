<?php
require '../includes/session_manager.php';
require '../includes/db_connect.php';

// 1. SECURITY & ROLE CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../student_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scholarship_id'])) {
    $user_id = $_SESSION['user_id'];
    $scholarship_id = (int)$_POST['scholarship_id'];

    try {
        $pdo->beginTransaction();

        // 2. FETCH STUDENT DETAILS (GWA & YearLevel)
        $user_stmt = $pdo->prepare("SELECT GPA, YearLevel FROM users WHERE UserID = ?");
        $user_stmt->execute([$user_id]);
        $student = $user_stmt->fetch(PDO::FETCH_ASSOC);
        
        $student_gwa = $student['GPA'] ?? 5.00;
        $student_year = $student['YearLevel'] ?? 'Not Specified';

        // 3. CREATE THE APPLICATION RECORD
        $app_stmt = $pdo->prepare("INSERT INTO application (ScholarshipID, UserID, Status, GPA, YearLevel) VALUES (?, ?, 'Submitted', ?, ?)");
        $app_stmt->execute([$scholarship_id, $user_id, $student_gwa, $student_year]);
        
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
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $doc_insert_stmt = $pdo->prepare("INSERT INTO submitted_document (ApplicationID, RequirementID, FilePath, VerificationStatus) VALUES (?, ?, ?, 'Pending')");
        foreach ($requirements as $req) {
            $doc_name = $req['DocumentName'];
            if (isset($vault_docs[$doc_name])) {
                $vault_file_path = $vault_docs[$doc_name];
                
                if (file_exists($vault_file_path)) {
                    $file_ext = pathinfo($vault_file_path, PATHINFO_EXTENSION);
                    $new_file_name = "STU-" . $user_id . "-APP-" . $application_id . "-REQ-" . $req['RequirementID'] . "-" . uniqid() . "." . $file_ext;
                    $final_file_path = $target_dir . $new_file_name;

                    if (copy($vault_file_path, $final_file_path)) {
                        $doc_insert_stmt->execute([$application_id, $req['RequirementID'], $final_file_path]);
                    }
                }
            }
        }

        $pdo->commit();

        // 7. SUCCESS REDIRECT
        $_SESSION['success'] = "Application submitted successfully! Your vault documents have been automatically attached.";
        header("Location: ../student/applications.php");
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
        header("Location: ../scholarship_details.php?id=" . $scholarship_id);
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "An unexpected error occurred: " . $e->getMessage();
        header("Location: ../scholarship_details.php?id=" . $scholarship_id);
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
