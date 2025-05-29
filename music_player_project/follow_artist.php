<!-- Özer Efe Engin 20210702033 -->
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

// Hata raporlamayı etkinleştir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Kullanıcı ID'sini al
$user = $_SESSION['username'];
$userIdQuery = "SELECT user_id FROM USERS WHERE username='$user'";
$userIdResult = $conn->query($userIdQuery);
if (!$userIdResult || $userIdResult->num_rows == 0) {
    die("User not found!");
}
$userId = $userIdResult->fetch_assoc()['user_id'];

// Sanatçı ID'sini al
if (!isset($_POST['artist_name'])) {
    die("Artist information missing!");
}
$artistName = $conn->real_escape_string($_POST['artist_name']);
$artistIdQuery = "SELECT artist_id FROM ARTISTS WHERE name='$artistName'";
$artistIdResult = $conn->query($artistIdQuery);
if (!$artistIdResult || $artistIdResult->num_rows == 0) {
    die("Artist not found!");
}
$artistId = $artistIdResult->fetch_assoc()['artist_id'];

// Takip durumunu kontrol et
$checkQuery = "SELECT * FROM FOLLOWS WHERE user_id = $userId AND artist_id = $artistId";
$checkResult = $conn->query($checkQuery);
$isFollowing = ($checkResult && $checkResult->num_rows > 0);

// Gelen isteğe göre işlem gerçekleştir
$action = isset($_POST['action']) ? $_POST['action'] : 'follow';

if ($action == 'unfollow' && $isFollowing) {
    // Takibi bırak
    $conn->query("DELETE FROM FOLLOWS WHERE user_id = $userId AND artist_id = $artistId");
    // Dinleyici sayısını azalt
    $conn->query("UPDATE ARTISTS SET listeners = listeners - 1 WHERE artist_id = $artistId");
} else if ($action == 'follow' && !$isFollowing) {
    // Takip et
    $conn->query("INSERT INTO FOLLOWS (user_id, artist_id, followed_date) VALUES ($userId, $artistId, NOW())");
    // Dinleyici sayısını artır
    $conn->query("UPDATE ARTISTS SET listeners = listeners + 1 WHERE artist_id = $artistId");
}

$conn->close();


$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'homepage.php';
header("Location: $referer");
exit();
?>
