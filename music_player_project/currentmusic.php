<!-- Özer Efe Engin 20210702033 -->
<?php
session_start(); // Session en başta başlatılmalı
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$songName = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';

// Session kontrolü - eğer session yoksa boş değer atayalım
$userId = 0;
if (isset($_SESSION['username'])) {
    $userQuery = "SELECT user_id FROM USERS WHERE username = '{$_SESSION['username']}'";
    $userResult = $conn->query($userQuery);
    if ($userResult && $userResult->num_rows > 0) {
        $userId = $userResult->fetch_assoc()['user_id'];
    }
}

$sql = "SELECT 
            S.title AS song_title, 
            S.duration, 
            S.image AS song_image,
            S.song_id,
            AL.title AS album_title, 
            AL.image AS album_image,
            A.name AS artist_name, 
            A.image AS artist_image
        FROM SONGS S 
        JOIN ALBUMS AL ON S.album_id = AL.album_id 
        JOIN ARTISTS A ON AL.artist_id = A.artist_id 
        WHERE S.title = '$songName'";

$result = $conn->query($sql);

// Eğer tam eşleşme bulunamazsa, arama yap
if (!$result || $result->num_rows == 0) {
    $searchSql = "SELECT 
                    S.title AS song_title, 
                    S.duration, 
                    S.image AS song_image,
                    S.song_id,
                    AL.title AS album_title, 
                    AL.image AS album_image,
                    A.name AS artist_name, 
                    A.image AS artist_image
                FROM SONGS S 
                JOIN ALBUMS AL ON S.album_id = AL.album_id 
                JOIN ARTISTS A ON AL.artist_id = A.artist_id 
                WHERE S.title LIKE '$songName%'
                ORDER BY S.title ASC
                LIMIT 20";
    
    $searchResult = $conn->query($searchSql);
}


include 'currentmusic.html';
?>
