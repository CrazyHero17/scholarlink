<?php
session_start();

// ✨ 1. SESSION MANAGER
include '../includes/session_manager.php';

// 🛑 2. THE BACK BUTTON KILLER
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 🔒 3. THE SECURITY GATEKEEPER
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'External_Admin') {
    header("Location: ../admin_login.php"); 
    exit();
}

include '../includes/db_connect.php';

// --- FETCH METRICS ---
try {
    // 1. Applications awaiting an Evaluator's score
    $pending_reviews = $pdo->query("SELECT COUNT(*) FROM application WHERE Status = 'Submitted' OR Status = 'Under Review'")->fetchColumn();
    
    // 2. Applications already scored and moved to shortlist
    $completed_evals = $pdo->query("SELECT COUNT(*) FROM application WHERE TotalScore > 0 AND (Status = 'Shortlisted' OR Status = 'Approved')")->fetchColumn();
    
    // 3. Average Score Given (To track grading trends)
    $avg_score = $pdo->query("SELECT AVG(TotalScore) FROM application WHERE TotalScore > 0")->fetchColumn();

    // 4. Fetch Recent Applications for the "Quick Score" table
    $recent_stmt = $pdo->query("
        SELECT a.ApplicationID, u.FirstName, u.LastName, sch.Name AS scholarship_name, a.DateSubmitted 
        FROM application a
        JOIN users u ON a.UserID = u.UserID
        JOIN scholarship sch ON a.ScholarshipID = sch.ScholarshipID
        WHERE a.Status = 'Submitted' OR a.Status = 'Under Review'
        ORDER BY a.DateSubmitted ASC LIMIT 5
    ");
    $recent_apps = $recent_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Evaluator Dashboard Error: " . $e->getMessage());
}

include '../includes/header.php';
include '../includes/external_sidebar.php'; 
?>

<main class="flex-1 p-5 lg:p-10 lg:ml-72 bg-slate-50/50 min-h-screen transition-all duration-300">
    <header class="mb-10">
        <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Evaluator Dashboard</h2>
        <p class="text-slate-500 font-medium mt-1">Review applicant data, verify documents, and assign merit scores.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mb-10">
        
        <div class="bg-white p-6 lg:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 shrink-0 bg-orange-50 text-orange-600 rounded-2xl text-xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all shadow-sm">📝</div>
                <span class="text-[10px] lg:text-[12px] font-black text-orange-400 uppercase tracking-widest">Awaiting Score</span>
            </div>
            <p class="text-[12px] lg:text-[14px] font-black text-slate-500 uppercase tracking-widest mb-1">Pending Review</p>
            <p class="text-4xl font-black text-slate-900"><?= number_format($pending_reviews) ?></p>
            <div class="absolute bottom-0 left-0 h-1 bg-orange-500 transition-all w-full opacity-20 group-hover:opacity-100"></div>
        </div>

        <div class="bg-white p-6 lg:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 shrink-0 bg-green-50 text-green-600 rounded-2xl text-xl flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-all shadow-sm">✅</div>
                <span class="text-[10px] lg:text-[12px] font-black text-green-400 uppercase tracking-widest">Scored</span>
            </div>
            <p class="text-[12px] lg:text-[14px] font-black text-slate-500 uppercase tracking-widest mb-1">Evaluations Finished</p>
            <p class="text-4xl font-black text-slate-900"><?= number_format($completed_evals) ?></p>
            <div class="absolute bottom-0 left-0 h-1 bg-green-500 transition-all w-full opacity-20 group-hover:opacity-100"></div>
        </div>

        <div class="bg-white p-6 lg:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 shrink-0 bg-blue-50 text-blue-600 rounded-2xl text-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">📊</div>
                <span class="text-[10px] lg:text-[12px] font-black text-blue-400 uppercase tracking-widest">System Avg</span>
            </div>
            <p class="text-[12px] lg:text-[14px] font-black text-slate-500 uppercase tracking-widest mb-1">Average Merit Score</p>
            <p class="text-4xl font-black text-slate-900"><?= number_format($avg_score, 1) ?> <span class="text-sm text-slate-300">/ 100</span></p>
            <div class="absolute bottom-0 left-0 h-1 bg-blue-500 transition-all w-full opacity-20 group-hover:opacity-100"></div>
        </div>

    </div>

    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-widest text-s">Priority Applications</h3>
                <p class="text-[14px] text-slate-900 font-bold mt-1">Oldest submissions requiring immediate score assignment.</p>
            </div>
            <a href="score.php" class="bg-slate-900 text-white px-6 py-3 rounded-xl text-[14px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-slate-200">View All Tasks</a>
        </div>
        
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-[14px] font-black text-black uppercase tracking-widest">
                    <th class="p-6">Applicant</th>
                    <th class="p-6">Scholarship</th>
                    <th class="p-6">Submission Date</th>
                    <th class="p-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($recent_apps as $app): ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-6">
                        <p class="font-black text-slate-900 text-base leading-none mb-1"><?= htmlspecialchars($app['FirstName'] . ' ' . $app['LastName']) ?></p>
                        <p class="text-[14px] font-bold text-slate-400 uppercase">APP-<?= str_pad($app['ApplicationID'], 4, '0', STR_PAD_LEFT) ?></p>
                    </td>
                    <td class="p-6 font-bold text-blue-600 text-sm"><?= htmlspecialchars($app['scholarship_name']) ?></td>
                    <td class="p-6 text-[11px] font-bold text-slate-500 uppercase tracking-tight"><?= date('M d, Y', strtotime($app['DateSubmitted'])) ?></td>
                    <td class="p-6 text-right">
                        <a href="score.php?id=<?= $app['ApplicationID'] ?>" class="inline-block bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl text-[14px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all">Score Now ✍️</a>
                    </td>
                </tr>
                <?php endforeach; if(empty($recent_apps)): ?>
                <tr>
                    <td colspan="4" class="p-20 text-center">
                        <div class="text-4xl mb-4 grayscale opacity-30">☕</div>
                        <h3 class="text-lg font-black text-slate-900">All caught up!</h3>
                        <p class="text-slate-400 font-medium text-sm mt-1">There are no pending applications awaiting your evaluation.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>