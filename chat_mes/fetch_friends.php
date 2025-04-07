<?php
session_start();
include "../config/config.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, name, avatar FROM users WHERE id != ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $avatar = !empty($row['avatar']) ? htmlspecialchars($row['avatar']) : '../images/default.png';
    echo "<li class='friend-item' data-id='" . $row['id'] . "'>
            <img src='" . $avatar . "' alt='Avatar' class='avatar' width='40' height='40' style='border-radius: 50%;'>
            <span>" . htmlspecialchars($row['name']) . "</span>
          </li>";
}
