<?php
session_start();
require 'includes/db_connect.php';
require 'includes/session_manager.php';

$scholarship_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$is_student = $is_logged_in && ($_SESSION['role'] === 'Student');
$user_id = $_SESSION['user_id'] ?? null;

// 1. FETCH SCHOLARSHIP DETAILS & SPONSOR INFO
try {
    $stmt = $pdo->prepare("
        SELECT s.*, p.ProgramName, u.Organization as SponsorName, u.FirstName, u.LastName 
        FROM scholarship s 
        LEFT JOIN program p ON s.ProgramID = p.ProgramID 
        LEFT JOIN users u ON s.CreatedBy = u.UserID 
        WHERE s.ScholarshipID = ?
    ");
    $stmt->execute([$scholarship_id]);
    $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$scholarship) {
        die("<h2>Scholarship not found.</h2><a href='index.php'>Go back</a>");
    }

    $sponsor_name = !empty($scholarship['SponsorName']) ? $scholarship['SponsorName'] : trim($scholarship['FirstName'] . ' ' . $scholarship['LastName']);
    if (empty($sponsor_name)) $sponsor_name = 'TAU Institutional Fund';

    // 2. FETCH REQUIREMENTS FOR THIS SCHOLARSHIP
    $req_stmt = $pdo->prepare("SELECT * FROM document_requirement WHERE ScholarshipID = ?");
    $req_stmt->execute([$scholarship_id]);
    $requirements = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. STUDENT ELIGIBILITY CHECKER
    $has_applied = false;
    $is_eligible_gwa = true;
    $missing_docs = [];
    $student_gwa = 5.00;
    $application_status = '';

    if ($is_student) {
        // Check if already applied
        $app_stmt = $pdo->prepare("SELECT Status FROM application WHERE ScholarshipID = ? AND UserID = ?");
        $app_stmt->execute([$scholarship_id, $user_id]);
        $existing_app = $app_stmt->fetch();
        
        if ($existing_app) {
            $has_applied = true;
            $application_status = $existing_app['Status'];
        } else {
            // Fetch Student Details
            $user_stmt = $pdo->prepare("SELECT GPA FROM users WHERE UserID = ?");
            $user_stmt->execute([$user_id]);
            $student_data = $user_stmt->fetch();
            $student_gwa = (float)($student_data['GPA'] ?? 5.00);

            if ($student_gwa > (float)$scholarship['MinimumGWA'] || $student_gwa == 0.00) {
                $is_eligible_gwa = false;
            }

            // Fetch Student Vault
            $vault_stmt = $pdo->prepare("SELECT DocumentType FROM user_vault WHERE UserID = ?");
            $vault_stmt->execute([$user_id]);
            $vault_docs = $vault_stmt->fetchAll(PDO::FETCH_COLUMN);

            // Cross-match requirements with vault
            foreach ($requirements as $req) {
                if (!in_array($req['DocumentName'], $vault_docs)) {
                    $missing_docs[] = $req['DocumentName'];
                }
            }
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($scholarship['Name']) ?> - ScholarLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .glass-panel { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3">
                <img src="assets/img/tau_logo.png" alt="TAU Logo" class="h-10 w-10">
                <div>
                    <h1 class="font-black text-slate-900 leading-tight">ScholarLink</h1>
                    <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest">Tarlac Agricultural University</p>
                </div>
            </a>
            <div>
                <?php if ($is_student): ?>
                    <a href="student/dashboard.php" class="text-sm font-bold text-slate-600 hover:text-green-600 mr-4">My Dashboard</a>
                <?php endif; ?>
                <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold py-2.5 px-5 rounded-xl transition-all">Back to Home</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Flash Messages -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 font-bold flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-bold flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN: Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-panel p-8 rounded-[2rem] shadow-sm relative overflow-hidden">
                    <!-- Ribbon -->
                    <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-green-500 to-green-700"></div>
                    
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-black uppercase tracking-widest rounded-lg mb-4">
                        <?= htmlspecialchars($scholarship['ScholarshipType']) ?> Grant
                    </span>
                    
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 mb-4 leading-tight">
                        <?= htmlspecialchars($scholarship['Name']) ?>
                    </h1>
                    
                    <div class="flex items-center gap-3 text-slate-500 font-medium mb-6">
                        <i class="fas fa-building text-slate-400"></i>
                        <span>Funded by <strong class="text-slate-700"><?= htmlspecialchars($sponsor_name) ?></strong></span>
                    </div>

                    <p class="text-slate-600 text-lg leading-relaxed mb-8">
                        <?= nl2br(htmlspecialchars($scholarship['Description'])) ?>
                    </p>

                    <!-- Fast Facts Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Target Program</p>
                            <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($scholarship['ProgramName'] ?? 'Open to All') ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Target Year</p>
                            <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($scholarship['YearLevel'] ?? 'All Levels') ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Min. GWA</p>
                            <p class="font-bold text-slate-800 text-sm"><?= number_format($scholarship['MinimumGWA'], 2) ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Award Amount</p>
                            <p class="font-black text-green-600 text-sm">₱<?= number_format($scholarship['AwardAmount'], 2) ?></p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-[2rem] shadow-sm">
                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                        <i class="fas fa-file-contract text-blue-500"></i> Required Documents
                    </h3>
                    
                    <?php if (empty($requirements)): ?>
                        <p class="text-slate-500 font-medium italic">No specific documents required.</p>
                    <?php else: ?>
                        <ul class="space-y-4">
                            <?php foreach($requirements as $req): ?>
                                <?php 
                                    $is_in_vault = $is_student && !in_array($req['DocumentName'], $missing_docs); 
                                ?>
                                <li class="flex items-center justify-between p-4 rounded-xl <?= $is_in_vault ? 'bg-green-50 border border-green-100' : 'bg-slate-50 border border-slate-200' ?>">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-pdf <?= $is_in_vault ? 'text-green-500' : 'text-red-400' ?> text-xl"></i>
                                        <span class="font-bold <?= $is_in_vault ? 'text-green-800' : 'text-slate-700' ?>"><?= htmlspecialchars($req['DocumentName']) ?></span>
                                    </div>
                                    <?php if ($is_student): ?>
                                        <?php if ($is_in_vault): ?>
                                            <span class="text-xs font-black bg-green-200 text-green-700 px-3 py-1 rounded-full uppercase tracking-widest">Ready in Vault</span>
                                        <?php else: ?>
                                            <span class="text-xs font-black bg-red-100 text-red-600 px-3 py-1 rounded-full uppercase tracking-widest">Missing</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT COLUMN: Application Panel & Timer -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Countdown Timer Widget -->
                <div class="glass-panel p-6 rounded-[2rem] shadow-sm text-center">
                    <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl">
                        <i class="fas fa-hourglass-half animate-pulse"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-1">Application Deadline</h3>
                    <p class="font-bold text-slate-800 mb-6"><?= date('F j, Y', strtotime($scholarship['Deadline'])) ?></p>
                    
                    <div id="countdown" class="grid grid-cols-3 gap-2" data-deadline="<?= $scholarship['Deadline'] ?> 23:59:59">
                        <div class="bg-slate-900 text-white rounded-xl p-3">
                            <span id="days" class="block text-2xl font-black">--</span>
                            <span class="text-[9px] uppercase tracking-widest opacity-70 font-bold">Days</span>
                        </div>
                        <div class="bg-slate-900 text-white rounded-xl p-3">
                            <span id="hours" class="block text-2xl font-black">--</span>
                            <span class="text-[9px] uppercase tracking-widest opacity-70 font-bold">Hours</span>
                        </div>
                        <div class="bg-slate-900 text-white rounded-xl p-3">
                            <span id="mins" class="block text-2xl font-black">--</span>
                            <span class="text-[9px] uppercase tracking-widest opacity-70 font-bold">Mins</span>
                        </div>
                    </div>
                </div>

                <!-- 1-Click Apply Command Center -->
                <div class="glass-panel p-6 rounded-[2rem] shadow-sm border-t-4 border-t-blue-500">
                    <h3 class="font-black text-slate-900 mb-4 text-lg">Application Status</h3>
                    
                    <?php if (!$is_logged_in): ?>
                        <div class="text-center p-4 bg-slate-50 rounded-xl border border-slate-200 mb-4">
                            <i class="fas fa-lock text-3xl text-slate-300 mb-2"></i>
                            <p class="text-sm text-slate-600 font-medium">Log in to your student account to check your eligibility and apply.</p>
                        </div>
                        <a href="student_login.php" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-md">
                            Log In to Apply
                        </a>

                    <?php elseif (!$is_student): ?>
                        <div class="text-center p-4 bg-orange-50 rounded-xl border border-orange-200 text-orange-800">
                            <i class="fas fa-shield-alt text-2xl mb-2"></i>
                            <p class="text-sm font-bold">Administrators cannot apply for scholarships.</p>
                        </div>

                    <?php elseif (strtolower($scholarship['Status']) !== 'active'): ?>
                        <div class="text-center p-4 bg-slate-100 rounded-xl border border-slate-300 text-slate-600">
                            <i class="fas fa-times-circle text-2xl mb-2"></i>
                            <p class="text-sm font-bold">This scholarship is currently closed.</p>
                        </div>

                    <?php elseif ($has_applied): ?>
                        <div class="text-center p-6 bg-green-50 rounded-xl border border-green-200">
                            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4 class="font-black text-green-800 text-lg mb-1">Application Submitted</h4>
                            <p class="text-xs font-bold text-green-600 uppercase tracking-widest mb-4">Status: <?= htmlspecialchars($application_status) ?></p>
                            <a href="student/applications.php" class="inline-block bg-white border border-green-200 text-green-700 font-bold py-2 px-6 rounded-lg hover:bg-green-100 transition-colors text-sm">Track Progress</a>
                        </div>

                    <?php else: ?>
                        <!-- Eligibility Diagnostics -->
                        <div class="space-y-3 mb-6">
                            <?php if (!$is_eligible_gwa): ?>
                                <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-100">
                                    <i class="fas fa-times-circle text-red-500 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-red-800">GWA Requirement Not Met</p>
                                        <p class="text-xs text-red-600 mt-1">Your GWA is <?= $student_gwa == 0.00 ? 'not yet encoded' : number_format($student_gwa, 2) ?>. Required is <?= number_format($scholarship['MinimumGWA'], 2) ?>.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm font-bold text-green-800">GWA Requirement Met</span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($missing_docs)): ?>
                                <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-lg border border-orange-100">
                                    <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-bold text-orange-800">Missing Vault Documents</p>
                                        <p class="text-xs text-orange-600 mt-1">You need to upload <?= count($missing_docs) ?> more document(s) to your vault before applying.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm font-bold text-green-800">All Documents Ready in Vault</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <?php if ($is_eligible_gwa && empty($missing_docs)): ?>
                            <form action="actions/process_application.php" method="POST" onsubmit="return confirm('Are you sure you want to submit your application? This will pull your requirements from your Document Vault.');">
                                <input type="hidden" name="scholarship_id" value="<?= $scholarship_id ?>">
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 px-4 rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2 text-lg">
                                    <i class="fas fa-bolt"></i> 1-Click Apply Now
                                </button>
                                <p class="text-[10px] text-center font-bold text-slate-400 mt-3"><i class="fas fa-shield-check text-green-500"></i> Documents will be automatically synced from your vault.</p>
                            </form>
                        <?php else: ?>
                            <button disabled class="w-full bg-slate-200 text-slate-400 font-black py-4 px-4 rounded-xl cursor-not-allowed text-lg">
                                <i class="fas fa-lock"></i> Apply Now
                            </button>
                            <?php if (!empty($missing_docs)): ?>
                                <a href="student/vault.php" class="mt-3 block w-full text-center bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-3 px-4 rounded-xl transition-colors border border-blue-200">
                                    Go to Document Vault <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <!-- Simple Countdown Timer Script -->
    <script>
        const countdownEl = document.getElementById('countdown');
        if (countdownEl) {
            const deadline = new Date(countdownEl.dataset.deadline).getTime();
            
            const timer = setInterval(() => {
                const now = new Date().getTime();
                const distance = deadline - now;

                if (distance < 0) {
                    clearInterval(timer);
                    document.getElementById('days').innerText = "00";
                    document.getElementById('hours').innerText = "00";
                    document.getElementById('mins').innerText = "00";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                document.getElementById('days').innerText = days < 10 ? '0' + days : days;
                document.getElementById('hours').innerText = hours < 10 ? '0' + hours : hours;
                document.getElementById('mins').innerText = mins < 10 ? '0' + mins : mins;
            }, 1000);
        }
    </script>
</body>
</html>