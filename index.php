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

// Setup Fallbacks (Upgraded with Persuasive Copywriting)
$hero_title = $cms['hero']['title'] ?? 'Fund your future with ScholarLink.';
$hero_body = $cms['hero']['body'] ?? "Don't let finances limit your potential. Access exclusive TAU grants, build your secure document vault, and apply to multiple scholarships with just one click.";
$grants_title = $cms['grants_header']['title'] ?? 'Available Scholarships & Grants';
$grants_body = $cms['grants_header']['body'] ?? 'Explore opportunities tailored for your course and year level.';
$no_grants_title = $cms['no_grants']['title'] ?? 'No scholarships available right now';
$no_grants_body = $cms['no_grants']['body'] ?? 'We are preparing new grants for you. Please check back later!';

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

// Map scholarships into a JSON-friendly array
$jsScholarshipMap = [];
$active_scholarship_count = 0;
$total_active_award_value = 0;
$nearest_active_deadline = null;

foreach ($all_scholarships as $sch) {
    $jsScholarshipMap[$sch['ScholarshipID']] = $sch;

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

// Dynamic Program Image Matcher (With Unsplash Security Parameters)
function getProgramImage($programName) {
    $prog = strtolower($programName ?? '');
    $params = '?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    
    if (strpos($prog, 'information technology') !== false || strpos($prog, 'computer') !== false || strpos($prog, 'it') !== false) {
        return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97' . $params;
    } elseif (strpos($prog, 'agri') !== false || strpos($prog, 'forest') !== false) {
        return 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449' . $params;
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

// Dynamic Urgency Logic
function scholarship_status_meta($status, $deadline = null) {
    $status = $status ?: 'Active';
    
    if (strtolower($status) === 'active' && !empty($deadline)) {
        $days_left = floor((strtotime($deadline) - strtotime('today')) / 86400);
        if ($days_left < 0) {
            return ['label' => 'Expired', 'class' => 'status-closed'];
        } elseif ($days_left === 0) {
            return ['label' => '🚨 Ends Today!', 'class' => 'status-urgent animate-pulse'];
        } elseif ($days_left <= 7) {
            return ['label' => '🔥 ' . $days_left . ' Days Left', 'class' => 'status-urgent'];
        }
    }

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
    
    <!-- PWA Settings -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <link rel="apple-touch-icon" href="assets/img/tau_logo.png">
    
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

        body { margin: 0; color: var(--ink); background: var(--wash); font-family: 'Plus Jakarta Sans', Arial, sans-serif; line-height: 1.6; }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }

        /* HEADER & TICKER */
        .site-header { position: sticky; top: 0; left: 0; right: 0; z-index: 50; background: rgba(255, 255, 255, 0.95); box-shadow: 0 7px 20px rgba(0, 0, 0, 0.06); backdrop-filter: blur(14px); }
        .nav-shell { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; min-height: 82px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: inline-flex; align-items: center; gap: 12px; min-width: 0; }
        .brand img { width: 44px; height: 44px; object-fit: contain; transition: transform 0.3s ease; }
        .brand:hover img { transform: scale(1.05); }
        .brand strong { display: block; font-size: 1.05rem; letter-spacing: -0.02em; color: var(--ink); }
        .brand span { display: block; color: var(--gold); font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; }

        .nav-links a:not(.login-link) { position: relative; color: var(--ink); font-weight: 800; }
        .nav-links a:not(.login-link)::after { content: ""; position: absolute; left: 0; right: 0; bottom: -6px; height: 2px; background: var(--green); transform: scaleX(0); transition: transform 0.2s ease; }
        .nav-links a:not(.login-link):hover::after { transform: scaleX(1); }

        .hamburger { display: none; background: transparent; border: none; font-size: 1.5rem; color: var(--ink); cursor: pointer; padding: 0; }

        @keyframes pulse-soft {
            0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
            70% { box-shadow: 0 0 0 12px rgba(25, 135, 84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }

        .login-link, .btn-primary { 
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border-radius: 8px; 
            background: var(--green) !important; border: 1px solid var(--green) !important; color: #fff !important; font-weight: 800; 
            transition: all 0.2s ease; box-shadow: 0 10px 20px rgba(25, 135, 84, 0.15); 
        }
        .btn-apply-pulse { animation: pulse-soft 2s infinite; }
        .login-link:hover, .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(25, 135, 84, 0.25); }
        .btn-ghost { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border-radius: 8px; font-weight: 800; background: #fff; color: var(--green) !important; border: 1px solid #fff; transition: all 0.2s ease; }
        .btn-ghost:hover { background: rgba(255, 255, 255, 0.9); transform: translateY(-2px); }

        /* MOBILE MENU ANIMATIONS */
        #mobileMenu { transform-origin: top; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .mobile-menu-active { transform: scaleY(1); opacity: 1; visibility: visible; }
        .mobile-menu-hidden { transform: scaleY(0); opacity: 0; visibility: hidden; }

        /* HERO SECTION */
        .hero { min-height: 720px; display: flex; align-items: center; position: relative; overflow: hidden; background: linear-gradient(135deg, #0f5132 0%, #198754 100%); padding: 80px 0 160px; color: #fff; }
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

        /* SMART MATCH & COMMAND CENTER */
        .search-command-center {
            position: relative; z-index: 10; margin: -60px auto 40px;
            width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2)));
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(16px);
            border-radius: 1.5rem; box-shadow: 0 20px 50px rgba(15, 81, 50, 0.15); border: 1px solid var(--line);
            padding: 24px; display: flex; flex-direction: column; gap: 16px;
        }

        .search-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1.5fr 1fr;
            gap: 16px;
            margin-top: 16px;
        }

        .search-wrap { position: relative; display: flex; align-items: center; width: 100%; }
        .search-wrap i { position: absolute; left: 16px; color: var(--green); font-size: 1.1rem; pointer-events: none; }
        .search-input { width: 100%; background: #f8fafc; border: 1px solid var(--line); color: var(--ink); font-size: 0.95rem; font-weight: 700; padding: 14px 16px 14px 44px; border-radius: 12px; outline: none; transition: all 0.2s ease; }
        .search-input:focus { border-color: var(--green); background: #fff; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1); }
        
        .search-command-center select { background: #f8fafc; border: 1px solid var(--line); color: var(--ink); font-size: 0.9rem; font-weight: 700; padding: 14px 16px; border-radius: 12px; outline: none; cursor: pointer; transition: all 0.2s ease; width: 100%; }
        .search-command-center select:focus { border-color: var(--green); background: #fff; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1); }

        /* GRANTS SECTION */
        section { padding: 40px 0 84px; }
        .section-inner { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; }
        .section-head { position: relative; width: 100%; margin-bottom: 40px; }
        .section-head-top { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 20px; }
        .section-kicker { color: var(--green); font-size: 0.8rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; }
        .section-head h2 { margin: 0 0 8px; font-size: clamp(1.9rem, 3.5vw, 2.8rem); line-height: 1.1; letter-spacing: -0.02em; color: var(--ink); }
        .section-head p { margin: 0; color: var(--muted); font-weight: 500; font-size: 1.05rem; max-width: 600px; }

        /* PROGRAM CARDS WITH IMAGES */
        .program-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .program-card { border: 1px solid var(--line); border-radius: 16px; background: #fff; box-shadow: 0 10px 28px rgba(25, 135, 84, 0.05); display: flex; flex-direction: column; transition: transform 0.25s ease, box-shadow 0.25s ease; position: relative; overflow: hidden; }
        .program-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(25, 135, 84, 0.12); border-color: #b7e4ca; }
        
        .program-media { height: 180px; width: 100%; position: relative; overflow: hidden; background: #0f5132; }
        .program-media img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .program-card:hover .program-media img { transform: scale(1.08); }
        
        .program-body { padding: 24px; flex: 1; display: flex; flex-direction: column; }
        .program-badge { width: fit-content; margin-bottom: 12px; padding: 6px 12px; border-radius: 6px; background: #eef8f1; color: var(--green); font-size: 0.70rem; font-weight: 900; letter-spacing: 0.06em; text-transform: uppercase; }
        
        .status-pill { position: absolute; top: 16px; right: 16px; z-index: 10; padding: 6px 12px; border-radius: 999px; font-size: 0.7rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .status-active { background: #eaf7ee; color: #198754; border: 1px solid #b7e4ca; }
        .status-urgent { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-closed { background: #f1f2f4; color: #6b7280; border: 1px solid #d1d5db; }
        .status-draft { background: #fff6e5; color: #b7791f; border: 1px solid #f6e05e; }
        .status-upcoming { background: #eaf1ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .status-default { background: #f1f2f4; color: #6b7280; border: 1px solid #d1d5db; }

        .program-body h3 { margin: 0 0 8px; font-size: 1.2rem; color: var(--ink); line-height: 1.3; }
        .program-body p { margin: 0 0 20px; color: var(--muted); font-size: 0.92rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        .fact-row { padding-top: 14px; margin-top: 14px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
        .fact-row span { color: var(--muted); font-size: 0.7rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; }
        .fact-row strong { color: var(--ink); font-size: 0.9rem; font-weight: 800; }
        .fact-row .highlight { color: var(--gold); }

        .program-link { display: flex; width: 100%; border: none; cursor: pointer; align-items: center; justify-content: center; gap: 8px; margin-top: 24px; padding: 14px; border-radius: 10px; background: #f8fafc; color: var(--green); font-size: 0.9rem; font-weight: 900; font-family: inherit; transition: all 0.2s ease; border: 1px solid var(--line); }
        .program-card:hover .program-link { background: var(--green); color: #fff; border-color: var(--green); }

        .pagination-controls { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 48px; padding-top: 32px; border-top: 1px solid var(--line); }
        .page-btn { width: 40px; height: 40px; border-radius: 8px; border: 1px solid var(--line); background: #fff; color: var(--ink); font-weight: 800; cursor: pointer; transition: all 0.2s ease; }
        .page-btn:not(:disabled):hover { border-color: var(--green); color: var(--green); }
        .page-btn.active { background: var(--green); color: #fff; border-color: var(--green); box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2); }
        .page-btn:disabled { background: var(--wash); color: #a0aec0; cursor: not-allowed; }

        /* HOW IT WORKS */
        .how-it-works { background: #fff; padding-top: 84px; }
        .steps-track { position: relative; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 32px; margin-top: 16px; }
        .steps-track::before { content: ""; position: absolute; top: 34px; left: 8%; right: 8%; height: 2px; background: var(--line); z-index: 0; border-style: dashed; }
        .step-card { position: relative; z-index: 1; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 16px; }
        .step-number { width: 68px; height: 68px; border-radius: 50%; background: var(--green); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 900; box-shadow: 0 12px 26px rgba(25, 135, 84, 0.28); border: 6px solid #fff; }
        .step-card h3 { margin: 0; font-size: 1.25rem; color: var(--ink); font-weight: 900; }
        .step-card p { margin: 0; color: var(--muted); font-size: 0.95rem; max-width: 280px; }
        .step-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; background: #eef8f1; color: var(--green); font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid #b7e4ca; }

        /* SUCCESS STORIES */
        .testimonials { background: var(--wash); padding-top: 84px; }
        .testimonial-card { padding: 32px; border-radius: 24px; background: #fff; border: 1px solid var(--line); transition: transform 0.3s ease; }
        .testimonial-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(25,135,84,0.06); border-color: var(--green); }

        /* SCROLL REVEAL */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }

        /* FAQ */
        .faq-layout { display: grid; grid-template-columns: 1.3fr 1fr; gap: 40px; align-items: start; }
        .faq-list { display: flex; flex-direction: column; gap: 12px; }
        .faq-visual { position: sticky; top: 110px; background: #fff; border: 1px solid var(--line); border-radius: 24px; padding: 32px; box-shadow: 0 20px 45px rgba(25, 135, 84, 0.08); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        .faq-item { border: 1px solid var(--line); border-radius: 12px; background: #fff; overflow: hidden; transition: all 0.3s ease; }
        .faq-item:hover { border-color: var(--green); box-shadow: 0 4px 12px rgba(25,135,84,0.05); }
        .faq-question { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 24px; background: none; border: none; cursor: pointer; text-align: left; font-family: inherit; font-size: 1.05rem; font-weight: 800; color: var(--ink); }
        .faq-question i { color: var(--green); transition: transform 0.25s ease; flex-shrink: 0; background: #eef8f1; padding: 8px; border-radius: 50%; }
        .faq-item.is-open .faq-question i { transform: rotate(180deg); background: var(--green); color: #fff; }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .faq-answer-inner { padding: 0 24px 24px; color: var(--muted); font-size: 0.95rem; line-height: 1.7; font-weight: 500; }

        /* CTA BANNER */
        .cta-banner { background: linear-gradient(120deg, #0f5132, #198754); border-radius: 24px; padding: 56px clamp(24px, 5vw, 64px); display: flex; align-items: center; justify-content: space-between; gap: 32px; flex-wrap: wrap; box-shadow: 0 24px 50px rgba(15, 81, 50, 0.25); position: relative; overflow: hidden; }
        .cta-banner::after { content: ""; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; }
        .cta-banner h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.8rem, 4vw, 2.5rem); letter-spacing: -0.02em; font-weight: 900; }
        .cta-banner p { margin: 0; color: rgba(255,255,255,0.85); font-size: 1.1rem; font-weight: 500; max-width: 500px; }
        .cta-banner .btn-primary { background: #fff !important; border-color: #fff !important; color: var(--green) !important; white-space: nowrap; font-size: 1.05rem; padding: 0 28px; min-height: 54px; }
        .cta-banner .btn-primary:hover { background: var(--wash) !important; transform: scale(1.02); }

        /* EMPTY STATE */
        .empty-state { grid-column: 1 / -1; padding: 64px 20px; text-align: center; background: #fff; border: 1px dashed #b7e4ca; border-radius: 16px; }
        .empty-state i { font-size: 3rem; color: #b7e4ca; margin-bottom: 16px; }

        /* FOOTER */
        .site-footer { padding: 48px 0 32px; background: #1a1b1f; color: rgba(255, 255, 255, 0.7); border-top: 4px solid var(--green); }
        .footer-inner { width: min(var(--page-max), calc(100% - (var(--page-gutter) * 2))); margin: 0 auto; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 24px; }
        .footer-logo { height: 56px; opacity: 0.9; margin-bottom: 8px; transition: transform 0.3s; }
        .footer-logo:hover { transform: scale(1.05); opacity: 1; }
        .footer-inner p { margin: 0; font-size: 0.95rem; }
        .footer-inner .small-text { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; color: var(--gold); text-transform: uppercase; }
        .footer-social { margin-top: 16px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.1); width: 100%; display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .social-link { display: inline-flex; align-items: center; gap: 10px; padding: 12px 24px; border-radius: 8px; background: #1877f2; color: #fff; font-weight: 700; font-size: 0.95rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .social-link:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(24, 119, 242, 0.3); color: #fff; }

        @media (max-width: 980px) {
            .program-grid { grid-template-columns: repeat(2, 1fr); }
            .search-grid { grid-template-columns: 1fr 1fr; }
            .faq-layout { grid-template-columns: 1fr; }
            .faq-visual { position: static; order: -1; margin-bottom: 24px; }
        }
        @media (max-width: 768px) {
            .nav-shell { min-height: 70px; }
            .brand span { display: none; }
            .nav-links { display: none; position: absolute; top: 100%; left: 0; right: 0; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(14px); flex-direction: column; padding: 24px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); border-top: 1px solid var(--line); z-index: 40; }
            .nav-links.mobile-open { display: flex; }
            .nav-links a { width: 100%; text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); }
            .nav-links a:last-child { border-bottom: none; }
            .nav-links .login-link { width: 100%; text-align: center; margin-top: 12px; }
            .hamburger { display: block; }
            
            .program-grid { grid-template-columns: 1fr; }
            .hero { padding-bottom: 140px; }
            .hero h1 { font-size: 2.2rem; }
            .hero-actions { flex-direction: column; width: 100%; max-width: 300px; }
            .hero-actions a { width: 100%; }
            .search-command-center { margin-top: -40px; padding: 20px; }
            .search-grid { grid-template-columns: 1fr; }
            .section-head-top { flex-direction: column; align-items: flex-start; }
            .steps-track { grid-template-columns: 1fr; gap: 36px; }
            .steps-track::before { display: none; }
            .cta-banner { flex-direction: column; text-align: center; padding: 40px 24px; }
            .cta-banner .btn-primary { width: 100%; }
        }
    </style>
</head>
<body>
    
    <!-- LIVE ANNOUNCEMENT TICKER -->
    <div class="bg-yellow-400 text-yellow-950 px-4 py-2.5 text-center text-[10px] sm:text-xs font-black uppercase tracking-widest relative z-[60] flex items-center justify-center gap-3">
        <span class="flex h-2 w-2 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
        </span>
        📢 Application for A.Y. 2026-2027 is now open! Secure your slots early via ScholarLink.
    </div>

    <header class="site-header">
        <nav class="nav-shell" aria-label="Primary navigation">
            <a class="brand" href="index.php">
                <img src="assets/img/tau_logo.png" alt="TAU Logo">
                <span>
                    <strong>ScholarLink</strong>
                    <span>Tarlac Agricultural University</span>
                </span>
            </a>
            
            <!-- Links -->
            <div class="nav-links" id="navLinks">
                <a href="#grants" onclick="document.getElementById('navLinks').classList.remove('mobile-open')">Grants</a>
                <a href="#how-it-works" onclick="document.getElementById('navLinks').classList.remove('mobile-open')">How It Works</a>
                <a href="#faq" onclick="document.getElementById('navLinks').classList.remove('mobile-open')">FAQ</a>
                <?php if ($is_logged_in): ?>
                    <a class="login-link btn-apply-pulse" href="<?= $role_redirect ?>">
                        Dashboard <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                <?php else: ?>
                    <a href="student_login.php">Sign In</a>
                    <a class="login-link btn-apply-pulse" href="student_login.php">Apply Now</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Button -->
            <button class="hamburger" onclick="document.getElementById('navLinks').classList.toggle('mobile-open')">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
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
                    <span class="eyebrow"><i class="fas fa-certificate text-green-300"></i> Official Scholarship Portal</span>
                    <h1 id="hero-title"><?= htmlspecialchars($hero_title) ?></h1>
                    <p class="hero-copy"><?= htmlspecialchars($hero_body) ?></p>
                    <div class="hero-actions">
                        <a class="btn btn-primary btn-apply-pulse" href="#grants"><i class="fas fa-magnifying-glass"></i> Browse Grants</a>
                        <?php if (!$is_logged_in): ?>
                            <a class="btn btn-ghost" href="student_login.php"><i class="fas fa-user-plus"></i> Create Account</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-8 flex flex-wrap items-center gap-4 sm:gap-6 text-xs font-bold text-white/90 bg-black/20 w-fit px-5 py-3 rounded-xl backdrop-blur-md border border-white/10">
                        <span class="flex items-center gap-2"><i class="fas fa-shield-check text-green-400"></i> Secure & Official</span>
                        <span class="hidden sm:block w-1 h-1 bg-white/30 rounded-full"></span>
                        <span class="flex items-center gap-2"><i class="fas fa-bolt text-yellow-400"></i> 1-Click Apply</span>
                        <span class="hidden sm:block w-1 h-1 bg-white/30 rounded-full"></span>
                        <span class="flex items-center gap-2"><i class="fas fa-folder-open text-blue-300"></i> Cloud Vault</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- SMART MATCH ELIGIBILITY FINDER -->
        <div class="search-command-center reveal">
            <div class="flex items-center justify-between w-full border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-900 text-lg flex items-center gap-2"><i class="fas fa-bullseye text-green-600"></i> Smart Match Finder</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hidden sm:block">Find your eligibility instantly</span>
            </div>
            <div class="search-grid w-full">
                <div class="search-wrap">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search grant name..." oninput="changeSearchFilter()">
                </div>
                <div class="search-wrap">
                    <i class="fas fa-star" style="color: var(--gold);"></i>
                    <input type="number" step="0.01" min="1.00" max="5.00" id="matchGwa" class="search-input" placeholder="Your GWA (e.g. 1.50)" onblur="validateGwa(this)" oninput="runSmartMatch()">
                </div>
                <select id="matchProgram" onchange="runSmartMatch()">
                    <option value="all">🎓 All Target Programs</option>
                    <?php foreach ($distinct_programs as $prog): ?>
                        <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="matchYear" onchange="runSmartMatch()">
                    <option value="all">📅 All Year Levels</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                </select>
            </div>
        </div>

        <!-- Grants Section -->
        <section id="grants" style="padding-top: 0;">
            <div class="section-inner">
                <div class="section-head reveal">
                    <div class="section-head-top">
                        <div>
                            <span class="section-kicker">Find your match</span>
                            <h2><?= htmlspecialchars($grants_title) ?></h2>
                            <p><?= htmlspecialchars($grants_body) ?></p>
                        </div>
                        
                        <!-- ✨ RESTORED: Items Per Page Dropdown -->
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest hidden sm:block">Show:</span>
                            <select id="itemsPerPage" onchange="changeItemsPerPage()" class="bg-white border border-slate-200 text-slate-700 text-sm font-bold py-2.5 px-4 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 cursor-pointer shadow-sm transition-all">
                                <option value="3">3 Grants</option>
                                <option value="6" selected>6 Grants</option>
                                <option value="9">9 Grants</option>
                                <option value="999">All Grants</option>
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
                                $statusMeta = scholarship_status_meta($statusValue, $sch['Deadline'] ?? null);
                                $isActiveScholarship = strtolower($statusValue) === 'active';
                            ?>
                            <article class="program-card reveal" 
                                     data-status="<?= htmlspecialchars($statusValue) ?>" 
                                     data-program="<?= htmlspecialchars($sch['ProgramName'] ?? 'Open to All Courses') ?>" 
                                     data-name="<?= htmlspecialchars(strtolower($sch['Name'])) ?>"
                                     data-year="<?= htmlspecialchars($sch['YearLevel'] ?? '') ?>"
                                     data-gwa="<?= htmlspecialchars($sch['MinimumGWA'] ?? '') ?>">
                                
                                <span class="status-pill <?= $statusMeta['class'] ?>"><?= $statusMeta['label'] ?></span>
                                
                                <!-- ✨ ADDED DYNAMIC PROGRAM IMAGES WITH FALLBACK HERE -->
                                <div class="program-media">
                                    <img src="<?= getProgramImage($sch['ProgramName']) ?>" onerror="this.style.display='none'" alt="<?= htmlspecialchars($sch['ProgramName'] ?? 'Program') ?>">
                                </div>
                                
                                <div class="program-body">
                                    <span class="program-badge"><?= htmlspecialchars($sch['ProgramName'] ?? 'Open to All Courses') ?></span>
                                    <h3><?= htmlspecialchars($sch['Name']) ?></h3>
                                    <p><?= htmlspecialchars($sch['Description']) ?></p>
                                    
                                    <div class="mt-auto">
                                        <div class="fact-row">
                                            <span>Min GWA Requirement</span>
                                            <strong><?= htmlspecialchars($sch['MinimumGWA']) ?></strong>
                                        </div>
                                        <div class="fact-row">
                                            <span>Grant Amount</span>
                                            <strong class="highlight">₱<?= number_format($sch['AwardAmount']) ?></strong>
                                        </div>
                                        <div class="fact-row">
                                            <span>Target Year</span>
                                            <strong><?= !empty($sch['YearLevel']) ? $sch['YearLevel'] : 'All Levels' ?></strong>
                                        </div>
                                        <a href="scholarship_details.php?id=<?= $sch['ScholarshipID'] ?>" class="program-link <?= $isActiveScholarship ? 'hover:bg-green-700 hover:border-green-700' : 'text-slate-500 hover:bg-slate-200 border-slate-200' ?>">
                                            <?= $isActiveScholarship ? 'View & Apply <i class="fas fa-arrow-right"></i>' : 'View Details <i class="fas fa-eye"></i>' ?> 
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($all_scholarships)): ?>
                    <div class="empty-state" id="noFilterResults" style="display:none;">
                        <i class="fas fa-filter-circle-xmark"></i>
                        <h3 class="text-xl font-black text-slate-900 mb-2">No scholarships match your criteria</h3>
                        <p class="text-slate-500 font-medium">Try clearing your search or adjusting your GWA and Category.</p>
                        <button onclick="clearSmartMatch()" class="mt-4 px-6 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors">Clear Filters</button>
                    </div>
                    <div class="pagination-controls" id="paginationControls"></div>
                    <div id="pageInfo" class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mt-4"></div>
                <?php endif; ?>

            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section id="how-it-works" class="how-it-works" aria-labelledby="how-it-works-title">
            <div class="section-inner">
                <div class="section-head reveal text-center mx-auto" style="max-width: 600px;">
                    <span class="section-kicker">Getting Started</span>
                    <h2 id="how-it-works-title">How ScholarLink Works</h2>
                    <p>From account creation to a submitted application — experience a frictionless journey in three simple steps.</p>
                </div>
                <div class="steps-track">
                    <div class="step-card reveal">
                        <span class="step-number">1</span>
                        <span class="step-badge"><i class="fas fa-user-plus"></i> Sign Up</span>
                        <h3>Create an Account</h3>
                        <p>Register with your student details in minutes and get instant access to your personalized dashboard.</p>
                    </div>
                    <div class="step-card reveal" style="transition-delay: 100ms;">
                        <span class="step-number">2</span>
                        <span class="step-badge"><i class="fas fa-lock"></i> Vault</span>
                        <h3>Build your Document Vault</h3>
                        <p>Upload your requirements once — COR, grades, IDs — and securely reuse them for every scholarship.</p>
                    </div>
                    <div class="step-card reveal" style="transition-delay: 200ms;">
                        <span class="step-number">3</span>
                        <span class="step-badge"><i class="fas fa-bolt"></i> Apply</span>
                        <h3>Apply with 1-Click</h3>
                        <p>No more re-uploading files. Pick a grant, confirm your vault documents, and submit instantly.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SUCCESS STORIES / TESTIMONIALS -->
        <section id="testimonials" class="testimonials">
            <div class="section-inner">
                <div class="section-head reveal text-center mx-auto" style="max-width: 600px;">
                    <span class="section-kicker">Success Stories</span>
                    <h2>Hear from our Scholars</h2>
                    <p>Join hundreds of TAU students who have successfully secured their education through ScholarLink.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                    <div class="testimonial-card reveal">
                        <div class="text-yellow-400 text-xl mb-4">★★★★★</div>
                        <p class="text-slate-700 font-medium italic mb-6">"ScholarLink helped me focus on my thesis without worrying about my tuition. The 1-click apply feature saved me so much time!"</p>
                        <div class="flex items-center gap-4 border-t border-slate-200 pt-4">
                            <div class="w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-black">JC</div>
                            <div>
                                <h4 class="font-black text-slate-900 text-sm">Juan Dela Cruz</h4>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">BSIT '26 • Academic Scholar</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card reveal" style="transition-delay: 100ms;">
                        <div class="text-yellow-400 text-xl mb-4">★★★★★</div>
                        <p class="text-slate-700 font-medium italic mb-6">"The Document Vault is a game changer. I uploaded my COR and grades once, and I applied to 3 grants effortlessly."</p>
                        <div class="flex items-center gap-4 border-t border-slate-200 pt-4">
                            <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-black">MR</div>
                            <div>
                                <h4 class="font-black text-slate-900 text-sm">Maria Rosario</h4>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">BSAgri '25 • CHED Grantee</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card reveal" style="transition-delay: 200ms;">
                        <div class="text-yellow-400 text-xl mb-4">★★★★★</div>
                        <p class="text-slate-700 font-medium italic mb-6">"I love the real-time tracking. I knew exactly when my documents were verified and when I was shortlisted by the OSSD!"</p>
                        <div class="flex items-center gap-4 border-t border-slate-200 pt-4">
                            <div class="w-10 h-10 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center font-black">AK</div>
                            <div>
                                <h4 class="font-black text-slate-900 text-sm">Angela D.</h4>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">BSEd '24 • Private Grantee</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ SECTION -->
        <section id="faq" aria-labelledby="faq-title" style="background: var(--wash);">
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
                            <div class="faq-answer-inner">This depends on the specific grant's guidelines. ScholarLink automatically checks for strict dual-scholarship policies to prevent hoarding. Check the "Program Description" in each grant's details page.</div>
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
                            <div class="faq-answer-inner">You will receive an official email notification, and you can also track your live application status directly from your student dashboard. Updates are reflected in real-time.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-visual reveal">
                    <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Still have questions?</h3>
                    <p class="text-slate-500 font-medium text-sm mb-6">Our OSSD team is ready to assist you with your scholarship journey.</p>
                    <a href="https://www.facebook.com/TAUOSSD" target="_blank" class="btn btn-primary w-full">Message OSSD Support</a>
                </div>
                </div>
            </div>
        </section>

        <!-- CTA BANNER -->
        <section aria-label="Call to action" style="padding-bottom: 0;">
            <div class="section-inner" style="transform: translateY(40px); position: relative; z-index: 10;">
                <div class="cta-banner reveal">
                    <div>
                        <h2>Ready to secure your future?</h2>
                        <p>Join ScholarLink today and manage all your TAU scholarship applications in one unified portal.</p>
                    </div>
                    <a class="btn btn-primary btn-apply-pulse" href="<?= $is_logged_in ? $role_redirect : 'student_login.php' ?>" style="color: var(--green) !important;">
                        <?= $is_logged_in ? 'Go to Dashboard' : '<i class="fas fa-user-plus"></i> Create Free Account' ?>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer" style="padding-top: 80px;">
        <div class="footer-inner">
            <img src="assets/img/tau_logo.png" alt="TAU Logo">
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

    <!-- SCRIPTS -->
    <script>
        // ✨ PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => console.log('PWA ServiceWorker registered successfully!'))
                    .catch(error => console.log('ServiceWorker registration failed:', error));
            });
        }

        // Keyboard Accessibility for Slider
        document.addEventListener('keydown', function(e) {
            const activeTag = document.activeElement.tagName.toLowerCase();
            if (activeTag !== 'input' && activeTag !== 'textarea') {
                if (e.key === 'ArrowLeft') document.querySelector('[data-hero-slide="prev"]')?.click();
                if (e.key === 'ArrowRight') document.querySelector('[data-hero-slide="next"]')?.click();
            }
        });

        // Hero Slider
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
                timerId = window.setInterval(function () { showSlide(activeIndex + 1); }, 4800);
            }

            document.querySelector('[data-hero-slide="prev"]')?.addEventListener('click', function () { showSlide(activeIndex - 1); startTimer(); });
            document.querySelector('[data-hero-slide="next"]')?.addEventListener('click', function () { showSlide(activeIndex + 1); startTimer(); });

            startTimer();
        })();

        // FAQ Accordion
        function toggleFaq(button) {
            const item = button.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const wasOpen = item.classList.contains('is-open');

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

        // Scroll Reveal Animations
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
                revealTargets.forEach(target => target.classList.add('is-visible'));
            }
        });
    </script>

    <?php if (!empty($all_scholarships)): ?>
    <script>
        // GWA Formatter and Validation
        function validateGwa(input) {
            let val = parseFloat(input.value);
            if (isNaN(val)) {
                input.value = '';
                runSmartMatch();
                return;
            }
            if (val < 1.00) val = 1.00;
            if (val > 5.00) val = 5.00;
            input.value = val.toFixed(2);
            runSmartMatch();
        }

        // ✨ RESTORED & FIXED: Pagination and Filter Logic
        let currentPage = 1;
        
        // Kukunin niya na ngayon yung value mula sa dropdown imbes na static "6"
        const itemsSelect = document.getElementById('itemsPerPage');
        let itemsPerPage = itemsSelect ? parseInt(itemsSelect.value) : 6;
        
        const allCards = Array.from(document.querySelectorAll('.program-card'));

        // Function para palitan ang dami ng items na nakikita
        function changeItemsPerPage() {
            const select = document.getElementById('itemsPerPage');
            if(select) {
                itemsPerPage = parseInt(select.value) || 6;
                currentPage = 1;
                renderPagination();
            }
        }

        function runSmartMatch() {
            currentPage = 1;
            renderPagination();
        }

        function clearSmartMatch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('matchGwa').value = '';
            document.getElementById('matchProgram').value = 'all';
            document.getElementById('matchYear').value = 'all';
            runSmartMatch();
        }

        function getFilteredCards() {
            const searchInput = document.getElementById('searchInput') ? document.getElementById('searchInput').value.trim().toLowerCase() : '';
            const matchGwa = document.getElementById('matchGwa') ? document.getElementById('matchGwa').value : '';
            const matchProgram = document.getElementById('matchProgram') ? document.getElementById('matchProgram').value : 'all';
            const matchYear = document.getElementById('matchYear') ? document.getElementById('matchYear').value : 'all';

            return allCards.filter(card => {
                const cardName = card.dataset.name;
                const cardProgram = card.dataset.program;
                const cardYear = card.dataset.year;
                const cardGwa = parseFloat(card.dataset.gwa) || 5.0;

                const searchMatch = searchInput === '' || cardName.includes(searchInput);
                const progMatch = matchProgram === 'all' || cardProgram === 'Open to All Courses' || cardProgram === '' || cardProgram === matchProgram;
                const yearMatch = matchYear === 'all' || cardYear === '' || cardYear === matchYear;
                
                let gwaMatch = true;
                if (matchGwa !== '') {
                    const userGwa = parseFloat(matchGwa);
                    if (!isNaN(userGwa) && !isNaN(cardGwa)) {
                        gwaMatch = userGwa <= cardGwa;
                    }
                }

                return searchMatch && progMatch && yearMatch && gwaMatch;
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
            cards.slice(start, end).forEach(card => {
                card.style.display = 'flex';
                setTimeout(() => card.classList.add('is-visible'), 50);
            });

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

        let searchDebounce = null;
        function changeSearchFilter() {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => {
                currentPage = 1;
                renderPagination();
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', renderPagination);
    </script>
    <?php endif; ?>

    <!-- ✨ CHATBOT INTEGRATION -->
    <?php include 'includes/chatbot.php'; ?>
</body>
</html>