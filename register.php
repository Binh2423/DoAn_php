<?php
session_start();
include 'config/config.php'; // Kết nối database

if (!$conn) {
    die("Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if ($password !== $confirm_password) {
        $error = "Mật khẩu không khớp!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Email đã tồn tại!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Đăng ký thất bại, vui lòng thử lại!";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Facebook - Đăng ký tài khoản</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="color-theme-blue">
    <div class="main-wrap">
        <div class="row">
            <div class="col-xl-5 d-none d-xl-block p-0 vh-100 bg-no-repeat">
                <svg class="banner-login" xmlns="http://www.w3.org/2000/svg" width="70%" height="360" fill="none" viewBox="0 0 1090 360">
                    <path fill="#0866FF" d="M881.583 257.897h29.48v-47.696..." />
                </svg>
                <span class="slogan-login">Facebook giúp bạn kết nối và chia sẻ </span>
                <span class="slogan-login">với mọi người trong cuộc sống của bạn.</span>
            </div>
            
            <div class="col-xl-7 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
                <div class="card shadow-none border-0 ms-auto me-auto login-card">
                    <div class="card-body rounded-0 text-left">
                        <h2 class="fw-700 display1-size display2-md-size mb-3">Đăng ký tài khoản</h2>
                        <form method="POST" action="">
                            <?php if (!empty($error)): ?>
                                <p class='error'><?= htmlspecialchars($error) ?></p>
                            <?php endif; ?>
                            <div class="form-group icon-input mb-3">
                                <i class="font-sm ti-user text-grey-500 pe-0"></i>
                                <input type="text" name="username" class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600" placeholder="Tên người dùng" required>
                            </div>
                            <div class="form-group icon-input mb-3">
                                <i class="font-sm ti-email text-grey-500 pe-0"></i>
                                <input type="email" name="email" class="style2-input ps-5 form-control text-grey-900 font-xsss fw-600" placeholder="Email" required>
                            </div>
                            <div class="form-group icon-input mb-3">
                                <input type="password" name="password" class="style2-input ps-5 form-control text-grey-900 font-xss ls-3" placeholder="Mật khẩu" required>
                                <i class="font-sm ti-lock text-grey-500 pe-0"></i>
                            </div>
                            <div class="form-group icon-input mb-3">
                                <input type="password" name="confirm_password" class="style2-input ps-5 form-control text-grey-900 font-xss ls-3" placeholder="Nhập lại mật khẩu" required>
                                <i class="font-sm ti-lock text-grey-500 pe-0"></i>
                            </div>
                            <div class="col-sm-12 p-0 text-left">
                                <button type="submit" class="form-control text-center style2-input text-white fw-600 bg-dark border-0 p-0">Đăng Ký</button>
                            </div>
                            <h6 class="text-grey-500 font-xsss fw-500 mt-0 mb-0 lh-32">Bạn đã có tài khoản? <a href="login.php" class="fw-700 ms-1">Đăng nhập</a></h6>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
