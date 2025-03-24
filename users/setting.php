<?php
session_start();
include 'config.php'; // Kết nối CSDL

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$sql = "SELECT email, phone, name, avatar, bio, contact_info, privacy_settings FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $contact_info = $_POST['contact_info'];
    $privacy_settings = json_encode($_POST['privacy_settings']);

    // Xử lý mật khẩu nếu có thay đổi
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $password_hash = $user['password_hash']; // Giữ nguyên nếu không đổi mật khẩu
    }

    // Xử lý avatar
    if (!empty($_FILES['avatar']['name'])) {
        $avatar_name = "uploads/" . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_name);
    } else {
        $avatar_name = $user['avatar']; // Giữ nguyên nếu không thay đổi
    }

    // Cập nhật thông tin vào database
    $update_sql = "UPDATE users SET email=?, phone=?, name=?, avatar=?, bio=?, contact_info=?, privacy_settings=?, password_hash=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssssi", $email, $phone, $name, $avatar_name, $bio, $contact_info, $privacy_settings, $password_hash, $user_id);

    if ($stmt->execute()) {
        $message = "Cập nhật thành công!";
    } else {
        $message = "Lỗi cập nhật!";
    }
}
?>

<?php include 'config/header.php'; ?>
<?php include 'config/navbar-top.php'; ?>
<?php include 'config/navbar-left.php'; ?>

<!-- main content -->
<div class="main-content bg-lightblue theme-dark-bg right-chat-active">

    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left">
            <div class="middle-wrap">
                <div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">
                    <div class="card-body p-lg-5 p-4 w-100 border-0">
                        <h4 class="mb-4 font-xxl fw-700 mont-font mb-lg-5 mb-4 font-md-xs">Chỉnh sửa thông tin cá nhân</h4>

                        <?php if (isset($message)) echo "<p class='alert alert-info'>$message</p>"; ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Số điện thoại:</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Họ và tên:</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Mật khẩu mới (nếu muốn đổi):</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Ảnh đại diện:</label>
                                <input type="file" name="avatar" class="form-control">
                                <img src="<?= $user['avatar'] ?>" width="100" class="mt-2">
                            </div>

                            <div class="form-group">
                                <label>Giới thiệu:</label>
                                <textarea name="bio" class="form-control"><?= htmlspecialchars($user['bio']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Thông tin liên hệ:</label>
                                <textarea name="contact_info" class="form-control"><?= htmlspecialchars($user['contact_info']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Cài đặt quyền riêng tư:</label>
                                <input type="text" name="privacy_settings" class="form-control" value="<?= htmlspecialchars($user['privacy_settings']) ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- main content -->

<?php include 'config/right-chat.php'; ?>

<script src="js/plugin.js"></script>
<script src="js/scripts.js"></script>

</body>
</html>
