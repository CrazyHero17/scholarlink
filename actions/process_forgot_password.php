<?php
session_start();
include '../includes/db_connect.php';

// 🔒 SECURITY GATEKEEPER
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Super_Admin') {
    die("Unauthorized Access. Intrusion Logged.");
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$admin_id = $_SESSION['user_id'] ?? 1;

// ========================================================
// ✨ 1. EXPORT: PURE PHP SQL DUMPER
// ========================================================
if ($action === 'export') {
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        $sqlScript = "-- ScholarLink Database Backup\n";
        $sqlScript .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sqlScript .= "-- Server: " . $_SERVER['SERVER_NAME'] . "\n\n";
        $sqlScript .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            // Get Table Creation Schema
            $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlScript .= $createStmt[1] . ";\n\n";

            // Get Table Data
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $columns = array_keys($row);
                $values = array_values($row);
                
                // Escape values securely
                $escaped_values = array_map(function($val) use ($pdo) {
                    return $val === null ? 'NULL' : $pdo->quote($val);
                }, $values);

                $sqlScript .= "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $escaped_values) . ");\n";
            }
            $sqlScript .= "\n";
        }
        $sqlScript .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        // Log the export action
        $log = $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, 'System Backup', NOW(), 'Super Admin exported a full database backup.', ?)");
        $log->execute([$admin_id, $_SERVER['REMOTE_ADDR']]);

        // Force download the file
        $backup_file_name = 'ScholarLink_Backup_' . date('Y-m-d_H-i') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $backup_file_name . '"');
        echo $sqlScript;
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Export Failed: " . $e->getMessage();
        header("Location: ../super_admin/database.php");
        exit;
    }
}

// ========================================================
// ✨ 2. RESTORE: SQL IMPORTER
// ========================================================
elseif ($action === 'restore') {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
        $file_path = $_FILES['backup_file']['tmp_name'];
        $sqlScript = file_get_contents($file_path);

        try {
            // Turn off foreign key constraints before dropping tables
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            
            // Execute the entire dumped SQL file
            $pdo->exec($sqlScript);
            
            // Turn constraints back on
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

            // Log the restore action
            $log = $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, 'System Restore', NOW(), 'Super Admin restored the database from a backup file.', ?)");
            $log->execute([$admin_id, $_SERVER['REMOTE_ADDR']]);

            $_SESSION['success'] = "System Successfully Restored from Backup!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Restore Failed. Ensure it is a valid ScholarLink .sql file. Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Please upload a valid .sql file.";
    }
    header("Location: ../super_admin/database.php");
    exit;
}

// ========================================================
// ✨ 3. WIPE & RESET: TRUNCATE TRANSACTIONS
// ========================================================
elseif ($action === 'reset') {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        
        // Wipe transaction/moving data but KEEP Users, Scholarships, and Programs
        $pdo->exec("TRUNCATE TABLE application");
        $pdo->exec("TRUNCATE TABLE submitted_document");
        $pdo->exec("TRUNCATE TABLE user_vault");
        $pdo->exec("TRUNCATE TABLE audit_log");
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Create a new log for the reset (since we just wiped the old logs)
        $log = $pdo->prepare("INSERT INTO audit_log (UserID, ActionPerformed, ActionDate, Description, IPAddress) VALUES (?, 'System Reset', NOW(), 'Super Admin wiped all application and document data for a fresh semester.', ?)");
        $log->execute([$admin_id, $_SERVER['REMOTE_ADDR']]);

        $_SESSION['success'] = "System Reset Complete. All active applications wiped.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Reset Failed: " . $e->getMessage();
    }
    header("Location: ../super_admin/database.php");
    exit;
}
else {
    header("Location: ../super_admin/database.php");
    exit;
}
?>