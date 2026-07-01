<?php
session_start();
include '../includes/session_manager.php'; 

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../student_login.php"); 
    exit();
}
include '../includes/db_connect.php'; 
include '../includes/header.php'; 
include '../includes/student_sidebar.php'; 

$app_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

// 1. Fetch Base Application
$stmt = $pdo->prepare("
    SELECT a.*, sch.Name AS scholarship_name, sch.ScholarshipID 
    FROM application a 
    JOIN scholarship sch ON a.ScholarshipID = sch.ScholarshipID 
    WHERE a.ApplicationID = ? AND a.UserID = ?
");
$stmt->execute([$app_id, $user_id]);
$app = $stmt->fetch();

if (!$app || !in_array($app['Status'], ['Submitted', 'Pending'])) { 
    header("Location: applications.php"); 
    exit(); 
}

// 2. Fetch Custom Fields
$fields_stmt = $pdo->prepare("
    SELECT f.*, a.AnswerText 
    FROM scholarship_custom_fields f
    LEFT JOIN application_custom_answers a ON f.FieldID = a.FieldID AND a.ApplicationID = ?
    WHERE f.ScholarshipID = ?
");
$fields_stmt->execute([$app_id, $app['ScholarshipID']]);
$customFields = $fields_stmt->fetchAll();

// ✨ 3. RESTORED: Fetch Attached Documents
$req_stmt = $pdo->prepare("
    SELECT dr.*, sd.FilePath, sd.VerificationStatus 
    FROM document_requirement dr 
    LEFT JOIN submitted_document sd ON dr.RequirementID = sd.RequirementID AND sd.ApplicationID = ?
    WHERE dr.ScholarshipID = ?
");
$req_stmt->execute([$app_id, $app['ScholarshipID']]);
$requirements = $req_stmt->fetchAll();
?>

<main class="flex-1 p-5 lg:p-10 lg:ml-72 bg-slate-50/50 min-h-screen transition-all duration-300">
    <header class="mb-8 lg:mb-10 flex items-center gap-4 lg:gap-6">
        <a href="applications.php" class="w-10 h-10 lg:w-14 lg:h-14 shrink-0 bg-white border border-slate-200 rounded-xl lg:rounded-[1.25rem] flex items-center justify-center font-black text-slate-400 hover:text-slate-900 transition-all">←</a>
        <div>
            <h2 class="text-2xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight">Edit Application</h2>
            <p class="text-slate-500 font-medium text-xs lg:text-sm mt-1">Update your details for <strong class="text-slate-700"><?= htmlspecialchars($app['scholarship_name']) ?></strong></p>
        </div>
    </header>

    <form action="../actions/process_crud.php" method="POST" enctype="multipart/form-data" id="applicationEditForm" class="bg-white p-6 lg:p-10 rounded-[1.5rem] lg:rounded-[2rem] border border-slate-200 shadow-sm max-w-4xl space-y-6 lg:space-y-8">
        <input type="hidden" name="module" value="student_apply">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="application_id" value="<?= $app['ApplicationID'] ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Latest GWA / GPA</label>
                <input type="number" step="0.01" name="gpa" id="modalGpa" value="<?= htmlspecialchars($app['GPA'] ?? '') ?>" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl lg:rounded-2xl font-bold outline-none focus:border-green-500 transition-colors">
            </div>
        </div>

        <?php if (!empty($customFields)): ?>
            <div class="space-y-6 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest pb-2">Update Application Form</h3>
                
                <?php foreach($customFields as $field): ?>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest block"><?= htmlspecialchars($field['FieldName']) ?></label>
                        
                        <?php if($field['FieldType'] == 'Textarea'): ?>
                            <textarea name="custom_answers[<?= $field['FieldID'] ?>]" rows="4" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:border-green-500 transition-colors"><?= htmlspecialchars($field['AnswerText'] ?? '') ?></textarea>
                        <?php elseif($field['FieldType'] == 'Number'): ?>
                            <input type="number" step="any" name="custom_answers[<?= $field['FieldID'] ?>]" value="<?= htmlspecialchars($field['AnswerText'] ?? '') ?>" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-green-500 transition-colors">
                        <?php elseif($field['FieldType'] == 'Date'): ?>
                            <input type="date" name="custom_answers[<?= $field['FieldID'] ?>]" value="<?= htmlspecialchars($field['AnswerText'] ?? '') ?>" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-green-500 transition-colors cursor-pointer text-slate-700">
                        <?php else: ?>
                            <input type="text" name="custom_answers[<?= $field['FieldID'] ?>]" value="<?= htmlspecialchars($field['AnswerText'] ?? '') ?>" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-green-500 transition-colors">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-4 pt-6 border-t border-slate-100">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest border-b border-slate-100 pb-2">Update Documents</h3>
            <?php foreach ($requirements as $req): ?>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-slate-200 rounded-xl bg-slate-50 gap-4">
                    <div class="flex-1">
                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($req['DocumentName']) ?></p>
                        <?php if($req['FilePath']): ?>
                            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mt-1">Currently Uploaded</p>
                        <?php else: ?>
                            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mt-1">Missing</p>
                        <?php endif; ?>
                    </div>
                    <div class="flex gap-2">
                        <input type="file" name="files[<?= $req['RequirementID'] ?>]" class="text-xs font-bold text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 lg:gap-4 pt-4 lg:pt-6 border-t border-slate-100">
            <a href="applications.php" class="w-full sm:w-auto text-center px-8 py-4 rounded-xl lg:rounded-2xl font-black text-slate-500 uppercase tracking-widest text-[10px] lg:text-xs hover:bg-slate-100 transition-all">Discard Edits</a>
            <button type="submit" class="w-full sm:w-auto bg-green-600 text-white px-10 py-4 rounded-xl lg:rounded-2xl font-black text-[10px] lg:text-xs uppercase tracking-widest hover:bg-green-700 transition-all shadow-xl shadow-green-600/20 active:scale-[0.98]">Save Changes & Update</button>
        </div>
    </form>
</main>
<?php include '../includes/footer.php'; ?>