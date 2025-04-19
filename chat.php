<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
        }

        .app-container {
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        .sidebar {
            width: 60px;
            background-color: #1e1e2f;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px 0;
        }

        .sidebar-item {
            color: white;
            font-size: 24px;
            padding: 15px 0;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .sidebar-item:hover {
            background-color: #3a3a4d;
        }

        .sidebar-top,
        .sidebar-bottom {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .main-content {
            flex-grow: 1;
            background: #f4f4f4;
            display: flex;
            flex-direction: column; /* Ensure vertical layout */
        }

        .chat-container {
            flex-grow: 1; /* Allow chat to take up all remaining space */
            display: flex;
            flex-direction: column; /* Stack messages and input */
            padding: 15px;
            overflow-y: auto; /* Scrollable chat area */
            background: white; /* Make chat area white */
            border: 1px solid #ccc; /* Optional: add border */
        }

        .message-box {
            display: flex;
            align-items: center;
            padding: 10px;
            border-top: 1px solid #ccc;
        }

        .message-input {
            flex-grow: 1; /* Allow the input to grow to take available space */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px; /* Space between input and button */
            width: calc(100% - 50px); /* Ensure it fits within the available space */
            box-sizing: border-box; /* Include padding in width calculation */
        }

        .send-button {
            padding: 10px 15px;
            background-color: #1e1e2f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .send-button:hover {
            background-color: #3a3a4d;
        }

        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }
            .sidebar {
                flex-direction: row;
                justify-content: space-around;
                padding: 10px 0;
            }
            .main-content {
                margin-top: 50px;
            }
            .message-input {
                width: calc(100% - 80px); /* Adjust to fit the send button */
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-top">
                <div class="sidebar-item" onclick="loadSection('chat')" title="Chats">
                    <i class="fas fa-message"></i> 
                </div>
                <div class="sidebar-item" onclick="loadSection('status')" title="Status">
                    <i class="fas fa-circle" style="color: green;"></i> 
                </div>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-item" onclick="loadSection('menu')" title="Menu">
                    <i class="fas fa-bars"></i> 
                </div>
                <div class="sidebar-item" onclick="loadSection('settings')" title="Settings">
                    <i class="fas fa-cog"></i> 
                </div>
            </div>
        </div>

        <!-- Main Section -->
        <div class="main-content" id="main-content">
            <div class="chat-container">
                <?php include("chat_section.php"); ?>
                
            </div>
        </div>
    </div>

    <script>
        function loadSection(section) {
            fetch(section + ".php")
                .then(res => res.text())
                .then(html => {
                    document.getElementById("main-content").innerHTML = `<div class='chat-container'>${html}</div>`;
                })
                .catch(err => console.error("Error loading:", section, err));
        }
    </script>
</body>
</html>