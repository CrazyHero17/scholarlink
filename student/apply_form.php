<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') { 
    header("Location: ../student_login.php"); exit(); 
}
include '../includes/db_connect.php'; 
include '../includes/header.php'; 
include '../includes/student_sidebar.php'; 

$user_id = $_SESSION['user_id'];
$scholarship_id = $_GET['id'] ?? 1;

// 1. Fetch Scholarship Details & Requirements
$stmt = $pdo->prepare("SELECT * FROM scholarship WHERE ScholarshipID = ?");
$stmt->execute([$scholarship_id]);
$scholarship = $stmt->fetch();

// 2. Security Check (Block if restricted)
$user_stmt = $pdo->prepare("SELECT ProgramID, YearLevel, Gender, GPA FROM users WHERE UserID = ?");
$user_stmt->execute([$user_id]);
$student = $user_stmt->fetch();

if (($scholarship['ProgramID'] && $scholarship['ProgramID'] != $student['ProgramID']) || 
    ($scholarship['YearLevel'] && $scholarship['YearLevel'] != $student['YearLevel']) ||
    (isset($scholarship['GenderRequirement']) && $scholarship['GenderRequirement'] !== 'Any' && $scholarship['GenderRequirement'] !== $student['Gender'])) {
    echo "<script>alert('You do not meet the core demographic requirements for this scholarship.'); window.location.href='programs.php';</script>";
    exit();
}

$req_stmt = $pdo->prepare("SELECT * FROM document_requirement WHERE ScholarshipID = ?");
$req_stmt->execute([$scholarship_id]);
$requirements = $req_stmt->fetchAll();

// ✨ 3. FETCH DYNAMIC CUSTOM FIELDS
$fields_stmt = $pdo->prepare("SELECT * FROM scholarship_custom_fields WHERE ScholarshipID = ?");
$fields_stmt->execute([$scholarship_id]);
$customFields = $fields_stmt->fetchAll();
?>

<main class="flex-1 p-5 lg:p-10 lg:ml-72 bg-slate-50/50 min-h-screen transition-all duration-300">
    <header class="mb-8 lg:mb-10 flex items-center gap-4 lg:gap-6">
        <a href="programs.php" class="w-10 h-10 lg:w-14 lg:h-14 shrink-0 bg-white border border-slate-200 rounded-xl lg:rounded-[1.25rem] flex items-center justify-center font-black text-slate-400 hover:text-slate-900 transition-all">←</a>
        <div>
            <h2 class="text-2xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight">Apply for <?= htmlspecialchars($scholarship['Name']) ?></h2>
            <p class="text-slate-500 font-medium text-xs lg:text-sm mt-1">Complete the dynamic form and upload your requirements.</p>
        </div>
    </header>

    <form action="../actions/process_crud.php" method="POST" id="scholarshipApplyForm" enctype="multipart/form-data" class="bg-white p-6 lg:p-10 rounded-[1.5rem] lg:rounded-[2rem] border border-slate-200 shadow-sm max-w-4xl space-y-8 lg:space-y-10">
        <input type="hidden" name="module" value="student_apply">
        <input type="hidden" name="action" value="create">
        <input type="hidden" name="scholarship_id" value="<?= $scholarship_id ?>">
        
        <div class="space-y-4">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest border-b border-slate-100 pb-2">Academic Profile</h3>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Declared GWA / GPA</label>
                <input type="number" step="0.01" name="gpa" id="modalGpa" value="<?= htmlspecialchars($student['GPA'] ?? '') ?>" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-blue-500 transition-colors">
            </div>
        </div>

        <?php if (!empty($customFields)): ?>
        <div class="space-y-6">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest border-b border-slate-100 pb-2">Custom Application Questions</h3>
            
            <?php foreach($customFields as $field): ?>
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest block"><?= htmlspecialchars($field['FieldName']) ?></label>
                    
                    <?php if($field['FieldType'] == 'Textarea'): ?>
                        <textarea name="custom_answers[<?= $field['FieldID'] ?>]" rows="4" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium outline-none focus:border-blue-500 transition-colors"></textarea>
                    
                    <?php elseif($field['FieldType'] == 'Number'): ?>
                        <input type="number" step="any" name="custom_answers[<?= $field['FieldID'] ?>]" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-blue-500 transition-colors">
                    
                    <?php elseif($field['FieldType'] == 'Date'): ?>
                        <input type="date" name="custom_answers[<?= $field['FieldID'] ?>]" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-blue-500 transition-colors cursor-pointer text-slate-700">
                    
                    <?php else: ?>
                        <input type="text" name="custom_answers[<?= $field['FieldID'] ?>]" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold outline-none focus:border-blue-500 transition-colors">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="space-y-4">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest border-b border-slate-100 pb-2">Required Documents</h3>
            <?php foreach ($requirements as $req): ?>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-slate-200 rounded-xl bg-slate-50 gap-4">
                    <div class="flex-1">
                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($req['DocumentName']) ?></p>
                        <p id="status_<?= $req['RequirementID'] ?>" class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Pending Upload</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="openVaultPicker(<?= $req['RequirementID'] ?>)" class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-blue-200">From Vault</button>
                        <input type="file" name="files[<?= $req['RequirementID'] ?>]" id="file_<?= $req['RequirementID'] ?>" class="text-xs font-bold text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">
                        <input type="hidden" name="vault_files[<?= $req['RequirementID'] ?>]" id="vault_input_<?= $req['RequirementID'] ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-10 py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">Submit Application</button>
        </div>
    </form>
</main>
<?php include '../includes/footer.php'; ?>