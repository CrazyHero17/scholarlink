<?php
session_start();
require '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$my_id = $_SESSION['user_id'];

// 1. FETCH MESSAGES
if ($action === 'fetch') {
    $other_person_id = $_GET['other_user_id']; // The admin or student you are talking to

    $stmt = $pdo->prepare("
        SELECT * FROM messages 
        WHERE (SenderID = :me AND ReceiverID = :them) 
           OR (SenderID = :them AND ReceiverID = :me)
        ORDER BY CreatedAt ASC
    ");
    $stmt->execute(['me' => $my_id, 'them' => $other_person_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read if I am the receiver
    $update = $pdo->prepare("UPDATE messages SET IsRead = 1 WHERE SenderID = :them AND ReceiverID = :me AND IsRead = 0");
    $update->execute(['them' => $other_person_id, 'me' => $my_id]);

    echo json_encode($messages);
    exit();
}

// 2. SEND MESSAGE
if ($action === 'send') {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (SenderID, ReceiverID, MessageText) VALUES (?, ?, ?)");
        $stmt->execute([$my_id, $receiver_id, $message]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Empty message']);
    }
    exit();
}

// 3. EDIT MESSAGE
if ($action === 'edit') {
    $message_id = $_POST['message_id'] ?? 0;
    $new_text = trim($_POST['message'] ?? '');

    if (!empty($new_text) && $message_id > 0) {
        // Security Check: Only allow the actual sender to edit their own message!
        $stmt = $pdo->prepare("UPDATE messages SET MessageText = ? WHERE MessageID = ? AND SenderID = ?");
        $stmt->execute([$new_text, $message_id, $my_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid text or message ID']);
    }
    exit();
}
?>