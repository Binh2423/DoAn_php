<?php
require_once "../config/config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập.");
}

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu từ form
$email = $_POST['email'];
$phone = $_POST['phone'];
$name = $_POST['name'];
$bio = $_POST['bio'];
$contact_info = $_POST['contact_info']; // Lấy thêm thông tin liên hệ

// Lấy đường dẫn ảnh cũ từ database
$sql = "SELECT avatar FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$old_avatar = $user['avatar'];
$stmt->close();

// Xử lý upload ảnh đại diện
$avatar_path = $old_avatar; // Giữ nguyên ảnh cũ
if (!empty($_FILES['avatar']['name'])) {
    $target_dir = "../images/";
    $file_name = time() . "_" . basename($_FILES["avatar"]["name"]);
    $target_file = $target_dir . $file_name;

    // Nếu file chưa tồn tại, thì lưu ảnh mới
    if (!file_exists($target_file)) {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $avatar_path = "../images/" . $file_name; // Lưu đường dẫn tương đối vào database
        }
    }
}

// Cập nhật dữ liệu vào database
if ($avatar_path !== $old_avatar) {
    $sql = "UPDATE users SET email = ?, phone = ?, name = ?, bio = ?, contact_info = ?, avatar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $email, $phone, $name, $bio, $contact_info, $avatar_path, $user_id);
} else {
    $sql = "UPDATE users SET email = ?, phone = ?, name = ?, bio = ?, contact_info = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $email, $phone, $name, $bio, $contact_info, $user_id);
}

if ($stmt->execute()) {
    echo "<script>alert('Cập nhật thành công!'); window.location.href = 'account-information.php';</script>";
} else {
    echo "<script>alert('Cập nhật thất bại!'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
