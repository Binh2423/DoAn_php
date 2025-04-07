<?php
session_start();
include "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['user_id']; // ID người gửi từ session
    $receiver_id = $_POST['receiver_id'];
    $content = $_POST['content'];

    if (!empty($content) && !empty($receiver_id)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iis", $sender_id, $receiver_id, $content);
            if ($stmt->execute()) {
                echo "Tin nhắn đã gửi!";
                header("Location: index.php?receiver_id=$receiver_id"); // Chuyển hướng về trang chat sau khi gửi tin nhắn
                exit();
            } else {
                echo "Lỗi gửi tin nhắn: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Lỗi SQL: " . $conn->error;
        }
    } else {
        echo "Vui lòng nhập tin nhắn!";
    }
}
$conn->close();
