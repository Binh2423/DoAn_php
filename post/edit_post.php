<?php
session_start();
require '../config/config.php'; // Kết nối database

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// Truy vấn danh sách bài viết
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

// Kiểm tra lỗi truy vấn
if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = trim($_POST["content"]);
    $media = $post['media'];

    // Xử lý file ảnh mới (nếu có)
    if (!empty($_FILES["media"]["name"])) {
        $target_dir = "../uploads/";
        $media = time() . "_" . basename($_FILES["media"]["name"]);
        move_uploaded_file($_FILES["media"]["tmp_name"], $target_dir . $media);

        // Xóa ảnh cũ (nếu có)
        if (!empty($post["media"]) && file_exists($target_dir . $post["media"])) {
            unlink($target_dir . $post["media"]);
        }
    }

    // Cập nhật bài viết
    $stmt = $conn->prepare("UPDATE posts SET content = ?, media = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $content, $media, $post_id);
    $stmt->execute();
    
    echo json_encode(["status" => "success", "message" => "Cập nhật thành công!"]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Bài Viết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { max-width: 600px; margin: auto; margin-top: 50px; }
        #previewMedia img { max-width: 100%; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            ✍️ Chỉnh Sửa Bài Viết
        </div>
        <div class="card-body">
            <form id="editPostForm" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                
                <div class="mb-3">
                    <label for="postContent" class="form-label fw-bold">Nội dung</label>
                    <textarea id="postContent" class="form-control" name="content" rows="4" required><?php echo isset($_GET['content']) ? htmlspecialchars($_GET['content']) : ''; ?></textarea>
                </div>

                <div id="previewMedia">
                    <?php if (!empty($post['media'])): ?>
                        <img src="../uploads/<?php echo $post['media']; ?>" class="img-fluid">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="uploadMedia" class="form-label fw-bold">Thay đổi ảnh/video</label>
                    <input type="file" id="uploadMedia" name="media" class="form-control">
                </div>

                <button type="submit" class="btn btn-success w-100">Lưu Thay Đổi</button>
                <a href="list_post.php" class="btn btn-secondary w-100 mt-2">Hủy</a>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#editPostForm").submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "edit_post.php?id=<?php echo $post_id; ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response){
                if (response.status === "success") {
                    alert(response.message);
                    window.location.href = "list_post.php";
                }
            }
        });
    });
});
</script>

</body>
</html>
