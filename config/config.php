<?php
$servername = "localhost"; // hoặc IP server database
$username = "root"; // Tên tài khoản MySQL
$password = ""; // Mật khẩu MySQL (nếu có)
$database = "social_network"; // Thay bằng tên database của bạn

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "UPDATE users SET last_active = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
