<?php session_start(); ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Animated Login & Signup Form</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .hidden {
      display: none;
    }
    .form {
      transition: all 0.5s ease;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="form-toggle">
        <button id="login-toggle" onclick="toggleLogin()">Log In</button>
        <button id="signup-toggle" onclick="toggleSignup()">Sign Up</button>
      </div>

      <div class="form-content">
        <form action="php/login.php" method="POST" id="login-form" class="form">
          <h2>Login</h2>
          
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
        <form action="php/signup.php" method="POST" id="signup-form" class="form hidden">
          <h2>Sign Up</h2>
          
         <input type="text" name="username" placeholder="Username" required>
         <input type="email" name="email" placeholder="Email" required>
         <input type="password" name="password" placeholder="Password" required>
         <button type="submit">Sign Up</button>
    
        </form>
      </div>
    </div>
  </div>

  <script>
    function toggleLogin() {
      document.getElementById('login-form').classList.remove('hidden');
      document.getElementById('signup-form').classList.add('hidden');
    }

    function toggleSignup() {
      document.getElementById('signup-form').classList.remove('hidden');
      document.getElementById('login-form').classList.add('hidden');
    }
  </script>
</body>
</html>