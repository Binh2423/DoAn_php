<?php
session_start();
include 'config/config.php'; // Kết nối database

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit();
}

$user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session

// Truy vấn thông tin người dùng từ database
$sql = "SELECT name, email FROM users WHERE id = ?";
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
    <title>Cài đặt</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'config/header.php'; ?>
    <?php include 'config/navbar-top.php'; ?>
    <?php include 'config/navbar-left.php'; ?>

    <div class="main-content bg-lightblue theme-dark-bg right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left">
                <div class="middle-wrap">
                    <div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">
                        <div class="card-body p-lg-5 p-4 w-100 border-0">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="mb-4 font-xxl fw-700 mont-font mb-lg-5 mb-4 font-md-xs">Cài đặt</h4>
                                    <div class="nav-caption fw-600 font-xssss text-grey-500 mb-2">Cài đặt chung</div>
                                    <ul class="list-inline mb-4">
                                        <li class="list-inline-item d-block border-bottom me-0">
                                            <a href="#" class="pt-2 pb-2 d-flex align-items-center">
                                                <i class="btn-round-md bg-primary-gradiant text-white feather-user font-md me-3"></i>
                                                <h4 class="fw-600 font-xsss mb-0 mt-0">
                                                    <?= htmlspecialchars($user['name'] ?? 'Người dùng') ?>
                                                </h4>
                                            </a>
                                        </li>
                                        <li class="list-inline-item d-block border-bottom me-0">
                                            <a href="#" class="pt-2 pb-2 d-flex align-items-center">
                                                <i class="btn-round-md bg-gold-gradiant text-white feather-mail font-md me-3"></i>
                                                <h4 class="fw-600 font-xsss mb-0 mt-0">
                                                    <?= htmlspecialchars($user['email'] ?? 'Chưa có email') ?>
                                                </h4>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="nav-caption fw-600 font-xsss text-grey-500 mb-2">Khác</div>
                                    <ul class="list-inline">
                                        <li class="list-inline-item d-block me-0">
                                            <a href="logout.php" class="pt-2 pb-2 d-flex align-items-center">
                                                <i class="btn-round-md bg-red-gradiant text-white feather-lock font-md me-3"></i>
                                                <h4 class="fw-600 font-xsss mb-0 mt-0">Đăng xuất</h4>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'config/right-chat.php'; ?>
    <script src="js/plugin.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
