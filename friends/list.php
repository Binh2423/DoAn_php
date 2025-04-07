<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách lời mời kết bạn
$pending_query = "SELECT u.id, u.name, u.avatar,
                 (SELECT COUNT(*) FROM friendships f2 
                  WHERE ((f2.user_id = u.id AND f2.friend_id IN 
                        (SELECT friend_id FROM friendships WHERE user_id = ? AND status = 'accepted'))
                        OR 
                        (f2.friend_id = u.id AND f2.user_id IN 
                        (SELECT friend_id FROM friendships WHERE user_id = ? AND status = 'accepted')))
                        AND f2.status = 'accepted') as mutual_friends
                 FROM users u
                 INNER JOIN friendships f ON f.user_id = u.id
                 WHERE f.friend_id = ? AND f.status = 'pending'";
$stmt = $conn->prepare($pending_query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$pending_result = $stmt->get_result();

// Lấy danh sách bạn bè
$friends_query = "SELECT u.id, u.name, u.avatar
                 FROM users u
                 INNER JOIN friendships f ON (f.user_id = u.id OR f.friend_id = u.id)
                 WHERE (f.user_id = ? OR f.friend_id = ?)
                 AND f.status = 'accepted'
                 AND u.id != ?";
$stmt = $conn->prepare($friends_query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$friends_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bạn bè | Facebook</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Tổng thể trang */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        /* Header */
        /* Navbar */
        .navbar {
            background-color: #4267B2;
            padding: 10px 20px;
        }

        .navbar-left {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-left a {
            color: white;
            text-decoration: none;
        }

        /* Sidebar */
        .sidebar {
            background-color: white;
            width: 250px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            height: 100vh;
        }

        .sidebar-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu-item {
            display: flex;
            align-items: center;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .sidebar-menu-item:hover,
        .sidebar-menu-item.active {
            background-color: #f0f2f5;
        }

        .sidebar-menu-item i {
            margin-right: 10px;
        }

        /* Main Content */
        .main-container {
            display: flex;
            padding: 20px;
        }

        .content {
            width: 100%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Friend Request */
        .friend-requests {
            margin-bottom: 20px;
        }

        .friend-request-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .friend-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .friend-info {
            flex-grow: 1;
        }

        .friend-name {
            font-size: 18px;
            font-weight: bold;
        }

        .friend-mutual {
            color: #666;
            margin-bottom: 10px;
        }

        .friend-actions button {
            margin-right: 10px;
        }

        /* Friend List */
        .friend-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .friend-card {
            width: 180px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .friend-card-cover {
            height: 60px;
            background-color: #f0f2f5;
        }

        .friend-card-content {
            padding: 15px;
            text-align: center;
        }

        .friend-card-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .friend-card-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .friend-card-actions button {
            width: 100%;
            margin-top: 5px;
        }

        /* Button Styles */
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #4267B2;
            color: white;
        }

        .btn-secondary {
            background-color: #ddd;
            color: #333;
        }

        .btn-primary:hover {
            background-color: #365899;
        }

        .btn-secondary:hover {
            background-color: #ccc;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-container">
            <nav class="navbar">
                <div class="navbar-left">
                    <a href="http://localhost:3000/index.php">Trang Chủ</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <div class="sidebar">
            <h2 class="sidebar-title">Bạn bè</h2>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item active">
                    <i class="fas fa-user-friends"></i>
                    <span>Lời mời kết bạn</span>
                </li>
                <li class="sidebar-menu-item">
                    <i class="fas fa-users"></i>
                    <span>Tất cả bạn bè</span>
                </li>
            </ul>
        </div>

        <div class="content">
            <?php if ($pending_result->num_rows > 0): ?>
                <div class="friend-requests">
                    <h3>Lời mời kết bạn</h3>
                    <?php while ($request = $pending_result->fetch_assoc()): ?>
                        <div class="friend-request-item">
                            <img class="friend-avatar" src="<?php echo $request['avatar'] ?? '../assets/images/default-avatar.png'; ?>" alt="Avatar">
                            <div class="friend-info">
                                <div class="friend-name"><?php echo htmlspecialchars($request['name']); ?></div>
                                <div class="friend-mutual"><?php echo $request['mutual_friends']; ?> bạn chung</div>
                                <div class="friend-actions">
                                    <button class="btn btn-primary" onclick="handleFriendRequest(<?php echo $request['id']; ?>, 'accept')">
                                        Xác nhận
                                    </button>
                                    <button class="btn btn-secondary" onclick="handleFriendRequest(<?php echo $request['id']; ?>, 'decline')">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <div class="friend-list">
                <?php while ($friend = $friends_result->fetch_assoc()): ?>
                    <div class="friend-card">
                        <div class="friend-card-cover"></div>
                        <div class="friend-card-content">
                            <img class="friend-card-avatar" src="<?php echo $friend['avatar'] ?? '../assets/images/default-avatar.png'; ?>" alt="Avatar">
                            <div class="friend-card-name"><?php echo htmlspecialchars($friend['name']); ?></div>
                            <div class="friend-card-actions">
                                <a href="http://localhost/php/DoAn/chat_mes/index.php?receiver_id=<?php echo $friend['id']; ?>" class="btn btn-primary">Nhắn tin</a>

                                <button class="btn btn-secondary" onclick="unfriend(<?php echo $friend['id']; ?>)">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        function handleFriendRequest(friendId, action) {
            fetch('status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `friend_id=${friendId}&action=${action}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    }
                });
        }

        function unfriend(friendId) {
            if (confirm('Bạn có chắc muốn hủy kết bạn?')) {
                fetch('un.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `friend_id=${friendId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        }
                    });
            }
        }
    </script>
</body>

</html>