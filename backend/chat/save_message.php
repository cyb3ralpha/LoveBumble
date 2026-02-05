<?php
session_start();
require_once 'config.php'; // DB connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $message = trim($_POST['message'] ?? '');
    $chat_type = $_POST['chat_type'] ?? 'group'; // group or private
    $receiver_id = $_POST['receiver_id'] ?? null;

    // Check user login
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
        exit;
    }

    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
        exit;
    }

    // Simple profanity filter (replace with your profanity_filter.py logic or extend)
    $profane_words = ['badword1','badword2','example']; // extend your list
    foreach ($profane_words as $word) {
        $message = preg_replace('/\b'.preg_quote($word,'/').'\b/i', '***', $message);
    }

    $timestamp = date("Y-m-d H:i:s");

    if ($chat_type === 'group') {
        // Save to group_messages table
        $stmt = $conn->prepare("INSERT INTO group_messages (user_id, message, created_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $message, $timestamp);
    } elseif ($chat_type === 'private' && $receiver_id) {
        // Save to messages table
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $receiver_id, $message, $timestamp);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid chat type or missing receiver.']);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message saved.', 'timestamp' => $timestamp]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save message.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
