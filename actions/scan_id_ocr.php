<?php
header('Content-Type: application/json');

// 1. Check kung may na-receive na image mula sa frontend scanner
if (!isset($_FILES['id_image']) || $_FILES['id_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No image uploaded or upload failed.']);
    exit;
}

// 2. Kunin ang API Key nang secure mula sa .env file
$env_path = __DIR__ . '/../.env'; // Ina-adjust nito ang path depende kung saan nakalagay ang .env mo
if (file_exists($env_path)) {
    $env = parse_ini_file($env_path);
    $api_key = $env['GOOGLE_VISION_API_KEY'] ?? '';
} else {
    echo json_encode(['success' => false, 'error' => 'System Error: .env file is missing.']);
    exit;
}

if (empty($api_key)) {
    echo json_encode(['success' => false, 'error' => 'System Error: API Key is not set in .env file.']);
    exit;
}

// 3. I-convert ang image to Base64 para mabasa ng Google Vision
$image_path = $_FILES['id_image']['tmp_name'];
$image_data = base64_encode(file_get_contents($image_path));

// 4. I-set up ang payload para sa Google Vision API
$request_body = [
    'requests' => [
        [
            'image' => [
                'content' => $image_data
            ],
            'features' => [
                [
                    'type' => 'TEXT_DETECTION',
                    'maxResults' => 1
                ]
            ]
        ]
    ]
];

// 5. I-send ang request via cURL
$url = 'https://vision.googleapis.com/v1/images:annotate?key=' . $api_key;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
curl_close($ch);

// 6. I-decode ang sagot ng Google
$result = json_encode(['success' => false, 'error' => 'API Error']);
$vision_data = json_decode($response, true);

if (isset($vision_data['responses'][0]['textAnnotations'][0]['description'])) {
    $extracted_text = $vision_data['responses'][0]['textAnnotations'][0]['description'];
  // ==========================================
    // 🧠 DATA EXTRACTION LOGIC (PERFECTED FOR TAU)
    // ==========================================
    $data = [
        'student_id' => '',
        'first_name' => '',
        'last_name' => '',
        'course' => '',
        'date_of_birth' => ''
    ];

    // 1. STUDENT NUMBER
    if (preg_match('/\b(20[0-9]{8})\b/', $extracted_text, $matches)) {
        $data['student_id'] = $matches[1];
    }

    // 2. DATE OF BIRTH
    if (preg_match('/(?:Date of Birth|DOB)[\s\S]{0,10}?([A-Za-z]+\s+\d{1,2},?\s+\d{4}|\d{2}[\/\-]\d{2}[\/\-]\d{4})/i', $extracted_text, $matches)) {
        $data['date_of_birth'] = date('Y-m-d', strtotime(trim($matches[1])));
    }

    // 3. COURSE / PROGRAM (Smart Major Extractor)
    if (preg_match('/(?:Bachelor\s+of\s+[A-Za-z\s]+\s+in\s+|Bachelor\s+of\s+|B\.?S\.?\s+(?:in\s+)?)([^\n\r]+)/i', $extracted_text, $matches)) {
        $data['course'] = trim($matches[1]);
    } elseif (preg_match('/(BSIT|BS[A-Z]{2,5})/i', $extracted_text, $matches)) {
        $data['course'] = trim($matches[1]);
    }

    // 4. SMART NAME PARSING
    $lines = explode("\n", $extracted_text);
    $blacklist = ['REPUBLIC', 'PHILIPPINES', 'TARLAC', 'AGRICULTURAL', 'UNIVERSITY', 'CAMILING', 'EMERGENCY', 'NOTIFY', 'ADDRESS', 'SIGNATURE', 'VALIDITY', 'SEMESTER', 'ID NUMBER', 'DATE OF BIRTH', 'STUDENT'];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $skip = false;
        foreach ($blacklist as $bad_word) {
            if (stripos($line, $bad_word) !== false) {
                $skip = true;
                break;
            }
        }
        if ($skip) continue;

        // Eksaktong hahanapin ang format na: SANIDAD, CHRIS JUNE B.
        if (empty($data['last_name']) && preg_match('/^([A-ZÑ\-\s]+),\s*([A-ZÑ\-\s\.]+)$/i', $line, $matches)) {
            $data['last_name'] = ucwords(strtolower(trim($matches[1])));
            $data['first_name'] = ucwords(strtolower(trim($matches[2])));
            break; 
        }
    }

    // 7. I-bato pabalik sa frontend ang extracted data
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} else {
    $google_error = isset($vision_data['error']['message']) ? $vision_data['error']['message'] : json_encode($vision_data);
    echo json_encode(['success' => false, 'error' => 'Google API Alert: ' . $google_error]);
}
?>