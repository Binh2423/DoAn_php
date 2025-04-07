<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications for the logged-in user
$query = "SELECT n.id, n.type, n.reference_id, n.is_read, n.created_at,
                 CASE 
                     WHEN n.type = 'like' THEN 'Ai đó đã thích bài viết của bạn'
                     WHEN n.type = 'comment' THEN 'Ai đó đã bình luận bài viết của bạn'
                     WHEN n.type = 'friend_request' THEN 'Có một lời mời kết bạn'
                     WHEN n.type = 'message' THEN 'Có tin nhắn mới'
                     WHEN n.type = 'mention' THEN 'Ai đó đã nhắc đến bạn trong bài viết'
                 END AS message
          FROM notifications n
          WHERE n.user_id = ?
          ORDER BY n.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
?>
