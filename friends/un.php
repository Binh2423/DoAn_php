<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Xóa mối quan hệ bạn bè
    $query = "DELETE FROM friendships 
             WHERE (user_id = ? AND friend_id = ?) 
             OR (user_id = ? AND friend_id = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Đã hủy kết bạn']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra']);
    }
    exit();
}
?>
