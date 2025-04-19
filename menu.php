<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "chat_db");

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
$photo = $user['photo'] ?? '';
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }
    
    .menu-wrapper {
        padding: 20px;
        max-width: 400px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: auto;
    }
    
    h2 {
        text-align: center;
        color: #333;
    }
    
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        background: #ddd;
        display: block;
        margin: 0 auto 20px; /* Center and add spacing below */
    }
    
    .form-section {
        margin: 20px 0;
    }
    
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }
    
    input[type="file"] {
        display: block; /* Make the file input take up a full width for better interaction */
        margin-bottom: 10px;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%; /* Full width */
        background-color: #fff;
    }
    
    button {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
        width: 100%; /* Full width for buttons */
    }
    
    button[type="submit"] {
        background-color: #007BFF; /* Primary color */
        color: white;
    }
    
    button[type="submit"]:hover {
        background-color: #0056b3; /* Darker shade */
    }
    
    form div {
        margin-top: 10px;
        text-align: center; /* Center the buttons and forms */
    }

    .remove-button {
        background-color: red; /* Red for remove */
        color: white;
    }
    
    .remove-button:hover {
        background-color: darkred; /* Darker red */
    }

    .logout-button {
        background-color: #333; /* Dark background for logout */
        color: white;
    }
    
    .logout-button:hover {
        background-color: #555; /* Slightly lighter on hover */
    }
</style>

<div class="menu-wrapper">
    <h2>Profile Settings</h2>
    
    <img src="<?= $photo ? 'uploads/' . $photo : 'uploads/default.png' ?>" class="profile-photo" alt="Profile Picture">

    <div class="form-section">
        <form action="update_photo.php" method="post" enctype="multipart/form-data">
            <label>Upload Profile Picture:</label>
            <input type="file" name="photo" required>
            <button type="submit">Upload</button>
        </form>
    </div>

    <?php if ($photo): ?>
    <div class="form-section">
        <form action="remove_photo.php" method="post">
            <button type="submit" class="remove-button">Remove Photo</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="form-section">
        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
</div>