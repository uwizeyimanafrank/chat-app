<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    $filename = uniqid() . '_' . basename($file['name']);
    $upload_dir = "uploads/";

    if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $conn = mysqli_connect("localhost", "root", "", "chat_db");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $user_id = $_SESSION['user_id'];
        $query = "UPDATE users SET photo='$filename' WHERE id=$user_id";
        mysqli_query($conn, $query);

        mysqli_close($conn);
    }
}

header("Location: chat.php");
exit();
