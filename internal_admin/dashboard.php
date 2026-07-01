<?php 
session_start();

// ✨ FIX: Session manager MUST be loaded before any HTML!
include '../includes/session_manager.php';

// 🛑 THE BACK BUTTON KILLER (Must be before any HTML)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// --- THE SECURITY GATEKEEPER ---
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Internal_Admin') { 
    header("Location: ../admin_login.php"); 
    exit(); 
}

include '../includes/db_connect.php'; 

// ✨ HTML OUTPUT STARTS HERE (Safe from header errors)
include '../includes/header.php'; 
include '../includes/internal_sidebar.php'; 

// --- INITIALIZE CHART ARRAYS ---
$status_labels = [];
$status_counts = [];
$prog_labels = [];
$prog_counts = [];

try {
    // 1. Metrics 
    $totalPrograms = $pdo->query("SELECT COUNT(*) FROM Scholarship")->fetchColumn();
    $pendingApps = $pdo->query("SELECT COUNT(*) FROM Application WHERE Status = 'Submitted'")->fetchColumn();
    $activeScholars = $pdo->query("SELECT COUNT(*) FROM Application WHERE Status = 'Approved'")->fetchColumn();

    // 2. Chart Data: Application Status Breakdown
    $status_stmt = $pdo->query("SELECT Status, COUNT(*) as count FROM Application GROUP BY Status");
    while ($row = $status_stmt->fetch(PDO::FETCH_ASSOC)) {
        $status_labels[] = $row['Status'];
        $status_counts[] = $row['count'];
    }

    // 3. Chart Data: Applications per Program
    $prog_stmt = $pdo->query("
        SELECT p.ProgramName, COUNT(a.ApplicationID) as count 
        FROM Program p 
        JOIN Users u ON p.ProgramID = u.ProgramID 
        JOIN Application a ON u.UserID = a.UserID 
        GROUP BY p.ProgramID
    ");
    while ($row = $prog_stmt->fetch(PDO::FETCH_ASSOC)) {
        $prog_labels[] = $row['ProgramName'];
        $prog_counts[] = $row['count'];
    }

    // 4. Recent Activity 
    $stmt = $pdo->query("
        SELECT u.FirstName, u.LastName, sch.Name AS scholarship_name, a.DateSubmitted 
        FROM Application a
        JOIN Users u ON a.UserID = u.UserID
        JOIN Scholarship sch ON a.ScholarshipID = sch.ScholarshipID
        ORDER BY a.DateSubmitted DESC LIMIT 5
    ");
    $recentActivity = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<main class="flex-1 ml-72 p-10 bg-slate-50/50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Admin Dashboard</h2>
        <p class="text-slate-500 font-medium mt-1">Hello, <?= htmlspecialchars($_SESSION['first_name']) ?>! Overview for today.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex items-center justify-between group transition-all hover:border-blue-400">
            <div>
                <p class="text-[14px] font-black text-slate-900 uppercase tracking-widest mb-1">Programs</p>
                <p class="text-5xl font-black text-slate-900"><?= $totalPrograms ?></p>
            </div>
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">🎓</div>
        </div>
        
        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex items-center justify-between group transition-all hover:border-orange-400">
            <div>
                <p class="text-[14px] font-black text-slate-900 uppercase tracking-widest mb-1">Pending</p>
                <p class="text-5xl font-black text-slate-900"><?= $pendingApps ?></p>
            </div>
            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl">📥</div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm flex items-center justify-between group transition-all hover:border-green-400">
            <div>
                <p class="text-[14px] font-black text-slate-900 uppercase tracking-widest mb-1">Scholars</p>
                <p class="text-5xl font-black text-slate-900"><?= $activeScholars ?></p>
            </div>
            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl">🏆</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:border-blue-400">
            <h3 class="font-black text-slate-900 uppercase tracking-widest text-md mb-6 text-center">Application Status Overview</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:border-blue-400">
            <h3 class="font-black text-slate-900 uppercase tracking-widest text-md mb-6 text-center">Applicants by Academic Program</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="programChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden mb-10">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center"><h3 class="font-black text-slate-900 uppercase">Recent Activity</h3></div>
        <div class="p-4">
            <?php foreach($recentActivity as $act): ?>
            <div class="flex items-center justify-between p-4 hover:bg-slate-50 rounded-2xl transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold"><?= substr($act['FirstName'], 0, 1) ?></div>
                    <div>
                        <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($act['FirstName'] . ' ' . $act['LastName']) ?></p>
                        <p class="text-[14px] font-bold text-slate-900 uppercase tracking-widest">Applied: <?= htmlspecialchars($act['scholarship_name']) ?></p>
                    </div>
                </div>
                <span class="text-[14px] font-black text-slate-300 uppercase"><?= date('M d', strtotime($act['DateSubmitted'])) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data injected from PHP
        const statusLabels = <?php echo json_encode($status_labels); ?>;
        const statusData = <?php echo json_encode($status_counts); ?>;
        const progLabels = <?php echo json_encode($prog_labels); ?>;
        const progData = <?php echo json_encode($prog_counts); ?>;

        // 1. Render Status Doughnut Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#f59e0b', '#22c55e', '#ef4444', '#3b82f6', '#64748b'], // Tailored for Pending/Approved/Rejected etc
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Sleek thin ring
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11, weight: 'bold' } } }
                }
            }
        });

        // 2. Render Programs Bar Chart
        const ctxProg = document.getElementById('programChart').getContext('2d');
        new Chart(ctxProg, {
            type: 'bar',
            data: {
                labels: progLabels,
                datasets: [{
                    label: 'Total Applicants',
                    data: progData,
                    backgroundColor: '#0f172a', // Matches your slate-900 theme
                    borderRadius: 8, // Rounded bar tops!
                    barThickness: 35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1, font: { family: 'Inter', weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', weight: 'bold', size: 10 } }
                    }
                }
            }
        });
    </script>
</main>
<?php include '../includes/footer.php'; ?>