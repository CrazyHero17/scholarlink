<?php
session_start();
require '../includes/db_connect.php';

header('Content-Type: application/json');

// 🔒 Security: Only the Super Admin can run this
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Super_Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']); 
    exit;
}

try {
    // 📊 STEP 1: Gather Live System Metrics
    $stats = [];
    $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['total_students'] = $pdo->query("SELECT COUNT(*) FROM users WHERE Role = 'Student'")->fetchColumn();
    $stats['total_applications'] = $pdo->query("SELECT COUNT(*) FROM application")->fetchColumn();
    $stats['pending_apps'] = $pdo->query("SELECT COUNT(*) FROM application WHERE Status = 'Pending'")->fetchColumn();
    $stats['approved_apps'] = $pdo->query("SELECT COUNT(*) FROM application WHERE Status = 'Approved'")->fetchColumn();
    $stats['active_scholarships'] = $pdo->query("SELECT COUNT(*) FROM scholarship")->fetchColumn(); 

    // 🧠 STEP 2: Setup Gemini API (Matching your chatbot exactly!)
    $api_key = "AIzaSyChhsFDiddz3X5OsuDjZrFQh3WbuhUQ0mY"; 
    $model = "gemini-flash-latest"; // Changed to match process_chat.php!

    $prompt = "You are the Executive AI Analyst for the ScholarLink Scholarship System. 
    Analyze this live database data:
    - Total Users: {$stats['total_users']} ({$stats['total_students']} are Students)
    - Total Scholarships: {$stats['active_scholarships']}
    - Total Applications: {$stats['total_applications']}
    - Pending Review: {$stats['pending_apps']}
    - Approved: {$stats['approved_apps']}

    Write a short, highly professional 2-paragraph executive summary for the Super Admin.
    Paragraph 1: 'System Health & Engagement' - Summarize the current user load and application volume.
    Paragraph 2: 'Strategic Recommendations' - Give 1 actionable tip based on the numbers.
    Do NOT use markdown bolding (**). Keep it strictly under 5 sentences total.";

    $payload = [
        "contents" => [ [ "parts" => [ [ "text" => $prompt ] ] ] ]
    ];

    // 🚀 STEP 3: Send to Google (Using your exact cURL method)
    $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-goog-api-key: ' . $api_key]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    // ✨ NEW: Debugger Catch!
    // If Google returns an error (like Quota Exceeded), we catch it and print it to the screen!
    if (isset($result['error'])) {
        echo json_encode(['success' => false, 'error' => "Google API Error: " . $result['error']['message']]);
        exit;
    }

    $ai_reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if ($ai_reply) {
        $ai_reply = trim(str_replace(["\r", "*", "\""], '', $ai_reply));
        echo json_encode(['success' => true, 'report' => $ai_reply]);
    } else {
        // If it fails but doesn't throw a standard error, print the raw response so we can debug it
        echo json_encode(['success' => false, 'error' => "Unexpected API Response. Raw Data: " . print_r($result, true)]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>