<?php
session_start();
include '../config/config.php'; // Kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin user
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc() ?? ['name' => 'Người dùng', 'email' => 'Chưa có email'];
$stmt->close();

// Lấy đường dẫn ảnh cũ từ database
$sql = "SELECT avatar FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$old_avatar = $user['avatar'];
$stmt->close();

// Xử lý đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Lấy mật khẩu cũ từ database
    $sql = "SELECT password_hash FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($current_password, $user['password_hash'])) {
        $error_message = "Mật khẩu hiện tại không đúng!";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Mật khẩu mới không khớp!";
    } else {
        // Mã hóa mật khẩu mới
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Cập nhật mật khẩu
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password_hash, $user_id);

        if ($stmt->execute()) {
            $success_message = "Mật khẩu đã được cập nhật thành công!";
            header("Location: ../users/account.php");
        } else {
            $error_message = "Có lỗi xảy ra, vui lòng thử lại!";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mật khẩu và Bảo mật</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../config/header.php'; ?>
    <?php include '../config/navbar-top.php'; ?>
    <?php include '../config/navbar-left.php'; ?>

    <!-- main content -->
    <div class="main-content bg-lightblue theme-dark-bg right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left">
                <div class="middle-wrap">
                    <div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">
                        <div class="card-body p-lg-5 p-4 w-100 border-0">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="mb-4 font-xxl fw-700 mont-font mb-lg-5 mb-4 font-md-xs">Mật khẩu và Bảo mật</h4>

                                    <!-- Thông báo -->
                                    <?php if (!empty($error_message)) : ?>
                                        <div class="alert alert-danger"><?= $error_message ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($success_message)) : ?>
                                        <div class="alert alert-success"><?= $success_message ?></div>
                                    <?php endif; ?>

                                    <form action="password.php" method="POST">
                                        <div class="form-group">
                                            <label>Mật khẩu hiện tại</label>
                                            <input type="password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Mật khẩu mới</label>
                                            <input type="password" name="new_password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Nhập lại mật khẩu mới</label>
                                            <input type="password" name="confirm_password" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../config/right-chat.php'; ?>
    <script src="../js/plugin.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
