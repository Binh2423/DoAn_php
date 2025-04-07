<?php
session_start();
include "../config/config.php";

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id']; // ID người nhận từ URL

$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $align = ($row['sender_id'] == $sender_id) ? "right" : "left";
    echo "<p style='text-align: $align;'>" . htmlspecialchars($row['content']) . " <small>(" . $row['created_at'] . ")</small></p>";
}

$stmt->close();
$conn->close();
