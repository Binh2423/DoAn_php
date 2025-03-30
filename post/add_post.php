<?php
session_start();
require '../config/config.php'; // Kết nối database


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = trim($_POST["content"]);
    $emotion = $_POST["emotion"] ?? "";
    $location = $_POST["location"] ?? "";
    $tags = $_POST["tags"] ?? "";
    $gif = $_POST["gif"] ?? "";
    $imagePath = "";

    // Xử lý upload ảnh
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Kiểm tra nội dung
    if (empty($content) && empty($imagePath) && empty($gif)) {
        $message = "Vui lòng nhập nội dung hoặc tải ảnh/GIF!";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, emotion, location, tags, image, gif, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issssss", $user['id'], $content, $emotion, $location, $tags, $imagePath, $gif);

        if ($stmt->execute()) {
            $message = "✅ Bài viết đã được đăng!";
        } else {
            $message = "❌ Lỗi khi đăng bài!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Bài Viết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f0f2f5; font-family: Arial, sans-serif; }
        .post-box { width: 500px; background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); padding: 15px; margin: 50px auto; }
        .preview { margin-top: 10px; display: none; }
        .preview img, .preview video { max-width: 100%; border-radius: 5px; }
        .post-footer { display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 10px; }
        .post-footer button { background: none; border: none; cursor: pointer; display: flex; align-items: center; font-size: 14px; padding: 5px 10px; }
        .post-btn { width: 100%; background: #e4e6eb; color: gray; border: none; padding: 10px; border-radius: 5px; font-size: 16px; cursor: not-allowed; }
        .post-btn.active { background: #1877f2; color: white; cursor: pointer; }
    </style>
</head>
<body>
    <div class="post-box">
        <div class="post-header">
            <img src="<?php echo $user['avatar'] ?? 'https://i.pravatar.cc/40'; ?>" class="profile-pic" width="40" height="40" style="border-radius:50%;">
            <div>
            </div>
        </div>
            <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger'; ?>">
            </div>

        <form method="POST" enctype="multipart/form-data">
            <textarea class="form-control" name="content" id="postContent" placeholder="Bạn đang nghĩ gì?" rows="3"></textarea>

            <input type="file" name="image" id="imageUpload" hidden>
            <input type="text" name="tags" id="tagsInput" hidden>
            <input type="text" name="emotion" id="emotionInput" hidden>
            <input type="text" name="location" id="locationInput" hidden>
            <input type="text" name="gif" id="gifInput" hidden>

            <div class="preview" id="preview"></div>

            <div class="post-footer">
                <button type="button" onclick="document.getElementById('imageUpload').click()">📷 Ảnh/Video</button>
                <button type="button" onclick="tagFriends()">👤 Gắn thẻ</button>
                <button type="button" onclick="selectEmotion()">😊 Cảm xúc</button>
                <button type="button" onclick="getLocation()">📍 Vị trí</button>
                <button type="button" onclick="selectGif()">🐦 GIF</button>
            </div>

            <button type="submit" class="post-btn" id="postBtn" disabled>Đăng</button>
        </form>
    </div>

    <script>
        document.getElementById("postContent").addEventListener("input", updatePostButton);

        document.getElementById("imageUpload").addEventListener("change", function(event) {
            let preview = document.getElementById("preview");
            let file = event.target.files[0];

            if (file) {
                let fileURL = URL.createObjectURL(file);
                if (file.type.startsWith("image")) {
                    preview.innerHTML = `<img src="${fileURL}" class="mt-2" style="max-width: 100%;">`;
                } else if (file.type.startsWith("video")) {
                    preview.innerHTML = `<video controls class="mt-2" style="max-width: 100%;"><source src="${fileURL}" type="${file.type}"></video>`;
                }
                preview.style.display = "block";
            }
            updatePostButton();
        });

        function tagFriends() {
            let friend = prompt("Nhập tên bạn bè để gắn thẻ:");
            if (friend) {
                document.getElementById("tagsInput").value = friend;
                showPreview("📌 Đã gắn thẻ: " + friend);
            }
            updatePostButton();
        }

        function selectEmotion() {
            let emotion = prompt("Nhập cảm xúc của bạn (Vui, Buồn, Hào hứng...)");
            if (emotion) {
                document.getElementById("emotionInput").value = emotion;
                showPreview("😊 Cảm xúc: " + emotion);
            }
            updatePostButton();
        }

        function getLocation() {
            navigator.geolocation.getCurrentPosition(function(position) {
                let locationText = `📍 Vị trí: (${position.coords.latitude}, ${position.coords.longitude})`;
                document.getElementById("locationInput").value = locationText;
                showPreview(locationText);
            });
            updatePostButton();
        }

        function selectGif() {
            let gifUrl = prompt("Nhập URL GIF (Giphy/Tenor)");
            if (gifUrl) {
                document.getElementById("gifInput").value = gifUrl;
                showPreview(`<img src="${gifUrl}" class="mt-2" style="max-width: 100%;">`);
            }
            updatePostButton();
        }

        function updatePostButton() {
            let content = document.getElementById("postContent").value.trim();
            let btn = document.getElementById("postBtn");
            btn.classList.toggle("active", content.length > 0 || document.getElementById("preview").innerHTML.length > 0);
            btn.disabled = !(content.length > 0 || document.getElementById("preview").innerHTML.length > 0);
        }
    </script>
</body>
</html>
