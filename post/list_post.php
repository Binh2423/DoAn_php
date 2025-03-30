<?php include '../config/header.php';
include '../config/navbar-top.php';
include '../config/navbar-left.php';
include '../config/right-chat.php'
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php
    require '../config/config.php'; // Kết nối database
    $user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session
    // Truy vấn danh sách bài viết
    $sql = "SELECT * FROM posts ORDER BY created_at DESC";
    $result = $conn->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Lỗi truy vấn: " . $conn->error);
    }
    ?>

    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($post = $result->fetch_assoc()): ?>
                    <div class="card w-100 shadow-xss rounded-xxl border-0 p-4 mb-3">
                        <div class="card-body p-0 d-flex">
                            <figure class="avatar me-3">
                                <img src="<?php echo $post['avatar'] ?? 'images/avatar_fb_DH.jpg'; ?>" alt="image" class="shadow-sm rounded-circle w45">
                            </figure>
                            <h4 class="fw-700 text-grey-900 font-xssss mt-1">
                                <?php echo isset($post['fullname']) ? htmlspecialchars($post['fullname']) : 'Người dùng ẩn danh'; ?>
                                <span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">
                                    <?php echo date('H:i d/m/Y', strtotime($post['created_at'])); ?>
                                </span>
                            </h4>
                            <a href="#" class="ms-auto" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti-more-alt text-grey-900 btn-round-md bg-greylight font-xss"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-4 rounded-xxl border-0 shadow-lg" aria-labelledby="dropdownMenu2">
                                <div class="card-body p-0 d-flex">
                                    <i class="feather-bookmark text-grey-500 me-3 font-lg"></i>
                                    <h4 class="fw-600 text-grey-900 font-xssss mt-0 me-4">Lưu bài viết</h4>
                                </div>
                                <div class="card-body p-0 d-flex mt-2">
                                    <i class="feather-alert-circle text-grey-500 me-3 font-lg"></i>
                                    <h4 class="fw-600 text-grey-900 font-xssss mt-0 me-4">Ẩn bài viết</h4>
                                </div>
                            </div>
                        </div>
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" class="post-img">
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
    </div>


    <!-- <div class="modal-popup-chat">
                    <div class="modal-popup-wrap bg-white p-0 shadow-lg rounded-3">
                        <div class="modal-popup-header w-100 border-bottom">
                            <div class="card p-3 d-block border-0 d-block">
                                <figure class="avatar mb-0 float-left me-2">
                                    <img src="images/avatar_fb_DQ.jpg" alt="image" class="w35 me-1">
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
                </div> -->

    <script src="../js/plugin.js"></script>

    <script src="../js/lightbox.js"></script>
    <script src="../js/scripts.js"></script>
</body>

</html>