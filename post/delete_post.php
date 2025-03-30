<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "social_network";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu JSON
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['post_id'])) {
    $post_id = $data['post_id'];

    // Xóa các dữ liệu liên quan trước (bình luận, cảm xúc)
    $conn->query("DELETE FROM reactions WHERE post_id = $post_id");
    $conn->query("DELETE FROM comments WHERE post_id = $post_id");

    // Xóa bài viết
    if ($conn->query("DELETE FROM posts WHERE id = $post_id") === TRUE) {
        echo "Bài viết đã được xóa thành công!";
    } else {
        echo "Lỗi khi xóa bài viết!";
    }
}
?>
