<?php
session_start();
include '../config/config.php'; // Kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$sql = "SELECT name, email, avatar, cover_photo, bio FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();


// Lấy danh sách bài đăng của người dùng
$sql = "SELECT id, content, media, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../config/header.php'; ?>
    <?php include '../config/navbar-top.php'; ?>
    <?php include '../config/navbar-left.php'; ?>

    <div class="main-content bg-lightblue theme-dark-bg right-chat-active">
        <div class="profile-header">
            <img src="../images/<?= $user['cover_photo'] ?: 'default-cover.jpg' ?>" class="cover-photo">
            <div class="profile-info">
                <img src="../images/<?= $user['avatar'] ?: 'default-avatar.png' ?>" class="profile-avatar">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <p>Email: <?= htmlspecialchars($user['email']) ?></p>
                <p>Bio: <?= htmlspecialchars($user['bio'] ?: 'Chưa có tiểu sử') ?></p>
            </div>
        </div>
        <div class="profile-content">
            <div class="posts-section">
                <h4>Bài đăng của bạn</h4>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <div class="post-card">
                        <h5>
                        <img src="../images/<?= $user['cover_photo'] ?: 'default-cover.jpg' ?>" class="cover-photo">
                        <h3><?= htmlspecialchars($user['name']) ?></h3>
                        </h5>
                        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <?php if (!empty($post['media'])): ?>
                            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $post['media'])): ?>
                                <img src="../images/<?= htmlspecialchars($post['media']) ?>" class="post-media">
                            <?php elseif (preg_match('/\.(mp4|webm|ogg)$/i', $post['media'])): ?>
                                <video controls class="post-media">
                                    <source src="../images/<?= htmlspecialchars($post['media']) ?>" type="video/mp4">
                                </video>
                            <?php endif; ?>
                        <?php endif; ?>
                        <small>Đăng ngày: <?= $post['created_at'] ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php include '../config/right-chat.php'; ?>
    <script src="../js/plugin.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
