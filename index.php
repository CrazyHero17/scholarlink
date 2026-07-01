<?php
session_start();
require 'includes/db_connect.php';

// ✨ FETCH CMS CONTENT FROM DATABASE
$cms = [];
try {
    $cms_stmt = $pdo->query("SELECT * FROM landing_content");
    while($row = $cms_stmt->fetch(PDO::FETCH_ASSOC)) {
        $cms[$row['section_key']] = $row;
    }
} catch(PDOException $e) {}

// Setup Fallbacks if database is empty
$hero_title = $cms['hero']['title'] ?? 'Unlock your future with ScholarLink.';
$hero_body = $cms['hero']['body'] ?? 'Discover financial assistance programs, track your applications, and focus on your education. Browse every TAU grant below to get started.';
$grants_title = $cms['grants_header']['title'] ?? 'Scholarships & Grants';
$grants_body = $cms['grants_header']['body'] ?? 'Every scholarship on ScholarLink — active, upcoming, and closed.';
$no_grants_title = $cms['no_grants']['title'] ?? 'No scholarships available';
$no_grants_body = $cms['no_grants']['body'] ?? 'Please check back later.';

// Fetch every scholarship regardless of status, with active ones surfaced first
try {
    $stmt = $pdo->query("
        SELECT s.*, p.ProgramName 
        FROM scholarship s 
        LEFT JOIN program p ON s.ProgramID = p.ProgramID 
        ORDER BY (s.Status = 'Active') DESC, s.Deadline ASC
    ");
    $all_scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_scholarships = [];
}

// ✨ NEW: Map scholarships into a JSON-friendly array for the Modal
$jsScholarshipMap = [];
$active_scholarship_count = 0;
$total_active_award_value = 0;
$nearest_active_deadline = null;

foreach ($all_scholarships as $sch) {
    // Add to JS Map
    $jsScholarshipMap[$sch['ScholarshipID']] = $sch;

    // Calculate Quick Facts
    if (strtolower($sch['Status'] ?? '') === 'active') {
        $active_scholarship_count++;
        $total_active_award_value += (float) ($sch['AwardAmount'] ?? 0);
        if (!empty($sch['Deadline'])) {
            $deadlineTime = strtotime($sch['Deadline']);
            if ($deadlineTime && ($nearest_active_deadline === null || $deadlineTime < $nearest_active_deadline)) {
                $nearest_active_deadline = $deadlineTime;
            }
        }
    }
}

$nearest_deadline_label = $nearest_active_deadline ? date('M j, Y', $nearest_active_deadline) : 'None open';
$distinct_statuses = array_values(array_unique(array_filter(array_column($all_scholarships, 'Status'))));
$distinct_programs = array_values(array_unique(array_filter(array_column($all_scholarships, 'ProgramName'))));
sort($distinct_programs);

// Default Hero Slider Images
$heroSlides = [
    'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1800&q=80',
    'https://images.unsplash.com/photo-1541339907198-e08756defefe?auto=format&fit=crop&w=1800&q=80',
    'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1800&q=80'
];

// Maps a raw Status value to a display label + badge color class
function scholarship_status_meta($status) {
    $status = $status ?: 'Active';
    switch (strtolower($status)) {
        case 'active': return ['label' => 'Active', 'class' => 'status-active'];
        case 'closed':
        case 'inactive': return ['label' => 'Closed', 'class' => 'status-closed'];
        case 'draft': return ['label' => 'Draft', 'class' => 'status-draft'];
        case 'upcoming': return ['label' => 'Upcoming', 'class' => 'status-upcoming'];
        default: return ['label' => htmlspecialchars($status), 'class' => 'status-default'];
    }
}

// Session routing
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$role_redirect = "student_login.php";
if ($is_logged_in) {
    if ($_SESSION['role'] === 'Student') $role_redirect = "student/dashboard.php";
    elseif ($_SESSION['role'] === 'Internal_Admin') $role_redirect = "internal_admin/dashboard.php";
    elseif ($_SESSION['role'] === 'External_Admin') $role_redirect = "external_admin/dashboard.php";
    elseif ($_SESSION['role'] === 'Super_Admin') $role_redirect = "super_admin/dashboard.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ScholarLink - Tarlac Agricultural University</title>
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
            --teal: #16845f;
            --green: #198754;
            --gold: #b7791f;
            --crimson: #2f6f4e;
            --blue: #198754;
            --page-max: 1180px;
            --page-gutter: clamp(20px, 4vw, 48px);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            color: var(--ink);
            background: var(--wash);
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }

        /* HEADER */
        .site-header { position: sticky; top: 0; left: 0; right: 0; z-index: 50; background: rgba(255, 255, 255, 0.95); box-shadow: 0 7px 20px rgba(0, 0, 0, 0.06); backdrop-filter: blur(14px); }
        .nav-shell { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; min-height: 82px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: inline-flex; align-items: center; gap: 12px; min-width: 0; }
        .brand img { width: 44px; height: 44px; object-fit: contain; transition: transform 0.3s ease; }
        .brand:hover img { transform: scale(1.05); }
        .brand strong { display: block; font-size: 1.05rem; letter-spacing: -0.02em; color: var(--ink); }
        .brand span { display: block; color: var(--gold); font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; }

        .nav-links { display: flex; align-items: center; gap: 24px; color: var(--ink); font-size: 0.9rem; font-weight: 700; }
        .nav-links a:not(.login-link) { position: relative; color: var(--ink); font-weight: 800; }
        .nav-links a:not(.login-link)::after { content: ""; position: absolute; left: 0; right: 0; bottom: -6px; height: 2px; background: var(--green); transform: scaleX(0); transition: transform 0.2s ease; }
        .nav-links a:not(.login-link):hover::after { transform: scaleX(1); }

        .login-link, .btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border-radius: 8px; background: var(--green) !important; border: 1px solid var(--green) !important; color: #fff !important; font-weight: 800; transition: all 0.2s ease; box-shadow: 0 10px 20px rgba(25, 135, 84, 0.15); }
        .login-link:hover, .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(25, 135, 84, 0.25); }
        .btn-ghost { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border-radius: 8px; font-weight: 800; background: #fff; color: var(--green) !important; border: 1px solid #fff; transition: all 0.2s ease; }
        .btn-ghost:hover { background: rgba(255, 255, 255, 0.9); transform: translateY(-2px); }

        /* HERO SECTION */
        .hero { min-height: 680px; display: flex; align-items: center; position: relative; overflow: hidden; background: linear-gradient(135deg, #0f5132 0%, #198754 100%); padding: 80px 0 120px; color: #fff; }
        .hero::before { content: ""; position: absolute; inset: 0; z-index: 1; background: linear-gradient(90deg, rgba(5, 46, 22, 0.95), rgba(15, 81, 50, 0.75) 52%, rgba(21, 128, 61, 0.4)), linear-gradient(45deg, rgba(6, 95, 70, 0.3), rgba(255, 255, 255, 0.1)); pointer-events: none; }

        .hero-slideshow { position: absolute; inset: 0; z-index: 0; overflow: hidden; background: #0f5132; }
        .hero-slide { position: absolute; inset: 0; opacity: 0; background-position: center; background-size: cover; transform: scale(1.04); transition: opacity 1s ease, transform 5.5s ease; }
        .hero-slide.is-active { opacity: 1; transform: scale(1); }

        .hero-slider-controls { position: absolute; left: var(--page-gutter); right: var(--page-gutter); top: 50%; z-index: 3; display: flex; justify-content: space-between; pointer-events: none; transform: translateY(-50%); }
        .hero-slider-btn { width: 46px; height: 46px; display: grid; place-items: center; border: 1px solid rgba(255, 255, 255, 0.38); border-radius: 999px; background: rgba(20, 33, 61, 0.58); color: #fff; cursor: pointer; pointer-events: auto; backdrop-filter: blur(8px); transition: background 0.2s ease, transform 0.2s ease; }
        .hero-slider-btn:hover { background: rgba(20, 33, 61, 0.84); transform: scale(1.04); }

        .shapes-container { position: absolute; top: 0; left: 0; right: 0; bottom: 90px; overflow: hidden; pointer-events: none; z-index: 1; }
        .shape { position: absolute; width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.16); }
        .shape::before { content: ""; position: absolute; inset: 8px; border: 2px solid rgba(255, 255, 255, 0.18); border-radius: 8px; }
        .shape:nth-child(1) { left: 8%; top: 18%; }
        .shape:nth-child(2) { left: 20%; top: 78%; width: 70px; height: 70px; }
        .shape:nth-child(3) { left: 44%; top: 16%; }
        .shape:nth-child(4) { right: 12%; top: 12%; width: 58px; height: 58px; }

        .hero-inner { position: relative; z-index: 2; width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; }
        .eyebrow { display: inline-flex; align-items: center; gap: 10px; padding: 8px 14px; border: 1px solid rgba(255, 255, 255, 0.28); border-radius: 99px; background: rgba(255, 255, 255, 0.12); font-size: 0.75rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        h1 { margin: 22px 0 18px; max-width: 860px; font-size: clamp(2.45rem, 5vw, 4.45rem); line-height: 1.1; letter-spacing: -0.02em; }
        .hero-copy { max-width: 620px; margin: 0 0 34px; color: rgba(255, 255, 255, 0.88); font-size: 1.1rem; font-weight: 500; line-height: 1.75; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 14px; }

        /* QUICK-FACTS PANEL */
        .pull-top { position: relative; z-index: 5; margin-top: -72px; padding: 0 var(--page-gutter); }
        .quick-guide-shell { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 24px 60px rgba(15, 81, 50, 0.18); padding: 28px clamp(20px, 4vw, 40px); }
        .quick-guide-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .quick-guide-card { display: flex; align-items: center; gap: 16px; }
        .quick-guide-card:not(:first-child) { border-left: 1px solid var(--line); padding-left: 24px; }
        .quick-guide-icon { width: 52px; height: 52px; border-radius: 12px; background: var(--wash); color: var(--green); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .quick-guide-card h3 { margin: 0; font-size: 1.5rem; color: var(--ink); line-height: 1.1; }
        .quick-guide-card p { margin: 4px 0 0; color: var(--muted); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }

        /* GRANTS SECTION */
        section { padding: 84px 0; }
        .section-inner { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; }
        .section-head { position: relative; max-width: 780px; margin-bottom: 40px; display: flex; flex-direction: column; gap: 8px; }
        .section-head-top { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 20px; }
        .section-kicker { color: var(--green); font-size: 0.8rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; }
        .section-head h2 { margin: 0; font-size: clamp(1.9rem, 3.5vw, 2.8rem); line-height: 1.1; letter-spacing: -0.02em; color: var(--ink); }
        .section-head p { margin: 0; color: var(--muted); font-weight: 500; font-size: 1.05rem; }

        .filter-controls { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .filter-controls select { background: #fff; border: 1px solid var(--line); color: var(--ink); font-size: 0.85rem; font-weight: 700; padding: 8px 12px; border-radius: 8px; outline: none; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }

        /* SEARCH BAR */
        .search-wrap { position: relative; display: flex; align-items: center; }
        .search-wrap i { position: absolute; left: 14px; color: var(--muted); font-size: 0.85rem; pointer-events: none; }
        .search-input { background: #fff; border: 1px solid var(--line); color: var(--ink); font-size: 0.85rem; font-weight: 600; padding: 9px 14px 9px 36px; border-radius: 8px; outline: none; box-shadow: 0 2px 8px rgba(0,0,0,0.04); min-width: 220px; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .search-input:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12); }

        /* SCROLL REVEAL */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }

        /* HOW IT WORKS */
        .how-it-works { background: #fff; }
        .steps-track { position: relative; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 32px; margin-top: 8px; }
        .steps-track::before { content: ""; position: absolute; top: 34px; left: 8%; right: 8%; height: 2px; background: var(--line); z-index: 0; }
        .step-card { position: relative; z-index: 1; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px; }
        .step-number { width: 68px; height: 68px; border-radius: 50%; background: var(--green); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 900; box-shadow: 0 12px 26px rgba(25, 135, 84, 0.28); border: 6px solid #fff; }
        .step-card h3 { margin: 0; font-size: 1.15rem; color: var(--ink); }
        .step-card p { margin: 0; color: var(--muted); font-size: 0.92rem; max-width: 280px; }
        .step-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; background: #eef8f1; color: var(--green); font-size: 0.7rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; }

        /* FAQ */
        .faq-layout { display: grid; grid-template-columns: 1.3fr 1fr; gap: 40px; align-items: start; }
        .faq-list { display: flex; flex-direction: column; gap: 12px; }
        .faq-visual { position: sticky; top: 110px; background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 28px; box-shadow: 0 20px 45px rgba(25, 135, 84, 0.1); display: flex; align-items: center; justify-content: center; }
        .faq-visual svg { width: 100%; height: auto; max-width: 340px; }
        .faq-item { border: 1px solid var(--line); border-radius: 12px; background: #fff; overflow: hidden; }
        .faq-question { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 22px; background: none; border: none; cursor: pointer; text-align: left; font-family: inherit; font-size: 1rem; font-weight: 700; color: var(--ink); }
        .faq-question i { color: var(--green); transition: transform 0.25s ease; flex-shrink: 0; }
        .faq-item.is-open .faq-question i { transform: rotate(180deg); }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .faq-answer-inner { padding: 0 22px 20px; color: var(--muted); font-size: 0.94rem; line-height: 1.7; }

        /* CTA BANNER */
        .cta-banner { background: linear-gradient(120deg, #0f5132, #198754); border-radius: 20px; padding: 48px clamp(24px, 5vw, 56px); display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; box-shadow: 0 24px 50px rgba(15, 81, 50, 0.25); }
        .cta-banner h2 { margin: 0 0 8px; color: #fff; font-size: clamp(1.5rem, 3vw, 2.1rem); letter-spacing: -0.02em; }
        .cta-banner p { margin: 0; color: rgba(255,255,255,0.85); font-size: 1rem; font-weight: 500; }
        .cta-banner .btn-primary { background: #fff !important; border-color: #fff !important; color: var(--green) !important; white-space: nowrap; }
        .cta-banner .btn-primary:hover { background: var(--wash) !important; }

        /* PROGRAM CARDS */
        .program-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .program-card { border: 1px solid var(--line); border-radius: 12px; background: #fff; box-shadow: 0 10px 28px rgba(25, 135, 84, 0.08); display: flex; flex-direction: column; transition: transform 0.25s ease, box-shadow 0.25s ease; position: relative; overflow: hidden; }
        .program-card::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--green); }
        .program-card:hover { transform: translateY(-6px); box-shadow: 0 18px 38px rgba(25, 135, 84, 0.16); border-color: #b7e4ca; }
        .program-body { padding: 28px; flex: 1; display: flex; flex-direction: column; }
        .program-badge { width: fit-content; margin-bottom: 16px; padding: 6px 12px; border-radius: 6px; background: #eef8f1; color: var(--green); font-size: 0.75rem; font-weight: 900; letter-spacing: 0.06em; text-transform: uppercase; }
        
        .status-pill { position: absolute; top: 16px; right: 16px; z-index: 2; padding: 4px 11px; border-radius: 999px; font-size: 0.66rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; }
        .status-active { background: #eaf7ee; color: #198754; }
        .status-closed { background: #f1f2f4; color: #6b7280; }
        .status-draft { background: #fff6e5; color: #b7791f; }
        .status-upcoming { background: #eaf1ff; color: #2563eb; }
        .status-default { background: #f1f2f4; color: #6b7280; }

        .program-body h3 { margin: 0 0 10px; font-size: 1.25rem; color: var(--ink); line-height: 1.3; }
        .program-body p { margin: 0 0 20px; color: var(--muted); font-size: 0.94rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        .fact-row { padding-top: 14px; margin-top: 14px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
        .fact-row span { color: var(--muted); font-size: 0.75rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; }
        .fact-row strong { color: var(--ink); font-size: 0.95rem; font-weight: 800; }
        .fact-row .highlight { color: var(--gold); }

        .program-link { display: flex; width: 100%; border: none; cursor: pointer; align-items: center; justify-content: center; gap: 8px; margin-top: 24px; padding: 12px; border-radius: 8px; background: var(--wash); color: var(--green); font-size: 0.9rem; font-weight: 800; font-family: inherit; transition: all 0.2s ease; }
        .program-card:hover .program-link { background: var(--green); color: #fff; }

        .pagination-controls { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 48px; padding-top: 32px; border-top: 1px solid var(--line); }
        .page-btn { width: 40px; height: 40px; border-radius: 8px; border: 1px solid var(--line); background: #fff; color: var(--ink); font-weight: 800; cursor: pointer; transition: all 0.2s ease; }
        .page-btn:not(:disabled):hover { border-color: var(--green); color: var(--green); }
        .page-btn.active { background: var(--green); color: #fff; border-color: var(--green); box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2); }
        .page-btn:disabled { background: var(--wash); color: #a0aec0; cursor: not-allowed; }

        .empty-state { grid-column: 1 / -1; padding: 64px 20px; text-align: center; background: #fff; border: 1px dashed #b7e4ca; border-radius: 16px; }
        .empty-state i { font-size: 3rem; color: #b7e4ca; margin-bottom: 16px; }

        .site-footer { padding: 48px 0 32px; background: #1a1b1f; color: rgba(255, 255, 255, 0.7); }
        .footer-inner { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 24px; }
        .footer-logo { height: 48px; opacity: 0.8; margin-bottom: 8px; }
        .footer-inner p { margin: 0; font-size: 0.9rem; }
        .footer-inner .small-text { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; color: var(--gold); text-transform: uppercase; }
        .footer-social { margin-top: 16px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.1); width: 100%; display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .social-link { display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px; border-radius: 8px; background: #1877f2; color: #fff; font-weight: 700; font-size: 0.9rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .social-link:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(24, 119, 242, 0.3); color: #fff; }

        @media (max-width: 980px) {
            .program-grid { grid-template-columns: repeat(2, 1fr); }
            .quick-guide-grid { grid-template-columns: 1fr; }
            .quick-guide-card:not(:first-child) { border-left: none; padding-left: 0; border-top: 1px solid var(--line); padding-top: 16px; }
        }
        @media (max-width: 720px) {
            .nav-shell { min-height: 70px; }
            .brand span, .nav-links a:not(.login-link) { display: none; }
            .program-grid { grid-template-columns: 1fr; }
            .hero { padding-bottom: 80px; }
            .hero h1 { font-size: 2.2rem; }
            .section-head-top { flex-direction: column; align-items: flex-start; }
            .hero-actions { flex-direction: column; width: 100%; max-width: 300px; }
            .hero-actions a { width: 100%; }
            .pull-top { margin-top: -56px; }
            .steps-track { grid-template-columns: 1fr; gap: 36px; }
            .steps-track::before { display: none; }
            .search-input { min-width: 100%; }
            .filter-controls { width: 100%; }
            .filter-controls select, .search-wrap { width: 100%; }
            .cta-banner { flex-direction: column; text-align: center; }
            .faq-layout { grid-template-columns: 1fr; }
            .faq-visual { position: static; order: -1; }
        }
    </style>
</head>
<body>
    
    <header class="site-header">
        <nav class="nav-shell" aria-label="Primary navigation">
            <a class="brand" href="index.php">
                <img src="assets/img/tau_logo.png" alt="TAU Logo">
                <span>
                    <strong>ScholarLink</strong>
                    <span>Tarlac Agricultural University</span>
                </span>
            </a>
            <div class="nav-links">
                <a href="#grants">Grants</a>
                <?php if ($is_logged_in): ?>
                    <a class="login-link" href="<?= $role_redirect ?>">
                        Dashboard <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                <?php else: ?>
                    <a href="student_login.php">Sign In</a>
                    <a class="login-link" href="student_login.php">
                        Apply Now
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-slideshow" aria-hidden="true">
                <?php foreach ($heroSlides as $slideIndex => $slideImage): ?>
                    <div class="hero-slide <?= $slideIndex === 0 ? 'is-active' : '' ?>" style="background-image: url('<?= htmlspecialchars($slideImage) ?>');"></div>
                <?php endforeach; ?>
            </div>

            <div class="hero-slider-controls" aria-label="Hero image controls">
                <button type="button" class="hero-slider-btn" data-hero-slide="prev" aria-label="Previous image">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="hero-slider-btn" data-hero-slide="next" aria-label="Next image">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="shapes-container" aria-hidden="true">
                <?php for ($shapeIndex = 0; $shapeIndex < 4; $shapeIndex++): ?>
                    <div class="shape"></div>
                <?php endfor; ?>
            </div>
            
            <div class="hero-inner">
                <div>
                    <span class="eyebrow"><i class="fas fa-seedling"></i> Official Scholarship Portal</span>
                    <h1 id="hero-title"><?= htmlspecialchars($hero_title) ?></h1>
                    <p class="hero-copy"><?= htmlspecialchars($hero_body) ?></p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#grants"><i class="fas fa-table-columns"></i> Browse Grants</a>
                        <?php if (!$is_logged_in): ?>
                            <a class="btn btn-ghost" href="student_login.php"><i class="fas fa-user-plus"></i> Create Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="pull-top" aria-label="Scholarship quick facts">
            <div class="quick-guide-shell">
                <div class="quick-guide-grid">
                    <article class="quick-guide-card reveal">
                        <span class="quick-guide-icon"><i class="fas fa-graduation-cap"></i></span>
                        <div>
                            <h3><?= $active_scholarship_count ?></h3>
                            <p>Active Scholarships</p>
                        </div>
                    </article>
                    <article class="quick-guide-card reveal">
                        <span class="quick-guide-icon"><i class="fas fa-peso-sign"></i></span>
                        <div>
                            <h3>₱<?= number_format($total_active_award_value) ?></h3>
                            <p>Total Grant Value</p>
                        </div>
                    </article>
                    <article class="quick-guide-card reveal">
                        <span class="quick-guide-icon"><i class="fas fa-hourglass-half"></i></span>
                        <div>
                            <h3><?= htmlspecialchars($nearest_deadline_label) ?></h3>
                            <p>Nearest Deadline</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="grants">
            <div class="section-inner">
                <div class="section-head">
                    <div class="section-head-top">
                        <div>
                            <span class="section-kicker">Browse All Grants</span>
                            <h2><?= htmlspecialchars($grants_title) ?></h2>
                            <p><?= htmlspecialchars($grants_body) ?></p>
                        </div>
                        <div class="filter-controls">
                            <div class="search-wrap">
                                <i class="fas fa-magnifying-glass"></i>
                                <input type="text" id="searchInput" class="search-input" placeholder="Search scholarship name..." oninput="changeSearchFilter()">
                            </div>
                            <select id="programFilter" onchange="changeProgramFilter()">
                                <option value="all">All Programs</option>
                                <?php foreach ($distinct_programs as $prog): ?>
                                    <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="statusFilter" onchange="changeStatusFilter()">
                                <option value="all">All Statuses</option>
                                <?php foreach ($distinct_statuses as $st): $stMeta = scholarship_status_meta($st); ?>
                                    <option value="<?= htmlspecialchars($st) ?>"><?= $stMeta['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="itemsPerPage" onchange="changeItemsPerPage()">
                                <option value="3">Show 3</option>
                                <option value="6" selected>Show 6</option>
                                <option value="9">Show 9</option>
                                <option value="999">Show All</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="scholarshipsGrid" class="program-grid">
                    <?php if (empty($all_scholarships)): ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3 class="text-xl font-black text-slate-900 mb-2"><?= htmlspecialchars($no_grants_title) ?></h3>
                            <p class="text-slate-500 font-medium"><?= htmlspecialchars($no_grants_body) ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($all_scholarships as $sch): ?>
                            <?php
                                $statusValue = $sch['Status'] ?? 'Active';
                                $statusMeta = scholarship_status_meta($statusValue);
                                $isActiveScholarship = strtolower($statusValue) === 'active';
                            ?>
                            <article class="program-card reveal" data-status="<?= htmlspecialchars($statusValue) ?>" data-program="<?= htmlspecialchars($sch['ProgramName'] ?? 'Open to All Courses') ?>" data-name="<?= htmlspecialchars(strtolower($sch['Name'])) ?>">
                                <span class="status-pill <?= $statusMeta['class'] ?>"><?= $statusMeta['label'] ?></span>
                                <div class="program-body">
                                    <span class="program-badge"><?= htmlspecialchars($sch['ProgramName'] ?? 'Open to All Courses') ?></span>
                                    <h3><?= htmlspecialchars($sch['Name']) ?></h3>
                                    <p><?= htmlspecialchars($sch['Description']) ?></p>
                                    
                                    <div class="mt-auto">
                                        <div class="fact-row">
                                            <span>Min GWA</span>
                                            <strong><?= htmlspecialchars($sch['MinimumGWA']) ?></strong>
                                        </div>
                                        <div class="fact-row">
                                            <span>Grant Amount</span>
                                            <strong class="highlight">₱<?= number_format($sch['AwardAmount']) ?></strong>
                                        </div>
                                        <div class="fact-row">
                                            <span>Slots Available</span>
                                            <strong><?= $sch['NumberOfSlots'] ?? 'Unlimited' ?></strong>
                                        </div>
                                        <button type="button" class="program-link" onclick='openDetailsModal(<?= $sch['ScholarshipID'] ?>)'>
                                            View Details <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($all_scholarships)): ?>
                    <div class="empty-state" id="noFilterResults" style="display:none;">
                        <i class="fas fa-filter-circle-xmark"></i>
                        <h3 class="text-xl font-black text-slate-900 mb-2">No scholarships match this filter</h3>
                        <p class="text-slate-500 font-medium">Try a different status from the dropdown above.</p>
                    </div>
                    <div class="pagination-controls" id="paginationControls"></div>
                    <div id="pageInfo" class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mt-4"></div>
                <?php endif; ?>

            </div>
        </section>

        <section class="how-it-works" aria-labelledby="how-it-works-title">
            <div class="section-inner">
                <div class="section-head reveal">
                    <span class="section-kicker">Getting Started</span>
                    <h2 id="how-it-works-title">How ScholarLink Works</h2>
                    <p>From account creation to a submitted application — here's the whole journey in three simple steps.</p>
                </div>
                <div class="steps-track">
                    <div class="step-card reveal">
                        <span class="step-number">1</span>
                        <span class="step-badge"><i class="fas fa-user-plus"></i> Sign Up</span>
                        <h3>Create an Account</h3>
                        <p>Register with your student details in minutes and get instant access to every open grant.</p>
                    </div>
                    <div class="step-card reveal">
                        <span class="step-number">2</span>
                        <span class="step-badge"><i class="fas fa-lock"></i> Vault</span>
                        <h3>Build your Document Vault 🔒</h3>
                        <p>Upload your requirements once — COR, grades, IDs — and reuse them for every scholarship you apply to.</p>
                    </div>
                    <div class="step-card reveal">
                        <span class="step-number">3</span>
                        <span class="step-badge"><i class="fas fa-bolt"></i> Apply</span>
                        <h3>Apply with 1-Click</h3>
                        <p>No more re-uploading files. Pick a grant, confirm your vault documents, and submit instantly.</p>
                    </div>
                </div>
            </div>
        </section>

        <section aria-labelledby="faq-title">
            <div class="section-inner">
                <div class="section-head reveal">
                    <span class="section-kicker">Need Help?</span>
                    <h2 id="faq-title">Frequently Asked Questions</h2>
                    <p>Quick answers to the questions students ask us most often.</p>
                </div>
                <div class="faq-layout">
                <div class="faq-list reveal">
                    <div class="faq-item">
                        <button type="button" class="faq-question" onclick="toggleFaq(this)">
                            Who is eligible to apply?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">Any currently enrolled TAU student can apply, as long as they meet the specific GWA, program, and slot requirements listed on each scholarship's details page.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" onclick="toggleFaq(this)">
                            Are dual scholarships allowed?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">This depends on the specific grant's guidelines. Some scholarships can be combined with others, while some require exclusivity. Check the "Program Description" in each grant's details modal, or ask the OSSD office to confirm.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" onclick="toggleFaq(this)">
                            How do I upload my documents?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">Once logged in, head to your Document Vault in the student dashboard. Upload each requirement once, and it will be available to attach automatically whenever you apply for a new scholarship.</div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button type="button" class="faq-question" onclick="toggleFaq(this)">
                            How will I know if my application is approved?
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">You'll be able to track your application status directly from your student dashboard, and updates are reflected as soon as the reviewing admin makes a decision.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-visual reveal">
                    <svg viewBox="0 0 340 340" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Illustration of a student with frequently asked questions">
                        <circle cx="170" cy="170" r="160" fill="#f3faf5"/>
                        <circle cx="170" cy="170" r="118" fill="#eaf7ee"/>
                        <rect x="70" y="205" width="200" height="14" rx="7" fill="#d7e8dd"/>
                        <g>
                            <rect x="110" y="90" width="120" height="90" rx="10" fill="#ffffff" stroke="#d7e8dd" stroke-width="2"/>
                            <rect x="128" y="112" width="84" height="10" rx="5" fill="#b7e4ca"/>
                            <rect x="128" y="132" width="60" height="10" rx="5" fill="#eaf7ee"/>
                            <rect x="128" y="152" width="72" height="10" rx="5" fill="#eaf7ee"/>
                        </g>
                        <g>
                            <ellipse cx="170" cy="245" rx="46" ry="10" fill="#d7e8dd" opacity="0.6"/>
                            <rect x="148" y="185" width="44" height="66" rx="16" fill="#198754"/>
                            <circle cx="170" cy="165" r="26" fill="#f6c453"/>
                            <path d="M148 200 q22 -14 44 0 v18 q-22 -10 -44 0 z" fill="#0f5132"/>
                        </g>
                        <g>
                            <circle cx="255" cy="95" r="26" fill="#198754"/>
                            <text x="255" y="104" font-family="Plus Jakarta Sans, Arial, sans-serif" font-size="26" font-weight="900" fill="#ffffff" text-anchor="middle">?</text>
                        </g>
                        <g>
                            <circle cx="70" cy="130" r="18" fill="#b7791f"/>
                            <text x="70" y="137" font-family="Plus Jakarta Sans, Arial, sans-serif" font-size="18" font-weight="900" fill="#ffffff" text-anchor="middle">?</text>
                        </g>
                        <g>
                            <circle cx="80" cy="235" r="14" fill="#2563eb"/>
                            <text x="80" y="240" font-family="Plus Jakarta Sans, Arial, sans-serif" font-size="14" font-weight="900" fill="#ffffff" text-anchor="middle">?</text>
                        </g>
                    </svg>
                </div>
                </div>
            </div>
        </section>

        <section aria-label="Call to action">
            <div class="section-inner">
                <div class="cta-banner reveal">
                    <div>
                        <h2>Ready to secure your future?</h2>
                        <p>Join ScholarLink today and get every TAU scholarship in one place.</p>
                    </div>
                    <a class="btn btn-primary" href="<?= $is_logged_in ? $role_redirect : 'student_login.php' ?>">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <img src="assets/img/tau_logo.png" alt="TAU Logo" class="footer-logo">
            <div>
                <p>Tarlac Agricultural University &copy; <?= date('Y') ?></p>
                <p class="small-text mt-1">ScholarLink Management System</p>
            </div>
            
            <div class="footer-social">
                <span class="text-[10px] font-black text-white/50 uppercase tracking-widest">Connect with us</span>
                <a href="https://www.facebook.com/TAUOSSD" target="_blank" rel="noopener noreferrer" class="social-link">
                    <i class="fab fa-facebook-f text-lg"></i>
                    <span>TAU OSSD Official</span>
                </a>
            </div>
        </div>
    </footer>

    <div id="detailsModal" class="fixed inset-0 z-[1000] hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeDetailsModal(event)">
        <div id="detailsModalContent" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl overflow-hidden relative transform transition-transform duration-300 scale-95 flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
            <div class="px-6 lg:px-8 py-6 border-b border-slate-100 flex justify-between items-start bg-slate-50 shrink-0">
                <div class="pr-4">
                    <span id="modalBadge" class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-200 mb-3">Program</span>
                    <h3 id="modalTitle" class="font-black text-slate-900 text-2xl lg:text-3xl tracking-tight leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">Scholarship Name</h3>
                </div>
                <button type="button" onclick="closeDetailsModal()" class="w-8 h-8 flex items-center justify-center bg-slate-200 text-slate-600 rounded-full hover:bg-red-500 hover:text-white transition-colors font-bold shrink-0">&times;</button>
            </div>
            
            <div class="p-6 lg:p-8 overflow-y-auto" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <div class="grid grid-cols-3 gap-2 sm:gap-4 py-4 border border-slate-100 mb-6 bg-slate-50/50 rounded-2xl px-2 sm:px-4">
                    <div class="text-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Min GWA</span>
                        <span id="modalGwa" class="text-lg font-black text-green-700">2.00</span>
                    </div>
                    <div class="text-center border-x border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Grant Amount</span>
                        <span id="modalAmount" class="text-lg font-black text-yellow-600">₱0</span>
                    </div>
                    <div class="text-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Slots</span>
                        <span id="modalSlots" class="text-lg font-black text-slate-700">Unlimited</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3">Program Description</h4>
                    <p id="modalDesc" class="text-slate-600 font-medium leading-relaxed whitespace-pre-wrap">Description goes here...</p>
                </div>
            </div>

            <div class="p-6 lg:p-8 border-t border-slate-100 bg-slate-50 shrink-0 flex flex-col sm:flex-row items-center justify-between gap-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Application Deadline</span>
                    <span id="modalDeadline" class="text-sm font-black text-red-600">Dec 31, 2024</span>
                </div>
                <a href="<?= $is_logged_in ? $role_redirect : 'student_login.php' ?>" id="modalApplyBtn" class="w-full sm:w-auto text-center bg-green-700 text-white hover:bg-yellow-400 hover:text-green-950 px-8 py-3.5 rounded-xl text-sm font-black transition-all shadow-md active:scale-95 uppercase tracking-widest">
                    Apply for this Grant
                </a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const slides = Array.from(document.querySelectorAll('.hero-slide'));
            if (slides.length <= 1) return;

            let activeIndex = 0;
            let timerId = null;

            function showSlide(nextIndex) {
                slides[activeIndex].classList.remove('is-active');
                activeIndex = (nextIndex + slides.length) % slides.length;
                slides[activeIndex].classList.add('is-active');
            }

            function startTimer() {
                window.clearInterval(timerId);
                timerId = window.setInterval(function () {
                    showSlide(activeIndex + 1);
                }, 4800);
            }

            document.querySelector('[data-hero-slide="prev"]')?.addEventListener('click', function () {
                showSlide(activeIndex - 1);
                startTimer();
            });

            document.querySelector('[data-hero-slide="next"]')?.addEventListener('click', function () {
                showSlide(activeIndex + 1);
                startTimer();
            });

            startTimer();
        })();
    </script>

    <script>
        // FAQ Accordion (always available, regardless of scholarship data)
        function toggleFaq(button) {
            const item = button.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const wasOpen = item.classList.contains('is-open');

            // Close any other open FAQ items
            document.querySelectorAll('.faq-item.is-open').forEach(openItem => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                    openItem.querySelector('.faq-answer').style.maxHeight = null;
                }
            });

            if (wasOpen) {
                item.classList.remove('is-open');
                answer.style.maxHeight = null;
            } else {
                item.classList.add('is-open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        }

        // Scroll Reveal Animations (always available, regardless of scholarship data)
        document.addEventListener('DOMContentLoaded', function () {
            const revealTargets = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window && revealTargets.length) {
                const revealObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

                revealTargets.forEach(target => revealObserver.observe(target));
            } else {
                // Fallback: just show everything if IntersectionObserver isn't supported
                revealTargets.forEach(target => target.classList.add('is-visible'));
            }
        });
    </script>

    <?php if (!empty($all_scholarships)): ?>
    <script>
        // Pass data from PHP to JS
        const scholarshipDataMap = <?= json_encode($jsScholarshipMap) ?>;

        function openDetailsModal(id) {
            const data = scholarshipDataMap[id];
            if(!data) return;

            document.getElementById('modalTitle').innerText = data.Name;
            document.getElementById('modalBadge').innerText = data.ProgramName || 'Open to All Courses';
            document.getElementById('modalGwa').innerText = data.MinimumGWA || '2.00';
            document.getElementById('modalAmount').innerText = '₱' + Number(data.AwardAmount).toLocaleString();
            document.getElementById('modalSlots').innerText = data.NumberOfSlots ? data.NumberOfSlots : 'Unlimited';
            document.getElementById('modalDesc').innerText = data.Description;
            
            // Format date
            const d = new Date(data.Deadline);
            const dateString = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('modalDeadline').innerText = dateString;

            // Handle active/inactive state for Apply button
            const applyBtn = document.getElementById('modalApplyBtn');
            if(data.Status && data.Status.toLowerCase() !== 'active') {
                applyBtn.style.display = 'none';
                document.getElementById('modalDeadline').innerText = 'Closed/Inactive';
                document.getElementById('modalDeadline').classList.replace('text-red-600', 'text-slate-500');
            } else {
                applyBtn.style.display = 'block';
                document.getElementById('modalDeadline').classList.replace('text-slate-500', 'text-red-600');
            }

            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('detailsModalContent');
            modal.classList.remove('hidden');
            
            // Trigger reflow for animation
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailsModal(event) {
            // If event is passed, check if we clicked outside the content box
            if(event && event.target.id !== 'detailsModal' && event.target.tagName !== 'BUTTON') return;

            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('detailsModalContent');
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        // Pagination Logic
        let currentPage = 1;
        let itemsPerPage = parseInt(document.getElementById('itemsPerPage').value) || 6;
        let statusFilter = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : 'all';
        let programFilter = document.getElementById('programFilter') ? document.getElementById('programFilter').value : 'all';
        let searchFilter = '';
        const allCards = Array.from(document.querySelectorAll('.program-card'));

        function getFilteredCards() {
            return allCards.filter(card => {
                const matchesStatus = statusFilter === 'all' || card.dataset.status === statusFilter;
                const matchesProgram = programFilter === 'all' || card.dataset.program === programFilter;
                const matchesSearch = searchFilter === '' || card.dataset.name.includes(searchFilter);
                return matchesStatus && matchesProgram && matchesSearch;
            });
        }

        function renderPagination() {
            const cards = getFilteredCards();
            const totalItems = cards.length;
            const noFilterResults = document.getElementById('noFilterResults');
            const paginationControls = document.getElementById('paginationControls');
            const pageInfo = document.getElementById('pageInfo');

            allCards.forEach(card => card.style.display = 'none');

            if (totalItems === 0) {
                if (noFilterResults) noFilterResults.style.display = 'block';
                if (paginationControls) paginationControls.innerHTML = '';
                if (pageInfo) pageInfo.innerText = '';
                return;
            }

            if (noFilterResults) noFilterResults.style.display = 'none';

            const totalPages = Math.ceil(totalItems / itemsPerPage);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            cards.slice(start, end).forEach(card => card.style.display = 'flex');

            if (pageInfo) pageInfo.innerText = `Showing ${start + 1} to ${Math.min(end, totalItems)} of ${totalItems} Grants`;

            let btnHtml = `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="page-btn">&larr;</button>`;

            let startPage = Math.max(1, currentPage - 1);
            let endPage = Math.min(totalPages, currentPage + 1);

            if (currentPage === 1) endPage = Math.min(totalPages, 3);
            else if (currentPage === totalPages) startPage = Math.max(1, totalPages - 2);

            if (startPage > 1) {
                btnHtml += `<button onclick="changePage(1)" class="page-btn">1</button>`;
                if (startPage > 2) btnHtml += `<span class="px-2 text-slate-400 font-bold">...</span>`;
            }

            for(let i = startPage; i <= endPage; i++) {
                btnHtml += `<button onclick="changePage(${i})" class="page-btn ${currentPage === i ? 'active' : ''}">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) btnHtml += `<span class="px-2 text-slate-400 font-bold">...</span>`;
                btnHtml += `<button onclick="changePage(${totalPages})" class="page-btn">${totalPages}</button>`;
            }

            btnHtml += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="page-btn">&rarr;</button>`;

            if (paginationControls) paginationControls.innerHTML = btnHtml;
        }

        function changePage(page) {
            const totalPages = Math.ceil(getFilteredCards().length / itemsPerPage);
            if(page < 1 || page > totalPages) return;
            currentPage = page;
            renderPagination();
            document.getElementById('grants').scrollIntoView({ behavior: 'smooth' });
        }

        function changeItemsPerPage() {
            itemsPerPage = parseInt(document.getElementById('itemsPerPage').value);
            currentPage = 1;
            renderPagination();
        }

        function changeStatusFilter() {
            statusFilter = document.getElementById('statusFilter').value;
            currentPage = 1;
            renderPagination();
        }

        function changeProgramFilter() {
            programFilter = document.getElementById('programFilter').value;
            currentPage = 1;
            renderPagination();
        }

        // Debounced search-as-you-type filter
        let searchDebounce = null;
        function changeSearchFilter() {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => {
                searchFilter = document.getElementById('searchInput').value.trim().toLowerCase();
                currentPage = 1;
                renderPagination();
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', renderPagination);
    </script>
    <?php endif; ?>
</body>
</html>