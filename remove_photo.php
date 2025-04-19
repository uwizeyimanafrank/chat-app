<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "your_database");
$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT photo FROM users WHERE id = $user_id");
$row = $result->fetch_assoc();

if ($row && $row['photo']) {
    $file = "uploads/" . $row['photo'];
    if (file_exists($file)) {
        unlink($file);
    }
    $conn->query("UPDATE users SET photo=NULL WHERE id=$user_id");
}
header("Location: chat.php");
exit();
