<?php
session_start();
include '../includes/session_manager.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'External_Admin') {
    header("Location: ../admin_login.php"); 
    exit();
}

include '../includes/db_connect.php'; 
include '../includes/header.php'; 
include '../includes/external_sidebar.php'; 

$filter_program = $_GET['program'] ?? '';
$filter_year    = $_GET['year_level'] ?? '';
$filter_status  = $_GET['status'] ?? 'All';
$min_slots      = $_GET['min_slots'] ?? '';
$sort_by        = $_GET['sort_by'] ?? '';
$filter_gender  = $_GET['gender_req'] ?? 'Any'; 

try {
    $programs = $pdo->query("SELECT ProgramID, ProgramName FROM program ORDER BY ProgramName ASC")->fetchAll(PDO::FETCH_ASSOC);

    $query = "
        SELECT sch.*, p.ProgramName,
            (SELECT GROUP_CONCAT(CONCAT(RequirementID, '::', DocumentName) SEPARATOR '||') 
             FROM document_requirement dr WHERE dr.ScholarshipID = sch.ScholarshipID) AS Requirements,
            (SELECT GROUP_CONCAT(CONCAT(CriteriaID, '::', CriteriaName) SEPARATOR '||') 
             FROM scholarship_criteria sc WHERE sc.ScholarshipID = sch.ScholarshipID) AS CriteriaList,
            (SELECT GROUP_CONCAT(CONCAT(FieldID, '::', FieldName, '::', FieldType) SEPARATOR '||') 
             FROM scholarship_custom_fields scf WHERE scf.ScholarshipID = sch.ScholarshipID) AS CustomFields
        FROM scholarship sch 
        LEFT JOIN program p ON sch.ProgramID = p.ProgramID 
        WHERE sch.CreatedBy = :evaluator_id
    ";
    
    $params = ['evaluator_id' => $_SESSION['user_id']];

    if (!empty($filter_program)) { $query .= " AND p.ProgramName LIKE :program"; $params['program'] = "%" . trim($filter_program) . "%"; }
    if (!empty($filter_year)) { $query .= " AND sch.YearLevel LIKE :year"; $params['year'] = "%" . trim($filter_year) . "%"; }
    if ($filter_status !== 'All') { $query .= " AND sch.Status = :status"; $params['status'] = $filter_status; }
    if ($min_slots !== '') { $query .= " AND sch.NumberOfSlots >= :slots"; $params['slots'] = $min_slots; }
    if ($filter_gender !== 'Any') { $query .= " AND sch.GenderRequirement = :gender"; $params['gender'] = $filter_gender; }

    if ($sort_by === 'amount_asc') { $query .= " ORDER BY sch.AwardAmount ASC, sch.Name ASC"; } 
    elseif ($sort_by === 'amount_desc') { $query .= " ORDER BY sch.AwardAmount DESC, sch.Name ASC"; } 
    elseif ($sort_by === 'slots_asc') { $query .= " ORDER BY sch.NumberOfSlots ASC, sch.Name ASC"; } 
    elseif ($sort_by === 'slots_desc') { $query .= " ORDER BY sch.NumberOfSlots DESC, sch.Name ASC"; } 
    else { $query .= " ORDER BY sch.ScholarshipID DESC"; }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $scholarships = $stmt->fetchAll();

    $notif_stmt = $pdo->prepare("SELECT * FROM system_notifications WHERE RecipientID = ? AND IsRead = 0 ORDER BY DateCreated DESC");
    $notif_stmt->execute([$_SESSION['user_id']]);
    $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) { die("Database Error: " . $e->getMessage()); }
?>

<main class="flex-1 p-5 lg:p-10 lg:ml-72 bg-slate-50/50 min-h-screen transition-all duration-300">
    <header class="mb-8 lg:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Manage Scholarships</h2>
            <p class="text-slate-500 text-sm lg:text-base font-medium mt-1">Create, edit, filter, and monitor official scholarship programs.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openCreateModal()" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl font-black text-[12px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-[0.98]">
                + Launch New
            </button>
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm shrink-0">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:inline">Show:</span>
                <select id="itemsPerPage" onchange="changeItemsPerPage()" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-green-500 focus:border-green-500 block px-2 py-1 outline-none cursor-pointer">
                    <option value="5">5 items</option>
                    <option value="10" selected>10 items</option>
                    <option value="25">25 items</option>
                    <option value="999">All items</option>
                </select>
            </div>
        </div>
    </header>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 text-sm font-bold flex items-center gap-3">
            ✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($notifications)): ?>
        <div class="mb-8 space-y-3">
            <?php foreach($notifications as $notif): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex justify-between items-start">
                    <div>
                        <h4 class="text-red-700 font-black uppercase tracking-widest text-[11px] mb-1">⚠️ <?= htmlspecialchars($notif['Title']) ?></h4>
                        <p class="text-red-900 font-medium text-sm"><?= htmlspecialchars($notif['Message']) ?></p>
                        <span class="text-[9px] font-bold text-red-400 mt-2 block"><?= date('F j, Y, g:i a', strtotime($notif['DateCreated'])) ?></span>
                    </div>
                    <form action="../actions/process_crud.php" method="POST" class="m-0 shrink-0">
                        <input type="hidden" name="module" value="notifications">
                        <input type="hidden" name="action" value="dismiss">
                        <input type="hidden" name="notif_id" value="<?= $notif['NotifID'] ?>">
                        <button type="submit" class="text-red-400 hover:text-red-700 font-black text-xl leading-none">&times;</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 lg:p-8 rounded-[1.5rem] lg:rounded-[2rem] border border-slate-200 shadow-sm mb-10">
        <form method="GET" class="flex flex-wrap items-end gap-5">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[14px] font-black text-black uppercase tracking-widest mb-2">🔍 Search Program</label>
                <input type="search" name="program" list="programList" value="<?= htmlspecialchars($filter_program) ?>" placeholder="Type e.g. Info..." class="w-full bg-slate-50 border border-slate-100 px-4 py-3.5 rounded-xl font-bold text-sm text-black outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white shadow-inner">
                <datalist id="programList">
                    <?php foreach($programs as $p): ?>
                        <option value="<?= htmlspecialchars($p['ProgramName']) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                <button type="submit" class="flex-1 sm:flex-none bg-slate-900 text-white px-8 py-3.5 rounded-xl font-black text-[14px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">Filter</button>
                <a href="scholarships.php" class="flex-1 sm:flex-none flex items-center justify-center bg-slate-100 text-black px-6 py-3.5 rounded-xl font-black text-[14px] uppercase tracking-widest hover:bg-slate-200 transition-all border border-slate-200">Reset</a>
            </div>
        </form>
    </div>

    <div id="scholarshipsGrid" class="flex flex-col gap-3">
        <?php foreach($scholarships as $s): ?>
            <div class="scholarship-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all flex flex-col lg:flex-row items-start lg:items-center justify-between p-5 group relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full <?= $s['Status'] === 'Active' ? 'bg-green-500' : 'bg-slate-300' ?>"></div>
                <div class="flex-1 min-w-0 pr-4 lg:pr-8 pl-3 w-full lg:w-auto">
                    <div class="flex items-center gap-3 mb-1.5 flex-wrap">
                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest border border-emerald-200 shrink-0">
                            <?= htmlspecialchars($s['ScholarshipType'] ?? 'Private') ?>
                        </span>
                        <?php if($s['AllowsDual'] === 'No'): ?>
                            <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest border border-red-200 shrink-0">Strict Dual Policy</span>
                        <?php endif; ?>
                        <h4 class="text-base font-black text-slate-900 truncate"><?= htmlspecialchars($s['Name']) ?></h4>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 truncate mb-2 lg:mb-0">
                        <span class="text-slate-700 font-bold"><?= htmlspecialchars($s['ProgramName'] ?? 'All Programs') ?></span>
                        <span class="hidden md:inline">•</span>
                        <span class="truncate hidden md:inline"><?= htmlspecialchars($s['Description']) ?></span>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-6 px-6 border-x border-slate-100 shrink-0">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Min GPA</span>
                        <span class="text-sm font-black text-slate-800"><?= number_format($s['MinimumGWA'] ?? 2.00, 2) ?></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Award Amount</span>
                        <span class="text-sm font-black text-green-600">₱ <?= number_format($s['AwardAmount'], 2) ?></span>
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block mt-0.5"><?= htmlspecialchars($s['ReleaseFrequency'] ?? 'Per Semester') ?></span>
                    </div>
                </div>

                <div class="mt-4 lg:mt-0 flex items-center justify-between lg:justify-end w-full lg:w-auto shrink-0 gap-3 pl-0 lg:pl-6">
                    <button type="button" 
                        data-id="<?= $s['ScholarshipID'] ?>"
                        data-name="<?= htmlspecialchars($s['Name'] ?? '', ENT_QUOTES) ?>"
                        data-desc="<?= htmlspecialchars($s['Description'] ?? '', ENT_QUOTES) ?>"
                        data-amount="<?= $s['AwardAmount'] ?? 0 ?>"
                        data-release="<?= htmlspecialchars($s['ReleaseFrequency'] ?? 'Per Semester', ENT_QUOTES) ?>" 
                        data-gpa="<?= $s['MinimumGWA'] ?? 2.00 ?>"
                        data-deadline="<?= $s['Deadline'] ?? '' ?>"
                        data-program="<?= $s['ProgramID'] ?? '' ?>"
                        data-year="<?= htmlspecialchars($s['YearLevel'] ?? '', ENT_QUOTES) ?>"
                        data-status="<?= htmlspecialchars($s['Status'] ?? '', ENT_QUOTES) ?>"
                        data-slots="<?= htmlspecialchars($s['NumberOfSlots'] ?? '', ENT_QUOTES) ?>"
                        data-requirements="<?= htmlspecialchars($s['Requirements'] ?? '', ENT_QUOTES) ?>"
                        data-criteria="<?= htmlspecialchars($s['CriteriaList'] ?? '', ENT_QUOTES) ?>"
                        data-customfields="<?= htmlspecialchars($s['CustomFields'] ?? '', ENT_QUOTES) ?>"
                        data-gender="<?= htmlspecialchars($s['GenderRequirement'] ?? 'Any', ENT_QUOTES) ?>"
                        data-type="<?= htmlspecialchars($s['ScholarshipType'] ?? 'Private', ENT_QUOTES) ?>"
                        data-dual="<?= htmlspecialchars($s['AllowsDual'] ?? 'No', ENT_QUOTES) ?>"
                        onclick="openEditModal(this)" 
                        class="bg-slate-900 text-white hover:bg-blue-600 px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-md active:scale-95 shrink-0 whitespace-nowrap">
                        Edit ⚙️
                    </button>

                    <form action="../actions/process_crud.php" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to permanently delete this scholarship? All applications and dynamic forms attached to it will be wiped out.');">
                        <input type="hidden" name="module" value="scholarships">
                        <input type="hidden" name="action" value="delete_single">
                        <input type="hidden" name="scholarship_id" value="<?= $s['ScholarshipID'] ?>">
                        <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-500 hover:text-white px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-sm active:scale-95 shrink-0 whitespace-nowrap">
                            Delete 🗑️
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-col items-center justify-center mt-12 gap-4 border-t border-slate-200 pt-8 pb-10">
        <div id="paginationControls" class="flex flex-wrap justify-center gap-2"></div>
        <div id="pageInfo" class="text-[11px] font-black text-slate-400 uppercase tracking-widest text-center mt-2"></div>
        <input type="hidden" id="itemsPerPage" value="10">
    </div>
</main>

<div id="createModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-[95%] sm:max-w-5xl rounded-[1.5rem] lg:rounded-[2.5rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-6 lg:p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
            <div>
                <h3 class="font-black text-black text-lg uppercase tracking-tight">Launch Scholarship</h3>
                <p class="text-[14px] text-black font-bold mt-1 uppercase tracking-widest">Define core parameters and build your dynamic form</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 bg-slate-200 text-black rounded-full font-bold hover:bg-red-500 hover:text-white transition-colors">&times;</button>
        </div>
        
        <div class="overflow-y-auto p-6 lg:p-8 custom-scrollbar">
            <form action="../actions/process_crud.php" method="POST" class="space-y-6">
                <input type="hidden" name="module" value="scholarships">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Scholarship Name</label><input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Description</label><input type="text" name="description" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-3 font-medium text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Amount (₱)</label><input type="number" name="award_amount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div>
                        <label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Release</label>
                        <select name="release_frequency" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all">
                            <option value="Per Semester">Per Semester</option>
                            <option value="Per Year">Per Year</option>
                            <option value="Per Month">Per Month</option>
                            <option value="Upon Completion">Upon Completion</option>
                        </select>
                    </div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Slots</label><input type="number" name="slots" min="1" placeholder="Unlimited" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Min. GPA</label><input type="number" step="0.01" name="min_gpa" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Deadline</label><input type="date" name="deadline" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                </div>

                <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <select name="scholarship_type" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="Private">🏢 Private</option><option value="Government">🏛️ Government</option></select>
                    <select name="program_id" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="">🌍 Any Program</option><?php foreach($programs as $p): ?><option value="<?= $p['ProgramID'] ?>"><?= htmlspecialchars($p['ProgramName']) ?></option><?php endforeach; ?></select>
                    <select name="year_level" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="">🌍 Any Year</option><option value="1st Year">1st Year</option><option value="2nd Year">2nd Year</option><option value="3rd Year">3rd Year</option><option value="4th Year">4th Year</option></select>
                    <select name="gender_req" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="Any">⚧ Any Gender</option><option value="Female">Female Only</option><option value="Male">Male Only</option></select>
                    <select name="allows_dual" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all">
                        <option value="No">🚫 No Dual Grants</option>
                        <option value="Yes">✅ Allow Dual Grants</option>
                    </select>
                </div>

                <div class="mt-6 p-6 bg-slate-50 border border-slate-200 rounded-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="text-[14px] font-black text-black uppercase tracking-widest">Requirements & Form Builder</h4>
                        </div>
                        <button type="button" onclick="addUnifiedField('create-fields-container')" class="text-[12px] font-black bg-blue-100 text-blue-700 px-4 py-2 rounded-lg uppercase tracking-widest hover:bg-blue-200 transition-all shadow-sm">+ Add Row</button>
                    </div>
                    <div id="create-fields-container" class="space-y-3"></div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 bg-slate-200 text-black py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-slate-300 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 bg-slate-900 text-white py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Launch Scholarship</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-[95%] sm:max-w-5xl rounded-[1.5rem] lg:rounded-[2.5rem] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-6 lg:p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
            <div>
                <h3 class="font-black text-black text-lg uppercase tracking-tight">Edit Scholarship</h3>
                <p class="text-[14px] text-blue-600 font-bold mt-1 uppercase tracking-widest">Update core rules and dynamic fields</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 bg-slate-200 text-black rounded-full font-bold hover:bg-red-500 hover:text-white transition-colors">&times;</button>
        </div>
        
        <div class="overflow-y-auto p-6 lg:p-8 custom-scrollbar">
            <form action="../actions/process_crud.php" method="POST" class="space-y-6">
                <input type="hidden" name="module" value="scholarships">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="scholarship_id" id="edit_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Scholarship Name</label><input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Description</label><input type="text" name="description" id="edit_desc" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-3 font-medium text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-6 gap-4">
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Amount (₱)</label><input type="number" name="award_amount" id="edit_amount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div>
                        <label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Release</label>
                        <select name="release_frequency" id="edit_release" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all">
                            <option value="Per Semester">Per Semester</option>
                            <option value="Per Year">Per Year</option>
                            <option value="Per Month">Per Month</option>
                            <option value="Upon Completion">Upon Completion</option>
                        </select>
                    </div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Slots</label><input type="number" name="slots" id="edit_slots" min="1" placeholder="Unlimited" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Min. GPA</label><input type="number" step="0.01" name="min_gpa" id="edit_gpa" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Deadline</label><input type="date" name="deadline" id="edit_deadline" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"></div>
                    <div><label class="block text-[10px] font-black text-black uppercase tracking-widest mb-2">Status</label><select name="status" id="edit_status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-sm outline-none focus:border-blue-500 transition-all"><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div>
                </div>

                <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <select name="scholarship_type" id="edit_type" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="Private">🏢 Private</option><option value="Government">🏛️ Government</option></select>
                    <select name="program_id" id="edit_program" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="">🌍 Any Program</option><?php foreach($programs as $p): ?><option value="<?= $p['ProgramID'] ?>"><?= htmlspecialchars($p['ProgramName']) ?></option><?php endforeach; ?></select>
                    <select name="year_level" id="edit_year" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="">🌍 Any Year</option><option value="1st Year">1st Year</option><option value="2nd Year">2nd Year</option><option value="3rd Year">3rd Year</option><option value="4th Year">4th Year</option></select>
                    <select name="gender_req" id="edit_gender" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all"><option value="Any">⚧ Any Gender</option><option value="Female">Female Only</option><option value="Male">Male Only</option></select>
                    <select name="allows_dual" id="edit_dual" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-black text-xs lg:text-[11px] uppercase tracking-widest outline-none focus:border-blue-500 transition-all">
                        <option value="No">🚫 No Dual Grants</option>
                        <option value="Yes">✅ Allow Dual Grants</option>
                    </select>
                </div>

                <div class="mt-6 p-6 bg-slate-50 border border-slate-200 rounded-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="text-[14px] font-black text-black uppercase tracking-widest">Requirements & Form Builder</h4>
                        </div>
                        <button type="button" onclick="addUnifiedField('edit-fields-container')" class="text-[12px] font-black bg-blue-100 text-blue-700 px-4 py-2 rounded-lg uppercase tracking-widest hover:bg-blue-200 transition-all shadow-sm">+ Add Row</button>
                    </div>
                    <div id="edit-fields-container" class="space-y-3"></div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-200 text-black py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-slate-300 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg active:scale-[0.98]">Save Changes 💾</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 1; let itemsPerPage = 10;
    const cards = Array.from(document.querySelectorAll('.scholarship-card'));
    const totalItems = cards.length;

    function renderPagination() {
        if (totalItems === 0) return;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        cards.forEach(card => card.style.display = 'none');
        const start = (currentPage - 1) * itemsPerPage; const end = start + itemsPerPage;
        cards.slice(start, end).forEach(card => card.style.display = 'flex');

        const pageInfo = document.getElementById('pageInfo');
        if (pageInfo) pageInfo.innerText = `Showing ${start + 1} to ${Math.min(end, totalItems)} of ${totalItems} Scholarships`;
        
        let btnHtml = `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-100">&larr;</button>`;
        btnHtml += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-100">&rarr;</button>`;
        document.getElementById('paginationControls').innerHTML = btnHtml;
    }
    function changePage(page) { currentPage = page; renderPagination(); }
    document.addEventListener('DOMContentLoaded', renderPagination);

    function addUnifiedField(containerId, presetId='', presetName='', presetType='Text', presetCategory='Custom') {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'flex flex-col sm:flex-row gap-2 sm:gap-3 items-center';
        
        if(presetId !== '') {
            div.id = `existing-field-${presetCategory}-${presetId}`;
            div.innerHTML += `<input type="hidden" name="existing_${presetCategory}_ids[]" value="${presetId}">`;
        }

        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'flex-1 w-full';
        inputWrapper.innerHTML = `<input type="text" name="${presetId !== '' ? `existing_${presetCategory}_names[]` : 'new_dynamic_names[]'}" value="${presetName}" required placeholder="Type requirement or question name..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 font-bold text-sm outline-none focus:border-blue-500 shadow-sm">`;

        const typeWrapper = document.createElement('div');
        typeWrapper.className = 'w-full sm:w-48 shrink-0';
        
        const types = [
            { val: 'Document', label: '📄 Document' },
            { val: 'Criteria', label: '⭐ Criteria' },
            { val: 'Text', label: '✍️ Short Text' },
            { val: 'Paragraph', label: '📝 Paragraph' },
            { val: 'Number', label: '🔢 Number' },
            { val: 'Date', label: '📅 Date' }
        ];

        let mappedType = presetType;
        if (presetCategory === 'Req') mappedType = 'Document';
        if (presetCategory === 'Crit') mappedType = 'Criteria';
        if (presetType === 'Textarea') mappedType = 'Paragraph';

        if (presetId !== '') {
            typeWrapper.innerHTML = `<input type="hidden" name="existing_${presetCategory}_types[]" value="${mappedType}"><select disabled class="w-full bg-slate-100 text-slate-500 border border-slate-200 rounded-xl px-3 py-3 font-bold text-xs uppercase tracking-widest cursor-not-allowed shadow-sm"><option>${types.find(t=>t.val===mappedType).label}</option></select>`;
        } else {
            let selectHtml = `<select name="new_dynamic_types[]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-3 font-bold text-xs uppercase tracking-widest outline-none focus:border-blue-500 cursor-pointer shadow-sm">`;
            types.forEach(t => { selectHtml += `<option value="${t.val}" ${t.val === mappedType ? 'selected' : ''}>${t.label}</option>`; });
            selectHtml += `</select>`;
            typeWrapper.innerHTML = selectHtml;
        }

        const btnWrapper = document.createElement('div');
        btnWrapper.className = 'w-full sm:w-auto shrink-0 flex justify-end';
        btnWrapper.innerHTML = `<button type="button" onclick="removeUnifiedField(this, '${presetId}', '${presetCategory}')" class="w-11 h-11 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors shadow-sm"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>`;

        div.appendChild(inputWrapper); div.appendChild(typeWrapper); div.appendChild(btnWrapper); container.appendChild(div);
    }

    function removeUnifiedField(btnElement, id, category) {
        if(id !== '') {
            if(confirm("⚠️ Delete this requirement? Existing data from students will be lost!")) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden'; hiddenInput.name = `delete_${category}_ids[]`; hiddenInput.value = id;
                document.getElementById('edit-fields-container').appendChild(hiddenInput);
                document.getElementById(`existing-field-${category}-${id}`).remove();
            }
        } else {
            btnElement.closest('.flex').remove();
        }
    }

    function openCreateModal() {
        document.getElementById('create-fields-container').innerHTML = '';
        document.getElementById('createModal').classList.remove('hidden'); document.getElementById('createModal').classList.add('flex');
    }
    function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); document.getElementById('createModal').classList.remove('flex'); }

    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_desc').value = btn.dataset.desc;
        document.getElementById('edit_amount').value = btn.dataset.amount;
        document.getElementById('edit_release').value = btn.dataset.release || 'Per Semester';
        document.getElementById('edit_slots').value = btn.dataset.slots; 
        document.getElementById('edit_gpa').value = btn.dataset.gpa;
        document.getElementById('edit_deadline').value = btn.dataset.deadline;
        document.getElementById('edit_program').value = btn.dataset.program;
        document.getElementById('edit_year').value = btn.dataset.year;
        document.getElementById('edit_status').value = btn.dataset.status;
        document.getElementById('edit_gender').value = btn.dataset.gender;
        document.getElementById('edit_type').value = btn.dataset.type || 'Private';
        document.getElementById('edit_dual').value = btn.dataset.dual || 'No';

        const fieldsContainer = document.getElementById('edit-fields-container');
        fieldsContainer.innerHTML = '';
        
        if (btn.dataset.requirements) { btn.dataset.requirements.split('||').forEach(req => { const parts = req.split('::'); addUnifiedField('edit-fields-container', parts[0], parts[1], 'Document', 'Req'); }); }
        if (btn.dataset.criteria) { btn.dataset.criteria.split('||').forEach(crit => { const parts = crit.split('::'); addUnifiedField('edit-fields-container', parts[0], parts[1], 'Criteria', 'Crit'); }); }
        if (btn.dataset.customfields) { btn.dataset.customfields.split('||').forEach(field => { const parts = field.split('::'); if(parts.length === 3) { addUnifiedField('edit-fields-container', parts[0], parts[1], parts[2], 'Custom'); } }); }

        document.getElementById('editModal').classList.remove('hidden'); document.getElementById('editModal').classList.add('flex');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); document.getElementById('editModal').classList.remove('flex'); }
</script>
<?php include '../includes/footer.php'; ?>