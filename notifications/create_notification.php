
<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type']; // 'like', 'comment', 'friend_request', 'message', 'mention'
$reference_id = $_POST['reference_id']; // ID of the related entity (e.g., post, comment, etc.)

// Validate type
$valid_types = ['like', 'comment', 'friend_request', 'message', 'mention'];
if (!in_array($type, $valid_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid notification type']);
    exit();
}

// Insert notification into the database
$query = "INSERT INTO notifications (user_id, type, reference_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("isi", $user_id, $type, $reference_id);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Notification created']);
?>
