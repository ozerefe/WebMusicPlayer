<!-- Özer Efe Engin 20210702033 -->
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user = $_SESSION['username'];
$name = $_SESSION['name'];

// Genel arama işlemi
if (isset($_GET['general_search'])) {
    $search = $conn->real_escape_string($_GET['general_search']);

    $checkPlaylist = $conn->query("SELECT * FROM PLAYLISTS WHERE title LIKE '%$search%'");
    if ($checkPlaylist && $checkPlaylist->num_rows > 0) {
        header("Location: playlistpage.php?name=" . urlencode($search));
        exit();
    }

    // Şarkı bulunsun bulunamadı her durumda currentmusic.php'ye yönlendir
    header("Location: currentmusic.php?name=" . urlencode($search));
        exit();
}

// Artist arama işlemi
if (isset($_GET['artist_search'])) {
    $search = $conn->real_escape_string($_GET['artist_search']);
        header("Location: artistpage.php?name=" . urlencode($search));
        exit();
}

// History song search operation - ONLY search, don't add to history
if (isset($_GET['history_search'])) {
    $search = $conn->real_escape_string($_GET['history_search']);
    
    // Just redirect to currentmusic.php without adding to play history
    header("Location: currentmusic.php?name=" . urlencode($search));
        exit();
    }

// HTML template'i include et
include 'homepage.html';
?>
