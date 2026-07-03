<?php
session_start();
require 'includes/db_connect.php';

// Validate requested ID
$scholarship_id = $_GET['id'] ?? null;
if (!$scholarship_id) {
    header("Location: index.php");
    exit();
}

// Fetch main scholarship data
try {
    $stmt = $pdo->prepare("
        SELECT s.*, p.ProgramName 
        FROM scholarship s 
        LEFT JOIN program p ON s.ProgramID = p.ProgramID 
        WHERE s.ScholarshipID = :id
    ");
    $stmt->execute(['id' => $scholarship_id]);
    $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$scholarship) {
        header("Location: index.php");
        exit();
    }

    // Fetch dynamic fields
    $req_stmt = $pdo->prepare("SELECT DocumentName FROM document_requirement WHERE ScholarshipID = ?");
    $req_stmt->execute([$scholarship_id]);
    $requirements = $req_stmt->fetchAll(PDO::FETCH_COLUMN);

    $crit_stmt = $pdo->prepare("SELECT CriteriaName FROM scholarship_criteria WHERE ScholarshipID = ?");
    $crit_stmt->execute([$scholarship_id]);
    $criteria = $crit_stmt->fetchAll(PDO::FETCH_COLUMN);

    $cust_stmt = $pdo->prepare("SELECT FieldName FROM scholarship_custom_fields WHERE ScholarshipID = ?");
    $cust_stmt->execute([$scholarship_id]);
    $custom_fields = $cust_stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    die("Error loading scholarship details.");
}

// ✨ FIXED: Added Security Parameters (ixlib) and verified Image IDs
function getProgramImage($programName) {
    $prog = strtolower($programName ?? '');
    $params = '?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
    
    if (strpos($prog, 'information technology') !== false || strpos($prog, 'computer') !== false || strpos($prog, 'it') !== false) {
        return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97' . $params;
    } elseif (strpos($prog, 'agri') !== false || strpos($prog, 'forest') !== false) {
        return 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449' . $params; // Stable agriculture image
    } elseif (strpos($prog, 'education') !== false || strpos($prog, 'teach') !== false || strpos($prog, 'ed') !== false) {
        return 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655' . $params;
    } elseif (strpos($prog, 'engineer') !== false || strpos($prog, 'arch') !== false) {
        return 'https://images.unsplash.com/photo-1581092160562-40aa08e78837' . $params;
    } elseif (strpos($prog, 'business') !== false || strpos($prog, 'account') !== false || strpos($prog, 'manage') !== false) {
        return 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40' . $params;
    } elseif (strpos($prog, 'vet') !== false || strpos($prog, 'med') !== false || strpos($prog, 'health') !== false || strpos($prog, 'sci') !== false) {
        return 'https://images.unsplash.com/photo-1532094349884-543bc11b234d' . $params;
    }
    
    return 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1' . $params; 
}

$heroImage = getProgramImage($scholarship['ProgramName']);

// Check session
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$role_redirect = "student_login.php";
if ($is_logged_in) {
    if ($_SESSION['role'] === 'Student') $role_redirect = "student/dashboard.php";
    elseif ($_SESSION['role'] === 'Internal_Admin') $role_redirect = "internal_admin/dashboard.php";
    elseif ($_SESSION['role'] === 'External_Admin') $role_redirect = "external_admin/dashboard.php";
    elseif ($_SESSION['role'] === 'Super_Admin') $role_redirect = "super_admin/dashboard.php";
}

$status = $scholarship['Status'] ?? 'Active';
$isActive = strtolower($status) === 'active';
$deadline = $scholarship['Deadline'] ? date('M d, Y', strtotime($scholarship['Deadline'])) : 'No deadline';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($scholarship['Name']) ?> - ScholarLink</title>
    <link rel="icon" type="image/png" href="assets/img/tau_logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --ink: #123524;
            --muted: #5f7469;
            --line: #d7e8dd;
            --wash: #f3faf5;
            --green: #198754;
            --gold: #b7791f;
        }
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background: var(--wash); color: var(--ink); }
        .site-header { position: sticky; top: 0; z-index: 50; background: rgba(255, 255, 255, 0.95); box-shadow: 0 7px 20px rgba(0, 0, 0, 0.06); backdrop-filter: blur(14px); }
        .nav-shell { width: min(1180px, calc(100% - 40px)); margin: 0 auto; min-height: 82px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: inline-flex; align-items: center; gap: 12px; }
        .brand img { width: 44px; height: 44px; transition: transform 0.3s ease; }
        .brand:hover img { transform: scale(1.05); }
        .brand strong { display: block; font-size: 1.05rem; letter-spacing: -0.02em; color: var(--ink); }
        .brand span { display: block; color: var(--gold); font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; }
        
        .page-container { width: min(1180px, calc(100% - 40px)); margin: 40px auto 80px; }
        .content-grid { display: grid; grid-template-columns: 2.2fr 1fr; gap: 32px; align-items: start; }

        /* FIXED HERO BANNER CSS */
        .hero-banner {
            position: relative;
            background: linear-gradient(135deg, #0f5132 0%, #198754 100%); /* Fallback gradient */
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 2.5rem;
            min-height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .btn-primary { background: var(--green); color: #fff; font-weight: 800; padding: 16px 24px; border-radius: 12px; text-align: center; display: block; width: 100%; transition: all 0.2s; box-shadow: 0 10px 20px rgba(25, 135, 84, 0.15); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(25, 135, 84, 0.25); color: #fff; }
        
        .btn-ghost { background: #fff; border: 1px solid var(--line); color: var(--ink); font-weight: 800; padding: 16px 24px; border-radius: 12px; text-align: center; display: block; width: 100%; transition: all 0.2s; }
        .btn-ghost:hover { border-color: var(--green); color: var(--green); background: #f8fafc; }

        .tag-pill { display: inline-block; background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); color: #fff; padding: 6px 16px; border-radius: 999px; font-size: 0.75rem; font-weight: 900; letter-spacing: 0.06em; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.3); }

        @media (max-width: 900px) {
            .content-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header class="site-header">
        <nav class="nav-shell">
            <a class="brand" href="index.php">
                <img src="assets/img/tau_logo.png" alt="TAU Logo">
                <span>
                    <strong>ScholarLink</strong>
                    <span>Tarlac Agricultural University</span>
                </span>
            </a>
            <div>
                <a href="index.php" class="text-slate-500 font-bold hover:text-green-700 transition-colors"><i class="fas fa-arrow-left mr-2"></i> Back to Grants</a>
            </div>
        </nav>
    </header>

    <div class="page-container">
        
        <!-- ✨ FIXED: Added onerror="this.style.display='none'" so broken images gracefully disappear -->
        <div class="hero-banner group">
            <img src="<?= $heroImage ?>" onerror="this.style.display='none'" alt="Program Background" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/70 to-transparent"></div>
            
            <div class="relative z-10 p-8 lg:p-12 text-white">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="tag-pill"><?= htmlspecialchars($scholarship['ProgramName'] ?? 'Open to All Courses') ?></span>
                    <?php if($isActive): ?>
                        <span class="inline-block bg-green-500/20 backdrop-blur-md text-green-300 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest border border-green-500/30 shadow-sm"><i class="fas fa-circle text-[8px] mr-1 animate-pulse"></i> Accepting Applicants</span>
                    <?php else: ?>
                        <span class="inline-block bg-slate-500/30 backdrop-blur-md text-slate-300 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest border border-slate-500/30 shadow-sm"><i class="fas fa-lock text-[10px] mr-1"></i> Closed</span>
                    <?php endif; ?>
                </div>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black tracking-tight leading-tight"><?= htmlspecialchars($scholarship['Name']) ?></h1>
            </div>
        </div>

        <div class="content-grid">
            
            <!-- LEFT COLUMN: Content -->
            <div class="space-y-8">
                
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3"><i class="fas fa-circle-info text-blue-600"></i> About this Scholarship</h3>
                    <p class="text-slate-600 font-medium leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($scholarship['Description']) ?></p>
                </div>

                <?php if (!empty($criteria)): ?>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3"><i class="fas fa-list-check text-green-600"></i> Qualification Criteria</h3>
                    <ul class="space-y-4">
                        <?php foreach($criteria as $c): ?>
                            <li class="flex items-start gap-4">
                                <span class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fas fa-check text-xs"></i></span>
                                <span class="text-slate-700 font-medium"><?= htmlspecialchars($c) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($requirements)): ?>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3"><i class="fas fa-folder-open text-amber-500"></i> Required Documents</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach($requirements as $r): ?>
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 flex items-center gap-3">
                                <i class="fas fa-file-pdf text-slate-400 text-xl"></i>
                                <span class="text-slate-700 font-bold text-sm"><?= htmlspecialchars($r) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($custom_fields)): ?>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3"><i class="fas fa-clipboard-question text-purple-600"></i> Additional Application Questions</h3>
                    <p class="text-slate-500 font-medium mb-4 text-sm">You will be required to answer these questions during your application process:</p>
                    <ul class="list-disc list-inside text-slate-700 font-medium space-y-2 ml-4">
                        <?php foreach($custom_fields as $cf): ?>
                            <li><?= htmlspecialchars($cf) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>

            <!-- RIGHT COLUMN: Sticky Sidebar -->
            <div class="sticky top-28 space-y-6">
                
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200">
                    <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Scholarship Overview</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Grant Amount</span>
                            <span class="text-3xl font-black text-yellow-600">₱<?= number_format($scholarship['AwardAmount']) ?></span>
                            <span class="text-xs font-bold text-slate-500 ml-1"><?= htmlspecialchars($scholarship['ReleaseFrequency'] ?? 'Per Semester') ?></span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-6">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Min GWA</span>
                                <span class="text-lg font-black text-green-700"><?= htmlspecialchars($scholarship['MinimumGWA']) ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Slots</span>
                                <span class="text-lg font-black text-slate-700"><?= $scholarship['NumberOfSlots'] ?? 'Unlimited' ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Target Year</span>
                                <span class="text-sm font-black text-slate-700"><?= !empty($scholarship['YearLevel']) ? $scholarship['YearLevel'] : 'All Levels' ?></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Deadline</span>
                                <span class="text-sm font-black text-red-600"><?= $deadline ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-100 space-y-3">
                        <?php if($isActive): ?>
                            <a href="<?= $is_logged_in ? $role_redirect : 'student_login.php' ?>" class="btn-primary">
                                Apply for this Grant
                            </a>
                        <?php else: ?>
                            <button disabled class="w-full bg-slate-100 text-slate-400 font-black py-4 rounded-xl cursor-not-allowed border border-slate-200 uppercase tracking-widest text-sm">Applications Closed</button>
                        <?php endif; ?>
                        
                        <button onclick="shareLink()" id="shareBtn" class="btn-ghost">
                            <i class="fas fa-share-nodes"></i> Copy Share Link
                        </button>
                    </div>
                </div>

                <div class="bg-green-50 rounded-3xl p-6 border border-green-100 text-center">
                    <div class="w-12 h-12 bg-white text-green-600 rounded-full flex items-center justify-center text-xl mx-auto mb-3 shadow-sm"><i class="fas fa-shield-check"></i></div>
                    <h4 class="font-black text-green-900 mb-1">Official TAU Grant</h4>
                    <p class="text-xs text-green-800/70 font-medium">This scholarship is verified and managed securely through the ScholarLink OSSD system.</p>
                </div>

            </div>
        </div>
    </div>

    <script>
        function shareLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const btn = document.getElementById('shareBtn');
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-green-600"></i> Copied to Clipboard!';
                setTimeout(() => { btn.innerHTML = origHtml; }, 2000);
            });
        }
    </script>

    <?php include 'includes/chatbot.php'; ?>
</body>
</html>