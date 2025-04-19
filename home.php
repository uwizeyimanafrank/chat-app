 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #18191A;
            color: white;
        }
        .top-nav {
            background-color: #242526;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-nav .menu-icons i {
            font-size: 1.5rem;
            margin: 0 15px;
            cursor: pointer;
        }
        .sidebar {
            width: 250px;
            background-color: #242526;
            padding: 15px;
            height: 100vh;
            position: fixed;
            top: 50px;
            left: 0;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .post-box {
            background-color: #3A3B3C;
            padding: 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    
    <!-- Top Navigation Bar -->
    <div class="top-nav d-flex align-items-center">
        <div class="logo"> <h4>MySocial</h4> </div>
        <div class="menu-icons">
            <i class="fas fa-home"></i>
            <i class="fas fa-envelope"></i>
            <i class="fas fa-video"></i>
            <i class="fas fa-user-friends"></i>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebar">
        <h5>Menu</h5>
        <ul class="list-unstyled">
            <li><i class="fas fa-user"></i> Profile</li>
            <li><i class="fas fa-bell"></i> Notifications</li>
            <li><i class="fas fa-cog"></i> Settings</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Post Form -->
        <div class="post-box">
            <textarea class="form-control" placeholder="What's on your mind?"></textarea>
            <button class="btn btn-primary mt-2">Post</button>
        </div>
        
        <!-- Posts Feed -->
        <div class="mt-3">
            <div class="post-box">
                <h6>User Name</h6>
                <p>This is a sample post</p>
            </div>
        </div>
    </div>
    
</body>
</html>
