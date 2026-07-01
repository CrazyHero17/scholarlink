<?php
header('Content-Type: application/json');

if (!isset($_FILES['id_image']) || $_FILES['id_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No image received.']);
    exit;
}

$api_key = "AIzaSyChhsFDiddz3X5OsuDjZrFQh3WbuhUQ0mY"; 
$model = "gemini-3.5-flash"; // ✨ 3.5 Flash is the correct model name

$image_path = $_FILES['id_image']['tmp_name'];
$mime_type = mime_content_type($image_path);
$base64_image = base64_encode(file_get_contents($image_path));

$prompt = "You are a TAU ID Scanner. Extract details from front and back.
Required JSON format:
{
    \"first_name\": \"Extracted First Name\",
    \"last_name\": \"Extracted Last Name\",
    \"student_id\": \"10-digit Number\",
    \"course\": \"The full program name, e.g., Bachelor of Science in Information Technology\",
    \"date_of_birth\": \"YYYY-MM-DD\"
}
NO backticks, NO markdown, just the JSON.";

$payload = ["contents" => [["parts" => [["text" => $prompt], ["inline_data" => ["mime_type" => $mime_type, "data" => $base64_image]]]]]];

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=" . $api_key);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$ai_text = str_replace(['```json', '```'], '', $result['candidates'][0]['content']['parts'][0]['text'] ?? '');
$parsed = json_decode(trim($ai_text), true);

if ($parsed) {
    echo json_encode(['success' => true, 'data' => $parsed]);
} else {
    echo json_encode(['success' => false, 'error' => 'Parse failed: ' . $ai_text]);
}
?>