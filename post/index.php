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

                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($post = $result->fetch_assoc()): ?>
                                <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
                                    <div class="card-body p-0 d-flex">
                                        <figure class="avatar me-3">
                                            <img src="<?php echo htmlspecialchars($post['avatar'] ?? $avatar); ?>" alt="image" class="shadow-sm rounded-circle w45">
                                        </figure>
                                        <h4 class="fw-700 text-grey-900 font-xssss mt-1">
                                            <?php echo isset($post['name']) ? htmlspecialchars($post['name']) : 'Người dùng ẩn danh'; ?>
                                            <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">
                                                <?php echo date('H:i d/m/Y', strtotime($post['created_at'])); ?>
                                            </span>
                                            <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">
                                                <?php echo htmlspecialchars($post['email']); ?> | <?php echo htmlspecialchars($post['phone']); ?>
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="post-content">
                                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                    </div>
                                    <?php if (!empty($post['media'])): ?>
                                        <img src="<?php echo htmlspecialchars($post['media']); ?>" class="post-img">
                                    <?php endif; ?>
                                    <div class="actions d-flex justify-content-around mt-3">
                                        <button class="btn btn-light like-btn" data-post-id="<?php echo $post['id']; ?>">
                                            <i class="far fa-thumbs-up"></i> Thích
                                        </button>
                                        <button class="btn btn-light comment-btn">
                                            <i class="far fa-comment"></i> Bình luận
                                        </button>
                                        <button class="btn btn-light">
                                            <i class="fas fa-share"></i> Chia sẻ
                                        </button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center">Không có bài viết nào.</p>
                        <?php endif; ?>
                    </div>

                    <div class="modal-popup-chat">
                        <div class="modal-popup-wrap bg-white p-0 shadow-lg rounded-3">
                            <div class="modal-popup-header w-100 border-bottom">
                                <div class="card p-3 d-block border-0 d-block">
                                    <figure class="avatar mb-0 float-left me-2">
                                        <img src="../images/avatar_fb_DQ.jpg" alt="image" class="w35 me-1">
                                    </figure>
                                    <h5 class="fw-700 text-primary font-xssss mt-1 mb-1">Hendrix Stamp</h5>
                                    <h4 class="text-grey-500 font-xsssss mt-0 mb-0"><span class="d-inline-block bg-success btn-round-xss m-0"></span> Available</h4>
                                    <a href="#" class="font-xssss position-absolute right-0 top-0 mt-3 me-4"><i class="ti-close text-grey-900 mt-2 d-inline-block"></i></a>
                                </div>
                            </div>
                            <div class="modal-popup-body w-100 p-3 h-auto">
                                <div class="message">
                                    <div class="message-content font-xssss lh-24 fw-500">Hi, how can I help you?</div>
                                </div>
                                <div class="date-break font-xsssss lh-24 fw-500 text-grey-500 mt-2 mb-2">Mon 10:20am</div>
                                <div class="message self text-right mt-2">
                                    <div class="message-content font-xssss lh-24 fw-500">I want those files for you. I want you to send 1 PDF and 1 image file.</div>
                                </div>
                                <div class="snippet pt-3 ps-4 pb-2 pe-3 mt-2 bg-grey rounded-xl float-right" data-title=".dot-typing">
                                    <div class="stage">
                                        <div class="dot-typing"></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="modal-popup-footer w-100 border-top">
                                <div class="card p-3 d-block border-0 d-block">
                                    <div class="form-group icon-right-input style1-input mb-0"><input type="text" placeholder="Start typing.." class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 ps-3"><i class="feather-send text-grey-500 font-md"></i></div>
                                </div>
                            </div>
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