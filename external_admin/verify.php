<?php 
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'External_Admin') { 
    header("Location: ../admin_login.php"); 
    exit(); 
}
include '../includes/db_connect.php'; 
include '../includes/header.php'; 
include '../includes/external_sidebar.php'; 

// ✨ DYNAMIC SEARCH QUERY
$search_query = $_GET['search'] ?? '';
$where_clause = "WHERE sd.VerificationStatus = 'Pending'";
$params = [];

if (!empty($search_query)) {
    $where_clause .= " AND (u.FirstName LIKE :search OR u.LastName LIKE :search OR u.StudentID_Num LIKE :search OR dr.DocumentName LIKE :search)";
    $params['search'] = "%" . trim($search_query) . "%";
}

try {
    $stmt = $pdo->prepare("
        SELECT sd.SubmittedDocID AS document_id, 
               dr.DocumentName AS document_type, 
               sd.VerificationStatus AS verification_status, 
               sd.FilePath AS file_path, 
               u.FirstName AS first_name, 
               u.LastName AS last_name, 
               u.StudentID_Num AS student_number 
        FROM submitted_document sd
        JOIN document_requirement dr ON sd.RequirementID = dr.RequirementID
        JOIN application a ON sd.ApplicationID = a.ApplicationID
        JOIN users u ON a.UserID = u.UserID
        $where_clause
        ORDER BY sd.SubmittedDocID ASC
    ");
    $stmt->execute($params);
    $pending_docs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='p-10 text-red-500 font-black'>Database Error: " . $e->getMessage() . "</div>");
}

$highlight_doc_id = $_GET['doc_id'] ?? null;
?>

<style>
    .modal-active { display: flex !important; animation: fadeIn 0.2s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .highlight-card { animation: highlightFade 3s ease-out; }
    @keyframes highlightFade { 0% { border-color: #f97316; box-shadow: 0 0 15px rgba(249,115,22,0.3); } 100% { border-color: #e2e8f0; } }
</style>

<main class="flex-1 p-5 lg:p-10 lg:ml-72 bg-slate-50/50 min-h-screen relative transition-all duration-300">
    <header class="mb-8 lg:mb-10 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Verify Documents</h2>
            <p class="text-slate-500 text-sm lg:text-base font-medium mt-1">Review student requirements and approve/reject based on TAU standards.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-orange-100 text-orange-600 border border-orange-200 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest inline-block shrink-0">
                <?= count($pending_docs) ?> Pending
            </span>
            <!-- ✨ ITEMS PER PAGE -->
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm shrink-0">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:inline">Show:</span>
                <select id="itemsPerPage" onchange="changeItemsPerPage()" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-lg focus:ring-orange-500 focus:border-orange-500 outline-none cursor-pointer">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="999">All</option>
                </select>
            </div>
        </div>
    </header>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-50 text-green-700 p-4 rounded-2xl mb-6 font-bold border border-green-200 text-sm flex items-center gap-3">
            <span class="text-lg">✅</span> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm mb-8">
        <form method="GET" class="flex flex-wrap items-end gap-5">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[14px] font-black text-black uppercase tracking-widest mb-2">🔍 Fast Search</label>
                <input type="search" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Type name, ID, or document type..." autocomplete="off" class="w-full bg-slate-50 border border-slate-200 px-4 py-3 rounded-xl font-bold text-sm text-black outline-none focus:ring-2 focus:ring-orange-500/20 transition-all shadow-inner">
            </div>
            <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                <button type="submit" class="flex-1 sm:flex-none bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-[14px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg active:scale-95">Filter</button>
            </div>
        </form>
    </div>

    <!-- ✨ ONE-LINER GRID -->
    <div id="verifyGrid" class="flex flex-col gap-3">
        <?php if(empty($pending_docs)): ?>
            <div class="col-span-full p-10 lg:p-20 bg-white rounded-[2rem] border border-slate-200 text-center shadow-sm">
                <div class="text-4xl mb-4">🎉</div>
                <h3 class="text-lg font-black text-slate-900">All caught up!</h3>
                <p class="text-slate-400 font-medium text-sm mt-1">There are no documents currently awaiting verification.</p>
            </div>
        <?php else: ?>
            <?php foreach($pending_docs as $doc): 
                $cleanUrl = str_replace('../', '', $doc['file_path']);
                $actualDownloadPath = "../" . $cleanUrl;
                $isHighlight = ($highlight_doc_id == $doc['document_id']) ? 'highlight-card' : '';
            ?>
            <div class="verify-card <?= $isHighlight ?> bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-orange-300 transition-all flex flex-col lg:flex-row items-start lg:items-center justify-between p-5 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                
                <div class="flex-1 min-w-0 pr-4 lg:pr-8 pl-3 w-full lg:w-auto">
                    <div class="flex items-center gap-3 mb-1.5 flex-wrap">
                        <span class="bg-orange-50 text-orange-600 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest border border-orange-200 shrink-0">
                            #DOC-<?= $doc['document_id'] ?>
                        </span>
                        <h4 class="text-base font-black text-slate-900 truncate"><?= htmlspecialchars($doc['first_name'].' '.$doc['last_name']) ?></h4>
                        <span class="text-xs text-slate-400 font-bold hidden md:inline">•</span>
                        <span class="text-xs font-black text-slate-600 uppercase tracking-widest truncate"><?= htmlspecialchars($doc['document_type']) ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 truncate">
                        <span class="text-slate-700 font-bold">ID: <?= htmlspecialchars($doc['student_number']) ?></span>
                    </div>
                </div>

                <div class="mt-4 lg:mt-0 flex items-center justify-end w-full lg:w-auto shrink-0 gap-2 pl-0 lg:pl-6">
                    <button type="button" onclick="openDocModal('<?= htmlspecialchars($doc['file_path']) ?>', '<?= htmlspecialchars(addslashes($doc['document_type'])) ?>')" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all border border-blue-100 shadow-sm whitespace-nowrap active:scale-95">
                        View 👁️
                    </button>
                    <form action="../actions/process_crud.php" method="POST" class="flex gap-2 m-0">
                        <input type="hidden" name="module" value="verification">
                        <input type="hidden" name="id" value="<?= $doc['document_id'] ?>">
                        <button type="submit" name="status" value="Rejected" onclick="return confirm('Reject this document? The student will need to re-upload.');" class="bg-white text-red-500 hover:bg-red-50 hover:border-red-200 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase border border-slate-200 transition-all shadow-sm active:scale-95 whitespace-nowrap">
                            Reject
                        </button>
                        <button type="submit" name="status" value="Verified" onclick="return confirm('Approve this document as officially verified?');" class="bg-slate-900 text-white hover:bg-green-600 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase transition-all shadow-md active:scale-95 whitespace-nowrap">
                            Verify
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ✨ CENTERED PAGINATION CONTROLS -->
    <div class="flex flex-col items-center justify-center mt-12 gap-4 border-t border-slate-200 pt-8 pb-10">
        <div id="paginationControls" class="flex flex-wrap justify-center gap-2"></div>
        <div id="pageInfo" class="text-[11px] font-black text-slate-400 uppercase tracking-widest text-center mt-2">
            Showing 1 to X of X Records
        </div>
    </div>
</main>

<div id="docViewerModal" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-4 md:p-10">
    <div class="bg-white w-full max-w-5xl h-full max-h-[90vh] rounded-[1.5rem] lg:rounded-[2rem] shadow-2xl relative flex flex-col overflow-hidden">
        <div class="flex justify-between items-center p-4 lg:p-6 border-b border-slate-100 bg-slate-50 relative z-20">
            <div>
                <h3 id="modalDocTitle" class="text-lg lg:text-xl font-black text-slate-900 uppercase tracking-tight">Document Viewer</h3>
                <p class="text-[14px] lg:text-xs font-bold text-slate-400 mt-1">ScholarLink Secure File Preview</p>
            </div>
            <div class="flex items-center gap-4">
                <button id="aiScanBtn" onclick="runAIScanner()" class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2.5 rounded-full font-black text-[12px] uppercase tracking-widest shadow-lg shadow-purple-500/30 hover:scale-105 transition-all">
                    <span id="aiScanIcon" class="text-lg">🪄</span> 
                    <span id="aiScanText">AI Auto-Scan</span>
                </button>
                <button onclick="closeDocModal()" class="w-10 h-10 bg-white border border-slate-200 text-slate-500 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        <div id="viewerCanvas" class="flex-1 bg-slate-200 overflow-hidden relative flex items-center justify-center">
            <img id="docImage" src="" draggable="false" class="hidden max-h-[80vh] max-w-full rounded-xl shadow-2xl bg-white select-none">
            <iframe id="docIframe" src="" class="hidden w-full h-[70vh] rounded-xl border border-slate-300 shadow-sm bg-white relative z-10" frameborder="0"></iframe>
            <div id="zoomControls" class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-md p-2 rounded-2xl flex items-center gap-2 z-50 hidden shadow-2xl border border-white/10">
                <button type="button" onclick="zoomImage(-0.2)" class="w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-xl flex items-center justify-center transition-all" title="Zoom Out"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg></button>
                <button type="button" onclick="resetZoom()" class="px-4 h-10 bg-white/10 hover:bg-white/20 text-white font-black text-[14px] rounded-xl flex items-center justify-center transition-all uppercase tracking-widest">Reset</button>
                <button type="button" onclick="zoomImage(0.2)" class="w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-xl flex items-center justify-center transition-all" title="Zoom In"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg></button>
            </div>
        </div>
    </div>
</div>

<script>
    // ✨ PAGINATION SCRIPT
    let currentPage = 1;
    let itemsPerPage = parseInt(document.getElementById('itemsPerPage')?.value) || 10;
    const cards = Array.from(document.querySelectorAll('.verify-card'));
    const totalItems = cards.length;

    function renderPagination() {
        if (totalItems === 0) return;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        cards.forEach(card => card.style.display = 'none');
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        cards.slice(start, end).forEach(card => card.style.display = 'flex');

        const pageInfo = document.getElementById('pageInfo');
        if (pageInfo) pageInfo.innerText = `Showing ${start + 1} to ${Math.min(end, totalItems)} of ${totalItems} Pending Docs`;

        let btnHtml = '';
        btnHtml += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center transition-all ${currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'}">&larr;</button>`;
        
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 1);

        if (currentPage === 1) endPage = Math.min(totalPages, 3);
        else if (currentPage === totalPages) startPage = Math.max(1, totalPages - 2);

        if (startPage > 1) {
            btnHtml += `<button onclick="changePage(1)" class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center transition-all bg-white border border-slate-200 text-slate-700 hover:bg-slate-100">1</button>`;
            if (startPage > 2) btnHtml += `<span class="w-10 h-10 flex items-center justify-center text-slate-400 font-bold">...</span>`;
        }

        for(let i = startPage; i <= endPage; i++) {
            btnHtml += `<button onclick="changePage(${i})" class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center transition-all ${currentPage === i ? 'bg-orange-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'}">${i}</button>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) btnHtml += `<span class="w-10 h-10 flex items-center justify-center text-slate-400 font-bold">...</span>`;
            btnHtml += `<button onclick="changePage(${totalPages})" class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center transition-all bg-white border border-slate-200 text-slate-700 hover:bg-slate-100">${totalPages}</button>`;
        }
        
        btnHtml += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="w-10 h-10 rounded-xl font-black text-sm flex items-center justify-center transition-all ${currentPage === totalPages ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'}">&rarr;</button>`;
        
        const paginationControls = document.getElementById('paginationControls');
        if(paginationControls) paginationControls.innerHTML = btnHtml;
    }

    function changePage(page) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        if(page < 1 || page > totalPages) return;
        currentPage = page;
        renderPagination();
        document.getElementById('verifyGrid').scrollIntoView({ behavior: 'smooth' });
    }

    function changeItemsPerPage() {
        const selector = document.getElementById('itemsPerPage');
        if(selector) {
            itemsPerPage = parseInt(selector.value);
            currentPage = 1;
            renderPagination();
        }
    }
    document.addEventListener('DOMContentLoaded', renderPagination);

    // ✨ ZOOM & PAN ENGINE
    let currentZoom = 1; let isDocDragging = false; let docStartX, docStartY, translateX = 0, translateY = 0; let currentViewedFile = "";
    const imgElement = document.getElementById('docImage'); const viewerCanvas = document.getElementById('viewerCanvas');

    function applyTransform() {
        imgElement.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
        imgElement.style.cursor = currentZoom > 1 ? (isDocDragging ? 'grabbing' : 'grab') : 'default';
    }
    function zoomImage(step) { currentZoom = Math.max(0.5, Math.min(5, currentZoom + step)); applyTransform(); }
    function resetZoom() { currentZoom = 1; translateX = 0; translateY = 0; applyTransform(); }

    viewerCanvas.addEventListener('wheel', e => { if (!imgElement.classList.contains('hidden')) { e.preventDefault(); zoomImage(e.deltaY > 0 ? -0.1 : 0.1); } }, { passive: false });
    imgElement.addEventListener('mousedown', e => { if (currentZoom > 1) { isDocDragging = true; docStartX = e.clientX - translateX; docStartY = e.clientY - translateY; applyTransform(); } });
    window.addEventListener('mousemove', e => { if (isDocDragging && !imgElement.classList.contains('hidden')) { translateX = e.clientX - docStartX; translateY = e.clientY - docStartY; applyTransform(); } });
    window.addEventListener('mouseup', () => { isDocDragging = false; applyTransform(); });

    function openDocModal(fileUrl, docType) {
        currentViewedFile = fileUrl; document.getElementById('modalDocTitle').innerText = docType; resetZoom(); 
        const cleanUrl = fileUrl.replace('../', ''); const rootPath = `../${cleanUrl}`;
        const ext = fileUrl.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            document.getElementById('docIframe').classList.add('hidden'); document.getElementById('docImage').classList.remove('hidden'); document.getElementById('docImage').src = rootPath; document.getElementById('zoomControls').classList.remove('hidden'); 
        } else {
            document.getElementById('docImage').classList.add('hidden'); document.getElementById('docIframe').classList.remove('hidden'); document.getElementById('docIframe').src = rootPath; document.getElementById('zoomControls').classList.add('hidden'); 
        }
        document.getElementById('docViewerModal').classList.add('modal-active'); document.body.style.overflow = 'hidden';
    }

    function closeDocModal() {
        document.getElementById('docViewerModal').classList.remove('modal-active'); document.body.style.overflow = 'auto';
        setTimeout(() => { document.getElementById('docIframe').src = ""; document.getElementById('docImage').src = ""; resetZoom(); }, 200); 
    }

    // ✨ AI SCANNER
    async function runAIScanner() {
        const btn = document.getElementById('aiScanBtn'); const icon = document.getElementById('aiScanIcon'); const text = document.getElementById('aiScanText');
        if (!currentViewedFile) return;
        btn.classList.remove('from-purple-600', 'to-indigo-600'); btn.classList.add('bg-slate-800', 'cursor-wait'); icon.classList.add('animate-spin'); icon.innerText = "⏳"; text.innerText = "Scanning Document...";
        try {
            const response = await fetch('../actions/ai_scan_document.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ file_url: currentViewedFile }) });
            const data = await response.json();
            icon.classList.remove('animate-spin');
            if (data.success && data.grade !== 'NOT_FOUND') {
                btn.classList.remove('bg-slate-800'); btn.classList.add('from-green-500', 'to-emerald-600'); icon.innerText = "✅"; text.innerText = "DETECTED GWA: " + data.grade;
            } else {
                btn.classList.remove('bg-slate-800'); btn.classList.add('from-amber-500', 'to-orange-500'); icon.innerText = "⚠️"; text.innerText = "GRADE NOT FOUND";
            }
        } catch (error) {
            icon.classList.remove('animate-spin'); btn.classList.remove('bg-slate-800'); btn.classList.add('from-red-500', 'to-rose-600'); icon.innerText = "❌"; text.innerText = "SCAN FAILED";
        }
        setTimeout(() => { btn.classList.remove('from-green-500', 'to-emerald-600', 'from-amber-500', 'to-orange-500', 'from-red-500', 'to-rose-600', 'cursor-wait'); btn.classList.add('from-purple-600', 'to-indigo-600'); icon.innerText = "🪄"; text.innerText = "AI Auto-Scan"; }, 4000);
    }
</script>

<?php include '../includes/footer.php'; ?>