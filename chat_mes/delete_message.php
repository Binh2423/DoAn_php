<?php
session_start();
include "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message_id = $_POST['message_id'];
    $user_id = $_SESSION['user_id'];

    // Kiểm tra xem tin nhắn có thuộc về user không
    $sql_check = "SELECT * FROM messages WHERE id = ? AND sender_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $message_id, $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Xóa tin nhắn
        $sql_delete = "DELETE FROM messages WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $message_id);

        if ($stmt_delete->execute()) {
            echo "Tin nhắn đã xóa!";
        } else {
            echo "Lỗi khi xóa!";
        }
        $stmt_delete->close();
    } else {
        echo "Bạn không có quyền xóa tin nhắn này!";
    }

    $stmt_check->close();
}
$conn->close();
