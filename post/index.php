<?php
session_start();
require '../config/config.php'; // Kết nối database

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn danh sách bài viết cùng thông tin người dùng
$sql = "SELECT posts.*, users.email, users.phone, users.name, users.avatar, users.bio, users.contact_info
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Xử lý tương tác AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Đảm bảo JSON header

    $action = $_POST['action'] ?? null;
    $post_id = $_POST['post_id'] ?? null;
    $comment_text = $_POST['comment'] ?? null;

    if (!$post_id) {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu ID bài viết']);
        exit;
    }

    switch ($action) {
        case 'like':
            $check = $conn->prepare("SELECT * FROM post_interactions WHERE user_id = ? AND post_id = ? AND interaction_type = 'like'");
            $check->bind_param("ii", $user_id, $post_id);
            $check->execute();

            if ($check->get_result()->num_rows > 0) {
                $delete = $conn->prepare("DELETE FROM post_interactions WHERE user_id = ? AND post_id = ?");
                $delete->bind_param("ii", $user_id, $post_id);
                $delete->execute();
                echo json_encode(['status' => 'unliked']);
            } else {
                $insert = $conn->prepare("INSERT INTO post_interactions (user_id, post_id, interaction_type) VALUES (?, ?, 'like')");
                $insert->bind_param("ii", $user_id, $post_id);
                $insert->execute();
                echo json_encode(['status' => 'liked']);
            }
            exit;

        case 'comment':
            if ($comment_text) {
                $insert = $conn->prepare("INSERT INTO post_interactions (user_id, post_id, interaction_type, comment_text) VALUES (?, ?, 'comment', ?)");
                $insert->bind_param("iis", $user_id, $post_id, $comment_text);
                $insert->execute();
                echo json_encode(['status' => 'success', 'message' => 'Bình luận thành công']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Bình luận không hợp lệ']);
            }
            exit;

        case 'share':
            $share_url = "https://yourwebsite.com/post.php?id=$post_id";
            echo json_encode(['status' => 'success', 'message' => 'Liên kết đã được tạo', 'url' => $share_url]);
            exit;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);
            exit;
    }
}
// Lấy đường dẫn ảnh đại diện từ database
$sql_avatar = "SELECT name,avatar FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_avatar);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_avatar = $stmt->get_result();
$user = $result_avatar->fetch_assoc();
$avatar = $user['avatar'];
$stmt->close();
?>

<?php
include '../config/header.php';
include '../config/navbar-top.php';
include '../config/navbar-left.php';
include '../config/right-chat.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .post-box {
            background-color: white;
            /* Màu nền của hộp bài viết */
            border-radius: 10px;
            padding: 15px;
            width: 100%;
            /* Hoặc chiều rộng cụ thể nếu cần */
            max-width: 800px;
            /* Chiều rộng tối đa */
            margin: 20px auto;
            /* Căn giữa */
            color: black;
            /* Màu chữ */
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .post-text p {
            margin: 0;
        }

        .post-actions {
            display: flex;
            justify-content: space-around;
        }

        .action-button {
            background-color: transparent;
            border: none;
            color: black;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .action-icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="main-content right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left">
                <div class="preloader-wrap p-3">
                    <div class="box shimmer">
                        <div class="lines">
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                        </div>
                    </div>
                    <div class="box shimmer mb-3">
                        <div class="lines">
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                        </div>
                    </div>
                    <div class="box shimmer">
                        <div class="lines">
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                            <div class="line s_shimmer"></div>
                        </div>
                    </div>
                </div>
                <div class="row feed-body">
                    <div class="col-xl-8 col-xxl-9 col-lg-8">
                        <div class="card w-100 shadow-none bg-transparent bg-transparent-card border-0 p-0 mb-0">
                            <div class="owl-carousel category-card owl-theme overflow-hidden nav-none">
                                <div class="item">
                                    <div data-bs-toggle="modal" data-bs-target="#Modalstory" class="card w125 h200 d-block border-0 shadow-none rounded-xxxl bg-dark overflow-hidden mb-3 mt-3">
                                        <div class="card-body d-block p-3 w-100 position-absolute bottom-0 text-center">
                                            <a href="#">
                                                <span class="btn-round-lg bg-white"><i class="feather-plus font-lg"></i></span>
                                                <div class="clearfix"></div>
                                                <h4 class="fw-700 position-relative z-index-1 ls-1 font-xssss text-white mt-2 mb-1">Add Story </h4>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div data-bs-toggle="modal" data-bs-target="#Modalstory" class="card w125 h200 d-block border-0 shadow-xss rounded-xxxl bg-gradiant-bottom overflow-hidden cursor-pointer mb-3 mt-3" style="background-image: url(../images/s-1.jpg);">
                                        <div class="card-body d-block p-3 w-100 position-absolute bottom-0 text-center">
                                            <a href="#">
                                                <figure class="avatar ms-auto me-auto mb-0 position-relative w50 z-index-1"><img src="../images/avatar_fb_DH.jpg" alt="image" class="float-right p-0 bg-white rounded-circle w-100 shadow-xss"></figure>
                                                <div class="clearfix"></div>
                                                <h4 class="fw-600 position-relative z-index-1 ls-1 font-xssss text-white mt-2 mb-1">Đăng Huy </h4>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="post-box">
                            <div class="post-header">
                                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar">
                                <div class="post-text">
                                    <p><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Người dùng ẩn danh'; ?> ơi, bạn đang nghĩ gì thế?</p>
                                </div>
                            </div>
                            <div class="post-actions">
                                <button class="action-button">
                                    <img src="../images/video.png" alt="Video" class="action-icon">
                                    Video trực tiếp
                                </button>
                                <button class="action-button">
                                    <img src="../images/picture.png" alt="Ảnh/video" class="action-icon">
                                    Ảnh/video
                                </button>
                                <button class="action-button">
                                    <img src="../images/emotion.png" alt="Cảm xúc" class="action-icon">
                                    Cảm xúc/hoạt động
                                </button>
                            </div>
                        </div>
                        <div class="middle-sidebar-bottom">
                            <div class="middle-sidebar-left">
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($post = $result->fetch_assoc()): ?>
                                        <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
                                            <div class="card-body p-0 d-flex">
                                                <figure class="avatar me-3">
                                                    <img src="<?php echo $post['avatar'] ?? 'images/avatar_fb_DH.jpg'; ?>" class="shadow-sm rounded-circle w45">
                                                </figure>
                                                <h4 class="fw-700 text-grey-900 font-xssss mt-1">
                                                    <?php echo isset($post['fullname']) ? htmlspecialchars($post['fullname']) : 'Người dùng ẩn danh'; ?>
                                                    <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">
                                                        <?php echo date('H:i d/m/Y', strtotime($post['created_at'])); ?>
                                                    </span>
                                                </h4>
                                            </div>
                                            <div class="post-content">
                                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                            </div>
                                            <div class="actions d-flex justify-content-around mt-3">
                                                <button class="btn btn-light like-btn" data-post-id="<?php echo $post['id']; ?>">
                                                    <i class="far fa-thumbs-up"></i> Thích
                                                </button>
                                                <button class="btn btn-light comment-btn" data-post-id="<?php echo $post['id']; ?>">
                                                    <i class="far fa-comment"></i> Bình luận
                                                </button>
                                                <button class="btn btn-light share-btn" data-post-id="<?php echo $post['id']; ?>">
                                                    <i class="fas fa-share"></i> Chia sẻ
                                                </button>
                                            </div>

                                            <!-- Hiển thị bình luận -->
                                            <div class="comments mt-3">
                                                <h5 class="fw-700 text-grey-900 font-xssss">Bình luận:</h5>
                                                <?php
                                                $comments = $conn->prepare("SELECT * FROM post_interactions WHERE post_id = ? AND interaction_type = 'comment' ORDER BY created_at ASC");
                                                $comments->bind_param("i", $post['id']);
                                                $comments->execute();

                                                $comments_result = [];
                                                $comments_query = $comments->get_result();
                                                while ($row = $comments_query->fetch_assoc()) {
                                                    $comments_result[] = $row;
                                                }

                                                if (!empty($comments_result)):
                                                    foreach ($comments_result as $comment): ?>
                                                        <div class="comment-item">
                                                            <p class="text-grey-700 font-xssss">
                                                                <strong>Người dùng <?php echo $comment['user_id']; ?>:</strong>
                                                                <?php echo htmlspecialchars($comment['comment_text']); ?>
                                                                <span class="d-block text-grey-500 font-xxs"><?php echo date('H:i d/m/Y', strtotime($comment['created_at'])); ?></span>
                                                            </p>
                                                        </div>
                                                    <?php endforeach;
                                                else: ?>
                                                    <p class="text-grey-500 font-xssss">Chưa có bình luận nào.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-center">Không có bài viết nào.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function() {
                                function handleAjaxResponse(response, button, action) {
                                    try {
                                        let data = typeof response === "string" ? JSON.parse(response) : response;
                                        console.log(data);
                                        if (data.status === "liked") {
                                            button.html('<i class="fas fa-thumbs-up"></i> Đã thích');
                                        } else if (data.status === "unliked") {
                                            button.html('<i class="far fa-thumbs-up"></i> Thích');
                                        } else if (data.status === "success") {
                                            alert(data.message);
                                            location.reload(); // Reload để hiển thị bình luận mới
                                        } else {
                                            alert("Lỗi: " + data.message);
                                        }
                                    } catch (error) {
                                        console.error("Lỗi JSON:", error, "Dữ liệu nhận được:", response);
                                        alert("Phản hồi không hợp lệ từ server");
                                    }
                                }

                                $(".like-btn").click(function() {
                                    let button = $(this);
                                    $.post("list_post.php", {
                                        action: "like",
                                        post_id: button.data("post-id")
                                    }, function(data) {
                                        handleAjaxResponse(data, button, "like");
                                    }).fail(function() {
                                        alert("Không thể kết nối đến server.");
                                    });
                                });

                                $(".comment-btn").click(function() {
                                    let comment = prompt("Nhập bình luận của bạn:");
                                    if (comment) {
                                        $.post("list_post.php", {
                                            action: "comment",
                                            post_id: $(this).data("post-id"),
                                            comment: comment
                                        }, function(data) {
                                            handleAjaxResponse(data, null, "comment");
                                        }).fail(function() {
                                            alert("Không thể kết nối đến server.");
                                        });
                                    }
                                });

                                $(".share-btn").click(function() {
                                    let postId = $(this).data("post-id");
                                    $.post("list_post.php", {
                                        action: "share",
                                        post_id: postId
                                    }, function(data) {
                                        try {
                                            let response = typeof data === "string" ? JSON.parse(data) : data;
                                            if (response.status === "success") {
                                                // Hiển thị modal với liên kết
                                                let shareLink = response.url;
                                                let modalHtml = `
                                <div id="shareModal" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); z-index: 1000;">
                                    <h4>Liên kết chia sẻ</h4>
                                    <input type="text" value="${shareLink}" id="shareLinkInput" style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px;" readonly>
                                    <button id="copyLinkBtn" style="margin-top: 10px; padding: 8px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Sao chép liên kết</button>
                                    <button id="closeModalBtn" style="margin-top: 10px; padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Đóng</button>
                                </div>
                                <div id="modalBackdrop" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>
                            `;
                                                $("body").append(modalHtml);

                                                // Xử lý sao chép liên kết
                                                $("#copyLinkBtn").click(function() {
                                                    let linkInput = document.getElementById("shareLinkInput");
                                                    linkInput.select();
                                                    document.execCommand("copy");
                                                    alert("Liên kết đã được sao chép!");
                                                });

                                                // Đóng modal
                                                $("#closeModalBtn, #modalBackdrop").click(function() {
                                                    $("#shareModal").remove();
                                                    $("#modalBackdrop").remove();
                                                });
                                            } else {
                                                alert("Lỗi: " + response.message);
                                            }
                                        } catch (error) {
                                            console.error("Lỗi JSON:", error, "Dữ liệu nhận được:", data);
                                            alert("Phản hồi không hợp lệ từ server.");
                                        }
                                    }).fail(function() {
                                        alert("Không thể kết nối đến server.");
                                    });
                                });
                            });
                        </script>
                        <script src="../js/plugin.js"></script>
                        <script src="../js/lightbox.js"></script>
                        <script src="../js/scripts.js"></script>
</body>

</html>