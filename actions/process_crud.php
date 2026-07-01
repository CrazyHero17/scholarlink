<?php
session_start();
require '../includes/db_connect.php';

// Using require_once to ensure the function is defined and paths are correct
if (file_exists('../includes/email_config.php')) {
    require_once '../includes/email_config.php';
}

// Security: Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../admin_login.php");
    exit();
}

$module = $_POST['module'] ?? '';
$action = $_POST['action'] ?? '';

// ==========================================
// MODULE: STUDENT APPLICATIONS (100% DYNAMIC)
// ==========================================
if ($module === 'student_apply') {
    $user_id = $_SESSION['user_id'];

   // --- ACTION: CREATE ---
    if ($action === 'create') {
        $scholarship_id = $_POST['scholarship_id'];
        $gpa = $_POST['gpa'] ?? 0;

        try {
            // ✨ STRICT DUAL SCHOLARSHIP CHECK (Backend Protection)
            $check_approved = $pdo->prepare("SELECT COUNT(*) FROM application WHERE UserID = ? AND Status = 'Approved'");
            $check_approved->execute([$user_id]);
            
            if ($check_approved->fetchColumn() > 0) {
                // IBABLOCK NATIN SILA AGAD KAPAG SCHOLAR NA
                $_SESSION['error'] = "Action Denied: You are already an active scholar. Multiple scholarships are strictly prohibited.";
                header("Location: ../student/programs.php");
                exit();
            }

            // Check if already applied to this specific one
            $check = $pdo->prepare("SELECT COUNT(*) FROM Application WHERE UserID = ? AND ScholarshipID = ?");
            $check->execute([$user_id, $scholarship_id]);
            
            if ($check->fetchColumn() > 0) {
                $_SESSION['error'] = "You have already applied for this scholarship.";
            } else {
                // 1. Insert Base Application Data
                $sql = "INSERT INTO Application (UserID, ScholarshipID, Status, DateSubmitted, GPA) 
                        VALUES (:uid, :sid, 'Submitted', NOW(), :gpa)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['uid' => $user_id, 'sid' => $scholarship_id, 'gpa' => $gpa]);
                
                $new_app_id = $pdo->lastInsertId();

                // ✨ 2. SAVE DYNAMIC CUSTOM ANSWERS
                if (isset($_POST['custom_answers']) && is_array($_POST['custom_answers'])) {
                    $ans_stmt = $pdo->prepare("INSERT INTO application_custom_answers (ApplicationID, FieldID, AnswerText) VALUES (?, ?, ?)");
                    foreach ($_POST['custom_answers'] as $fieldId => $answerText) {
                        $ans_stmt->execute([$new_app_id, $fieldId, $answerText]);
                    }
                }

                // ✨ 3. SAVE UPLOADED / VAULT DOCUMENTS
                $upload_dir = '../uploads/documents/';
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

                // A. Handling Direct PC Uploads
                if (isset($_FILES['new_docs'])) {
                    foreach ($_FILES['new_docs']['tmp_name'] as $req_id => $tmp_name) {
                        if ($_FILES['new_docs']['error'][$req_id] === UPLOAD_ERR_OK) {
                            $file_name = $_FILES['new_docs']['name'][$req_id];
                            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                            
                            $new_file_name = "STU-" . $user_id . "-APP-" . $new_app_id . "-REQ-" . $req_id . "-" . uniqid() . "." . $file_ext;
                            $destination = $upload_dir . $new_file_name;

                            if (move_uploaded_file($tmp_name, $destination)) {
                                $ins_doc = $pdo->prepare("INSERT INTO submitted_document (ApplicationID, RequirementID, FilePath, VerificationStatus) VALUES (?, ?, ?, 'Pending')");
                                $ins_doc->execute([$new_app_id, $req_id, $destination]);
                            }
                        }
                    }
                }

                // B. Handling Vault Documents
                if (isset($_POST['vault_docs']) && is_array($_POST['vault_docs'])) {
                    foreach ($_POST['vault_docs'] as $req_id => $vault_id) {
                        if (!empty($vault_id)) {
                            $v_stmt = $pdo->prepare("SELECT FilePath FROM user_vault WHERE VaultID = ? AND UserID = ?");
                            $v_stmt->execute([$vault_id, $user_id]);
                            $vault_file = $v_stmt->fetch();
                            
                            if ($vault_file) {
                                $ins_doc = $pdo->prepare("INSERT INTO submitted_document (ApplicationID, RequirementID, FilePath, VerificationStatus) VALUES (?, ?, ?, 'Pending')");
                                $ins_doc->execute([$new_app_id, $req_id, $vault_file['FilePath']]);
                            }
                        }
                    }
                }

                $_SESSION['success'] = "Application submitted successfully!";
            }
        } catch (PDOException $e) { $_SESSION['error'] = "Application Error: " . $e->getMessage(); }
        
        header("Location: ../student/applications.php");
        exit();
    }

    // --- ACTION: UPDATE ---
    if ($action === 'update') {
        $app_id = $_POST['application_id'];
        $gpa    = $_POST['gpa'] ?? 0;

        try {
            // 1. Update Base Application Data
            $stmt = $pdo->prepare("UPDATE Application SET GPA = :gpa WHERE ApplicationID = :aid AND UserID = :uid AND Status = 'Submitted'");
            $stmt->execute(['gpa' => $gpa, 'aid' => $app_id, 'uid' => $user_id]);

            // 2. UPDATE DYNAMIC CUSTOM ANSWERS
            if (isset($_POST['custom_answers']) && is_array($_POST['custom_answers'])) {
                // Delete old answers and insert new ones
                $del_ans = $pdo->prepare("DELETE FROM application_custom_answers WHERE ApplicationID = ?");
                $del_ans->execute([$app_id]);

                $ans_stmt = $pdo->prepare("INSERT INTO application_custom_answers (ApplicationID, FieldID, AnswerText) VALUES (?, ?, ?)");
                foreach ($_POST['custom_answers'] as $fieldId => $answerText) {
                    $ans_stmt->execute([$app_id, $fieldId, $answerText]);
                }
            }

            // ✨ 3. NEW: HANDLE DOCUMENT UPLOADS FROM THE EDIT MODAL
            if (isset($_FILES['files'])) {
                $upload_dir = '../uploads/documents/';
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

                foreach ($_FILES['files']['tmp_name'] as $req_id => $tmp_name) {
                    if ($_FILES['files']['error'][$req_id] === UPLOAD_ERR_OK) {
                        $file_name = $_FILES['files']['name'][$req_id];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        // Force a clean, unique file name to avoid overlaps
                        $new_file_name = "STU-" . $user_id . "-APP-" . $app_id . "-REQ-" . $req_id . "-" . uniqid() . "." . $file_ext;
                        $destination = $upload_dir . $new_file_name;

                        if (move_uploaded_file($tmp_name, $destination)) {
                            // Check if this document requirement already has an uploaded file
                            $check_doc = $pdo->prepare("SELECT SubmittedDocID, FilePath FROM submitted_document WHERE ApplicationID = ? AND RequirementID = ?");
                            $check_doc->execute([$app_id, $req_id]);
                            $existing = $check_doc->fetch();

                            if ($existing) {
                                // Delete old physical file if it exists
                                if (file_exists($existing['FilePath'])) unlink($existing['FilePath']);
                                
                                // Update DB record
                                $upd_doc = $pdo->prepare("UPDATE submitted_document SET FilePath = ?, VerificationStatus = 'Pending', UploadDate = NOW() WHERE SubmittedDocID = ?");
                                $upd_doc->execute([$destination, $existing['SubmittedDocID']]);
                            } else {
                                // Insert new DB record
                                $ins_doc = $pdo->prepare("INSERT INTO submitted_document (ApplicationID, RequirementID, FilePath, VerificationStatus) VALUES (?, ?, ?, 'Pending')");
                                $ins_doc->execute([$app_id, $req_id, $destination]);
                            }
                        }
                    }
                }
            }

            $_SESSION['success'] = "Application updated successfully!";
        } catch (PDOException $e) { 
            $_SESSION['error'] = "Update Error: " . $e->getMessage(); 
        }
        
        header("Location: ../student/applications.php");
        exit();
    }
}

// ==========================================
// MODULE: STUDENT DOCUMENT UPLOADS
// ==========================================
if ($module === 'student_documents') {
    
    // --- ACTION: UPLOAD / REPLACE ---
    if ($action === 'upload') {
        $application_id = $_POST['application_id'];
        $requirement_id = $_POST['requirement_id'];
        $user_id = $_SESSION['user_id'];
        
        $upload_dir = '../uploads/documents/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['document']['tmp_name'];
            $file_name = $_FILES['document']['name'];
            $file_size = $_FILES['document']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array($file_ext, $allowed_exts)) {
                $_SESSION['error'] = "Invalid file type. Only PDF, JPG, and PNG are allowed.";
                header("Location: ../student/requirements.php"); exit();
            }

            if ($file_size > 5 * 1024 * 1024) {
                $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
                header("Location: ../student/requirements.php"); exit();
            }

            $new_file_name = "APP" . $application_id . "_REQ" . $requirement_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                try {
                    $check_stmt = $pdo->prepare("SELECT SubmittedDocID, FilePath FROM submitted_document WHERE ApplicationID = ? AND RequirementID = ?");
                    $check_stmt->execute([$application_id, $requirement_id]);
                    $existing_doc = $check_stmt->fetch();

                    if ($existing_doc) {
                        if (file_exists($existing_doc['FilePath'])) { unlink($existing_doc['FilePath']); }
                        
                        $update_stmt = $pdo->prepare("UPDATE submitted_document SET FilePath = ?, VerificationStatus = 'Pending', UploadDate = NOW() WHERE SubmittedDocID = ?");
                        $update_stmt->execute([$destination, $existing_doc['SubmittedDocID']]);
                        $_SESSION['success'] = "Document successfully replaced! It is now pending review.";
                    } else {
                        $insert_stmt = $pdo->prepare("INSERT INTO submitted_document (ApplicationID, RequirementID, FilePath, VerificationStatus) VALUES (?, ?, ?, 'Pending')");
                        $insert_stmt->execute([$application_id, $requirement_id, $destination]);
                        $_SESSION['success'] = "Document uploaded successfully! It is now pending review.";
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database Error: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Failed to move uploaded file to server.";
            }
        } else {
            $_SESSION['error'] = "Please select a valid file to upload.";
        }
        
        header("Location: ../student/requirements.php");
        exit();
    }

    // --- ACTION: DELETE ---
    if ($action === 'delete') {
        $doc_id = $_POST['doc_id'];

        try {
            $stmt = $pdo->prepare("SELECT FilePath, VerificationStatus FROM submitted_document WHERE SubmittedDocID = ?");
            $stmt->execute([$doc_id]);
            $doc = $stmt->fetch();

            if ($doc) {
                if ($doc['VerificationStatus'] === 'Verified') {
                    $_SESSION['error'] = "Action Denied: You cannot delete a document after it has been verified by the university.";
                } else {
                    if (file_exists($doc['FilePath'])) {
                        unlink($doc['FilePath']);
                    }
                    $del_stmt = $pdo->prepare("DELETE FROM submitted_document WHERE SubmittedDocID = ?");
                    $del_stmt->execute([$doc_id]);
                    $_SESSION['success'] = "Document was successfully deleted.";
                }
            } else {
                $_SESSION['error'] = "Document not found.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
        header("Location: ../student/requirements.php");
        exit();
    }
}

// ==========================================
// MODULE: EXTERNAL ADMIN SCORING
// ==========================================
if ($module === 'applications' && $action === 'score') {
    $application_id = $_POST['id'];
    $total_score = $_POST['score'];

    try {
        $stmt = $pdo->prepare("
            UPDATE application 
            SET TotalScore = :score, 
                Status = 'Shortlisted' 
            WHERE ApplicationID = :id
        ");
        $stmt->execute(['score' => $total_score, 'id' => $application_id]);
        
        // ✨ NEW: NOTIFY THE STUDENT!
        $info_stmt = $pdo->prepare("SELECT a.UserID, sch.Name FROM application a JOIN scholarship sch ON a.ScholarshipID = sch.ScholarshipID WHERE a.ApplicationID = ?");
        $info_stmt->execute([$application_id]);
        $info = $info_stmt->fetch();
        if ($info) {
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (UserID, Title, Message, Type) VALUES (?, ?, ?, ?)");
            $notif_stmt->execute([
                $info['UserID'],
                "Application Evaluated! ✍️",
                "Your application for the " . $info['Name'] . " has been officially scored and Shortlisted by an evaluator.",
                "info"
            ]);
        }

        $_SESSION['success'] = "Application successfully scored ($total_score/100) and Shortlisted!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
    }
    header("Location: ../external_admin/score.php");
    exit();
}

// ==========================================
// MODULE: STATUS UPDATES (INTERNAL ADMIN)
// ==========================================
if ($module === 'applications' && $action === 'update_status') {
    $app_id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? '';
    
    if ($app_id && !empty($status)) {
        try {
            $stmt = $pdo->prepare("UPDATE Application SET Status = :status WHERE ApplicationID = :id");
            $stmt->execute(['status' => $status, 'id' => $app_id]);

            $info_stmt = $pdo->prepare("
                SELECT u.Email, u.FirstName, sch.Name AS scholarship_name, sch.ScholarshipID, sch.NumberOfSlots, sch.CreatedBy, a.UserID 
                FROM Application a
                JOIN Users u ON a.UserID = u.UserID
                JOIN Scholarship sch ON a.ScholarshipID = sch.ScholarshipID
                WHERE a.ApplicationID = :aid
            ");
            $info_stmt->execute(['aid' => $app_id]);
            $info = $info_stmt->fetch();

            if ($info) {
                // Email Notification
                if (!empty($info['Email'])) {
                    sendScholarshipEmail($info['Email'], $info['FirstName'], $info['scholarship_name'], $status);
                }

                // In-App Notification
                try {
                    $notif_title = "Status Update: " . $status;
                    $notif_msg = "Your application for the " . $info['scholarship_name'] . " has been updated to: " . $status . ".";
                    $notif_type = ($status === 'Rejected') ? 'danger' : (($status === 'Approved') ? 'success' : 'info');
                    
                    $notif_stmt = $pdo->prepare("INSERT INTO notifications (UserID, Title, Message, Type) VALUES (:uid, :title, :msg, :type)");
                    $notif_stmt->execute(['uid' => $info['UserID'], 'title' => $notif_title, 'msg' => $notif_msg, 'type' => $notif_type]);
                } catch (PDOException $e) {
                    error_log("Notification Error: " . $e->getMessage());
                }

                // ✨ AUTO-DEACTIVATION CHECK
                if ($status === 'Approved' && $info['NumberOfSlots'] > 0) {
                    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM application WHERE ScholarshipID = ? AND Status = 'Approved'");
                    $count_stmt->execute([$info['ScholarshipID']]);
                    $approved_count = $count_stmt->fetchColumn();

                    if ($approved_count >= $info['NumberOfSlots']) {
                        $pdo->prepare("UPDATE scholarship SET Status = 'Inactive' WHERE ScholarshipID = ?")->execute([$info['ScholarshipID']]);

                        $ext_msg = "Your scholarship program '{$info['scholarship_name']}' is now FULL ({$approved_count}/{$info['NumberOfSlots']} slots) and has been automatically deactivated.";
                        $notif = $pdo->prepare("INSERT INTO system_notifications (RecipientID, Title, Message) VALUES (?, 'Scholarship Full', ?)");
                        $notif->execute([$info['CreatedBy'], $ext_msg]);

                        $int_msg = "The scholarship program '{$info['scholarship_name']}' has reached its maximum quota ({$approved_count}/{$info['NumberOfSlots']} slots) and was auto-deactivated.";
                        $internal_admins = $pdo->query("SELECT UserID FROM users WHERE Role = 'Internal_Admin'")->fetchAll(PDO::FETCH_COLUMN);
                        foreach($internal_admins as $admin_id) { 
                            $notif->execute([$admin_id, $int_msg]); 
                        }
                    }
                }
            }
            $_SESSION['success'] = "Status updated to $status and student notified via email & portal.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    header("Location: ../internal_admin/applications.php");
    exit();
}

// ==========================================
// MODULE: EXTERNAL ADMIN SCHOLARSHIPS (100% UNIFIED BUILDER)
// ==========================================
if ($module === 'scholarships') {
    
    if ($action === 'create') {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $amount = $_POST['award_amount'];
        $release = $_POST['release_frequency'] ?? 'Per Semester';
        $slots = (isset($_POST['slots']) && $_POST['slots'] !== '') ? $_POST['slots'] : null; 
        $min_gpa = $_POST['min_gpa'];
        $deadline = $_POST['deadline'];
        $program_id = !empty($_POST['program_id']) ? $_POST['program_id'] : null;
        $year_level = !empty($_POST['year_level']) ? $_POST['year_level'] : null;
        $scholarship_type = $_POST['scholarship_type'] ?? 'Private';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO scholarship (Name, Description, AwardAmount, ReleaseFrequency, NumberOfSlots, MinimumGWA, Deadline, ProgramID, YearLevel, Status, CreatedBy, ScholarshipType) 
                VALUES (:name, :desc, :amount, :release, :slots, :gpa, :deadline, :pid, :yl, 'Active', :created_by, :type)
            ");
            $stmt->execute(['name' => $name, 'desc' => $desc, 'amount' => $amount, 'release' => $release, 'slots' => $slots, 'gpa' => $min_gpa, 'deadline' => $deadline, 'pid' => $program_id, 'yl' => $year_level, 'created_by' => $_SESSION['user_id'], 'type' => $scholarship_type]);

            $new_scholarship_id = $pdo->lastInsertId();

            if (!empty($_POST['new_dynamic_names']) && is_array($_POST['new_dynamic_names'])) {
                $req_stmt = $pdo->prepare("INSERT INTO document_requirement (ScholarshipID, DocumentName) VALUES (?, ?)");
                $crit_stmt = $pdo->prepare("INSERT INTO scholarship_criteria (ScholarshipID, CriteriaName) VALUES (?, ?)");
                $field_stmt = $pdo->prepare("INSERT INTO scholarship_custom_fields (ScholarshipID, FieldName, FieldType) VALUES (?, ?, ?)");

                for($i = 0; $i < count($_POST['new_dynamic_names']); $i++) {
                    $row_name = trim($_POST['new_dynamic_names'][$i]);
                    $row_type = $_POST['new_dynamic_types'][$i] ?? 'Text';

                    if ($row_name !== '') {
                        if ($row_type === 'Document') {
                            $req_stmt->execute([$new_scholarship_id, $row_name]);
                        } elseif ($row_type === 'Criteria') {
                            $crit_stmt->execute([$new_scholarship_id, $row_name]);
                        } else {
                            $db_type = 'Text';
                            if ($row_type === 'Paragraph') $db_type = 'Textarea';
                            if ($row_type === 'Number') $db_type = 'Number';
                            if ($row_type === 'Date') $db_type = 'Date';
                            $field_stmt->execute([$new_scholarship_id, $row_name, $db_type]);
                        }
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "Scholarship successfully launched with custom dynamic fields!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
        header("Location: ../external_admin/scholarships.php");
        exit();
    }

    if ($action === 'edit') {
        $id = $_POST['scholarship_id'];
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $amount = $_POST['award_amount'];
        $release = $_POST['release_frequency'] ?? 'Per Semester';
        $slots = (isset($_POST['slots']) && $_POST['slots'] !== '') ? $_POST['slots'] : null; 
        $min_gpa = $_POST['min_gpa'];
        $deadline = $_POST['deadline'];
        $status = $_POST['status'];
        $program_id = !empty($_POST['program_id']) ? $_POST['program_id'] : null;
        $year_level = !empty($_POST['year_level']) ? $_POST['year_level'] : null;
        $scholarship_type = $_POST['scholarship_type'] ?? 'Private';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE scholarship SET Name = :name, Description = :desc, AwardAmount = :amount, ReleaseFrequency = :release, NumberOfSlots = :slots, MinimumGWA = :gpa, Deadline = :deadline, ProgramID = :pid, YearLevel = :yl, Status = :status, ScholarshipType = :type WHERE ScholarshipID = :id");
            $stmt->execute(['name' => $name, 'desc' => $desc, 'amount' => $amount, 'release' => $release, 'slots' => $slots, 'gpa' => $min_gpa, 'deadline' => $deadline, 'pid' => $program_id, 'yl' => $year_level, 'status' => $status, 'type' => $scholarship_type, 'id' => $id]);

            // UPDATE EXISTING FIELDS
            if (isset($_POST['existing_Req_ids']) && isset($_POST['existing_Req_names'])) {
                $upd = $pdo->prepare("UPDATE document_requirement SET DocumentName = ? WHERE RequirementID = ? AND ScholarshipID = ?");
                for ($i = 0; $i < count($_POST['existing_Req_ids']); $i++) { if (trim($_POST['existing_Req_names'][$i]) !== '') $upd->execute([trim($_POST['existing_Req_names'][$i]), $_POST['existing_Req_ids'][$i], $id]); }
            }
            if (isset($_POST['existing_Crit_ids']) && isset($_POST['existing_Crit_names'])) {
                $upd = $pdo->prepare("UPDATE scholarship_criteria SET CriteriaName = ? WHERE CriteriaID = ? AND ScholarshipID = ?");
                for ($i = 0; $i < count($_POST['existing_Crit_ids']); $i++) { if (trim($_POST['existing_Crit_names'][$i]) !== '') $upd->execute([trim($_POST['existing_Crit_names'][$i]), $_POST['existing_Crit_ids'][$i], $id]); }
            }
            if (isset($_POST['existing_Custom_ids']) && isset($_POST['existing_Custom_names'])) {
                $upd = $pdo->prepare("UPDATE scholarship_custom_fields SET FieldName = ? WHERE FieldID = ? AND ScholarshipID = ?");
                for ($i = 0; $i < count($_POST['existing_Custom_ids']); $i++) { if (trim($_POST['existing_Custom_names'][$i]) !== '') $upd->execute([trim($_POST['existing_Custom_names'][$i]), $_POST['existing_Custom_ids'][$i], $id]); }
            }

            // DELETE REMOVED FIELDS
            if (!empty($_POST['delete_Req_ids'])) {
                $del = $pdo->prepare("DELETE FROM document_requirement WHERE RequirementID = ? AND ScholarshipID = ?");
                foreach ($_POST['delete_Req_ids'] as $del_id) { $del->execute([$del_id, $id]); }
            }
            if (!empty($_POST['delete_Crit_ids'])) {
                $del = $pdo->prepare("DELETE FROM scholarship_criteria WHERE CriteriaID = ? AND ScholarshipID = ?");
                foreach ($_POST['delete_Crit_ids'] as $del_id) { $del->execute([$del_id, $id]); }
            }
            if (!empty($_POST['delete_Custom_ids'])) {
                $del = $pdo->prepare("DELETE FROM scholarship_custom_fields WHERE FieldID = ? AND ScholarshipID = ?");
                foreach ($_POST['delete_Custom_ids'] as $del_id) { $del->execute([$del_id, $id]); }
            }

            // ADD NEW FIELDS DURING EDIT
            if (!empty($_POST['new_dynamic_names']) && is_array($_POST['new_dynamic_names'])) {
                $req_stmt = $pdo->prepare("INSERT INTO document_requirement (ScholarshipID, DocumentName) VALUES (?, ?)");
                $crit_stmt = $pdo->prepare("INSERT INTO scholarship_criteria (ScholarshipID, CriteriaName) VALUES (?, ?)");
                $field_stmt = $pdo->prepare("INSERT INTO scholarship_custom_fields (ScholarshipID, FieldName, FieldType) VALUES (?, ?, ?)");

                for($i = 0; $i < count($_POST['new_dynamic_names']); $i++) {
                    $row_name = trim($_POST['new_dynamic_names'][$i]);
                    $row_type = $_POST['new_dynamic_types'][$i] ?? 'Text';

                    if ($row_name !== '') {
                        if ($row_type === 'Document') { $req_stmt->execute([$id, $row_name]); } 
                        elseif ($row_type === 'Criteria') { $crit_stmt->execute([$id, $row_name]); } 
                        else {
                            $db_type = 'Text';
                            if ($row_type === 'Paragraph') $db_type = 'Textarea';
                            if ($row_type === 'Number') $db_type = 'Number';
                            if ($row_type === 'Date') $db_type = 'Date';
                            $field_stmt->execute([$id, $row_name, $db_type]);
                        }
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "Scholarship successfully updated!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
        header("Location: ../external_admin/scholarships.php");
        exit();
    }
}

// ==========================================
// MODULE: DOCUMENT VERIFICATION (EXTERNAL ADMIN)
// ==========================================
if ($module === 'verification') {
    $document_id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? '';

    if ($document_id && in_array($status, ['Verified', 'Rejected'])) {
        try {
            $pdo->prepare("UPDATE submitted_document SET VerificationStatus = :status WHERE SubmittedDocID = :did")->execute(['status' => $status, 'did' => $document_id]);
            $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, ?, NOW(), ?, ?)")->execute([$_SESSION['user_id'], "Document $status", "Evaluator marked Document #$document_id as $status", $_SERVER['REMOTE_ADDR']]);

            $info_stmt = $pdo->prepare("SELECT a.UserID, dr.DocumentName, sch.Name AS ScholarshipName FROM submitted_document sd JOIN application a ON sd.ApplicationID = a.ApplicationID JOIN document_requirement dr ON sd.RequirementID = dr.RequirementID WHERE sd.SubmittedDocID = ?");
            $info_stmt->execute([$document_id]);
            $info = $info_stmt->fetch();
            
            if ($info) {
                $type = ($status === 'Verified') ? 'success' : 'danger';
                $title = ($status === 'Verified') ? 'Document Verified ✅' : 'Document Rejected 🛑';
                $msg = "Your document '" . $info['DocumentName'] . "' for the " . $info['ScholarshipName'] . " has been officially " . $status . ".";
                $pdo->prepare("INSERT INTO notifications (UserID, Title, Message, Type) VALUES (?, ?, ?, ?)")->execute([$info['UserID'], $title, $msg, $type]);
            }
            $_SESSION['success'] = "Document #$document_id successfully marked as $status.";
        } catch (PDOException $e) { $_SESSION['error'] = "Verification Error: " . $e->getMessage(); }
    }
    header("Location: ../external_admin/verify.php");
    exit();
}

// ==========================================
// MODULE: INTERNAL ADMIN FINAL DECISIONS
// ==========================================
if ($module === 'applications' && $action === 'final_decision') {
    $application_id = $_POST['application_id'];
    $status = $_POST['status']; 

    if (in_array($status, ['Approved', 'Rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE application SET Status = :status WHERE ApplicationID = :id");
            $stmt->execute(['status' => $status, 'id' => $application_id]);

            $log = $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, ?, NOW(), ?, ?)");
            $log->execute([$_SESSION['user_id'], "Application $status", "Internal Admin marked Application #$application_id as $status", $_SERVER['REMOTE_ADDR']]);

            $info_stmt = $pdo->prepare("
                SELECT a.UserID, u.Email, u.FirstName, sch.Name, sch.ScholarshipID, sch.NumberOfSlots, sch.CreatedBy 
                FROM application a 
                JOIN scholarship sch ON a.ScholarshipID = sch.ScholarshipID 
                JOIN users u ON a.UserID = u.UserID
                WHERE a.ApplicationID = ?
            ");
            $info_stmt->execute([$application_id]);
            $info = $info_stmt->fetch();
            
            if ($info) {
                $type = ($status === 'Approved') ? 'success' : 'danger';
                $title = ($status === 'Approved') ? 'Application Approved! 🏆' : 'Application Update';
                $msg = ($status === 'Approved') 
                    ? "Congratulations! Your application for the " . $info['Name'] . " has been officially Approved." 
                    : "Your application for the " . $info['Name'] . " has been reviewed and marked as Rejected.";
                
                $notif_stmt = $pdo->prepare("INSERT INTO notifications (UserID, Title, Message, Type) VALUES (?, ?, ?, ?)");
                $notif_stmt->execute([$info['UserID'], $title, $msg, $type]);

                if (!empty($info['Email'])) {
                    sendScholarshipEmail($info['Email'], $info['FirstName'], $info['Name'], $status);
                }

                // ✨ AUTO-DEACTIVATION CHECK
                if ($status === 'Approved' && $info['NumberOfSlots'] > 0) {
                    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM application WHERE ScholarshipID = ? AND Status = 'Approved'");
                    $count_stmt->execute([$info['ScholarshipID']]);
                    $approved_count = $count_stmt->fetchColumn();

                    if ($approved_count >= $info['NumberOfSlots']) {
                        $pdo->prepare("UPDATE scholarship SET Status = 'Inactive' WHERE ScholarshipID = ?")->execute([$info['ScholarshipID']]);

                        $ext_msg = "Your scholarship program '{$info['Name']}' is now FULL ({$approved_count}/{$info['NumberOfSlots']} slots) and has been automatically deactivated.";
                        $notif = $pdo->prepare("INSERT INTO system_notifications (RecipientID, Title, Message) VALUES (?, 'Scholarship Full', ?)");
                        $notif->execute([$info['CreatedBy'], $ext_msg]);

                        $int_msg = "The scholarship program '{$info['Name']}' has reached its maximum quota ({$approved_count}/{$info['NumberOfSlots']} slots) and was auto-deactivated.";
                        $internal_admins = $pdo->query("SELECT UserID FROM users WHERE Role = 'Internal_Admin'")->fetchAll(PDO::FETCH_COLUMN);
                        foreach($internal_admins as $admin_id) { 
                            $notif->execute([$admin_id, $int_msg]); 
                        }
                    }
                }
            }

            if($status === 'Approved') {
                $_SESSION['success'] = "Application officially Approved! The student is now a Scholar and has been notified via email.";
            } else {
                $_SESSION['success'] = "Application has been Rejected and the student has been notified.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
    }
    header("Location: ../internal_admin/shortlist.php");
    exit();
}

// ==========================================
// MODULE: ADMIN ACTIONS (MOA DEACTIVATION NOTICE)
// ==========================================
if ($module === 'super_admin' && $action === 'notify_moa') {
    $scholarship_id = $_POST['scholarship_id'];
    
    try {
        $sch_stmt = $pdo->prepare("SELECT Name, CreatedBy FROM scholarship WHERE ScholarshipID = ?");
        $sch_stmt->execute([$scholarship_id]);
        $sch = $sch_stmt->fetch();

        if ($sch) {
            // Warn External Admin
            $ext_msg = "System Notice: Your scholarship program '{$sch['Name']}' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the University Admin.";
            $notif = $pdo->prepare("INSERT INTO system_notifications (RecipientID, Title, Message) VALUES (?, 'MOA Deactivation Notice', ?)");
            $notif->execute([$sch['CreatedBy'], $ext_msg]);
            
            // Notify all Internal Admins
            $int_msg = "The scholarship program '{$sch['Name']}' was flagged for MOA Deactivation. The External Provider has been officially notified.";
            $internal_admins = $pdo->query("SELECT UserID FROM users WHERE Role = 'Internal_Admin'")->fetchAll(PDO::FETCH_COLUMN);
            foreach($internal_admins as $admin_id) { $notif->execute([$admin_id, $int_msg]); }

            $_SESSION['success'] = "MOA Deactivation Notice successfully sent to the External Provider and Internal Admins.";
        }
    } catch (PDOException $e) { $_SESSION['error'] = "Error: " . $e->getMessage(); }
    header("Location: ../super_admin/programs.php");
    exit();
}

if ($module === 'admin_actions' && $action === 'notify_moa') {
    $scholarship_id = $_POST['scholarship_id'];
    
    try {
        $sch_stmt = $pdo->prepare("SELECT Name, CreatedBy FROM scholarship WHERE ScholarshipID = ?");
        $sch_stmt->execute([$scholarship_id]);
        $sch = $sch_stmt->fetch();

        if ($sch) {
            $ext_msg = "System Notice: Your scholarship program '{$sch['Name']}' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the University Admin.";
            $notif = $pdo->prepare("INSERT INTO system_notifications (RecipientID, Title, Message) VALUES (?, 'MOA Deactivation Notice', ?)");
            $notif->execute([$sch['CreatedBy'], $ext_msg]);

            $_SESSION['success'] = "MOA Deactivation Notice successfully sent to the External Provider.";
        }
    } catch (PDOException $e) { $_SESSION['error'] = "Error: " . $e->getMessage(); }
    header("Location: ../internal_admin/programs.php");
    exit();
}

// ==========================================
// MODULE: DISMISS SYSTEM NOTIFICATIONS
// ==========================================
if ($module === 'notifications' && $action === 'dismiss') {
    $notif_id = $_POST['notif_id'];
    $stmt = $pdo->prepare("UPDATE system_notifications SET IsRead = 1 WHERE NotifID = ?");
    $stmt->execute([$notif_id]);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ==========================================
// MODULE: REQUIREMENTS (STUDENT)
// ==========================================
if ($module === 'requirements' && $action === 'delete') {
    $document_id = $_POST['document_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if ($document_id) {
        try {
            $stmt = $pdo->prepare("
                SELECT d.file_path 
                FROM document d
                JOIN application a ON d.application_id = a.application_id
                JOIN student s ON a.student_id = s.student_id
                WHERE d.document_id = :did AND s.user_id = :uid
            ");
            $stmt->execute(['did' => $document_id, 'uid' => $user_id]);
            $doc = $stmt->fetch();

            if ($doc) {
                if (file_exists($doc['file_path'])) { unlink($doc['file_path']); }
                $delete_stmt = $pdo->prepare("DELETE FROM document WHERE document_id = :did");
                $delete_stmt->execute(['did' => $document_id]);
                $_SESSION['success'] = "Document deleted successfully.";
            } else {
                $_SESSION['error'] = "Unauthorized action or document not found.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
    }
    header("Location: ../student/requirements.php");
    exit();
}

// ==========================================
// MODULE: LOGS (SUPER ADMIN)
// ==========================================
if ($module === 'logs') {
    if ($action === 'delete_single') {
        $log_id = $_POST['log_id'] ?? null;
        if ($log_id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM audit_log WHERE audit_id = :id");
                $stmt->execute(['id' => $log_id]);
                $_SESSION['success'] = "Log entry successfully deleted.";
            } catch (PDOException $e) { $_SESSION['error'] = "Failed to delete log: " . $e->getMessage(); }
        }
    } elseif ($action === 'delete_batch') {
        $log_ids = $_POST['log_ids'] ?? [];
        if (!empty($log_ids)) {
            try {
                $placeholders = implode(',', array_fill(0, count($log_ids), '?'));
                $stmt = $pdo->prepare("DELETE FROM audit_log WHERE audit_id IN ($placeholders)");
                $stmt->execute($log_ids);
                $_SESSION['success'] = count($log_ids) . " log entries successfully deleted.";
            } catch (PDOException $e) { $_SESSION['error'] = "Failed to delete logs: " . $e->getMessage(); }
        } else {
            $_SESSION['error'] = "No logs were selected for deletion.";
        }
    }
    header("Location: ../super_admin/logs.php");
    exit();
}

// ==========================================
// MODULE: SUPER ADMIN USERS & SECURITY
// ==========================================
if ($module === 'users' && $action === 'create') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role']; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        if ($role === 'Super_Admin') {
            $check = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Super_Admin'");
            if ($check->fetchColumn() > 0) {
                $_SESSION['error'] = "Error: Bawal mag-create ng isa pang Super Admin. Isa lang ang dapat umiral.";
                header("Location: ../super_admin/users.php");
                exit();
            }
        }
        $pdo->prepare("INSERT INTO users (FirstName, LastName, Email, Role, PasswordHash) VALUES (?, ?, ?, ?, ?)")->execute([$first_name, $last_name, $email, $role, $password]);
        $_SESSION['success'] = "User account created successfully!";
    } catch (PDOException $e) { $_SESSION['error'] = "Database Error: " . $e->getMessage(); }
    header("Location: ../super_admin/users.php");
    exit();
}

if ($module === 'users' && $action === 'toggle_archive') {
    $user_id = $_POST['user_id'];
    $new_status = ($_POST['current_status'] === 'Active') ? 'Archived' : 'Active';
    try {
        $pdo->prepare("UPDATE users SET AccountStatus = :status WHERE UserID = :uid")->execute(['status' => $new_status, 'uid' => $user_id]);
        $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, ?, NOW(), ?, ?)")->execute([$_SESSION['user_id'], "User $new_status", "Super Admin changed UserID $user_id status to $new_status", $_SERVER['REMOTE_ADDR']]);
        $_SESSION['success'] = "User account successfully marked as $new_status!";
    } catch (PDOException $e) { $_SESSION['error'] = "Database Error: " . $e->getMessage(); }
    header("Location: ../super_admin/users.php");
    exit();
}

if ($module === 'security' && $action === 'toggle_setting') {
    $setting_key = $_POST['setting_key'];
    $new_value = $_POST['new_value'];

    try {
        $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = :val WHERE setting_key = :key");
        $stmt->execute(['val' => $new_value, 'key' => $setting_key]);

        $formatted_name = ucwords(str_replace('_', ' ', $setting_key));
        $log_value = ($new_value === '1') ? 'ON' : (($new_value === '0') ? 'OFF' : $new_value . ' seconds');

        $log = $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, 'Security Update', NOW(), ?, ?)");
        $log->execute([$_SESSION['user_id'], "Super Admin changed $formatted_name to $log_value", $_SERVER['REMOTE_ADDR']]);

        $_SESSION['success'] = "$formatted_name updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
    }
    header("Location: ../super_admin/security.php");
    exit();
}
?>