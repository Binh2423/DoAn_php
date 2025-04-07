<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_id']) && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        $query = "UPDATE friendships 
                 SET status = 'accepted' 
                 WHERE friend_id = ? AND user_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $friend_id);
        
        if ($stmt->execute()) {
            // Thêm thông báo
            $notification_query = "INSERT INTO notifications (user_id, type, reference_id) 
                                 VALUES (?, 'friend_request', ?)";
            $stmt = $conn->prepare($notification_query);
            $stmt->bind_param("ii", $friend_id, $user_id);
            $stmt->execute();
            
            echo json_encode(['status' => 'success', 'message' => 'Đã chấp nhận lời mời kết bạn']);
        }
    } elseif ($action === 'decline') {
        $query = "UPDATE friendships 
                 SET status = 'declined' 
                 WHERE friend_id = ? AND user_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $friend_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Đã từ chối lời mời kết bạn']);
        }
    }
    exit();
}
?>
