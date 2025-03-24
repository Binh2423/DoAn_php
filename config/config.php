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
?>
