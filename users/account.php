<?php
session_start();
include '../config/config.php'; // Kết nối database

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit();
}

$user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session

// Truy vấn thông tin người dùng từ database
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Đổi $id thành $user_id
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc() ?? ['name' => 'Người dùng', 'email' => 'Chưa có email']; // Đảm bảo không bị lỗi
$stmt->close();
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

<!-- main content -->
<div class="main-content bg-lightblue theme-dark-bg right-chat-active">

    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left">
            <div class="middle-wrap">
                <div class="card w-100 border-0 bg-white shadow-xs p-0 mb-4">

                    <div class="card-body p-lg-5 p-4 w-100 border-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="mb-4 font-xxl fw-700 mont-font mb-lg-5 mb-4 font-md-xs">Cài đặt</h4>
                                <div class="nav-caption fw-600 font-xssss text-grey-500 mb-2">Cài đăt chung</div>
                                <ul class="list-inline mb-4">
                                    <li class="list-inline-item d-block border-bottom me-0"><a href="account-information.php" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-primary-gradiant text-white feather-user font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Thông tin cá nhân</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>
                                    <li class="list-inline-item d-block border-bottom me-0"><a href="password.php" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-gold-gradiant text-white feather-shield font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Mật khẩu và bảo mật</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>
                                    <li class="list-inline-item d-block me-0"><a href="social.html" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-red-gradiant text-white feather-lock font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Quyền riêng tư</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>
                                </ul>

                                <div class="nav-caption fw-600 font-xsss text-grey-500 mb-2">Thanh toán</div>
                                <ul class="list-inline mb-4">
                                    <li class="list-inline-item d-block border-bottom me-0"><a href="payment.php" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-mini-gradiant text-white feather-credit-card font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Tài khoản thanh toán</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>
                                    <!-- <li class="list-inline-item d-block  me-0"><a href="password.html" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-blue-gradiant text-white feather-inbox font-md me-3"></i> <h4 class="fw-600 font-xsss mb-0 mt-0">Password</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i></a></li>
                                             -->
                                </ul>

                                <div class="nav-caption fw-600 font-xsss text-grey-500 mb-2">Khác</div>
                                <ul class="list-inline">
                                    <li class="list-inline-item d-block border-bottom me-0"><a href="notification.php" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-gold-gradiant text-white feather-bell font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Thông báo</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>
                                    <li class="list-inline-item d-block border-bottom me-0"><a href="help.php" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-primary-gradiant text-white feather-help-circle font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Hỗ trợ</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>
                                    <li class="list-inline-item d-block me-0"><a href="#" class="pt-2 pb-2 d-flex align-items-center"><i class="btn-round-md bg-red-gradiant text-white feather-lock font-md me-3"></i>
                                            <h4 class="fw-600 font-xsss mb-0 mt-0">Đăng xuất</h4><i class="ti-angle-right font-xsss text-grey-500 ms-auto mt-3"></i>
                                        </a></li>

                                </ul>
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
