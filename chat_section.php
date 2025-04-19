<?php
// session_start(); // Start the session

// Redirect to login page if user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Validate receiver_id from GET
if (!isset($_GET['receiver_id']) || empty($_GET['receiver_id'])) {
    die("Invalid receiver ID.");
}

$receiver_id = intval($_GET['receiver_id']); // Ensure receiver_id is an integer

// Connect to the database (Procedural)
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    
    <style>* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body, html {
    height: 100%;
    font-family: Arial, sans-serif;
    overflow: hidden;
    
}

.message.sent{
    background-color: #007bff;
    color: white;
    padding: 10px;
    border-radius: 10px;
    margin: 5px 0;
    align-self: flex-end;
    max-width: 60%;
    word-wrap: break-word;
    align-items: flex-end;
    display: flex;
    justify-content: flex-end;
    text-align: right;
    flex-direction: row-reverse;
    flex-wrap: wrap;
    gap: 5px;
    position: relative;
    word-break: break-word;
    overflow-wrap: break-word;
}

.message.received{
    background-color: #f1f1f1;
    color: black;
    padding: 10px;
    border-radius: 10px;
    margin: 5px 0;
    align-self: flex-start;
    max-width: 60%;
    word-wrap: break-word;
    align-items: flex-start;
    display: flex;
    justify-content: flex-start;
    text-align: left;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 5px;
    position: relative;
    word-break: break-word;
    overflow-wrap: break-word;
    
}

.chat-wrapper {
    display: flex;
    height: 100vh;
}

.user-list {
    width: 25%;
    background-color: #f1f1f1;
    padding: 10px;
    overflow-y: auto;
}

.user-list h3 {
    margin-bottom: 10px;
}

.user-list ul {
    list-style-type: none;
}

.user-list li {
    margin: 8px 0;
}

.user-list a {
    text-decoration: none;
    color: #007bff;
}

.chat-area {
    width: 75%;
    display: flex;
    flex-direction: column;
    padding: 10px;
    overflow: hidden;
}

.chat-area h2 {
    margin-bottom: 10px;
}

.chat-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}

#chat-box {
    flex: 1;
    overflow-y: auto;
    background: #f9f9f9;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
}

.message-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #333;
    border-radius: 20px;
    padding: 10px;
    color: white;
    
}

textarea {
    flex: 1;
    height: 40px;
    border-radius: 50px;
    border: 2px solid #007bff;
    padding: 10px 15px;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
    background-color: #f1f1f1;
    resize: none;
}

/* Change color on focus */
textarea:focus {
    border-color: #0056b3;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
}

/* Send Button */
#send-button {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}

#send-button:hover {
    background: #0056b3;
}

/* Emoji Picker */
.emoji-picker {
    position: absolute;
    bottom: 60px;
    left: 20px;
    background: #444;
    padding: 5px;
    border-radius: 10px;
    display: none;
    flex-wrap: wrap;
    gap: 5px;
}

.emoji-picker span {
    cursor: pointer;
    font-size: 18px;
    transition: 0.3s;
}

.emoji-picker span:hover {
    transform: scale(1.2);
}

/* File preview styles */
.preview-container {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    background: #333;
    padding: 5px;
    border-radius: 5px;
    max-width: 250px;
    position: relative;
}

.preview-image {
    max-width: 60px;
    max-height: 60px;
    border-radius: 5px;
    margin-right: 10px;
}

.preview-file {
    font-size: 14px;
    color: white;
    padding: 5px;
    border-radius: 5px;
}

.remove-preview {
    position: absolute;
    top: -5px;
    right: -5px;
    background: red;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    font-size: 12px;
}

/* Responsive: Stack layout on small screens */
@media screen and (max-width: 768px) {
    .chat-wrapper {
        flex-direction: column;
    }

    .user-list, .chat-area {
        width: 100%;
        height: 50%;
    }

    .chat-area {
        height: 50%;
    }
}
/* Chat Messages */
.chat-message {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 20px;
    margin: 5px 0;
    font-size: 14px;
    word-wrap: break-word;
    clear: both;
}

/* Received messages (Left-aligned) */
.received {
    background-color: #e4e6eb;
    color: black;
    align-self: flex-start;
    border-top-left-radius: 0;
    float: left down;
    width: max-content;
}

/* Sent messages (Right-aligned) */
.sent {
    background-color: #0084ff;
    color: white;
    align-self: flex-end;
    border-top-right-radius: 0;
    float: right down;
    width: max-content;
    justify-content: flex-end;
}

/* Clearfix for float issues */
#chat-box::after {
    content: '';
    display: block;
    clear: both;
}
.user-photo {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover; /* Ensures the image maintains its aspect ratio and doesn't stretch */
    margin-right: 10px;
}

</style>
</head>
<body>
    
<div class="chat-wrapper">
    <!-- Left: User List -->
    <aside class="user-list">
        <h3>Registered Users</h3>
        <ul>
            <?php
            $conn = new mysqli("localhost", "root", "", "chat_db");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT id, username FROM users WHERE id != " . $_SESSION["user_id"];
            $sql = "SELECT id, username, photo FROM users WHERE id != " . $_SESSION["user_id"];
            $result = $conn->query($sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Check if the photo field is empty and set photo variable accordingly
                    $photo = !empty($row['photo']) ? 'uploads/' . htmlspecialchars($row['photo']) : ''; // No image path for icon
            
                    
                    $conn = mysqli_connect("localhost", "root", "", "chat_db");
                    
                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }
                    
                    $sql = "SELECT id, username, photo FROM users WHERE id != " . $_SESSION["user_id"];
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $photo = !empty($row['photo']) ? 'uploads/' . htmlspecialchars($row['photo']) : 'data:image/png;base64,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';
                    
                            echo '<li style="display: flex; align-items: center; margin-bottom: 10px;">';
                    
                            // 👇 Image only goes to profile page
                            echo '<a href="profile.php?user_id=' . $row["id"] . '">
                                    <img src="' . (!empty($photo) ? $photo : 'default_user_icon.png') . '" alt="Profile Photo" style="width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:10px;" class="user-photo">
                                  </a>';
                    
                            // 👇 Username links to chat page
                            echo '<a href="chat.php?receiver_id=' . $row["id"] . '" style="text-decoration:none; color:inherit;">' . htmlspecialchars($row["username"]) . '</a>';
                    
                            echo '</li>';
                        
                       }
                    }
                }}
                 else {
                echo "<li>No users found</li>";
            }
            mysqli_close($conn);  
            ?>
        </ul>
    </aside>

    <!-- Right: Chat Area -->
    <main class="chat-area">
        <h2>
            <?php 
           include('db.php'); 

           $getname = "SELECT username, photo FROM users WHERE id = ?";
           $stmt = mysqli_prepare($conn, $getname);
           mysqli_stmt_bind_param($stmt, "i", $receiver_id);
           mysqli_stmt_execute($stmt);
           $result = mysqli_stmt_get_result($stmt);
           
           if ($row = mysqli_fetch_assoc($result)) {
               // Prepare the photo
               if (!empty($row['photo'])) {
                   // Sanitize the filename for output
                   $photo = htmlspecialchars($row['photo']);
                   // Display the image and the username
                   echo '<img class="user-photo" src="uploads/' . $photo . '" alt="User Photo" ">';
               } else {
                   echo '<img src="default-user-icon.png" alt="" class="user-photo" ;">'; // For missing photos
               }
           
               // Display username safely
               echo htmlspecialchars($row['username']);
           } else {
               echo "Unknown User";
           }
           
           mysqli_stmt_close($stmt);
            ?>
        </h2>

        <div class="chat-container">
            <div id="chat-box"></div>
            <div id="preview-container"></div>

            <div class="message-box">
                <input type="hidden" id="receiver_id" value="<?= $_GET['receiver_id']; ?>">

                <!-- File Upload -->
                <label class="file-upload" for="file-input">📎</label>
                <input type="file" id="file-input" style="display: none;">

                <!-- Message Input -->
                <textarea id="message-input" placeholder="Type your message..."></textarea>

                <!-- Send Button -->
                <button id="send-button">Send</button>
            </div>

            <!-- Emoji Picker -->
            <div id="emoji-picker" class="emoji-picker">
                <span onclick="addEmoji('😀')">😀</span>
                <span onclick="addEmoji('😂')">😂</span>
                <span onclick="addEmoji('😍')">😍</span>
                <span onclick="addEmoji('🤖')">🤖</span>
                <span onclick="addEmoji('👍')">👍</span>
            </div>
        </div>
    </main>
</div>



    <script>
        let selectedFile = null;

        document.getElementById("file-input").addEventListener("change", function (event) {
            let file = event.target.files[0];
            let previewContainer = document.getElementById("preview-container");
            previewContainer.innerHTML = ""; // Clear previous preview

            if (file) {
                selectedFile = file;

                let previewBox = document.createElement("div");
                previewBox.classList.add("preview-container");

                let removeBtn = document.createElement("button");
                removeBtn.textContent = "❌";
                removeBtn.classList.add("remove-preview");
                removeBtn.onclick = function () {
                    selectedFile = null;
                    previewContainer.innerHTML = "";
                    document.getElementById("file-input").value = ""; 
                };

                if (file.type.startsWith("image/")) {
                    // Show image preview
                    let img = document.createElement("img");
                    img.src = URL.createObjectURL(file);
                    img.classList.add("preview-image");
                    previewBox.appendChild(img);
                } else {
                    // Show file name
                    let fileInfo = document.createElement("div");
                    fileInfo.textContent = file.name;
                    fileInfo.classList.add("preview-file");
                    previewBox.appendChild(fileInfo);
                }

                previewBox.appendChild(removeBtn);
                previewContainer.appendChild(previewBox);
            }
        });
        document.getElementById("message-input").addEventListener("keypress", function (event) {
            if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault();
            document.getElementById("send-button").click();
            }
        });
        function addEmoji(emoji) {
            document.getElementById("message-input").value += emoji;
        }

        document.getElementById("send-button").addEventListener("click", function () {
            let message = document.getElementById("message-input").value;
            let receiverId = document.getElementById("receiver_id").value;
            let formData = new FormData();
            
            formData.append("message", message);
            formData.append("receiver_id", receiverId);
            
            if (selectedFile) {
                formData.append("file", selectedFile);
            }

            fetch("send_message.php", {
                method: "POST",
                body: formData
            }).then(response => response.text()).then(data => {
                document.getElementById("message-input").value = "";
                document.getElementById("preview-container").innerHTML = "";
                document.getElementById("file-input").value = "";
                selectedFile = null;
                console.log(data);
            });
        });
    </script>

    <script src="js/chat.js"></script>
</body>
</html>
