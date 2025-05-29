<!-- Özer Efe Engin 20210702033 -->
<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT * FROM USERS WHERE username='$user' AND password='$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $row['username'];
    $_SESSION['name'] = $row['name'];
    header("Location: homepage.php");
    exit();
} else {
    header("Location: login.html?error=1");
    exit();
}
?>
