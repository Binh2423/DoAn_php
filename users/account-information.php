<?php
require_once "../config/config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập.");
}

$user_id = $_SESSION['user_id'];

// Truy vấn lấy thông tin người dùng
$sql = "SELECT email, phone, name, avatar, bio,contact_info FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../config/header.php'; ?>
    <?php include '../config/navbar-top.php'; ?>
    <?php include '../config/navbar-left.php'; ?>

    <div class="main-content bg-lightblue theme-dark-bg right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left">
                <div class="middle-wrap">
                    <div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">
                        <div class="card-body p-lg-5 p-4 w-100 border-0">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="mb-4 font-xxl fw-700 mont-font mb-lg-5 mb-4 font-md-xs">Cài đặt tài khoản</h4>
                                    
                                    <!-- Form chỉnh sửa thông tin cá nhân -->
                                    <form action="update-account.php" method="POST" enctype="multipart/form-data">
                                        <div class="text-center mb-4">
                                            <img id="avatarPreview" src="../images/<?= $user['avatar'] ? $user['avatar'] : 'default-avatar.jpg' ?>" class="rounded-circle" width="120" height="120" alt="Avatar">
                                            <input type="file" name="avatar" id="avatarInput" class="d-none">
                                            <br>
                                            <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('avatarInput').click();">Chọn ảnh</button>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Họ và Tên</label>
                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Giới thiệu</label>
                                            <textarea name="bio" class="form-control"><?= htmlspecialchars($user['bio']) ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Liên hệ</label>
                                            <textarea name="contact_info" class="form-control"><?= htmlspecialchars($user['contact_info']) ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">Cập nhật</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// Hiển thị ảnh khi chọn file
document.getElementById('avatarInput').addEventListener('change', function(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('avatarPreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
});
</script>

<?php include '../config/right-chat.php'; ?>
<script src="../js/plugin.js"></script>
<script src="../js/scripts.js"></script>
</body>
</html>
