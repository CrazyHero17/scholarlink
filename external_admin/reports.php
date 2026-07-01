<?php
session_start();

// ✨ 1. SESSION MANAGER (Absolute top, checks for timeouts immediately)
include '../includes/session_manager.php';

// 🛑 2. THE BACK BUTTON KILLER (Prevents browser caching of sensitive admin data)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 🔒 3. THE SECURITY GATEKEEPER
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'External_Admin') {
    header("Location: ../admin_login.php"); 
    exit();
}


include '../includes/db_connect.php'; 
include '../includes/header.php'; 
include '../includes/external_sidebar.php'; 

try {
    // Metric 1: How many applications have been scored?
    $scored_apps = $pdo->query("SELECT COUNT(*) FROM application WHERE TotalScore > 0")->fetchColumn();

    // Metric 2: How many active scholarships are there?
    $active_scholars = $pdo->query("SELECT COUNT(*) FROM scholarship WHERE Status = 'Active'")->fetchColumn();

    // Fetch scholarships for the filter dropdown
    $scholarships = $pdo->query("SELECT ScholarshipID, Name FROM scholarship ORDER BY Name ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<main class="flex-1 p-5 lg:p-10 lg:ml-72 bg-slate-50/50 min-h-screen transition-all duration-300">
    <header class="mb-8 lg:mb-10">
        <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Evaluation Reports</h2>
        <p class="text-slate-500 text-sm lg:text-base font-medium mt-1">Export official applicant scores and evaluation statuses.</p>
    </header>

    <form method="GET" target="_blank">
        <div class="bg-white p-6 lg:p-8 rounded-[1.5rem] lg:rounded-[2rem] border border-slate-200 shadow-sm mb-8 lg:mb-10">
            <h3 class="text-s font-black text-black uppercase tracking-widest mb-4 lg:mb-6">Report Filters</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                <div>
                    <label class="block text-[14px] font-black text-black uppercase tracking-widest mb-2">Target Scholarship</label>
                    <select name="scholarship_id" class="w-full bg-slate-50 border border-slate-100 px-4 py-3 rounded-xl font-bold text-s text-black outline-none focus:ring-2 focus:ring-blue-500/20 transition-all">
                        <option value="">All Scholarships</option>
                        <?php foreach($scholarships as $s): ?>
                            <option value="<?= $s['ScholarshipID'] ?>"><?= htmlspecialchars($s['Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[14px] font-black text-black uppercase tracking-widest mb-2">Application Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-100 px-4 py-3 rounded-xl font-bold text-s text-black outline-none focus:ring-2 focus:ring-blue-500/20 transition-all">
                        <option value="">All Statuses</option>
                        <option value="Under Review">Under Review (Pending Score)</option>
                        <option value="Shortlisted">Shortlisted (Scored)</option>
                        <option value="Approved">Approved (Finalized)</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 mb-10 lg:mb-12">
            <div class="bg-white p-8 lg:p-10 rounded-[1.5rem] lg:rounded-[2rem] border border-slate-200 shadow-sm hover:border-green-400 transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-green-50 text-green-600 rounded-[1.25rem] flex items-center justify-center text-2xl lg:text-3xl mb-6 group-hover:bg-green-600 group-hover:text-white transition-all">📊</div>
                    <h3 class="text-xl lg:text-2xl font-black text-slate-900 mb-2">Raw Data (CSV)</h3>
                    <p class="text-slate-500 text-s lg:text-sm mb-8 font-medium">Export a spreadsheet of applicants and their evaluator scores.</p>
                </div>
                <button type="submit" formaction="../actions/export_evaluations_csv.php" class="w-full sm:w-auto self-start bg-slate-900 text-white px-8 py-4 rounded-xl lg:rounded-2xl font-black text-[14px] lg:text-s uppercase tracking-widest hover:bg-green-600 transition-all shadow-xl shadow-slate-200 active:scale-[0.98]">
                    Download CSV Data
                </button>
            </div>

            <div class="bg-white p-8 lg:p-10 rounded-[1.5rem] lg:rounded-[2rem] border border-slate-200 shadow-sm hover:border-blue-400 transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-blue-50 text-blue-600 rounded-[1.25rem] flex items-center justify-center text-2xl lg:text-3xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">📄</div>
                    <h3 class="text-xl lg:text-2xl font-black text-slate-900 mb-2">Evaluation Summary (PDF)</h3>
                    <p class="text-slate-500 text-s lg:text-sm mb-8 font-medium">Generate a formal, printable PDF report of evaluated applicants.</p>
                </div>
                <button type="submit" formaction="../actions/export_evaluations_pdf.php" class="w-full sm:w-auto self-start bg-slate-900 text-white px-8 py-4 rounded-xl lg:rounded-2xl font-black text-[14px] lg:text-s uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-[0.98]">
                    Generate PDF Report
                </button>
            </div>
        </div>
    </form>

    <div class="bg-slate-900 rounded-[1.5rem] lg:rounded-[2rem] p-8 lg:p-10 text-white shadow-2xl shadow-slate-300">
        <h4 class="text-[9px] lg:text-[14px] font-black uppercase tracking-widest text-slate-400 mb-6">Evaluator Milestones</h4>
        <div class="flex flex-col sm:flex-row gap-8 sm:gap-20">
            <div>
                <p class="text-s lg:text-sm font-bold text-slate-400">Applications Scored</p>
                <p class="text-3xl lg:text-4xl font-black text-green-400"><?= $scored_apps ?></p>
            </div>
            <div>
                <p class="text-s lg:text-sm font-bold text-slate-400">Active Scholarships Monitored</p>
                <p class="text-3xl lg:text-4xl font-black text-blue-400"><?= $active_scholars ?></p>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>