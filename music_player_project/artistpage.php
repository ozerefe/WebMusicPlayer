<!-- Özer Efe Engin 20210702033 -->
<?php
session_start(); // Session başlat
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$artistName = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';

// Boş arama değeri kontrolü
if (empty($artistName)) {
    $result = false;
    $searchResult = false;
} else {
    $sql = "SELECT A.*, C.country_name 
            FROM ARTISTS A 
            JOIN COUNTRY C ON A.country_id = C.country_id 
            WHERE A.name='$artistName'";
    $result = $conn->query($sql);

    // Eğer tam eşleşme bulunamazsa, arama yap
    if (!$result || $result->num_rows == 0) {
        $searchSql = "SELECT A.*, C.country_name 
                     FROM ARTISTS A 
                     JOIN COUNTRY C ON A.country_id = C.country_id 
                     WHERE A.name LIKE '$artistName%'
                     ORDER BY A.name ASC
                     LIMIT 20";
        
        $searchResult = $conn->query($searchSql);
    }
}

// Kullanıcı takip ediyor mu kontrol et (giriş yapmışsa)
$isFollowing = false;
$isLoggedIn = isset($_SESSION['username']);
$userId = null;

if ($result && $result->num_rows > 0) {
    $artist = $result->fetch_assoc();
    
    if ($isLoggedIn) {
        $userQuery = "SELECT user_id FROM USERS WHERE username = '{$_SESSION['username']}'";
        $userResult = $conn->query($userQuery);
        if ($userResult && $userResult->num_rows > 0) {
            $userId = $userResult->fetch_assoc()['user_id'];
            $followQuery = "SELECT * FROM FOLLOWS WHERE user_id = $userId AND artist_id = {$artist['artist_id']}";
            $followResult = $conn->query($followQuery);
            $isFollowing = ($followResult && $followResult->num_rows > 0);
        }
    }
    
    // Albüm verileri
    $albumSql = "SELECT * FROM ALBUMS WHERE artist_id=" . $artist['artist_id'] . " ORDER BY release_date DESC LIMIT 5";
    $albumRes = $conn->query($albumSql);
    
    // Şarkı verileri
    $songSql = "SELECT S.title, S.image, S.duration, S.song_id FROM SONGS S 
                JOIN ALBUMS A ON S.album_id = A.album_id 
                WHERE A.artist_id=" . $artist['artist_id'] . " 
                ORDER BY S.song_rank ASC LIMIT 5";
    $songRes = $conn->query($songSql);
}

// HTML template'i include et
include 'artistpage.html';

$conn->close();
?>
