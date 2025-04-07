<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để sử dụng chức năng chat.");
}
$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : 0;

if (!$conn) {
    die("Lỗi kết nối CSDL: " . mysqli_connect_error());
}

// Lấy danh sách người dùng
$users_sql = "SELECT id, name, avatar FROM users WHERE id != ?";

$users_stmt = $conn->prepare($users_sql);
if (!$users_stmt) {
    die("Lỗi truy vấn SQL (users): " . $conn->error);
}
$users_stmt->bind_param("i", $sender_id);
$users_stmt->execute();
$users_result = $users_stmt->get_result();

$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi truy vấn SQL (messages): " . $conn->error);
}
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();
// Lấy đường dẫn ảnh đại diện từ database
$users_data = [];
$user_query = "SELECT id, name, avatar FROM users WHERE id IN (?, ?)";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("ii", $sender_id, $receiver_id);
$stmt->execute();
$user_result = $stmt->get_result();
while ($user = $user_result->fetch_assoc()) {
    $users_data[$user['id']] = $user;
}
$stmt->close();

?>
<?php
include '../config/header.php';
include '../config/navbar-top.php';
include '../config/navbar-left.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="main-content right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left pe-0 ps-lg-3 ms-0 me-0" style="max-width: 100%;">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="user-list bg-white p-3">
                            <h4>Danh sách người dùng</h4>
                            <ul>
                                <?php while ($user = $users_result->fetch_assoc()): ?>
                                    <?php
                                    // Kiểm tra nếu có avatar, nếu không thì dùng ảnh mặc định
                                    $avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : '../images/default.png';
                                    ?>
                                    <li class="friend-item">
                                        <a href="?receiver_id=<?= $user['id'] ?>" style="display: flex; align-items: center;">
                                            <img src="<?= $avatar ?>" alt="Avatar" class="avatar" width="40" height="40" style="border-radius: 50%; margin-right: 10px;">
                                            <span><?= htmlspecialchars($user['name']) ?></span>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9 position-relative">
                        <div class="chat-wrapper pt-0 w-100 position-relative scroll-bar bg-white theme-dark-bg">
                            <div class="chat-body p-3">
                                <div class="messages-content pb-5">
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <div class="message-item <?= ($row['sender_id'] == $sender_id) ? 'outgoing-message' : '' ?>">
                                            <div class="message-user">
                                                <figure class="avatar">
                                                    <img src="<?= htmlspecialchars($users_data[$row['sender_id']]['avatar']) ?>" alt="Avatar" class="avatar">
                                                </figure>
                                                <div>
                                                    <h5><?= htmlspecialchars($users_data[$row['sender_id']]['name']) ?></h5>


                                                    <div class="time"> <?= date('h:i A', strtotime($row['created_at'])) ?> </div>
                                                </div>
                                            </div>
                                            <div class="message-wrap"> <?= htmlspecialchars($row['content']) ?> </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                        <div class="chat-bottom dark-bg p-3 shadow-none theme-dark-bg" style="width: 98%;">
                            <form action="send_message.php" method="POST" class="chat-form">
                                <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                                <button class="bg-grey float-left"><i class="ti-microphone text-grey-600"></i></button>
                                <div class="form-group"><input type="text" name="content" placeholder="Start typing.." required></div>
                                <button type="submit" class="bg-current"><i class="ti-arrow-right text-white"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/plugin.js"></script>
    <script src="../js/lightbox.js"></script>
    <script src="../js/scripts.js"></script>
</body>

</html>