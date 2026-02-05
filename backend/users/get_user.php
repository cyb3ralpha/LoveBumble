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

$current_user_id = $_SESSION['user_id'];

try {
    // Fetch all users except the current user
    $stmt = $conn->prepare("SELECT id, full_name, profile_picture, dob FROM users WHERE id != ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        // If profile picture is empty, use default
        if (empty($row['profile_picture'])) {
            $row['profile_picture'] = DEFAULT_PROFILE_PIC;
        }
        // Calculate age from dob
        $dob = new DateTime($row['dob']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        $row['age'] = $age;

        $users[] = $row;
    }

    echo json_encode(['status' => 'success', 'users' => $users]);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
