<?php
session_start();
require_once 'db.php';       // Database connection
require_once 'constants.php'; // Constants

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $liked_user_id = $_POST['liked_user_id'] ?? null;

    if (!$liked_user_id || $liked_user_id == $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user selection.']);
        exit();
    }

    // Check if already liked
    $stmt_check = $conn->prepare("SELECT id FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
    $stmt_check->bind_param("ii", $user_id, $liked_user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You already liked this user.']);
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // Insert like
    $timestamp = date("Y-m-d H:i:s");
    $stmt_insert = $conn->prepare("INSERT INTO user_likes (user_id, liked_user_id, created_at) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("iis", $user_id, $liked_user_id, $timestamp);

    if ($stmt_insert->execute()) {
        // Optional: check if mutual like (match)
        $stmt_match = $conn->prepare("SELECT id FROM user_likes WHERE user_id = ? AND liked_user_id = ?");
        $stmt_match->bind_param("ii", $liked_user_id, $user_id);
        $stmt_match->execute();
        $stmt_match->store_result();

        $is_match = $stmt_match->num_rows > 0 ? true : false;
        $stmt_match->close();

        echo json_encode([
            'status' => 'success',
            'message' => $is_match ? 'It’s a match!' : 'User liked successfully.',
            'match' => $is_match
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to like user.']);
    }

    $stmt_insert->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
