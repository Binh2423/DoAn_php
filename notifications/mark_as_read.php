
<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$notification_id = $_POST['notification_id'] ?? null;

if ($notification_id) {
    // Update the notification as read
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Notification marked as read']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Notification ID is required']);
}
?>
