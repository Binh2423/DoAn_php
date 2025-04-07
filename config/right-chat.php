<?php
include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để sử dụng chức năng chat.");
}

$user_id = $_SESSION['user_id'];

// Danh sách bạn bè (trừ chính mình)
$query = "SELECT id, name, avatar FROM users WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_chat = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chat nổi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .message-item {
            display: flex;
            margin-bottom: 10px;
        }

        .message-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .incoming-message {
            justify-content: flex-start;
        }

        .outgoing-message {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 14px;
            border-radius: 18px;
            font-size: 14px;
            position: relative;
            line-height: 1.4;
        }

        .incoming-message .message-bubble {
            background-color: #e4e6eb;
            color: #050505;
            border-bottom-left-radius: 0;
        }

        .outgoing-message .message-bubble {
            background-color: #1877f2;
            color: white;
            border-bottom-right-radius: 0;
        }

        .message-time {
            font-size: 11px;
            color: gray;
            margin-top: 2px;
            padding-left: 45px;
        }

        /* Align the main user's messages (outgoing) to the right */
        .outgoing-message .message-bubble {
            margin-left: auto;
            /* Align to the right */
            background-color: #1877f2;
            color: white;
        }
    </style>
</head>

<body>

    <div class="right-chat nav-wrap mt-2 right-scroll-bar">
        <div class="middle-sidebar-right-content bg-white shadow rounded p-3">
            <h4 class="text-uppercase text-muted fw-bold">Người liên hệ</h4>
            <ul class="list-group list-group-flush">
                <?php while ($user_chat = $result_chat->fetch_assoc()):
                    $avatar = (!empty($user_chat['avatar']) && file_exists("../images/" . $user_chat['avatar']))
                        ? "../images/" . htmlspecialchars($user_chat['avatar'])
                        : "../images/default.png";
                ?>
                    <li class="list-group-item d-flex align-items-center border-0 px-0 py-2">
                        <img src="<?= $avatar ?>" class="rounded-circle w35 me-2" alt="Avatar">
                        <a href="javascript:void(0);" class="text-dark fw-bold" onclick="openChatModal(<?= $user_chat['id'] ?>, '<?= htmlspecialchars($user_chat['name']) ?>')">
                            <?= htmlspecialchars($user_chat['name']) ?>
                        </a>
                        <span class="bg-success ms-auto btn-round-xss"></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- Floating Chat Modal -->
    <div id="chatModal" class="chat-box shadow" style="display: none; position: fixed; bottom: 10px; right: 10px; width: 360px; background: #fff; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); overflow: hidden; z-index: 1000; display: flex; flex-direction: column;">

        <!-- Header -->
        <div class="chat-header d-flex align-items-center justify-content-between px-3 py-2" style="background-color: #1877f2; color: white;">
            <div id="chatUserName" class="fw-bold">Tên người nhận</div>
            <button onclick="closeChatModal()" class="btn-close btn-close-white"></button>
        </div>

        <!-- Messages -->
        <div id="chatMessages" class="chat-body px-3 py-2" style="flex: 1; overflow-y: auto; background-color: #f0f2f5;">
            <!-- Message bubbles loaded dynamically -->
        </div>

        <!-- Footer -->
        <div class="chat-footer p-2" style="background-color: #fff; border-top: 1px solid #ddd;">
            <form id="chatForm" onsubmit="sendMessage(event)">
                <input type="hidden" id="receiverId" name="receiver_id">
                <div class="input-group">
                    <input type="text" id="messageInput" name="content" class="form-control rounded-pill" placeholder="Aa" required>
                    <button class="btn btn-primary ms-2 rounded-pill px-3" type="submit">Gửi</button>
                </div>
            </form>
        </div>
    </div>



    <script>
        function openChatModal(userId, userName) {
            document.getElementById('chatUserName').innerText = userName;
            document.getElementById('receiverId').value = userId;
            document.getElementById('chatModal').style.display = "block";
            fetchMessages(userId);
        }

        function closeChatModal() {
            document.getElementById('chatModal').style.display = "none";
        }

        function fetchMessages(userId) {
            fetch(`../chat_mes/floating.php?receiver_id=${userId}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('chatMessages').innerHTML = html;
                });
        }

        function sendMessage(event) {
            event.preventDefault();
            const receiverId = document.getElementById('receiverId').value;
            const content = document.getElementById('messageInput').value;

            fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `receiver_id=${receiverId}&content=${encodeURIComponent(content)}`
                })
                .then(res => res.text())
                .then(() => {
                    document.getElementById('messageInput').value = '';
                    fetchMessages(receiverId);
                });
        }
    </script>

</body>

</html>