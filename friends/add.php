<?php
session_start();
require_once('../config/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_id'])) {
    $friend_id = $_POST['friend_id'];

    $check_query = "SELECT * FROM friendships 
                   WHERE (user_id = ? AND friend_id = ?) 
                   OR (user_id = ? AND friend_id = ?)";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $query = "INSERT INTO friendships (user_id, friend_id, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $friend_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Đã gửi lời mời kết bạn']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Đã tồn tại lời mời kết bạn']);
    }
    exit();
}

$query = "SELECT u.* FROM users u 
          WHERE u.id != ? 
          AND u.id NOT IN (
              SELECT CASE 
                         WHEN user_id = ? THEN friend_id 
                         ELSE user_id 
                     END 
              FROM friendships
              WHERE (user_id = ? OR friend_id = ?)
          )";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gợi ý kết bạn</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="http://localhost:3000/index.php">Trang Chủ</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="middle-sidebar-header">
            <h2>Gợi ý kết bạn</h2>
        </div>

        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left">
                <div class="card w-100 shadow-xss rounded-xxl border-0 mb-3">
                    <div class="card-body d-flex align-items-center p-4">
                        <h4 class="fw-700 mb-0 font-xssss text-grey-900">Những người bạn có thể biết</h4>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-3 p-4">
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="<?php echo $user['avatar'] ?? '../assets/images/default-avatar.png'; ?>" 
                                         alt="avatar" class="shadow-sm rounded-circle w75 mb-2">
                                    <h4 class="fw-700 text-grey-900 font-xssss">
                                        <?php echo htmlspecialchars($user['name']); ?>
                                    </h4>
                                    <button class="btn btn-primary mt-2" 
                                            onclick="addFriend(<?php echo $user['id']; ?>)">
                                        Thêm bạn bè
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addFriend(friendId) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `friend_id=${friendId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.status === 'success' ? '✅ ' + data.message : '❌ ' + data.message);
                location.reload();
            })
            .catch(() => alert('Đã xảy ra lỗi! Vui lòng thử lại.'));
        }
    </script>
</body>
</html>
<style>
    /* Your custom CSS code */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
    }

    .main-content {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
    }

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

    /* Add more CSS here as needed */
</style>
