<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

$user_id = $_SESSION['user_id'];

// Fetch unique users who posted a status
$usersResult = mysqli_query($conn, "
    SELECT DISTINCT u.id, u.username, u.profile_pic 
    FROM status s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.created_at DESC
");

$users = [];
while ($row = mysqli_fetch_assoc($usersResult)) {
    $uid = $row['id'];
    $statusesResult = mysqli_query($conn, "SELECT * FROM status WHERE user_id = $uid ORDER BY created_at ASC");
    $statuses = [];
    while ($s = mysqli_fetch_assoc($statusesResult)) {
        $statuses[] = $s;
    }
    $row['statuses'] = $statuses;
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Status</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #000; color: #fff; font-family: Arial, sans-serif; overflow: hidden; }
        .story-container { position: relative; width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .status { max-width: 400px; max-height: 90vh; border-radius: 15px; background: #222; display: none; flex-direction: column; align-items: center; justify-content: space-between; overflow: hidden; position: absolute; transition: transform 0.3s ease; }

        .status { width: 500px; height: 500px; }
        .status.active { display: flex; transform: scale(1.05); }
        .status img, .status video { max-width: 100%; max-height: 100%; object-fit: cover; }
        .status {
    max-width: 500px;
    height: 90vh; /* Set a fixed height to fit within the screen */
    min-height: 300px; /* Optional: Set a minimum height */
    border-radius: 15px; 
    background: #222; 
    display: none; 
    flex-direction: column; 
    align-items: center; 
    justify-content: center; /* Center the content vertically */
    overflow: auto; /* Allow scrolling if content overflows */
    position: absolute; 
    transition: transform 0.3s ease; 
}
        .progress-bar {
            display: flex;
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            gap: 5px;
        }

        .progress {
            flex: 1;
            height: 5px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            overflow: hidden;
        }

        .progress span {

            display: block;
            height: 100%;
            background: white;
            width: 0%;
        }

        .top-info {
            position: absolute;
            top: 25px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 40px;
            color: white;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            cursor: pointer;
        }

        .nav-left { left: 20px; }
        .nav-right { right: 20px; }
    </style>
</head>
<body>

<div class="story-container">
    <?php foreach ($users as $uIndex => $user): ?>
        <div class="status" data-user="<?= $uIndex ?>">
            <div class="progress-bar">
                <?php foreach ($user['statuses'] as $s): ?>
                    <div class="progress"><span></span></div>
                <?php endforeach; ?>
            </div>
            <div class="top-info">
                <img src="uploads/<?= $user['profile_pic'] ?>" alt="">
                <span><?= htmlspecialchars($user['username']) ?></span>
            </div>
            <div class="media">
                <!-- Will be replaced by JS -->
            </div>
        </div>
    <?php endforeach; ?>

    <div class="nav-arrow nav-left" onclick="prevUser()">←</div>
    <div class="nav-arrow nav-right" onclick="nextUser()">→</div>
</div>

<script>
    const stories = document.querySelectorAll('.status');
    let currentUser = 0;
    let currentStatus = 0;
    let timer = null;

    function showStory(uIndex, sIndex) {
        clearTimeout(timer);
        stories.forEach((story, i) => {
            story.classList.toggle('active', i === uIndex);
        });

        const user = stories[uIndex];
        const mediaContainer = user.querySelector('.media');
        const statuses = <?= json_encode(array_column($users, 'statuses')) ?>;
        const status = statuses[uIndex][sIndex];

        mediaContainer.innerHTML = '';
        const progressBars = user.querySelectorAll('.progress span');
        progressBars.forEach((bar, i) => bar.style.width = i < sIndex ? '100%' : '0%');

        let element;
        if (status.type === 'image') {
            element = document.createElement('img');
            element.src = "uploads/" + status.media;
        } else if (status.type === 'video') {
            element = document.createElement('video');
            element.src = "uploads/" + status.media;
            element.autoplay = true;
            element.controls = false;
        } else {
            element = document.createElement('div');
            element.style = "color:white;padding:20px;text-align:center;";
            element.innerText = status.media;
        }

        mediaContainer.appendChild(element);

        let duration = 4000;
        if (status.type === 'video') {
            element.onloadedmetadata = () => {
                duration = (element.duration * 1000) || 5000;
                playProgressBar(progressBars[sIndex], duration);
                timer = setTimeout(() => nextStatus(), duration);
            };
        } else {
            playProgressBar(progressBars[sIndex], duration);
            timer = setTimeout(() => nextStatus(), duration);
        }
    }

    function playProgressBar(bar, duration) {
        let width = 0;
        const interval = setInterval(() => {
            width += 100 / (duration / 50);
            bar.style.width = width + "%";
            if (width >= 100) clearInterval(interval);
        }, 50);
    }

    function nextStatus() {
        const userStatuses = <?= json_encode(array_column($users, 'statuses')) ?>;
        if (currentStatus + 1 < userStatuses[currentUser].length) {
            currentStatus++;
            showStory(currentUser, currentStatus);
        } else {
            nextUser();
        }
    }

    function prevStatus() {
        if (currentStatus > 0) {
            currentStatus--;
            showStory(currentUser, currentStatus);
        }
    }

    function nextUser() {
        const totalUsers = stories.length;
        if (currentUser + 1 < totalUsers) {
            currentUser++;
            currentStatus = 0;
            showStory(currentUser, currentStatus);
        }
    }

    function prevUser() {
        if (currentUser > 0) {
            currentUser--;
            currentStatus = 0;
            showStory(currentUser, currentStatus);
        }
    }

    window.onload = () => showStory(currentUser, currentStatus);
</script>

</body>
</html>
