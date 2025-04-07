<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để sử dụng chức năng chat.");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

// 1. Lấy tất cả tin nhắn giữa 2 người
$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

// 2. Lấy avatar người gửi/nhận
$userAvatars = [];
$avatar_sql = "SELECT id, avatar FROM users WHERE id IN (?, ?)";
$avatar_stmt = $conn->prepare($avatar_sql);
$avatar_stmt->bind_param("ii", $sender_id, $receiver_id);
$avatar_stmt->execute();
$avatar_result = $avatar_stmt->get_result();

while ($user = $avatar_result->fetch_assoc()) {
    $avatar_path = (!empty($user['avatar']) && file_exists("../images/" . $user['avatar']))
        ? "../images/" . $user['avatar']
        : "../images/default.png";
    $userAvatars[$user['id']] = $avatar_path;
}
$avatar_stmt->close();
?>

<?php while ($row = $result->fetch_assoc()):
    $isSender = $row['sender_id'] == $sender_id;
    $avatar = $userAvatars[$row['sender_id']] ?? "../images/default.png";
?>
    <div class="message-item <?= ($row['sender_id'] == $sender_id) ? 'outgoing-message' : 'incoming-message' ?>">
        <?php if ($row['sender_id'] != $sender_id): ?>
            <img src="<?= $avatar ?>" class="message-avatar">
        <?php endif; ?>

        <div>
            <div class="message-bubble"><?= htmlspecialchars($row['content']) ?></div>
            <div class="message-time"><?= date('H:i d/m/Y', strtotime($row['created_at'])) ?></div>
        </div>
    </div>


<?php endwhile; ?>