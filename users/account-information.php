
<?php
require_once "../config/config.php"; // File chứa thông tin kết nối database

// Giả sử bạn đã có session để lấy user_id
session_start();
$user_id = $_SESSION['user_id']; // Đảm bảo session đã khởi tạo

$sql = "SELECT email, phone, name, avatar, bio, contact_info, privacy_settings FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Gán tham số user_id vào câu lệnh SQL
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Không tìm thấy thông tin người dùng.";
}

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập.");
}

$user_id = $_SESSION['user_id']; // ID của người dùng đang đăng nhập

// Chuẩn bị truy vấn
$sql = "SELECT email, phone, name, avatar, bio, contact_info, privacy_settings FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id); // Gán giá trị user_id
    $stmt->execute(); // Không truyền tham số vào execute()
    $result = $stmt->get_result(); // Lấy kết quả
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Lỗi truy vấn: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt</title>
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
                                        <img id="avatarPreview" src="../images/avatar_fb_TB.jpg" class="rounded-circle" width="120" height="120" alt="Avatar">
                                        <input type="file" name="avatar" id="avatarInput" class="d-none">
                                        <br>
                                        <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('avatarInput').click();">Chọn ảnh</button>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Họ và Tên</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Giới thiệu</label>
                                        <textarea name="bio" class="form-control"></textarea>
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

<script>
function editInfo(field) {
    let newValue = prompt("Nhập giá trị mới:");
    if (newValue !== null) {
        fetch('update_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ field, value: newValue })
        }).then(response => response.json()).then(data => {
            if (data.success) location.reload();
            else alert("Cập nhật thất bại");
        });
    }
}
</script>

<body></head>