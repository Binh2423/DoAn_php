<?php
session_start();
require '../config/config.php'; // Kết nối database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Kiểm tra email có tồn tại không
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Cập nhật mật khẩu mới
        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
        if ($update_stmt->execute()) {
            $message = "Mật khẩu đã được cập nhật thành công!";
            header("Location: login.php");
        } else {
            $message = "Lỗi cập nhật mật khẩu!";
        }
    } else {
        $message = "Email không tồn tại trong hệ thống!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="main-wrap">
        <div class="row">
            <div class="col-xl-3 p-0"></div>
            <div class="col-xl-7 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
                <div class="card shadow-none border-0 ms-auto me-auto login-card">
                    <div class="card-body">
                        <h2 class="fw-700 mb-4">Quên mật khẩu</h2>
                        <?php if (isset($message)) echo "<p>$message</p>"; ?>
                        <form method="POST" action="">
                            <div class="form-group mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Nhập Email đăng ký" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới" required>
                            </div>
                            <div class="form-check text-left mb-3">
                                <input type="checkbox" class="form-check-input" id="agree" required>
                                <label for="agree">Chấp nhận Điều khoản</label>
                            </div>
                            <button type="submit" class="btn btn-dark">Đổi mật khẩu</button>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</body>
</html>
