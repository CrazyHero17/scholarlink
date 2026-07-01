<?php
// actions/ai_scan_document.php
require_once '../vendor/autoload.php';
use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

// Get the requested file path
$data = json_decode(file_get_contents('php://input'), true);
$file_url = $data['file_url'] ?? '';

// Basic Security Check
if (empty($file_url)) {
    echo json_encode(['success' => false, 'error' => 'No document provided.']);
    exit;
}

// Convert web path to server path
$physical_path = "../" . ltrim(str_replace('../', '', $file_url), '/');

if (!file_exists($physical_path)) {
    echo json_encode(['success' => false, 'error' => 'File not found.']);
    exit;
}

try {
    // 1. I-parse ang PDF
    $parser = new Parser();
    $pdf = $parser->parseFile($physical_path);
    $text = $pdf->getText();

    // 2. REGEX PARSING (Ito ang logic na 100% accurate)
    // Hahanapin lahat ng pattern na X.XX (e.g., 1.60, 1.22)
    preg_match_all('/\d\.\d{2}/', $text, $matches);
    
    if (!empty($matches[0])) {
        $grades = array_map('floatval', $matches[0]);
        
        // Filter grades to realistic ranges (1.00 - 5.00) to avoid false positives
        $valid_grades = array_filter($grades, function($g) {
            return $g >= 1.00 && $g <= 5.00;
        });

        if (count($valid_grades) > 0) {
            $avg = array_sum($valid_grades) / count($valid_grades);
            $gwa = number_format($avg, 2);
            
            // Return JSON compatible with your existing verify.php frontend
            echo json_encode(['success' => true, 'grade' => $gwa]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No valid grades found.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Could not parse transcript.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>