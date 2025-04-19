 
<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "";  // Change if needed
$dbname = "chat_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
