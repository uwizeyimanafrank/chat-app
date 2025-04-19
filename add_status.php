<?php

session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $status_type = $_POST['status_type'];
    $overlay_text = mysqli_real_escape_string($conn, $_POST['overlay_text'] ?? '');
    $bg_music = '';

    // Handle music file
    if (isset($_FILES['bg_music']) && $_FILES['bg_music']['size'] > 0) {
        $music_name = time() . '_music_' . basename($_FILES['bg_music']['name']);
        move_uploaded_file($_FILES['bg_music']['tmp_name'], "uploads/$music_name");
        $bg_music = $music_name;
    }

    // Handle text-only status
    if ($status_type == 'text') {
        $text = mysqli_real_escape_string($conn, $_POST['text_status']);
        mysqli_query($conn, "INSERT INTO status (user_id, media, type, overlay_text, bg_music) 
                             VALUES ($user_id, '$text', 'text', '', '')");
        header("Location: status.php");
        exit;
    }

    // Handle media (image/video) + optional music
    if (isset($_FILES['media_status']) && $_FILES['media_status']['size'] > 0) {
        $file = $_FILES['media_status'];
        $filename = time() . '_' . basename($file['name']);
        $filepath = "uploads/$filename";
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $type_map = [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'video' => ['mp4', 'webm', 'mov']
        ];

        $statusType = '';
        foreach ($type_map as $type => $exts) {
            if (in_array($ext, $exts)) {
                $statusType = $type;
                break;
            }
        }

        if ($statusType) {
            move_uploaded_file($file['tmp_name'], $filepath);
            mysqli_query($conn, "INSERT INTO status (user_id, media, type, overlay_text, bg_music) 
                                 VALUES ($user_id, '$filename', '$statusType', '$overlay_text', '$bg_music')");
            header("Location: status.php");
            exit;
        } else {
            echo "Unsupported media type!";
        }
    }
}
?>





<form method="post" enctype="multipart/form-data">
    <label>Status Type:</label><br>
    <select name="status_type" onchange="toggleFields(this.value)">
        <option value="text">Text</option>
        <option value="image">Image + Music</option>
        <option value="video">Video + Music</option>
    </select>

    <div id="text_field">
        <label>Text Status:</label>
        <textarea name="text_status" rows="4" placeholder="What's on your mind?"></textarea>
    </div>

    <div id="media_field">
        <label>Upload Image/Video:</label>
        <input type="file" name="media_status" accept="image/*,video/*">
    </div>

    <div id="overlay_box">
        <label>Overlay Text (on image/video):</label>
        <input type="text" name="overlay_text" placeholder="Add a caption...">
    </div>

    <div id="music_field">
        <label>Attach Background Music (MP3, optional):</label>
        <input type="file" name="bg_music" accept="audio/*">
    </div>

    <button type="submit">Post Status</button>
</form>

<script>
    function toggleFields(type) {
        document.getElementById('text_field').style.display = type === 'text' ? 'block' : 'none';
        document.getElementById('media_field').style.display = type !== 'text' ? 'block' : 'none';
        document.getElementById('overlay_box').style.display = type !== 'text' ? 'block' : 'none';
        document.getElementById('music_field').style.display = type !== 'text' ? 'block' : 'none';
    }
    toggleFields("text"); // Default state
</script>
