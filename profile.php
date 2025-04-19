<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

// Fetch latest status
$status_query = mysqli_query($conn, "SELECT * FROM status WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
$status = mysqli_fetch_assoc($status_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        .profile-card { background: #fff; padding: 20px; border-radius: 15px; max-width: 400px; margin: auto; text-align: center; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .profile-card img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .username { font-size: 24px; margin: 10px 0; font-weight: bold; }
        .status-box { background: #f0f0f0; padding: 10px; margin: 20px 0; border-radius: 10px; }
        .buttons a { margin: 10px; display: inline-block; background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        .buttons a:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="profile-card">
    <img src="uploads/<?= $user['profile'] ?? 'default.png' ?>" alt="Profile Image">
    <div class="username"><?= htmlspecialchars($user['username']) ?></div>

    <div class="status-box">
        <?php if ($status): ?>
            <strong>Latest Status (<?= $status['type'] ?>):</strong><br>
            <?php if ($status['type'] == 'image'): ?>
                <img src="uploads/<?= $status['media'] ?>" style="max-width:100%; margin-top:10px;">
            <?php else: ?>
                <p><?= htmlspecialchars($status['media']) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p>No status posted yet.</p>
        <?php endif; ?>
    </div>

    <div class="buttons">
        <a href="add_status.php">Add Status</a>
        <a href="view_status.php?user_id=<?= $user_id ?>">View My Status</a>
