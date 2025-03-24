<?php

$host = 'localhost';
$dbname = 'Social_Network';
$username = 'root'; // Thay đổi nếu cần
$password = ''; // Thay đổi nếu cần

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
}

?>
