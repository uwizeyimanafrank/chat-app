<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

$user_id = $_SESSION['user_id'];

// Get all users with at least one status (latest per user)
$query = "
    SELECT s.*, u.username, u.profile_pic 
    FROM status s
    INNER JOIN (
        SELECT user_id, MAX(created_at) as latest
        FROM status GROUP BY user_id
    ) latest_status ON s.user_id = latest_status.user_id AND s.created_at = latest_status.latest
    JOIN users u ON s.user_id = u.id
    ORDER BY s.created_at DESC
";

$result = mysqli_query($conn, $query);

// Check if the user has at least one status
$my_status = mysqli_query($conn, "SELECT * FROM status WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
$has_status = mysqli_num_rows($my_status) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Status</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
        .status-bar {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .status-box {
            width: 90px;
            text-align: center;
            text-decoration: none;
            color: black;
            position: relative;
        }
        .status-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #999;
            margin: auto;
            transition: 0.3s ease;
        }
        .status-circle.seen {
            border-color: #ccc;
        }
        .status-circle.unseen {
            border-color: #25d366;
        }
        .status-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .username {
            margin-top: 5px;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .add-circle {
            border: 2px dashed #777;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eee;
        }
        .add-icon {
            font-size: 24px;
            color: #25d366;
        }
        .tooltip {
            position: absolute;
            background: #333;
            color: white;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 12px;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            display: none;
        }
        .status-box:hover .tooltip {
            display: block;
        }
    </style>
</head>
<body>

<h2>Status</h2>

<div class="status-bar">
    <!-- Your status -->
    <a href="<?= $has_status ? 'view_status.php' : 'add_status.php' ?>" class="status-box">
        <div class="status-circle <?= $has_status ? 'unseen' : 'add-circle' ?>">
            <?php if ($has_status): ?>
                <img src="uploads/<?= $_SESSION['profile_pic'] ?? 'default.jpg' ?>" alt="You">
            <?php else: ?>
                <div class="add-icon">+</div>
            <?php endif; ?>
        </div>
        <div class="username">You</div>
        <div class="tooltip"><?= $has_status ? 'View your status' : 'Click to add status' ?></div>
    </a>

    <!-- Others' statuses -->
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php if ($row['user_id'] != $user_id): ?>
            <a href="view_status.php?user_id=<?= $row['user_id'] ?>" class="status-box">
                <div class="status-circle unseen">
                    <img src="uploads/<?= $row['profile_pic'] ?? 'default.jpg' ?>" alt="<?= htmlspecialchars($row['username']) ?>">
                </div>
                <div class="username"><?= htmlspecialchars($row['username']) ?></div>
                <div class="tooltip">
                    <?= ucfirst($row['type']) ?> • <?= date("M d, H:i", strtotime($row['created_at'])) ?>
                </div>
            </a>
        <?php endif; ?>
    <?php endwhile; ?>
</div>

</body>
</html>
